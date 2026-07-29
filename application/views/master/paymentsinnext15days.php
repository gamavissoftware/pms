<?php
$CIA =& get_instance();
$CIA->load->model('Task_model');

$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = $company_q->num_rows() > 0 ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

$reportid = $this->uri->segment(3);
$today = date('Y-m-d');
$next15 = date('Y-m-d', strtotime('+15 days'));

$rowsData = array();

$paymentSubQuery = "
    (
        SELECT 
            df_id,
            MIN(end_date) AS payment_due_date,
            MAX(po_id) AS po_id
        FROM task_department_wise_scheduling
        WHERE paymentstage = 1
        AND df_id > 0
        AND end_date BETWEEN ".$this->db->escape($today)." AND ".$this->db->escape($next15)."
        GROUP BY df_id
    ) ps
";

$this->db->select('
    b.id,
    b.df_no,
    b.df_upload,
    b.added_on,
    b.df_status,
    b.on_hold,
    b.priority_marked,
    ps.payment_due_date,
    ps.po_id,
    p.company_name,
    p.podate,
    u.title,
    u.first_name,
    u.last_name
');
$this->db->from('df_release b');
$this->db->join($paymentSubQuery, 'ps.df_id = b.id', 'inner', false);
$this->db->join('poreceived p', 'p.id = ps.po_id', 'left');
$this->db->join('system_users u', 'u.user_id = p.added_by', 'left');
$this->db->where('b.df_status', 0);
$this->db->order_by('ps.payment_due_date', 'ASC');
$main_q = $this->db->get();

$totalReports = 0;
$totalDelayedReports = 0;
$totalCriticalReports = 0;
$totalOnHoldReports = 0;
$totalProgress = 0;

if ($main_q->num_rows() > 0) {
    foreach ($main_q->result() as $rows) {

        $plannedDate = '';
        $plannedDateRaw = '';

        $planned_q = $this->db
            ->select('MAX(end_date) as enddate')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $rows->id)
            ->get();

        if ($planned_q->num_rows() > 0 && !empty($planned_q->row()->enddate) && $planned_q->row()->enddate != '0000-00-00') {
            $plannedDateRaw = $planned_q->row()->enddate;
            $plannedDate = date('d-m-Y', strtotime($plannedDateRaw));
        }

        $task_q = $this->db
            ->select('id')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $rows->id)
            ->get();

        $totalTasks = $task_q->num_rows();

        $completed_q = $this->db
            ->select('id, task_completed_on, end_date')
            ->from('task_department_wise_scheduling')
            ->where('df_id', $rows->id)
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

                        $daysDelayed = 0;
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

        $actualCompletionDate = '';
        $actualCompletionRaw = '';

        if (!empty($plannedDateRaw)) {
            $actualCompletionRaw = date('Y-m-d', strtotime($plannedDateRaw . ' +' . $totalDaysDelayed . ' days'));

            if (method_exists($CIA->Task_model, 'SKIPsingle_holidays')) {
                $actualCompletionRaw = $CIA->Task_model->SKIPsingle_holidays(date('d-m-Y', strtotime($actualCompletionRaw)));
            }

            if (!empty($actualCompletionRaw) && $actualCompletionRaw != '0000-00-00') {
                $actualCompletionDate = date('d-m-Y', strtotime($actualCompletionRaw));
            }
        }

        $show = 1;
        if ($reportid != '') {
            $show = ($delayedPercentage > 0) ? 1 : 0;
        }

        if ($show == 1) {
            $dfOwner = trim($rows->title . ' ' . $rows->first_name . ' ' . $rows->last_name);

            $paymentDueRaw = (!empty($rows->payment_due_date) && $rows->payment_due_date != '0000-00-00') ? $rows->payment_due_date : '';
            $daysLeft = '';
            $urgencyClass = 'success';
            $urgencyText = 'Normal';

            if (!empty($paymentDueRaw)) {
                $daysLeft = floor((strtotime($paymentDueRaw) - strtotime($today)) / 86400);

                if ($daysLeft <= 3) {
                    $urgencyClass = 'danger';
                    $urgencyText = 'Urgent';
                } elseif ($daysLeft <= 7) {
                    $urgencyClass = 'warning';
                    $urgencyText = 'Soon';
                }
            }

            if ($delayedPercentage >= 50) {
                $totalCriticalReports++;
            }

            if ($delayedPercentage > 0) {
                $totalDelayedReports++;
            }

            if ((int)$rows->on_hold == 1) {
                $totalOnHoldReports++;
            }

            $totalReports++;
            $totalProgress += $percentage;

            $rowsData[] = array(
                'id' => $rows->id,
                'df_no' => $rows->df_no,
                'df_upload' => $rows->df_upload,
                'po_date' => (!empty($rows->podate) && $rows->podate != '0000-00-00') ? date('d-m-Y', strtotime($rows->podate)) : '',
                'company_name' => $rows->company_name,
                'df_owner' => $dfOwner,
                'df_release_date' => (!empty($rows->added_on) && $rows->added_on != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($rows->added_on)) : '',
                'payment_due_date' => (!empty($paymentDueRaw)) ? date('d-m-Y', strtotime($paymentDueRaw)) : '',
                'planned_date' => $plannedDate,
                'actual_completion_date' => $actualCompletionDate,
                'percentage' => $percentage,
                'delayed_percentage' => $delayedPercentage,
                'total_tasks' => $totalTasks,
                'total_completed' => $totalCompleted,
                'delayed_tasks' => $delayedTasks,
                'total_days_delayed' => $totalDaysDelayed,
                'days_left' => $daysLeft,
                'urgency_class' => $urgencyClass,
                'urgency_text' => $urgencyText,
                'on_hold' => $rows->on_hold,
                'priority_marked' => $rows->priority_marked
            );
        }
    }
}

