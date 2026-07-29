<?php
$CIA =& get_instance();
$CIA->load->model('Task_model');

$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

function safeDateShowPenaltyReport($date, $format = 'd-m-Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '';
    }
    return date($format, strtotime($date));
}

function safeCurrencyPenaltyReport($amount, $CIA)
{
    $amount = round((float)$amount);

    if (isset($CIA->Task_model) && method_exists($CIA->Task_model, 'formatIndianCurrency')) {
        return $CIA->Task_model->formatIndianCurrency($amount);
    }

    return number_format($amount, 2);
}

$rowsData = array();

$totalDf = 0;
$totalLoss = 0;
$totalPenaltyAmount = 0;
$totalDelayedDf = 0;
$totalCriticalDf = 0;
$totalProgress = 0;

$penaltySubQuery = "
    (
        SELECT 
            df_id,
            MAX(id) AS po_id,
            MAX(company_name) AS company_name,
            MIN(podate) AS podate,
            MAX(added_by) AS added_by,
            SUM(IFNULL(order_value, 0)) AS order_value,
            SUM(IFNULL(penalityamount, 0)) AS penalty_amount
        FROM poreceived
        WHERE IFNULL(penalityamount, 0) != 0
        AND df_id IS NOT NULL
        AND df_id > 0
        GROUP BY df_id
    ) p
";

