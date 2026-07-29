<?php 
$CIA =& get_instance();
$CIA->load->model('Task_model');

$current_user_id = (int)$this->session->userdata['logged_in']['user_id'];

$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

function safeDateOnHoldDfReport($date, $format = 'd-m-Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '';
    }

    return date($format, strtotime($date));
}

function safeNameOnHoldDfReport($name)
{
    $name = trim((string)$name);

    if ($name == '') {
        return '-';
    }

    return ucwords(strtolower($name));
}

$rowsData = array();

$totalHoldDf = 0;
$totalTasks = 0;
$totalCompletedTasks = 0;
$totalPendingTasks = 0;
$totalProgress = 0;
$totalReleasableDf = 0;

$reportid = $this->uri->segment(3);

/*
|--------------------------------------------------------------------------
| Main Query - On Hold DF
|--------------------------------------------------------------------------
*/
$this->db->select("
    df.id,
    df.df_no,
    df.added_on,
    df.df_upload,
    df.added_by,
    df.on_hold,

    po.company_name,
    po.podate,
    po.po_attachment,
    CONCAT(u.title, ' ', u.first_name, ' ', u.last_name) as marketing_person,

    COUNT(DISTINCT tasks.id) as total_tasks,
    SUM(CASE WHEN tasks.task_status = 1 THEN 1 ELSE 0 END) as completed_tasks,
    MAX(tasks.end_date) as planned_end_date,
    GROUP_CONCAT(
        CASE 
            WHEN tasks.task_status = 1 
            THEN CONCAT(tasks.task_completed_on, '|', tasks.end_date) 
            ELSE NULL 
        END SEPARATOR ';'
    ) as completed_tasks_data
", false);

$this->db->from('df_release df');

$this->db->join(
    '(SELECT p1.*
      FROM poreceived p1
      INNER JOIN (
          SELECT df_id, MAX(id) as max_id
          FROM poreceived
          WHERE df_id IS NOT NULL AND df_id > 0
          GROUP BY df_id
      ) p2 ON p1.id = p2.max_id
    ) po',
    'po.df_id = df.id',
    'left',
    false
);

$this->db->join('system_users u', 'po.added_by = u.user_id', 'left');
$this->db->join('task_department_wise_scheduling tasks', 'tasks.df_id = df.id', 'left');

$this->db->where('df.df_status', 0);
$this->db->where('df.on_hold', 1);
$this->db->group_by('df.id');
$this->db->order_by('df.id', 'DESC');

$q = $this->db->get();

