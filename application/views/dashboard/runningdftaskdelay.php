<?php
$CIA =& get_instance();
$CIA->load->model('Task_model');

$administrator_user_ids = [161, 139, 61, 162, 167];
$current_user_id = (int) $this->session->userdata['logged_in']['user_id'];
$is_df_admin = in_array($current_user_id, $administrator_user_ids, true);

$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

function safeDateShowClosedDfReport($date, $format = 'd-m-Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '';
    }

    return date($format, strtotime($date));
}

function safeCurrencyClosedDfReport($amount, $CIA)
{
    $amount = round((float)$amount);

    if (isset($CIA->Task_model) && method_exists($CIA->Task_model, 'formatIndianCurrency')) {
        return $CIA->Task_model->formatIndianCurrency($amount);
    }

    return number_format($amount, 2);
}

function getPositiveDateDiffClosedDfReport($startDate, $endDate)
{
    if (
        empty($startDate) ||
        empty($endDate) ||
        $startDate == '0000-00-00' ||
        $endDate == '0000-00-00'
    ) {
        return 0;
    }

    $startDateObj = new DateTime($startDate);
    $endDateObj = new DateTime($endDate);

    if ($endDateObj <= $startDateObj) {
        return 0;
    }

    return (int)$startDateObj->diff($endDateObj)->days;
}

$rowsData = array();

$totalClosedDf = 0;
$totalDelayedDf = 0;
$totalLoss = 0;
$totalOrderValue = 0;
$totalProgress = 0;

$poSubQuery = "
    (
        SELECT 
            df_id,
            MAX(id) AS po_id,
            MAX(company_name) AS company_name,
            MIN(podate) AS podate,
            MAX(added_by) AS added_by,
            SUM(IFNULL(order_value, 0)) AS order_value
        FROM poreceived
        WHERE df_id IS NOT NULL
        AND df_id > 0
        GROUP BY df_id
    ) po
";

$this->db->select('
    df.id,
    df.df_no,
    df.added_on,
    df.df_upload,
    df.df_status,
    po.po_id,
    po.company_name,
    po.podate,
    po.order_value,
    u.title,
    u.first_name,
    u.last_name
');
$this->db->from('df_release df');
$this->db->join($poSubQuery, 'po.df_id = df.id', 'left', false);
$this->db->join('system_users u', 'u.user_id = po.added_by', 'left');
$this->db->where('df.df_status', 1);

if (!$is_df_admin) {
    $this->db->where(
        'EXISTS (
            SELECT 1
            FROM task_department_wise_scheduling user_tasks
            WHERE user_tasks.df_id = df.id
            AND user_tasks.assigned_user = ' . $current_user_id . '
        )',
        null,
        false
    );
}

$this->db->order_by('df.id', 'DESC');

$main_q = $this->db->get();