$this->db->select('
    a.id,
    a.df_no,
    a.added_on,
    a.df_upload,
    a.df_status,
    p.po_id,
    p.company_name,
    p.podate,
    p.order_value,
    p.penalty_amount,
    u.title,
    u.first_name,
    u.last_name
');
$this->db->from('df_release a');
$this->db->join($penaltySubQuery, 'p.df_id = a.id', 'inner', false);
$this->db->join('system_users u', 'u.user_id = p.added_by', 'left');
$this->db->where('a.df_status', 0);
$this->db->order_by('p.penalty_amount', 'DESC');

$main_q = $this->db->get();

if ($main_q->num_rows() > 0) {
    foreach ($main_q->result() as $rows) {

        $dfId = (int)$rows->id;

        $plannedDateRaw = '';
        $plannedDate = '';

        $planned_q = $this->db
            ->select('MAX(end_date) as enddate')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->get();

        if ($planned_q->num_rows() > 0 && !empty($planned_q->row()->enddate) && $planned_q->row()->enddate != '0000-00-00') {
            $plannedDateRaw = $planned_q->row()->enddate;
            $plannedDate = date('d-m-Y', strtotime($plannedDateRaw));
        }

        $task_q = $this->db
            ->select('id')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->get();

        $totalTasks = $task_q->num_rows();

        $completed_q = $this->db
            ->select('id, task_completed_on, end_date')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->where('task_status', 1)
            ->get();

        $totalCompleted = $completed_q->num_rows();
        $percentage = 0;

        if ($totalTasks > 0) {
            $percentage = round(($totalCompleted * 100) / $totalTasks);
        }

        $delayedTasks = 0;
        $totalDaysDelayed = 0;

        if ($completed_q->num_rows() > 0) {
            foreach ($completed_q->result() as $taskRow) {
                if (
                    !empty($taskRow->task_completed_on) &&
                    $taskRow->task_completed_on != '0000-00-00 00:00:00' &&
                    !empty($taskRow->end_date) &&
                    $taskRow->end_date != '0000-00-00'
                ) {
                    $completedDate = date('Y-m-d', strtotime($taskRow->task_completed_on));

                    if ($completedDate > $taskRow->end_date) {
                        $delayedTasks++;

                        if (method_exists($CIA->Task_model, 'getDays')) {
                            $daysDelayed = $CIA->Task_model->getDays($taskRow->end_date, $completedDate, 1);
                        } else {
                            $daysDelayed = abs(round((strtotime($completedDate) - strtotime($taskRow->end_date)) / 86400));
                        }

                        $totalDaysDelayed += (int)$daysDelayed;
                    }
                }
            }
        }

        $delayedPercentage = 0;
        if ($totalCompleted > 0) {
            $delayedPercentage = round(($delayedTasks * 100) / $totalCompleted);
        }

        $maxDays = 0;
        if (method_exists($CIA->Task_model, 'workDelayed')) {
            $maxDays = (int)$CIA->Task_model->workDelayed($dfId, 0);
        }

        $expectedCompletionDate = '';

        if (!empty($plannedDateRaw)) {
            if ($maxDays > 0) {
                $expectedCompletionDate = date('d-m-Y', strtotime($plannedDateRaw . ' +' . $maxDays . ' days'));
            } else {
                $expectedCompletionDate = date('d-m-Y', strtotime($plannedDateRaw));
            }
        }

        $scheduleStartDate = '';
        $scheduleEndDate = '';
        $totalScheduleDays = 0;

        $schedule_q = $this->db
            ->select('MIN(start_date) as start_date, MAX(end_date) as end_date')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->get();

        if ($schedule_q->num_rows() > 0) {
            $scheduleRow = $schedule_q->row();
            $scheduleStartDate = $scheduleRow->start_date;
            $scheduleEndDate = $scheduleRow->end_date;

            if (
                !empty($scheduleStartDate) &&
                !empty($scheduleEndDate) &&
                $scheduleStartDate != '0000-00-00' &&
                $scheduleEndDate != '0000-00-00'
            ) {
                $startDateObj = new DateTime($scheduleStartDate);
                $endDateObj = new DateTime($scheduleEndDate);
                $interval = $startDateObj->diff($endDateObj);
                $totalScheduleDays = (int)$interval->days;

                if ($totalScheduleDays <= 0) {
                    $totalScheduleDays = 1;
                }
            }
        }

        $orderValue = (float)$rows->order_value;
        $penaltyAmount = (float)$rows->penalty_amount;

        $calculatedLoss = 0;
        if ($orderValue > 0 && $totalScheduleDays > 0 && $maxDays > 0) {
            $oneDayLoss = $orderValue / $totalScheduleDays;
            $calculatedLoss = round($oneDayLoss * $maxDays);
        }

        $ownerName = trim($rows->title . ' ' . $rows->first_name . ' ' . $rows->last_name);

        $riskLevel = 'Low';
        $riskClass = 'success';

        if ($maxDays >= 15 || $calculatedLoss >= 100000) {
            $riskLevel = 'Critical';
            $riskClass = 'danger';
        } elseif ($maxDays >= 7 || $calculatedLoss >= 50000) {
            $riskLevel = 'High';
            $riskClass = 'warning';
        } elseif ($maxDays > 0) {
            $riskLevel = 'Medium';
            $riskClass = 'info';
        }

        if ($maxDays > 0) {
            $totalDelayedDf++;
        }

        if ($riskLevel == 'Critical') {
            $totalCriticalDf++;
        }

        $totalDf++;
        $totalLoss += $calculatedLoss;
        $totalPenaltyAmount += $penaltyAmount;
        $totalProgress += $percentage;

        $rowsData[] = array(
            'id' => $dfId,
            'df_no' => $rows->df_no,
            'df_upload' => $rows->df_upload,
            'company_name' => $rows->company_name,
            'po_date' => safeDateShowPenaltyReport($rows->podate),
            'owner_name' => $ownerName,
            'df_release_date' => safeDateShowPenaltyReport($rows->added_on),
            'planned_date' => $plannedDate,
            'expected_completion_date' => $expectedCompletionDate,
            'percentage' => $percentage,
            'total_tasks' => $totalTasks,
            'total_completed' => $totalCompleted,
            'delayed_tasks' => $delayedTasks,
            'delayed_percentage' => $delayedPercentage,
            'max_days' => $maxDays,
            'order_value' => $orderValue,
            'penalty_amount' => $penaltyAmount,
            'calculated_loss' => $calculatedLoss,
            'total_schedule_days' => $totalScheduleDays,
            'risk_level' => $riskLevel,
            'risk_class' => $riskClass
        );
    }
}

$avgProgress = ($totalDf > 0) ? round($totalProgress / $totalDf) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo sitetitle; ?> Penalty DF Report</title>

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

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
            min-width: 210px;
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
            min-width: 95px;
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

        .penalty-amount {
            color: #c2410c;
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
        .filter-panel select {
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
                            <h3 class="report-title">Penalty Marked DF Report</h3>
                            <div class="report-subtitle">
                                DF-wise penalty, delay, projected completion and calculated loss tracking report.
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-exclamation-triangle"></i> Active DF with penalty amount marked
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div style="margin-bottom:12px;">
                                <a href="<?php echo page_url; ?>Task/dfreleasedashboard/" class="btn btn-default waves-effect waves-light" style="border-radius:20px; font-weight:800;">
                                    <i class="fa fa-exchange"></i> Running DF Dashboard
                                </a>
                            </div>
                            <div style="font-size:13px; opacity:.85;">Total Calculated Loss</div>
                            <div style="font-size:28px; font-weight:900; margin-top:5px;">
                                <i class="fa fa-inr"></i> <?php echo safeCurrencyPenaltyReport($totalLoss, $CIA); ?>
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
                    <div class="kpi-icon"><i class="fa fa-file-text-o"></i></div>
                    <div class="kpi-label">Penalty DF</div>
                    <div class="kpi-value"><?php echo $totalDf; ?></div>
                    <div class="kpi-hint">Active DF with penalty marked</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-inr"></i></div>
                    <div class="kpi-label">Penalty Amount</div>
                    <div class="kpi-value" style="font-size:24px;">
                        <?php echo safeCurrencyPenaltyReport($totalPenaltyAmount, $CIA); ?>
                    </div>
                    <div class="kpi-hint">Total penalty amount marked in PO</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-clock-o"></i></div>
                    <div class="kpi-label">Delayed DF</div>
                    <div class="kpi-value"><?php echo $totalDelayedDf; ?></div>
                    <div class="kpi-hint">DF having delay days greater than zero</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-label">Avg Progress</div>
                    <div class="kpi-value"><?php echo $avgProgress; ?>%</div>
                    <div class="kpi-hint">Overall completion percentage</div>
                </div>
            </div>
        </div>

        <div class="filter-panel">
            <div class="filter-title">
                <i class="fa fa-filter"></i> Smart Filters
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>Risk Level</label>
                    <select id="riskFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Critical">Critical</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Delay Status</label>
                    <select id="delayFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Delayed">Delayed</option>
                        <option value="No Delay">No Delay</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Progress</label>
                    <select id="progressFilter" class="form-control">
                        <option value="">All</option>
                        <option value="0-50">0% - 50%</option>
                        <option value="51-80">51% - 80%</option>
                        <option value="81-100">81% - 100%</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>&nbsp;</label>
                    <button type="button" id="resetFilters" class="btn btn-default btn-block" style="border-radius:20px; font-weight:800;">
                        <i class="fa fa-refresh"></i> Reset Filters
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
                        <th>DF Release Date</th>
                        <th>Projected Completion</th>
                        <th>Expected Completion</th>
                        <th>Progress</th>
                        <th>Delay</th>
                        <th>Penalty Amount</th>
                        <th>Calculated Loss</th>
                        <th>Risk</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($rowsData)) { ?>
                        <?php $m = 1; foreach ($rowsData as $row) { 

                            $progressClass = 'progress-bar-success';
                            if ($row['percentage'] < 50) {
                                $progressClass = 'progress-bar-danger';
                            } elseif ($row['percentage'] < 80) {
                                $progressClass = 'progress-bar-warning';
                            }

                            $riskPillClass = 'pill-success';
                            if ($row['risk_class'] == 'danger') {
                                $riskPillClass = 'pill-danger';
                            } elseif ($row['risk_class'] == 'warning') {
                                $riskPillClass = 'pill-warning';
                            } elseif ($row['risk_class'] == 'info') {
                                $riskPillClass = 'pill-info';
                            }

                            $delayStatus = ($row['max_days'] > 0) ? 'Delayed' : 'No Delay';
                        ?>
                            <tr 
                                data-risk="<?php echo $row['risk_level']; ?>"
                                data-delay="<?php echo $delayStatus; ?>"
                                data-progress="<?php echo $row['percentage']; ?>"
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
                                            <?php echo !empty($row['owner_name']) ? $row['owner_name'] : 'Marketing person not mapped'; ?>
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
                                        <?php echo !empty($row['expected_completion_date']) ? $row['expected_completion_date'] : '-'; ?>
                                        <?php if ($row['max_days'] > 0) { ?>
                                            <div class="date-muted">
                                                +<?php echo $row['max_days']; ?> days delay
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="progress-wrap">
                                        <div class="progress">
                                            <div class="progress-bar <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo $row['percentage']; ?>%;"></div>
                                        </div>
                                        <strong><?php echo $row['percentage']; ?>%</strong>
                                        <div class="date-muted">
                                            <?php echo $row['total_completed']; ?>/<?php echo $row['total_tasks']; ?> tasks
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?php if ($row['max_days'] > 0) { ?>
                                        <span class="status-pill pill-danger">
                                            <?php echo $row['max_days']; ?> Days
                                        </span>
                                    <?php } else { ?>
                                        <span class="status-pill pill-success">
                                            No Delay
                                        </span>
                                    <?php } ?>

                                    <?php if ($row['delayed_tasks'] > 0) { ?>
                                        <div class="date-muted">
                                            <?php echo $row['delayed_tasks']; ?> delayed task
                                        </div>
                                    <?php } ?>
                                </td>

                                <td>
                                    <span class="penalty-amount">
                                        <i class="fa fa-inr"></i> <?php echo safeCurrencyPenaltyReport($row['penalty_amount'], $CIA); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="loss-amount">
                                        <i class="fa fa-inr"></i> <?php echo safeCurrencyPenaltyReport($row['calculated_loss'], $CIA); ?>
                                    </span>
                                    <div class="date-muted">
                                        Based on <?php echo $row['total_schedule_days']; ?> project days
                                    </div>
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
                    <h4>No Penalty Marked DF Found</h4>
                    <p>Currently, no active DF is available with penalty amount marked.</p>
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
        order: [[10, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'Penalty Marked DF Report'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'Penalty Marked DF Report'
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var rowNode = table.row(dataIndex).node();

        var riskFilter = $('#riskFilter').val();
        var delayFilter = $('#delayFilter').val();
        var progressFilter = $('#progressFilter').val();

        var rowRisk = $(rowNode).data('risk');
        var rowDelay = $(rowNode).data('delay');
        var rowProgress = parseInt($(rowNode).data('progress'), 10);

        if (riskFilter !== '' && rowRisk !== riskFilter) {
            return false;
        }

        if (delayFilter !== '' && rowDelay !== delayFilter) {
            return false;
        }

        if (progressFilter !== '') {
            if (progressFilter === '0-50' && !(rowProgress >= 0 && rowProgress <= 50)) {
                return false;
            }

            if (progressFilter === '51-80' && !(rowProgress >= 51 && rowProgress <= 80)) {
                return false;
            }

            if (progressFilter === '81-100' && !(rowProgress >= 81 && rowProgress <= 100)) {
                return false;
            }
        }

        return true;
    });

    $('#riskFilter, #delayFilter, #progressFilter').on('change', function () {
        table.draw();
    });

    $('#resetFilters').on('click', function () {
        $('#riskFilter').val('');
        $('#delayFilter').val('');
        $('#progressFilter').val('');
        table.search('').columns().search('').draw();
    });

});
</script>

</body>
</html>