$avgProgress = ($totalReports > 0) ? round($totalProgress / $totalReports) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo sitetitle; ?> Payment in Next 15 Days</title>

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
            background: linear-gradient(135deg, <?php echo $themeColor;?> 0%, #1f2937 100%);
            border-radius: 18px;
            padding: 26px;
            margin-bottom: 22px;
            color: #fff;
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
            position: relative;
            overflow: hidden;
        }

        .report-hero:before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            right: -80px;
            top: -90px;
        }

        .report-title {
            font-size: 26px;
            font-weight: 800;
            margin: 0;
            letter-spacing: .2px;
        }

        .report-subtitle {
            margin-top: 7px;
            opacity: .9;
            font-size: 14px;
        }

        .report-date-pill {
            display: inline-block;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            padding: 8px 14px;
            border-radius: 30px;
            font-weight: 600;
            margin-top: 10px;
        }

        .kpi-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.07);
            min-height: 118px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:after {
            content: "";
            position: absolute;
            right: -25px;
            bottom: -25px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(72,114,184,0.08);
        }

        .kpi-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .kpi-value {
            font-size: 32px;
            font-weight: 900;
            color: #111827;
            margin-top: 8px;
            line-height: 1;
        }

        .kpi-hint {
            color: #8a94a6;
            font-size: 12px;
            margin-top: 8px;
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: <?php echo $themeColor;?>;
            color: #fff;
            font-size: 20px;
            position: absolute;
            right: 16px;
            top: 16px;
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
            font-weight: 800;
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

        .df-no-badge {
            background: #eef4ff;
            color: <?php echo $themeColor;?>;
            border: 1px solid rgba(72,114,184,0.18);
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 800;
            display: inline-block;
            white-space: nowrap;
        }

        .company-box {
            text-align: left;
            min-width: 190px;
        }

        .company-name {
            font-weight: 800;
            color: #111827;
        }

        .owner-name {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
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
            font-weight: 800;
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

        .pill-dark {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-action {
            border-radius: 20px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 700;
            margin: 2px;
        }

        .delay-box {
            min-width: 110px;
        }

        .date-stack {
            min-width: 95px;
            font-weight: 700;
        }

        .date-muted {
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 45px 15px;
            color: #6b7280;
        }

        .empty-state h4 {
            font-weight: 800;
            color: #111827;
        }

        .dataTables_wrapper .dt-buttons .btn {
            border-radius: 20px !important;
            margin-right: 5px;
            font-weight: 700;
        }

        .dataTables_filter input,
        .dataTables_length select {
            border-radius: 20px;
            border: 1px solid #d8dee9;
            padding: 6px 12px;
        }

        @media(max-width: 767px) {
            .report-title {
                font-size: 21px;
            }

            .kpi-value {
                font-size: 26px;
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
                            <h3 class="report-title">Payment Due in Next 15 Days</h3>
                            <div class="report-subtitle">
                                Live DF-wise payment stage tracking with completion, delay and urgency indicators.
                            </div>
                            <div class="report-date-pill">
                                <?php echo date('d M Y', strtotime($today)); ?> to <?php echo date('d M Y', strtotime($next15)); ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Report Status</div>
                            <div style="font-size:28px; font-weight:900; margin-top:5px;">
                                <?php echo $totalReports; ?> Active DF
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
                    <div class="kpi-label">Total DF</div>
                    <div class="kpi-value"><?php echo $totalReports; ?></div>
                    <div class="kpi-hint">Payment stage due within 15 days</div>
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

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-clock-o"></i></div>
                    <div class="kpi-label">Delayed DF</div>
                    <div class="kpi-value"><?php echo $totalDelayedReports; ?></div>
                    <div class="kpi-hint">DF having delayed completed tasks</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-exclamation-triangle"></i></div>
                    <div class="kpi-label">Critical / On Hold</div>
                    <div class="kpi-value"><?php echo $totalCriticalReports; ?> / <?php echo $totalOnHoldReports; ?></div>
                    <div class="kpi-hint">High delay and hold cases</div>
                </div>
            </div>
        </div>

        <div class="filter-panel">
            <div class="filter-title">
                <i class="fa fa-filter"></i> Smart Filters
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>Urgency</label>
                    <select id="urgencyFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Soon">Soon</option>
                        <option value="Normal">Normal</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Delay Status</label>
                    <select id="delayFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Delayed">Delayed</option>
                        <option value="On Time">On Time</option>
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
                    <button type="button" id="resetFilters" class="btn btn-default btn-block" style="border-radius:20px; font-weight:700;">
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
                        <th>DF Release</th>
                        <th>Payment Due</th>
                        <th>Projected Completion</th>
                        <th>Expected Completion</th>
                        <th>Progress</th>
                        <th>Delay</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($rowsData)) { ?>
                        <?php $m = 1; foreach ($rowsData as $row) { 
                            $delayStatus = ($row['delayed_percentage'] > 0) ? 'Delayed' : 'On Time';

                            $progressClass = 'progress-bar-success';
                            if ($row['percentage'] < 50) {
                                $progressClass = 'progress-bar-danger';
                            } elseif ($row['percentage'] < 80) {
                                $progressClass = 'progress-bar-warning';
                            }

                            $delayPillClass = ($row['delayed_percentage'] > 0) ? 'pill-danger' : 'pill-success';
                            $urgencyPillClass = 'pill-success';

                            if ($row['urgency_class'] == 'danger') {
                                $urgencyPillClass = 'pill-danger';
                            } elseif ($row['urgency_class'] == 'warning') {
                                $urgencyPillClass = 'pill-warning';
                            }
                        ?>
                            <tr 
                                data-urgency="<?php echo $row['urgency_text']; ?>" 
                                data-delay="<?php echo $delayStatus; ?>" 
                                data-progress="<?php echo $row['percentage']; ?>"
                            >
                                <td><?php echo $m; ?></td>

                                <td>
                                    <span class="df-no-badge"><?php echo strtoupper(trim($row['df_no'])); ?></span>
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
                                        <?php echo !empty($row['payment_due_date']) ? $row['payment_due_date'] : '-'; ?>
                                        <?php if ($row['days_left'] !== '') { ?>
                                            <div class="date-muted">
                                                <?php echo ($row['days_left'] == 0) ? 'Today' : $row['days_left'] . ' days left'; ?>
                                            </div>
                                        <?php } ?>
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
                                        <?php if ($row['total_days_delayed'] > 0) { ?>
                                            <div class="date-muted">
                                                +<?php echo $row['total_days_delayed']; ?> delayed days
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
                                    <div class="delay-box">
                                        <span class="status-pill <?php echo $delayPillClass; ?>">
                                            <?php echo $row['delayed_percentage']; ?>%
                                        </span>
                                        <div class="date-muted">
                                            <?php echo $row['delayed_tasks']; ?> delayed task
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="status-pill <?php echo $urgencyPillClass; ?>">
                                        <?php echo $row['urgency_text']; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ((int)$row['on_hold'] == 1) { ?>
                                        <span class="status-pill pill-warning">On Hold</span>
                                    <?php } elseif ((int)$row['priority_marked'] == 1) { ?>
                                        <span class="status-pill pill-danger">Priority</span>
                                    <?php } else { ?>
                                        <span class="status-pill pill-info">Active</span>
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
                    <h4>No Payment Stage DF Found</h4>
                    <p>No active DF payment stage is due between <?php echo date('d M Y', strtotime($today)); ?> and <?php echo date('d M Y', strtotime($next15)); ?>.</p>
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
                title: 'Payment Due in Next 15 Days'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'Payment Due in Next 15 Days'
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var rowNode = table.row(dataIndex).node();

        var urgencyFilter = $('#urgencyFilter').val();
        var delayFilter = $('#delayFilter').val();
        var progressFilter = $('#progressFilter').val();

        var rowUrgency = $(rowNode).data('urgency');
        var rowDelay = $(rowNode).data('delay');
        var rowProgress = parseInt($(rowNode).data('progress'), 10);

        if (urgencyFilter !== '' && rowUrgency !== urgencyFilter) {
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

    $('#urgencyFilter, #delayFilter, #progressFilter').on('change', function () {
        table.draw();
    });

    $('#resetFilters').on('click', function () {
        $('#urgencyFilter').val('');
        $('#delayFilter').val('');
        $('#progressFilter').val('');
        table.search('').columns().search('').draw();
    });

});
</script>

</body>
</html>