if ($q->num_rows() > 0) {
    foreach ($q->result() as $row) {

        $dfId = (int)$row->id;

        $totalTaskCount = (int)$row->total_tasks;
        $completedTaskCount = (int)$row->completed_tasks;
        $pendingTaskCount = $totalTaskCount - $completedTaskCount;

        if ($pendingTaskCount < 0) {
            $pendingTaskCount = 0;
        }

        $percentage = 0;
        if ($totalTaskCount > 0) {
            $percentage = round(($completedTaskCount * 100) / $totalTaskCount);
        }

        $delayCount = 0;
        $totalDaysDelayed = 0;

        if (!empty($row->completed_tasks_data)) {
            $completedTasksList = explode(';', $row->completed_tasks_data);

            foreach ($completedTasksList as $taskData) {
                $parts = explode('|', $taskData);

                if (count($parts) < 2) {
                    continue;
                }

                $completedOn = $parts[0];
                $endDate = $parts[1];

                if (
                    !empty($completedOn) &&
                    $completedOn != '0000-00-00' &&
                    $completedOn != '0000-00-00 00:00:00' &&
                    !empty($endDate) &&
                    $endDate != '0000-00-00'
                ) {
                    $completedDate = date('Y-m-d', strtotime($completedOn));

                    if ($completedDate > $endDate) {
                        $delayCount++;

                        if (method_exists($CIA->Task_model, 'getDays')) {
                            $daysDelayed = (int)$CIA->Task_model->getDays($endDate, $completedDate, 1);
                        } else {
                            $daysDelayed = abs(round((strtotime($completedDate) - strtotime($endDate)) / 86400));
                        }

                        $totalDaysDelayed += $daysDelayed;
                    }
                }
            }
        }

        $delayPercentage = 0;
        if ($completedTaskCount > 0) {
            $delayPercentage = round(($delayCount * 100) / $completedTaskCount);
        }

        $show = 1;

        if ($reportid != '') {
            $show = ($delayPercentage > 0) ? 1 : 0;
        }

        if ($show != 1) {
            continue;
        }

        $canRelease = false;

        if ($current_user_id == (int)$row->added_by || $current_user_id == 161) {
            $canRelease = true;
        }

        $plannedDate = safeDateOnHoldDfReport($row->planned_end_date);

        $expectedDate = '';

        if (!empty($row->planned_end_date) && $row->planned_end_date != '0000-00-00') {
            $expectedRaw = date('Y-m-d', strtotime($row->planned_end_date . ' +' . $totalDaysDelayed . ' days'));

            if (method_exists($CIA->Task_model, 'SKIPsingle_holidays')) {
                $expectedRaw = $CIA->Task_model->SKIPsingle_holidays($expectedRaw);
            }

            $expectedDate = safeDateOnHoldDfReport($expectedRaw);
        }

        $dfDownload = '-';

        if (!empty($row->df_upload)) {
            $dfDownload = '
                <a href="' . sfdocument . 'Taskdocument/dfattachment/' . $row->df_upload . '" download class="btn btn-primary btn-xs btn-action">
                    <i class="fa fa-download"></i> Download DF
                </a>
            ';
        }

        $poAttachment = '';

        if (!empty($row->po_attachment)) {
            $poAttachment = '
                <br>
                <a href="' . sfdocument . 'Taskdocument/' . $row->po_attachment . '" download class="btn btn-info btn-xs btn-action">
                    <i class="fa fa-download"></i> PO
                </a>
            ';
        }

        $companyName = !empty($row->company_name) ? strtoupper($row->company_name) : '-';
        $marketingPerson = safeNameOnHoldDfReport($row->marketing_person);

        $holdAction = '';

        if ($canRelease) {
            $holdAction = '
                <span class="status-pill pill-danger">
                    <i class="fa fa-pause-circle"></i> On Hold
                </span>
                <br><br>
                <a class="btn btn-primary btn-xs btn-action" href="' . page_url . 'Task/markasunhold/' . $dfId . '" onclick="return confirm(\'Are you sure you want to release this DF again?\');">
                    <i class="fa fa-play"></i> Release Again
                </a>
            ';
            $totalReleasableDf++;
        } else {
            $holdAction = '
                <span class="status-pill pill-danger">
                    <i class="fa fa-pause-circle"></i> On Hold
                </span>
                <br>
                <small class="text-muted">Release permission not available</small>
            ';
        }

        $riskStatus = 'Normal';
        $riskClass = 'success';

        if ($percentage < 50 || $totalDaysDelayed > 7) {
            $riskStatus = 'High Attention';
            $riskClass = 'danger';
        } elseif ($percentage < 80 || $delayCount > 0) {
            $riskStatus = 'Attention';
            $riskClass = 'warning';
        }

        $totalHoldDf++;
        $totalTasks += $totalTaskCount;
        $totalCompletedTasks += $completedTaskCount;
        $totalPendingTasks += $pendingTaskCount;
        $totalProgress += $percentage;

        $rowsData[] = array(
            'id' => $dfId,
            'df_no' => !empty($row->df_no) ? strtoupper($row->df_no) : '-',
            'download' => $dfDownload,
            'po_date' => safeDateOnHoldDfReport($row->podate),
            'po_attachment' => $poAttachment,
            'company_name' => $companyName,
            'marketing_person' => $marketingPerson,
            'df_release_date' => safeDateOnHoldDfReport($row->added_on),
            'planned_date' => $plannedDate,
            'expected_date' => $expectedDate,
            'total_tasks' => $totalTaskCount,
            'completed_tasks' => $completedTaskCount,
            'pending_tasks' => $pendingTaskCount,
            'percentage' => $percentage,
            'delay_count' => $delayCount,
            'total_days_delayed' => $totalDaysDelayed,
            'delay_percentage' => $delayPercentage,
            'hold_action' => $holdAction,
            'risk_status' => $riskStatus,
            'risk_class' => $riskClass,
            'can_release' => $canRelease ? 'Yes' : 'No'
        );
    }
}