if ($main_q->num_rows() > 0) {
    foreach ($main_q->result() as $rows) {

        $dfId = (int)$rows->id;

        $schedule_q = $this->db
            ->select('MIN(start_date) as start_date, MAX(end_date) as end_date')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->get();

        $startDateRaw = '';
        $plannedDateRaw = '';
        $plannedDate = '';
        $totalScheduleDays = 0;

        if ($schedule_q->num_rows() > 0) {
            $scheduleRow = $schedule_q->row();

            if (!empty($scheduleRow->start_date) && $scheduleRow->start_date != '0000-00-00') {
                $startDateRaw = $scheduleRow->start_date;
            }

            if (!empty($scheduleRow->end_date) && $scheduleRow->end_date != '0000-00-00') {
                $plannedDateRaw = $scheduleRow->end_date;
                $plannedDate = date('d-m-Y', strtotime($plannedDateRaw));
            }

            if (!empty($startDateRaw) && !empty($plannedDateRaw)) {
                $startDateObj = new DateTime($startDateRaw);
                $endDateObj = new DateTime($plannedDateRaw);
                $interval = $startDateObj->diff($endDateObj);
                $totalScheduleDays = (int)$interval->days;

                if ($totalScheduleDays <= 0) {
                    $totalScheduleDays = 1;
                }
            }
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

        $maxDays = 0;
        if (method_exists($CIA->Task_model, 'workDelayed')) {
            $maxDays = (int)$CIA->Task_model->workDelayed($dfId, 0);
        }

        $extraDelayedDays = (int)$maxDays;

        $isFullyCompleted = ($totalTasks > 0 && $completedTasks >= $totalTasks);
        $todayDateRaw = date('Y-m-d');
        $expectedCompletionRaw = '';

        if ($isFullyCompleted && !empty($latestCompletion)) {
            $expectedCompletionRaw = $latestCompletion;
        } elseif (!empty($plannedDateRaw)) {
            if ($extraDelayedDays > 0) {
                $expectedCompletionRaw = date('Y-m-d', strtotime($plannedDateRaw . ' +' . $extraDelayedDays . ' days'));
            } else {
                $expectedCompletionRaw = $plannedDateRaw;
            }
        }

        $actualCompletionDate = '';

        if (!empty($expectedCompletionRaw)) {
            $actualCompletionDate = date('d-m-Y', strtotime($expectedCompletionRaw));
        }

        $orderValue = (float)$rows->order_value;
        $calculatedLoss = 0;
        $lossDelayDays = 0;

        /*
         * Updated Loss Formula:
         * Loss starts only after the projected completion date is crossed.
         * For completed DFs, we compare actual completion vs projected completion.
         * For running DFs, we compare today's date vs projected completion.
         */
        if (!empty($plannedDateRaw)) {
            if ($isFullyCompleted && !empty($latestCompletion) && $latestCompletion > $plannedDateRaw) {
                $lossDelayDays = getPositiveDateDiffClosedDfReport($plannedDateRaw, $latestCompletion);
            } elseif (!$isFullyCompleted && $todayDateRaw > $plannedDateRaw) {
                $lossDelayDays = getPositiveDateDiffClosedDfReport($plannedDateRaw, $todayDateRaw);
            }
        }

        if ($orderValue > 0 && $lossDelayDays > 0) {
            $calculatedLoss = round($orderValue / $lossDelayDays);
        }

        $riskLevel = 'Normal';
        $riskClass = 'success';

        if ($lossDelayDays >= 15 || $calculatedLoss >= 100000) {
            $riskLevel = 'High Loss';
            $riskClass = 'danger';
        } elseif ($lossDelayDays > 0 || $calculatedLoss > 0) {
            $riskLevel = 'Delayed';
            $riskClass = 'warning';
        }

        if ($extraDelayedDays > 0 || $delayPercent > 0) {
            $totalDelayedDf++;
        }

        $totalClosedDf++;
        $totalLoss += $calculatedLoss;
        $totalOrderValue += $orderValue;
        $totalProgress += $completionPercent;

        $dfOwner = trim($rows->title . ' ' . $rows->first_name . ' ' . $rows->last_name);

        $rowsData[] = array(
            'id' => $dfId,
            'df_no' => $rows->df_no,
            'df_upload' => $rows->df_upload,
            'company_name' => $rows->company_name,
            'po_date' => safeDateShowClosedDfReport($rows->podate),
            'df_owner' => $dfOwner,
            'df_release_date' => safeDateShowClosedDfReport($rows->added_on),
            'planned_date' => $plannedDate,
            'actual_completion_date' => $actualCompletionDate,
            'completion_percent' => $completionPercent,
            'delay_percent' => $delayPercent,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'delayed_tasks' => $delayedTasks,
            'max_days' => $maxDays,
            'extra_delayed_days' => $extraDelayedDays,
            'loss_delay_days' => $lossDelayDays,
            'total_schedule_days' => $totalScheduleDays,
            'order_value' => $orderValue,
            'calculated_loss' => $calculatedLoss,
            'risk_level' => $riskLevel,
            'risk_class' => $riskClass
        );
    }
}

$avgProgress = ($totalClosedDf > 0) ? round($totalProgress / $totalClosedDf) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Closed DF Report</title>

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

        .loss-amount {
            color: #b91c1c;
            font-size: 14px;
            font-weight: 900;
            white-space: nowrap;
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
                            <h3 class="report-title"><?php echo $is_df_admin ? 'Closed DF Report' : 'My Closed DF Report'; ?></h3>
                            <div class="report-subtitle">
                                <?php echo $is_df_admin ? 'DF-wise closure report with company, marketing person, delay, completion tracking, and loss counted only after projected completion is crossed.' : 'DF-wise closure report showing only those DF records where tasks were assigned to you.'; ?>
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-check-circle"></i> <?php echo $is_df_admin ? 'Completed / Closed DF records' : 'Only DFs with at least one task assigned to you'; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Total Closed DF</div>
                            <div style="font-size:28px; font-weight:900; margin-top:5px;">
                                <?php echo $totalClosedDf; ?> DF
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
                    <div class="kpi-icon"><i class="fa fa-folder"></i></div>
                    <div class="kpi-label">Closed DF</div>
                    <div class="kpi-value"><?php echo $totalClosedDf; ?></div>
                    <div class="kpi-hint">Total completed DF records</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-clock-o"></i></div>
                    <div class="kpi-label">Delayed Closed DF</div>
                    <div class="kpi-value"><?php echo $totalDelayedDf; ?></div>
                    <div class="kpi-hint">Closed DF having delay</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-inr"></i></div>
                    <div class="kpi-label">Calculated Loss</div>
                    <div class="kpi-value" style="font-size:24px;">
                        <?php echo safeCurrencyClosedDfReport($totalLoss, $CIA); ?>
                    </div>
                    <div class="kpi-hint">Loss starts only after the projected completion date is exceeded</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-label">Average Progress</div>
                    <div class="kpi-value"><?php echo $avgProgress; ?>%</div>
                    <div class="kpi-hint">Closed DF completion percentage</div>
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
                    <label>Delay Status</label>
                    <select id="delayFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Delayed">Delayed</option>
                        <option value="On Time">On Time</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Risk</label>
                    <select id="riskFilter" class="form-control">
                        <option value="">All</option>
                        <option value="High Loss">High Loss</option>
                        <option value="Delayed">Delayed</option>
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
                        <th>Projected Completion</th>
                        <th>Expected / Actual Completion</th>
                        <th>Progress</th>
                        <th>Delay</th>
                        <th>Loss Against DF</th>
                        <th>Risk</th>
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

                            $delayStatus = ($row['extra_delayed_days'] > 0 || $row['delay_percent'] > 0) ? 'Delayed' : 'On Time';

                            $riskPillClass = 'pill-success';
                            if ($row['risk_class'] == 'danger') {
                                $riskPillClass = 'pill-danger';
                            } elseif ($row['risk_class'] == 'warning') {
                                $riskPillClass = 'pill-warning';
                            }
                        ?>
                            <tr 
                                data-owner="<?php echo htmlspecialchars($row['df_owner']); ?>"
                                data-delay="<?php echo $delayStatus; ?>"
                                data-risk="<?php echo $row['risk_level']; ?>"
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
                                        <?php echo !empty($row['planned_date']) ? $row['planned_date'] : '-'; ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['actual_completion_date']) ? $row['actual_completion_date'] : '-'; ?>
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
                                    <?php if ($row['extra_delayed_days'] > 0) { ?>
                                        <span class="status-pill pill-danger">
                                            <?php echo $row['extra_delayed_days']; ?> Days
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
                                    <span class="loss-amount">
                                        <i class="fa fa-inr"></i> <?php echo safeCurrencyClosedDfReport($row['calculated_loss'], $CIA); ?>
                                    </span>

                                    <?php if ((int)$row['loss_delay_days'] > 0) { ?>
                                        <div class="date-muted">
                                            Order Value / <?php echo (int)$row['loss_delay_days']; ?> project-overrun days
                                        </div>
                                    <?php } else { ?>
                                        <div class="date-muted">
                                            No loss until projected completion date is exceeded
                                        </div>
                                    <?php } ?>
                                </td>

                                <td>
                                    <span class="status-pill <?php echo $riskPillClass; ?>">
                                        <?php echo $row['risk_level']; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if (!empty($row['df_upload'])) { ?>
                                        <a href="<?php echo sfdocument;?>Taskdocument/dfattachment/<?php echo $row['df_upload'];?>" download class="btn btn-primary btn-xs btn-action">
                                            <i class="fa fa-download"></i> DF
                                        </a>
                                    <?php } ?>

                                    <a href="<?php echo page_url;?>Task/viewdfmeetingmom/<?php echo $row['id'];?>" class="btn btn-success btn-xs btn-action">
                                        <i class="fa fa-comments"></i> MOM
                                    </a>

                                    <a href="<?php echo page_url;?>gantt/<?php echo $row['id'];?>" target="_blank" class="btn btn-warning btn-xs btn-action">
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
                    <h4>No Closed DF Found</h4>
                    <p>Currently, no closed DF record is available.</p>
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
        order: [[0, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'Closed DF Report'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'Closed DF Report'
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'example5') {
            return true;
        }

        var rowNode = table.row(dataIndex).node();

        var ownerFilter = $('#marketingPersonFilter').val();
        var delayFilter = $('#delayFilter').val();
        var riskFilter = $('#riskFilter').val();

        var rowOwner = $(rowNode).data('owner');
        var rowDelay = $(rowNode).data('delay');
        var rowRisk = $(rowNode).data('risk');

        if (ownerFilter !== '' && rowOwner !== ownerFilter) {
            return false;
        }

        if (delayFilter !== '' && rowDelay !== delayFilter) {
            return false;
        }

        if (riskFilter !== '' && rowRisk !== riskFilter) {
            return false;
        }

        return true;
    });

    $('#customSearch').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#marketingPersonFilter, #delayFilter, #riskFilter').on('change', function () {
        table.draw();
    });

    $('#resetFilters').on('click', function () {
        $('#customSearch').val('');
        $('#marketingPersonFilter').val('');
        $('#delayFilter').val('');
        $('#riskFilter').val('');

        table.search('');
        table.columns().search('');
        table.draw();
    });

});
</script>

</body>
</html>