$avgProgress = ($totalHoldDf > 0) ? round($totalProgress / $totalHoldDf) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> On Hold DF Report</title>

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

        h3{
            color:#fff !important;
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
            min-height: 118px;
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
            min-width: 190px;
        }

        .company-name {
            font-weight: 900;
            color: #111827;
        }

        .owner-name {
            color: #6b7280;
            font-size: 11px;
            margin-top: 3px;
            font-weight: 700;
        }

        .date-stack {
            min-width: 95px;
            font-weight: 800;
        }

        .date-muted {
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
            margin-top: 3px;
        }

        .progress-wrap {
            min-width: 125px;
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

        .summary-strip {
            background: #f8fafc;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 14px;
            color: #374151;
            font-weight: 700;
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
                            <h3 class="report-title">On Hold DF Report</h3>
                            <div class="report-subtitle">
                                Track all paused DF records with company details, progress, risk level and release action visibility.
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-pause-circle"></i> Active DF currently marked as On Hold
                            </div>
                        </div>

                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Total On Hold DF</div>
                            <div style="font-size:28px; font-weight:900; margin-top:5px;">
                                <?php echo $totalHoldDf; ?> DF
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
                    <div class="kpi-icon"><i class="fa fa-pause-circle"></i></div>
                    <div class="kpi-label">On Hold DF</div>
                    <div class="kpi-value"><?php echo $totalHoldDf; ?></div>
                    <div class="kpi-hint">Total DF currently on hold</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-tasks"></i></div>
                    <div class="kpi-label">Pending Tasks</div>
                    <div class="kpi-value"><?php echo $totalPendingTasks; ?></div>
                    <div class="kpi-hint"><?php echo $totalCompletedTasks; ?> completed of <?php echo $totalTasks; ?></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-label">Average Progress</div>
                    <div class="kpi-value"><?php echo $avgProgress; ?>%</div>
                    <div class="kpi-hint">Average completion across hold DF</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-play"></i></div>
                    <div class="kpi-label">Releasable By You</div>
                    <div class="kpi-value"><?php echo $totalReleasableDf; ?></div>
                    <div class="kpi-hint">DF where release action is available</div>
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
                    <input type="text" id="customSearch" class="form-control" placeholder="Search DF, company, person...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Marketing Person</label>
                    <select id="ownerFilter" class="form-control">
                        <option value="">All Marketing Person</option>
                        <?php
                        $owners = array();

                        foreach ($rowsData as $row) {
                            if (!empty($row['marketing_person']) && $row['marketing_person'] != '-') {
                                $owners[$row['marketing_person']] = $row['marketing_person'];
                            }
                        }

                        if (!empty($owners)) {
                            ksort($owners);
                            foreach ($owners as $owner) {
                                echo '<option value="'.htmlspecialchars($owner).'">'.$owner.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Risk</label>
                    <select id="riskFilter" class="form-control">
                        <option value="">All</option>
                        <option value="High Attention">High Attention</option>
                        <option value="Attention">Attention</option>
                        <option value="Normal">Normal</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Release Permission</label>
                    <select id="releaseFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Yes">Can Release</option>
                        <option value="No">View Only</option>
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
            <div class="summary-strip">
                <i class="fa fa-info-circle"></i>
                This report includes only running DF records where <strong>on_hold = 1</strong>. Release action is visible only to the DF creator or user ID 161.
            </div>

            <table id="example5" class="table manglesh table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>S. NO.</th>
                        <th>DF NO.</th>
                        <th>DOWNLOAD</th>
                        <th>PO DATE</th>
                        <th>COMPANY / MARKETING PERSON</th>
                        <th>DF RELEASE DATE</th>
                        <th>PROJECTED DATE</th>
                        <th>EXPECTED DATE</th>
                        <th>HOLD STATUS</th>
                        <th>DF PROGRESS</th>
                        <th>RISK</th>
                        <th>VIEW GANTT CHART</th>
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

                            $riskClass = 'pill-success';

                            if ($row['risk_class'] == 'danger') {
                                $riskClass = 'pill-danger';
                            } elseif ($row['risk_class'] == 'warning') {
                                $riskClass = 'pill-warning';
                            }
                        ?>
                            <tr 
                                data-owner="<?php echo htmlspecialchars($row['marketing_person']); ?>"
                                data-risk="<?php echo $row['risk_status']; ?>"
                                data-release="<?php echo $row['can_release']; ?>"
                            >
                                <td><?php echo $m; ?></td>

                                <td>
                                    <span class="df-badge">
                                        <?php echo $row['df_no']; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo $row['download']; ?>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['po_date']) ? $row['po_date'] : '-'; ?>
                                    </div>
                                    <?php echo $row['po_attachment']; ?>
                                </td>

                                <td>
                                    <div class="company-box">
                                        <div class="company-name">
                                            <?php echo $row['company_name']; ?>
                                        </div>
                                        <div class="owner-name">
                                            <?php echo strtoupper($row['marketing_person']); ?>
                                        </div>
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
                                        <?php echo !empty($row['expected_date']) ? $row['expected_date'] : '-'; ?>
                                    </div>
                                    <?php if ($row['total_days_delayed'] > 0) { ?>
                                        <div class="date-muted">
                                            <?php echo $row['total_days_delayed']; ?> delayed day(s)
                                        </div>
                                    <?php } ?>
                                </td>

                                <td>
                                    <?php echo $row['hold_action']; ?>
                                </td>

                                <td>
                                    <div class="progress-wrap">
                                        <div class="progress">
                                            <div class="progress-bar <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo $row['percentage']; ?>%;"></div>
                                        </div>
                                        <strong><?php echo $row['percentage']; ?>%</strong>
                                        <div class="date-muted">
                                            <?php echo $row['completed_tasks']; ?>/<?php echo $row['total_tasks']; ?> tasks completed
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="status-pill <?php echo $riskClass; ?>">
                                        <?php echo $row['risk_status']; ?>
                                    </span>
                                </td>

                                <td>
                                    <a href="<?php echo page_url;?>Task/dfgantchartNew/<?php echo $row['id'];?>" target="_blank" class="btn btn-warning btn-xs btn-action">
                                        <i class="fa fa-bar-chart"></i> Gantt Chart
                                    </a>
                                </td>
                            </tr>
                        <?php $m++; } ?>
                    <?php } ?>
                </tbody>
            </table>
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
function stripHtmlOnHoldDf(html) {
    if (html === null || html === undefined) {
        return '';
    }

    var div = document.createElement('div');
    div.innerHTML = html;

    return div.textContent || div.innerText || '';
}

$(document).ready(function () {

    var table = $('#example5').DataTable({
        processing: true,
        fixedHeader: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 250, 500, -1], [25, 50, 100, 250, 500, 'All']],
        responsive: false,
        scrollX: true,
        order: [[0, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'On Hold DF Report',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            return stripHtmlOnHoldDf(data);
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'On Hold DF Report',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            return stripHtmlOnHoldDf(data);
                        }
                    }
                }
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'example5') {
            return true;
        }

        var rowNode = table.row(dataIndex).node();

        var ownerFilter = $('#ownerFilter').val();
        var riskFilter = $('#riskFilter').val();
        var releaseFilter = $('#releaseFilter').val();

        var rowOwner = $(rowNode).data('owner');
        var rowRisk = $(rowNode).data('risk');
        var rowRelease = $(rowNode).data('release');

        if (ownerFilter !== '' && rowOwner !== ownerFilter) {
            return false;
        }

        if (riskFilter !== '' && rowRisk !== riskFilter) {
            return false;
        }

        if (releaseFilter !== '' && rowRelease !== releaseFilter) {
            return false;
        }

        return true;
    });

    $('#customSearch').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#ownerFilter, #riskFilter, #releaseFilter').on('change', function () {
        table.draw();
    });

    $('#resetFilters').on('click', function () {
        $('#customSearch').val('');
        $('#ownerFilter').val('');
        $('#riskFilter').val('');
        $('#releaseFilter').val('');

        table.search('');
        table.columns().search('');
        table.draw();
    });

});
</script>

</body>
</html>