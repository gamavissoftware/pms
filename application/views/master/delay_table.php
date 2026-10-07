<?php
$user_id = $this->session->userdata['logged_in']['user_id'];

$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

function safeDateShowRunningTaskDelay($date, $format = 'd-m-Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '';
    }

    return date($format, strtotime($date));
}

$rowsData = array();
$departmentOptions = array();

$totalDf = 0;
$totalDelayedDf = 0;
$totalDepartmentsDelayed = 0;
$totalTasks = 0;
$totalDoneTasks = 0;
$totalPendingTasks = 0;
$totalProgress = 0;
$today = date('Y-m-d');
$this->load->helper('df_delay');
$departmentDelayCondition = df_department_overdue_sql($this->db, 't', $today);

$this->db->select('
    a.id,
    a.df_no,
    a.df_description,
    a.added_on,
    a.df_upload,
    b.title,
    b.first_name,
    b.last_name,
    po.company_name,
    po.pono,
    po.po_attachment
');
$this->db->from('df_release a');
$this->db->join('system_users b', 'a.added_by = b.user_id', 'left');
$this->db->join('poreceived po', 'po.df_id = a.id', 'left');
$this->db->where('a.df_status', 0);
$this->db->where('a.on_hold', 0);
$this->db->group_by('a.id');
$this->db->order_by('a.id', 'DESC');

$df_q = $this->db->get();

if ($df_q->num_rows() > 0) {
    foreach ($df_q->result() as $dfRow) {

        $dfId = (int)$dfRow->id;

        $taskSummary_q = $this->db
            ->select('
                COUNT(id) as total_tasks,
                SUM(CASE WHEN task_status = 1 THEN 1 ELSE 0 END) as done_tasks,
                SUM(CASE WHEN task_status IN (0,2) THEN 1 ELSE 0 END) as pending_tasks
            ', false)
            ->from('task_department_wise_scheduling')
            ->where('df_id', $dfId)
            ->get();

        $taskSummary = $taskSummary_q->row();

        $dfTotalTasks = !empty($taskSummary->total_tasks) ? (int)$taskSummary->total_tasks : 0;
        $dfDoneTasks = !empty($taskSummary->done_tasks) ? (int)$taskSummary->done_tasks : 0;
        $dfPendingTasks = !empty($taskSummary->pending_tasks) ? (int)$taskSummary->pending_tasks : 0;

        $workDonePercentage = ($dfTotalTasks > 0) ? round(($dfDoneTasks / $dfTotalTasks) * 100, 2) : 0;

        $departmentRows = array();
        $dfHasDelay = false;

        $overdueCondition = $departmentDelayCondition;
        $dfDelayCondition = df_open_overdue_sql($this->db, 't_df_delay', $today, 'df_delay_release');
        $overdueUsersByDepartment = array();
        $overdueUserRows = $this->db
            ->select('t.department_id, t.assigned_user, assignee.title, assignee.first_name, assignee.last_name')
            ->from('task_department_wise_scheduling t')
            ->join('system_users assignee', 'assignee.user_id = t.assigned_user', 'left')
            ->where('t.df_id', $dfId)
            ->where($overdueCondition, null, false)
            ->get()->result();
        foreach ($overdueUserRows as $overdueUser) {
            $assigneeName = trim((string)$overdueUser->title . ' ' . (string)$overdueUser->first_name . ' ' . (string)$overdueUser->last_name);
            $assigneeName = preg_replace('/\s+/', ' ', $assigneeName);
            $assigneeName = ucwords(strtolower($assigneeName));
            if ($assigneeName === '') {
                $assigneeName = (int)$overdueUser->assigned_user > 0 ? 'User #' . (int)$overdueUser->assigned_user : 'Not Assigned';
            }
            $overdueUsersByDepartment[(int)$overdueUser->department_id][(int)$overdueUser->assigned_user] = $assigneeName;
        }
        foreach ($overdueUsersByDepartment as &$departmentUsers) {
            natcasesort($departmentUsers);
        }
        unset($departmentUsers);

        $dept_q = $this->db
            ->select('
                d.department,
                t.department_id,
                COUNT(t.id) as total_tasks,
                SUM(CASE WHEN t.task_status = 1 THEN 1 ELSE 0 END) as done_tasks,
                SUM(CASE WHEN t.task_status IN (0,2) THEN 1 ELSE 0 END) as pending_tasks,
                MAX(CASE WHEN t.task_status IN (0,2) THEN t.end_date ELSE NULL END) as max_due_date,
                SUM(CASE WHEN ' . $overdueCondition . ' THEN 1 ELSE 0 END) as delayed_tasks,
                MIN(CASE WHEN ' . $overdueCondition . ' THEN t.end_date ELSE NULL END) as oldest_overdue_date
            ', false)
            ->from('task_department_wise_scheduling t')
            ->join('departments d', 't.department_id = d.department_id', 'left')
            ->where('t.df_id', $dfId)
            ->group_by('t.department_id')
            ->order_by('d.department', 'ASC')
            ->get();

        if ($dept_q->num_rows() > 0) {
            foreach ($dept_q->result() as $deptRow) {

                $maxDueDate = $deptRow->max_due_date;
                $delayDays = 0;
                $delayStatus = 'On Time';

                if ((int)$deptRow->delayed_tasks > 0) {
                    $delayDays = (int) round((strtotime($today) - strtotime($deptRow->oldest_overdue_date)) / 86400);
                    $delayStatus = 'Delayed';
                    $totalDepartmentsDelayed++;
                }

                $departmentRows[] = array(
                    'department_id' => (int)$deptRow->department_id,
                    'department' => !empty($deptRow->department) ? ucwords(strtolower($deptRow->department)) : 'N/A',
                    'total_tasks' => (int)$deptRow->total_tasks,
                    'done_tasks' => (int)$deptRow->done_tasks,
                    'pending_tasks' => (int)$deptRow->pending_tasks,
                    'delayed_tasks' => (int)$deptRow->delayed_tasks,
                    'delayed_users' => isset($overdueUsersByDepartment[(int)$deptRow->department_id]) ? array_values($overdueUsersByDepartment[(int)$deptRow->department_id]) : array(),
                    'max_due_date' => safeDateShowRunningTaskDelay($maxDueDate),
                    'delay_days' => $delayDays,
                    'delay_status' => $delayStatus,
                    'progress_percentage' => ((int)$deptRow->total_tasks > 0) ? round(((int)$deptRow->done_tasks / (int)$deptRow->total_tasks) * 100, 2) : 0
                );

                $departmentOptions[(int)$deptRow->department_id] = !empty($deptRow->department) ? ucwords(strtolower($deptRow->department)) : 'N/A';
            }
        }

        $marketingPerson = trim($dfRow->title . ' ' . $dfRow->first_name . ' ' . $dfRow->last_name);
        $marketingPerson = !empty($marketingPerson) ? ucwords(strtolower($marketingPerson)) : 'N/A';

        $dfUpload = '';
        if (!empty($dfRow->df_upload)) {
            $dfUpload = '<a href="' . sfdocument . 'Taskdocument/dfattachment/' . $dfRow->df_upload . '" download class="btn btn-primary btn-xs btn-action"><i class="fa fa-download"></i> DF</a>';
        }

        $poAttachment = '';
        if (!empty($dfRow->po_attachment)) {
            $poAttachment = '<a href="' . sfdocument . 'Taskdocument/' . $dfRow->po_attachment . '" download class="btn btn-warning btn-xs btn-action"><i class="fa fa-download"></i> PO</a>';
        }

        $totalDf++;
        $totalTasks += $dfTotalTasks;
        $totalDoneTasks += $dfDoneTasks;
        $totalPendingTasks += $dfPendingTasks;
        $totalProgress += $workDonePercentage;

        $dfHasDelay = $this->db
            ->select('t_df_delay.id')
            ->from('task_department_wise_scheduling t_df_delay')
            ->join('df_release df_delay_release', 'df_delay_release.id = t_df_delay.df_id', 'inner')
            ->where('t_df_delay.df_id', $dfId)
            ->where($dfDelayCondition, null, false)
            ->limit(1)
            ->get()
            ->num_rows() > 0;

        if ($dfHasDelay) {
            $totalDelayedDf++;
        }

        $rowsData[] = array(
            'id' => $dfId,
            'df_no' => $dfRow->df_no,
            'df_description' => $dfRow->df_description,
            'added_on' => safeDateShowRunningTaskDelay($dfRow->added_on),
            'company_name' => $dfRow->company_name,
            'po_no' => $dfRow->pono,
            'po_attachment' => $poAttachment,
            'df_upload' => $dfUpload,
            'marketing_person' => $marketingPerson,
            'total_tasks' => $dfTotalTasks,
            'done_tasks' => $dfDoneTasks,
            'pending_tasks' => $dfPendingTasks,
            'work_done_percentage' => $workDonePercentage,
            'delay_status' => $dfHasDelay ? 'Delayed' : 'On Time',
            'department_rows' => $departmentRows
        );
    }
}

if (!empty($departmentOptions)) {
    natcasesort($departmentOptions);
}

$avgProgress = ($totalDf > 0) ? round($totalProgress / $totalDf, 2) : 0;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Running DF Task Wise Delay Report</title>

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/our.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <style>
        body {
            background: #f3f6fb;
        }

        h3{
            color : #fff !important;
        }
        .report-hero {
            background: linear-gradient(135deg, <?php echo $themeColor; ?> 0%, #111827 100%);
            border-radius: 18px;
            padding: 26px;
            margin-bottom: 22px;
            color: #fff;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.13);
            position: relative;
            overflow: hidden;
        }

        .report-hero:before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
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
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.25);
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
            box-shadow: 0 8px 24px rgba(31, 41, 55, 0.07);
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
            background: rgba(72, 114, 184, 0.08);
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: <?php echo $themeColor; ?>;
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
            box-shadow: 0 8px 24px rgba(31, 41, 55, 0.05);
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
            box-shadow: 0 10px 28px rgba(31, 41, 55, 0.07);
        }

        table.pretty5 thead th,
        table.pretty4 thead th {
            background: <?php echo $themeColor; ?> !important;
            color: #fff !important;
            font-size: 12px;
            font-weight: 800;
            text-align: center;
            vertical-align: middle !important;
            white-space: nowrap;
        }

        table.pretty5 tbody td,
        table.pretty4 tbody td {
            text-align: center;
            vertical-align: middle !important;
            font-size: 12px;
            color: #374151;
        }

        table.pretty4 thead th {
            background: #111827 !important;
        }

        .df-badge {
            background: #eef4ff;
            color: <?php echo $themeColor; ?>;
            border: 1px solid rgba(72, 114, 184, 0.18);
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 900;
            display: inline-block;
            white-space: nowrap;
        }

        .description-box {
            text-align: left;
            min-width: 220px;
            white-space: normal;
            line-height: 1.5;
        }

        .progress-wrap {
            min-width: 130px;
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

        .child-table-wrap {
            padding: 12px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #e5e7eb;
            margin: 8px 0;
        }

        .task-detail-table thead th {
            background: #f8fafc !important;
            color: #111827 !important;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .task-detail-table tbody td {
            font-size: 12px;
            line-height: 1.5;
            vertical-align: top !important;
            white-space: normal;
        }

        .inline-task-loading,
        .inline-task-error {
            padding: 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .inline-task-loading {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .inline-task-error {
            background: #fef2f2;
            color: #b91c1c;
        }

        .child-table-caption {
            font-size: 12px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .summary-hint {
            font-size: 12px;
            color: #6b7280;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .df-summary-row {
            cursor: pointer;
        }

        .df-summary-row:hover {
            background: #f8fbff !important;
        }

        .shown {
            background: #f8fafc !important;
        }

        #taskModal .modal-dialog, #departmentDfDelayModal .modal-dialog {
            width: calc(100% - 20px);
            max-width: none !important;
            margin: 10px auto;
        }

        #taskModal .modal-body, #departmentDfDelayModal .modal-body {
            max-height: calc(100vh - 110px);
            overflow: auto;
        }

        .modal-content {
            border-radius: 18px;
            border: none;
            box-shadow: 0 20px 55px rgba(0, 0, 0, .18);
        }

        .modal-header {
            background: linear-gradient(135deg, <?php echo $themeColor; ?> 0%, #111827 100%);
            color: #fff;
            border-radius: 18px 18px 0 0;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 900;
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
        .department-delay-chart { margin-bottom: 20px; padding: 22px; background: #fff; border-radius: 16px; }
        .department-delay-chart h4 { margin: 0 0 8px; font-weight: 700; }
        .department-delay-bar-row { display: grid; grid-template-columns: minmax(120px, 210px) minmax(60px, 1fr) 85px; gap: 12px; align-items: center; margin: 14px 0; }
        button.department-delay-bar-row { width: 100%; border: 0; background: transparent; text-align: left; padding: 6px; cursor: pointer; color: inherit; }
        button.department-delay-bar-row:hover { background: #fff7ed; }
        button.department-delay-bar-row:focus { outline: 2px solid #2563eb; outline-offset: 2px; }
        .department-delay-name { font-weight: 600; overflow-wrap: anywhere; }
        .department-delay-track { height: 22px; background: #f1f5f9; border-radius: 5px; overflow: hidden; }
        .department-delay-fill { height: 100%; background: #dc633b; border-radius: 5px; }
        .department-delay-value { text-align: right; font-weight: 700; color: #9a3412; }
        @media (max-width: 600px) {
            .department-delay-chart { padding: 14px; }
            .department-delay-bar-row { grid-template-columns: 105px minmax(40px, 1fr) 65px; gap: 8px; font-size: 12px; }
        }
    </style>
</head>

<body>

<header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
</header>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <div class="report-hero">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="report-title">Running DF Task Wise Delay Report</h3>
                            <div class="report-subtitle">
                                DF-wise task completion, department-wise pending work, due dates and delay tracking.
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-tasks"></i> Active and non-hold DF records
                            </div>
                        </div>

                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Total Running DF</div>
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
                    <div class="kpi-icon"><i class="fa fa-folder-open"></i></div>
                    <div class="kpi-label">Running DF</div>
                    <div class="kpi-value"><?php echo $totalDf; ?></div>
                    <div class="kpi-hint">Active non-hold DF records</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-clock-o"></i></div>
                    <div class="kpi-label">Delayed DF</div>
                    <div class="kpi-value"><?php echo $totalDelayedDf; ?></div>
                    <div class="kpi-hint">DF having delayed department work</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-list"></i></div>
                    <div class="kpi-label">Pending Tasks</div>
                    <div class="kpi-value"><?php echo $totalPendingTasks; ?></div>
                    <div class="kpi-hint">Total pending or in-process tasks</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-label">Average Progress</div>
                    <div class="kpi-value"><?php echo $avgProgress; ?>%</div>
                    <div class="kpi-hint">Average DF work done percentage</div>
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
                    <input type="text" id="customSearch" class="form-control" placeholder="Search DF, description, person...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Marketing Person</label>
                    <select id="marketingPersonFilter" class="form-control">
                        <option value="">All Marketing Persons</option>
                        <?php
                        $owners = array();

                        foreach ($rowsData as $row) {
                            if (!empty($row['marketing_person']) && $row['marketing_person'] != 'N/A') {
                                $owners[$row['marketing_person']] = $row['marketing_person'];
                            }
                        }

                        if (!empty($owners)) {
                            ksort($owners);
                            foreach ($owners as $owner) {
                                echo '<option value="' . htmlspecialchars($owner) . '">' . $owner . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Department</label>
                    <select id="departmentFilter" class="form-control">
                        <option value="">All Departments</option>
                        <?php foreach ($departmentOptions as $departmentId => $departmentName) { ?>
                            <option value="<?php echo (int) $departmentId; ?>">
                                <?php echo htmlspecialchars($departmentName, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php } ?>
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
                    <label>Progress</label>
                    <select id="progressFilter" class="form-control">
                        <option value="">All</option>
                        <option value="0-50">0% - 50%</option>
                        <option value="51-80">51% - 80%</option>
                        <option value="81-100">81% - 100%</option>
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

        <section class="department-delay-chart" aria-labelledby="departmentDelayTitle">
            <h4 id="departmentDelayTitle">Maximum Delay by Department</h4>
            <p class="summary-hint">Longest overdue pending task in each department, in days. Highest delay first; based on all DFs matching the filters, across every table page. Click a bar to see DF-wise delays.</p>
            <p id="departmentDelayScope" class="date-muted" aria-live="polite"></p>
            <div id="departmentDelayBars" role="list" aria-label="Departments ranked by maximum delay in days"></div>
        </section>

        <div class="modern-table-card table-responsive">
            <div class="summary-hint">
                Click any DF row to open department progress. If you choose a department filter, matching rows open automatically with that department's task summary.
            </div>
            <table class="table table-striped table-bordered pretty5" id="exampleS" style="width:100%">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>DF No.</th>
                        <th>DF Description</th>
                        <th>DF Release Date</th>
                        <th>Marketing Person</th>
                        <th>Work Done</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 1; foreach ($rowsData as $row) {

                        $progressClass = 'progress-bar-success';

                        if ($row['work_done_percentage'] < 50) {
                            $progressClass = 'progress-bar-danger';
                        } elseif ($row['work_done_percentage'] < 80) {
                            $progressClass = 'progress-bar-warning';
                        }

                        $rowDepartmentIds = array();
                        foreach ($row['department_rows'] as $departmentRow) {
                            $rowDepartmentIds[] = (int) $departmentRow['department_id'];
                        }
                    ?>
                        <tr
                            class="df-summary-row"
                            data-dfid="<?php echo (int) $row['id']; ?>"
                            data-owner="<?php echo htmlspecialchars($row['marketing_person']); ?>"
                            data-df-no="<?php echo htmlspecialchars(strtoupper($row['df_no']), ENT_QUOTES, 'UTF-8'); ?>"
                            data-df-description="<?php echo htmlspecialchars((string) $row['df_description'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-company-name="<?php echo htmlspecialchars((string) $row['company_name'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-po-no="<?php echo htmlspecialchars((string) $row['po_no'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-delay="<?php echo $row['delay_status']; ?>"
                            data-progress="<?php echo $row['work_done_percentage']; ?>"
                            data-overall-progress="<?php echo $row['work_done_percentage']; ?>"
                            data-overall-done="<?php echo (int) $row['done_tasks']; ?>"
                            data-overall-total="<?php echo (int) $row['total_tasks']; ?>"
                            data-overall-pending="<?php echo (int) $row['pending_tasks']; ?>"
                            data-overall-delay="<?php echo htmlspecialchars($row['delay_status'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-departments="<?php echo htmlspecialchars(implode(',', $rowDepartmentIds), ENT_QUOTES, 'UTF-8'); ?>"
                            data-department-summary="<?php echo htmlspecialchars(json_encode($row['department_rows']), ENT_QUOTES, 'UTF-8'); ?>"
                        >
                            <td><?php echo $i; ?></td>

                            <td>
                                <span class="df-badge">
                                    <?php echo strtoupper($row['df_no']); ?>
                                </span>
                            </td>

                            <td>
                                <div class="description-box">
                                    <?php echo !empty($row['df_description']) ? $row['df_description'] : '-'; ?>

                                    <?php if (!empty($row['company_name'])) { ?>
                                        <div style="font-size:11px; color:#6b7280; margin-top:5px;">
                                            Company: <?php echo $row['company_name']; ?>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($row['po_no'])) { ?>
                                        <div style="font-size:11px; color:#6b7280;">
                                            PO: <?php echo $row['po_no']; ?>
                                        </div>
                                    <?php } ?>

                                    <div style="margin-top:6px;">
                                        <?php echo $row['df_upload']; ?>
                                        <?php echo $row['po_attachment']; ?>
                                    </div>
                                </div>
                            </td>

                            <td><?php echo !empty($row['added_on']) ? $row['added_on'] : '-'; ?></td>

                            <td><?php echo $row['marketing_person']; ?></td>

                            <td>
                                <div class="progress-wrap summary-progress-cell">
                                    <div class="progress">
                                        <div class="progress-bar progress-summary-bar <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo $row['work_done_percentage']; ?>%;"></div>
                                    </div>
                                    <strong class="progress-summary-value"><?php echo $row['work_done_percentage']; ?>%</strong>
                                    <div class="progress-summary-meta" style="font-size:11px; color:#6b7280;">
                                        <?php echo $row['done_tasks']; ?>/<?php echo $row['total_tasks']; ?> done,
                                        <?php echo $row['pending_tasks']; ?> pending
                                    </div>
                                </div>
                            </td>

                            <td class="summary-status-cell">
                                <?php if ($row['delay_status'] == 'Delayed') { ?>
                                    <span class="status-pill pill-danger">Delayed</span>
                                <?php } else { ?>
                                    <span class="status-pill pill-success">On Time</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>

            <?php foreach ($rowsData as $row) { ?>
                <div id="child-detail-<?php echo $row['id']; ?>" style="display:none;">
                    <div class="child-table-wrap">
                        <div class="child-table-caption">Department-wise task summary</div>
                        <table class="table table-striped table-bordered pretty4" style="width:100%; margin-bottom:0px;">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Department Name</th>
                                    <th>No. of Tasks</th>
                                    <th>Done</th>
                                    <th>Pending</th>
                                    <th>Delayed</th>
                                    <th>Due Date to Close</th>
                                    <th>Delay Days</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($row['department_rows'])) { ?>
                                    <?php $k = 1; foreach ($row['department_rows'] as $deptRow) { ?>
                                        <tr class="department-detail-row" data-department-id="<?php echo (int) $deptRow['department_id']; ?>">
                                            <td><?php echo $k; ?></td>

                                            <td><?php echo $deptRow['department']; ?></td>

                                            <?php foreach (array('all' => 'total_tasks', 'done' => 'done_tasks', 'pending' => 'pending_tasks', 'delayed' => 'delayed_tasks') as $taskFilter => $countKey) { ?>
                                            <td>
                                                <button type="button"
                                                    class="view-task-details status-pill <?php echo $taskFilter === 'delayed' ? 'pill-danger' : ($taskFilter === 'pending' ? 'pill-warning' : 'pill-success'); ?>"
                                                    data-dfid="<?php echo (int)$row['id']; ?>"
                                                    data-deptid="<?php echo (int)$deptRow['department_id']; ?>"
                                                    data-dfno="<?php echo htmlspecialchars(strtoupper($row['df_no']), ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-department="<?php echo htmlspecialchars($deptRow['department'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-status="<?php echo $taskFilter; ?>"
                                                    style="cursor:pointer; border:0;"
                                                    aria-label="Show <?php echo $taskFilter; ?> tasks">
                                                    <?php echo (int)$deptRow[$countKey]; ?>
                                                </button>
                                            </td>
                                            <?php } ?>

                                            <td>
                                                <?php echo !empty($deptRow['max_due_date']) ? $deptRow['max_due_date'] : 'N/A'; ?>
                                            </td>

                                            <td
                                                class="view-task-details"
                                                data-dfid="<?php echo $row['id']; ?>"
                                                data-deptid="<?php echo $deptRow['department_id']; ?>"
                                                data-dfno="<?php echo strtoupper($row['df_no']); ?>"
                                                data-department="<?php echo htmlspecialchars($deptRow['department']); ?>"
                                                data-status="<?php echo ($deptRow['delay_days'] > 0) ? 'delayed' : 'on-time'; ?>"
                                                style="cursor:pointer;"
                                            >
                                                <?php if ($deptRow['delay_days'] > 0) { ?>
                                                    <span class="status-pill pill-danger">
                                                        <?php echo $deptRow['delay_days']; ?> days delay
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="status-pill pill-success">
                                                        On Time
                                                    </span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php $k++; } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="8">No department-wise task found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="departmentDfDelayModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="departmentDfDelayTitle">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">&times;</button>
                        <h4 class="modal-title" id="departmentDfDelayTitle">DF-wise Department Delays</h4>
                    </div>
                    <div class="modal-body">
                        <p id="departmentDfDelaySummary"></p>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead><tr><th>DF No.</th><th>DF Description</th><th>Company</th><th>Responsible Users (Overdue Tasks)</th><th>Maximum Delay (Days)</th><th>Overdue Pending Tasks</th></tr></thead>
                                <tbody id="departmentDfDelayRows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="taskModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
                        <h4 class="modal-title">
                            <strong>DF No:</strong> <span id="modalDfNo"></span> |
                            <strong>Department:</strong> <span id="modalDepartment"></span> | <span id="modalTaskFilter"></span>
                        </h4>
                    </div>

                    <div class="modal-body">
                        <div id="taskData">
                            <div style="text-align:center; padding:20px;">
                                Please wait...
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php $this->load->view('common/footer'); ?>

    </div>
</div>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/detect.js"></script>
<script src="<?php echo assets_url; ?>js/fastclick.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url; ?>js/waves.js"></script>
<script src="<?php echo assets_url; ?>js/wow.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>

<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

<script>
var table = null;
var lastOpenedDfId = null;
var taskDetailsCache = {};
var taskDetailsDataCache = {};
var departmentExportCookieName = 'delay_table_export_token';
var departmentExportPoller = null;
var departmentExportTimeout = null;
var departmentExportInProgress = false;

function clearDepartmentExportWatchers() {
    if (departmentExportPoller) {
        clearInterval(departmentExportPoller);
        departmentExportPoller = null;
    }

    if (departmentExportTimeout) {
        clearTimeout(departmentExportTimeout);
        departmentExportTimeout = null;
    }
}

function setDepartmentExportCookie(name, value, maxAgeSeconds) {
    var cookie = name + '=' + encodeURIComponent(value) + '; path=/';

    if (typeof maxAgeSeconds === 'number') {
        cookie += '; max-age=' + maxAgeSeconds;
    }

    document.cookie = cookie;
}

function getDepartmentExportCookie(name) {
    var cookiePrefix = name + '=';
    var cookies = document.cookie ? document.cookie.split(';') : [];

    for (var index = 0; index < cookies.length; index++) {
        var currentCookie = $.trim(cookies[index]);

        if (currentCookie.indexOf(cookiePrefix) === 0) {
            return decodeURIComponent(currentCookie.substring(cookiePrefix.length));
        }
    }

    return '';
}

function clearDepartmentExportCookie() {
    setDepartmentExportCookie(departmentExportCookieName, '', 0);
}

function blockDepartmentExportUi(message) {
    if (!$.blockUI) {
        return;
    }

    $.blockUI({
        message: '' +
            '<div style="padding:20px 24px; min-width:320px; background:#111827; color:#fff; border-radius:16px; box-shadow:0 14px 40px rgba(0,0,0,.24);">' +
                '<div style="font-size:28px; margin-bottom:10px;"><i class="fa fa-spinner fa-spin"></i></div>' +
                '<div style="font-size:18px; font-weight:800; margin-bottom:6px;">Preparing Excel Export</div>' +
                '<div style="font-size:13px; line-height:1.6; opacity:.92;">' + escapeHtml(message || 'Please wait while the report is being prepared. Do not close this page.') + '</div>' +
            '</div>',
        css: {
            border: 'none',
            background: 'transparent',
            width: 'auto',
            top: '35%',
            left: '50%',
            transform: 'translateX(-50%)'
        },
        overlayCSS: {
            backgroundColor: '#0f172a',
            opacity: 0.45,
            cursor: 'wait'
        }
    });
}

function unblockDepartmentExportUi() {
    clearDepartmentExportWatchers();
    clearDepartmentExportCookie();
    departmentExportInProgress = false;

    if ($.unblockUI) {
        $.unblockUI();
    }
}

function formatProgressValue(value) {
    var numericValue = parseFloat(value) || 0;
    return numericValue.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1');
}

function getProgressBarClass(progressValue) {
    if (progressValue < 50) {
        return 'progress-bar-danger';
    }

    if (progressValue < 80) {
        return 'progress-bar-warning';
    }

    return 'progress-bar-success';
}

function getDepartmentSummaryList($row) {
    var cachedSummary = $row.data('departmentSummaryParsed');

    if (cachedSummary) {
        return cachedSummary;
    }

    var rawSummary = $row.attr('data-department-summary') || '[]';
    var parsedSummary = [];

    try {
        parsedSummary = JSON.parse(rawSummary);
    } catch (error) {
        parsedSummary = [];
    }

    $row.data('departmentSummaryParsed', parsedSummary);

    return parsedSummary;
}

function getSelectedDepartmentId() {
    return $('#departmentFilter').val();
}

function getSelectedDepartmentSummary($row) {
    var selectedDepartmentId = getSelectedDepartmentId();

    if (!selectedDepartmentId) {
        return null;
    }

    var departmentSummaryList = getDepartmentSummaryList($row);

    for (var index = 0; index < departmentSummaryList.length; index++) {
        if (String(departmentSummaryList[index].department_id) === String(selectedDepartmentId)) {
            return departmentSummaryList[index];
        }
    }

    return null;
}

function getActiveRowMetrics($row) {
    var selectedDepartmentSummary = getSelectedDepartmentSummary($row);

    if (selectedDepartmentSummary) {
        return {
            progress: parseFloat(selectedDepartmentSummary.progress_percentage) || 0,
            done: parseInt(selectedDepartmentSummary.done_tasks, 10) || 0,
            total: parseInt(selectedDepartmentSummary.total_tasks, 10) || 0,
            pending: parseInt(selectedDepartmentSummary.pending_tasks, 10) || 0,
            delayStatus: selectedDepartmentSummary.delay_status || 'On Time',
            delayDays: parseInt(selectedDepartmentSummary.delay_days, 10) || 0
        };
    }

    return {
        progress: parseFloat($row.data('overall-progress')) || 0,
        done: parseInt($row.data('overall-done'), 10) || 0,
        total: parseInt($row.data('overall-total'), 10) || 0,
        pending: parseInt($row.data('overall-pending'), 10) || 0,
        delayStatus: $row.data('overall-delay') || 'On Time',
        delayDays: ($row.data('overall-delay') === 'Delayed') ? 1 : 0
    };
}

function renderRowSummary($row) {
    var activeMetrics = getActiveRowMetrics($row);
    var progressValue = activeMetrics.progress;
    var $progressBar = $row.find('.progress-summary-bar');

    $progressBar
        .css('width', progressValue + '%')
        .removeClass('progress-bar-danger progress-bar-warning progress-bar-success')
        .addClass(getProgressBarClass(progressValue));

    $row.find('.progress-summary-value').text(formatProgressValue(progressValue) + '%');
    $row.find('.progress-summary-meta').text(activeMetrics.done + '/' + activeMetrics.total + ' done, ' + activeMetrics.pending + ' pending');

    if (activeMetrics.delayDays > 0 && getSelectedDepartmentId()) {
        $row.find('.summary-status-cell').html('<span class="status-pill pill-danger">' + activeMetrics.delayDays + ' days delay</span>');
        return;
    }

    if (activeMetrics.delayStatus === 'Delayed') {
        $row.find('.summary-status-cell').html('<span class="status-pill pill-danger">Delayed</span>');
        return;
    }

    $row.find('.summary-status-cell').html('<span class="status-pill pill-success">On Time</span>');
}

function buildChildHtml(dfId) {
    var $row = $('#exampleS tbody tr.df-summary-row[data-dfid="' + dfId + '"]').first();
    var selectedDepartmentSummary = getSelectedDepartmentSummary($row);
    var $childTemplate = $('#child-detail-' + dfId).clone();
    var selectedDepartmentId = getSelectedDepartmentId();
    var selectedDepartmentLabel = $('#departmentFilter option:selected').text();

    if (selectedDepartmentId && selectedDepartmentSummary) {
        return '' +
            '<div class="child-table-wrap">' +
                '<div class="child-table-caption">' + escapeHtml(selectedDepartmentLabel) + ' task details</div>' +
                '<div class="summary-hint" style="margin-bottom:10px;">Showing task-wise detail for the selected department.</div>' +
                '<div class="inline-task-shell" id="inline-task-shell-' + dfId + '-' + selectedDepartmentSummary.department_id + '">' +
                    '<div class="inline-task-loading"><i class="fa fa-spinner fa-spin"></i> Loading task details...</div>' +
                '</div>' +
            '</div>';
    }

    if (selectedDepartmentId) {
        $childTemplate.find('tbody tr.department-detail-row').each(function() {
            if (String($(this).data('department-id')) !== String(selectedDepartmentId)) {
                $(this).remove();
            }
        });

        $childTemplate.find('.child-table-caption').text(selectedDepartmentLabel + ' task summary');

        if ($childTemplate.find('tbody tr.department-detail-row').length === 0) {
            $childTemplate.find('tbody').html('<tr><td colspan="7">No matching department task found for this DF.</td></tr>');
        }
    }

    return $childTemplate.html();
}

function closeAllChildRows() {
    if (!table) {
        return;
    }

    table.rows().every(function() {
        if (this.child.isShown()) {
            this.child.hide();
        }

        $(this.node()).removeClass('shown');
    });
}

function escapeHtml(value) {
    return $('<div>').text(value || '').html();
}

function fetchTaskDetailRows(dfId, departmentId) {
    var cacheKey = String(dfId) + '-' + String(departmentId);

    if (taskDetailsDataCache[cacheKey]) {
        return Promise.resolve(taskDetailsDataCache[cacheKey]);
    }

    return new Promise(function(resolve, reject) {
        $.ajax({
            url: '<?php echo page_url . "Task/get_task_details"; ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                df_id: dfId,
                department_id: departmentId,
                detail_scope: 'all',
                response_type: 'json'
            }
        }).done(function(response) {
            var rows = response && response.success && $.isArray(response.rows) ? response.rows : [];
            taskDetailsDataCache[cacheKey] = rows;
            resolve(rows);
        }).fail(function() {
            reject();
        });
    });
}

function submitDepartmentExcelExport(exportRows, departmentLabel) {
    if (departmentExportInProgress) {
        return Promise.reject(new Error('Another export is already in progress.'));
    }

    departmentExportInProgress = true;
    clearDepartmentExportWatchers();
    clearDepartmentExportCookie();
    blockDepartmentExportUi('The department-wise Excel file is being prepared. This can take a few seconds.');

    var exportToken = 'delay_export_' + Date.now();
    var iframeName = 'departmentExportFrame_' + Date.now();
    var $iframe = $('<iframe>', {
        name: iframeName,
        style: 'display:none;'
    });

    var $form = $('<form>', {
        method: 'post',
        action: '<?php echo page_url . "Master/User_management/export_delay_table_department_excel"; ?>',
        style: 'display:none;',
        target: iframeName
    });

    $form.append($('<textarea>', {
        name: 'export_rows_json',
        text: JSON.stringify(exportRows)
    }));

    $form.append($('<input>', {
        type: 'hidden',
        name: 'department_label',
        value: departmentLabel
    }));

    $form.append($('<input>', {
        type: 'hidden',
        name: 'export_token',
        value: exportToken
    }));

    $('body').append($iframe).append($form);

    return new Promise(function(resolve, reject) {
        var isFinished = false;

        function finish(isSuccess, errorMessage) {
            if (isFinished) {
                return;
            }

            isFinished = true;
            unblockDepartmentExportUi();

            setTimeout(function() {
                $form.remove();
                $iframe.remove();
            }, 1000);

            if (isSuccess) {
                resolve();
                return;
            }

            reject(new Error(errorMessage || 'The Excel export could not be completed.'));
        }

        departmentExportPoller = setInterval(function() {
            if (getDepartmentExportCookie(departmentExportCookieName) === exportToken) {
                finish(true);
            }
        }, 500);

        departmentExportTimeout = setTimeout(function() {
            finish(false, 'The Excel export is taking longer than expected. Please try again.');
        }, 90000);

        $iframe.on('load', function() {
            var iframeDocument = this.contentDocument || this.contentWindow.document;

            if (!iframeDocument || !iframeDocument.body) {
                return;
            }

            var iframeText = $.trim($(iframeDocument.body).text());

            if (iframeText && /(export error|fatal error|warning|notice|exception|parse error)/i.test(iframeText)) {
                finish(false, iframeText);
            }
        });

        try {
            $form.trigger('submit');
        } catch (error) {
            finish(false, 'The Excel export request could not be started.');
        }
    });
}

function exportSelectedDepartmentDetails(buttonApi, fallbackButtonConfig, buttonNode, dtApi, eventObject) {
    var selectedDepartmentId = getSelectedDepartmentId();

    if (!selectedDepartmentId) {
        $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, eventObject, dtApi, buttonNode, fallbackButtonConfig);
        return;
    }

    var selectedDepartmentLabel = $('#departmentFilter option:selected').text() || 'Department';
    var exportDefinitions = [];

    table.rows({ search: 'applied' }).every(function() {
        var $row = $(this.node());
        var selectedDepartmentSummary = getSelectedDepartmentSummary($row);

        if (!selectedDepartmentSummary) {
            return;
        }

        exportDefinitions.push({
            dfId: parseInt($row.data('dfid'), 10) || 0,
            departmentId: parseInt(selectedDepartmentSummary.department_id, 10) || 0,
            dfNo: $.trim($row.data('df-no')),
            dfDescription: $.trim($row.data('df-description')),
            companyName: $.trim($row.data('company-name')),
            poNo: $.trim($row.data('po-no')),
            marketingPerson: $.trim($row.data('owner'))
        });
    });

    if (!exportDefinitions.length) {
        alert('No department-wise records are available for export.');
        return;
    }

    buttonApi.text('<i class="fa fa-spinner fa-spin"></i> Preparing...');

    Promise.all(exportDefinitions.map(function(definition) {
        return fetchTaskDetailRows(definition.dfId, definition.departmentId).then(function(taskRows) {
            var normalizedRows = [];

            if (!taskRows.length) {
                normalizedRows.push({
                    df_no: definition.dfNo,
                    df_description: definition.dfDescription,
                    company_name: definition.companyName,
                    po_no: definition.poNo,
                    marketing_person: definition.marketingPerson,
                    task_name: '',
                    assigned_to: '',
                    start_date: '',
                    end_date: '',
                    actual_completion_date: '',
                    delay_in_day: '',
                    task_remark: '',
                    ticket_info: ''
                });
                return normalizedRows;
            }

            $.each(taskRows, function(_, taskRow) {
                normalizedRows.push({
                    df_no: definition.dfNo,
                    df_description: definition.dfDescription,
                    company_name: definition.companyName,
                    po_no: definition.poNo,
                    marketing_person: definition.marketingPerson,
                    task_name: taskRow.task_name || '',
                    assigned_to: taskRow.assigned_to || '',
                    start_date: taskRow.start_date || '',
                    end_date: taskRow.end_date || '',
                    actual_completion_date: taskRow.actual_completion_date || '',
                    delay_in_day: taskRow.delay_in_day || '',
                    task_remark: taskRow.task_remark || '',
                    ticket_info: taskRow.ticket_info || ''
                });
            });

            return normalizedRows;
        });
    })).then(function(resultSets) {
        var exportRows = [];

        $.each(resultSets, function(_, rowSet) {
            exportRows = exportRows.concat(rowSet);
        });

        return submitDepartmentExcelExport(exportRows, selectedDepartmentLabel);
    }).catch(function(error) {
        alert((error && error.message) ? error.message : 'The department-wise Excel export could not be prepared. Please try again.');
    }).finally(function() {
        buttonApi.text('<i class="fa fa-file-excel-o"></i> Excel');
    });
}

function loadInlineDepartmentTasks($row) {
    var selectedDepartmentSummary = getSelectedDepartmentSummary($row);

    if (!selectedDepartmentSummary) {
        return;
    }

    var dfId = $row.data('dfid');
    var departmentId = selectedDepartmentSummary.department_id;
    var cacheKey = String(dfId) + '-' + String(departmentId);
    var $taskShell = $('#inline-task-shell-' + dfId + '-' + departmentId);

    if (!$taskShell.length) {
        return;
    }

    if (taskDetailsCache[cacheKey]) {
        $taskShell.html(taskDetailsCache[cacheKey]);
        return;
    }

    $.ajax({
        url: '<?php echo page_url . "Task/get_task_details"; ?>',
        type: 'POST',
        data: {
            df_id: dfId,
            department_id: departmentId,
            detail_scope: 'all'
        },
        success: function(response) {
            taskDetailsCache[cacheKey] = response;
            $taskShell.html(response);
        },
        error: function() {
            $taskShell.html('<div class="inline-task-error">Unable to load task details.</div>');
        }
    });
}

function openRowDetails($row, rememberSelection) {
    var rowApi = table.row($row);
    var childHtml = buildChildHtml($row.data('dfid'));

    if (!childHtml) {
        return;
    }

    rowApi.child(childHtml).show();
    $row.addClass('shown');

    if (getSelectedDepartmentId()) {
        loadInlineDepartmentTasks($row);
    }

    if (rememberSelection) {
        lastOpenedDfId = $row.data('dfid');
    }
}

function syncExpandedRows() {
    closeAllChildRows();

    var selectedDepartmentId = getSelectedDepartmentId();

    if (selectedDepartmentId) {
        table.rows({ search: 'applied', page: 'current' }).every(function() {
            var $row = $(this.node());

            if (!$row.hasClass('df-summary-row')) {
                return;
            }

            openRowDetails($row, false);
        });

        lastOpenedDfId = null;
        return;
    }

    if (!lastOpenedDfId) {
        return;
    }

    var $selectedRow = $('#exampleS tbody tr.df-summary-row[data-dfid="' + lastOpenedDfId + '"]').first();

    if ($selectedRow.length) {
        openRowDetails($selectedRow, false);
    }
}

function updateVisibleRowSummaries() {
    $('#exampleS tbody tr.df-summary-row').each(function() {
        renderRowSummary($(this));
    });
}

// Aggregate the maximum, never the sum, across all filtered DF records.
function consolidateDepartmentDelays(summaries, selectedDepartmentId) {
    var departments = Object.create(null);
    summaries.forEach(function(summary) {
        summary.forEach(function(department) {
            var id = String(department.department_id);
            if (selectedDepartmentId && id !== String(selectedDepartmentId)) { return; }
            var days = Math.max(0, parseInt(department.delay_days, 10) || 0);
            if (days === 0) { return; }
            if (!departments[id]) {
                departments[id] = { id: id, name: department.department || 'N/A', days: days };
            } else {
                departments[id].days = Math.max(departments[id].days, days);
            }
        });
    });
    return Object.keys(departments).map(function(id) { return departments[id]; }).sort(function(a, b) {
        return b.days - a.days || a.name.localeCompare(b.name);
    });
}

function departmentDfDelayRecords(records, departmentId) {
    var result = [];
    records.forEach(function(record) {
        record.departments.forEach(function(department) {
            var days = Math.max(0, parseInt(department.delay_days, 10) || 0);
            if (String(department.department_id) !== String(departmentId) || days === 0) { return; }
            result.push({ dfId: record.dfId, dfNo: record.dfNo, description: record.description, company: record.company,
                days: days, tasks: parseInt(department.delayed_tasks, 10) || 0, users: (department.delayed_users || []).join(', ') });
        });
    });
    return result.sort(function(a, b) { return b.days - a.days || String(a.dfNo).localeCompare(String(b.dfNo)); });
}

var departmentDetailGeneration = 0;

function loadDepartmentOverdueTasks(job, departmentId, generation) {
    if (job.loading || job.loaded) { return; }
    job.loading = true;
    job.target.text('Loading overdue task remarks and ticket details…');
    return $.ajax({
        url: '<?php echo page_url . "Task/get_task_details"; ?>',
        type: 'POST',
        data: { df_id: job.dfId, department_id: departmentId, task_status: 'delayed', detail_scope: 'filtered' }
    }).done(function(html) {
        if (generation === departmentDetailGeneration) { job.loaded = true; job.target.html(html); }
    }).fail(function() {
        if (generation !== departmentDetailGeneration) { return; }
        job.target.empty().append($('<span>').text('Unable to load task details. '));
        $('<button>', { type: 'button', 'class': 'btn btn-default btn-sm', text: 'Retry' })
            .on('click', function() { loadDepartmentOverdueTasks(job, departmentId, generation); }).appendTo(job.target);
    }).always(function() { job.loading = false; });
}

function showDepartmentDfDelays(departmentId, departmentName) {
    var generation = ++departmentDetailGeneration;
    var records = [];
    table.rows({ search: 'applied', page: 'all' }).every(function() {
        var $row = $(this.node());
        records.push({ dfId: $row.attr('data-dfid'), dfNo: $row.attr('data-df-no') || '', description: $row.attr('data-df-description') || '',
            company: $row.attr('data-company-name') || '', departments: getDepartmentSummaryList($row) });
    });
    var rows = departmentDfDelayRecords(records, departmentId);
    $('#departmentDfDelayTitle').text(departmentName + ' — DF-wise Delays');
    $('#departmentDfDelaySummary').text(rows.length + ' delayed running DFs matching the current filters. Maximum overdue pending-task delay per DF, highest first. Click a DF number or row to show or hide its tasks.');
    var $body = $('#departmentDfDelayRows').empty();
    rows.forEach(function(row, index) {
        var detailId = 'department-df-tasks-' + generation + '-' + index;
        var $tr = $('<tr>', { 'class': 'department-df-toggle-row', style: 'cursor:pointer;' });
        var $toggle = $('<button>', { type: 'button', 'class': 'btn btn-link btn-sm',
            'aria-expanded': 'false', 'aria-controls': detailId, text: '+ ' + row.dfNo });
        $('<td>').append($toggle).appendTo($tr);
        [row.description, row.company, row.users, row.days, row.tasks].forEach(function(value) {
            $('<td>').text(value).appendTo($tr);
        });
        $tr.appendTo($body);
        var $details = $('<div>', { id: detailId, 'aria-live': 'polite' });
        var $detailRow = $('<tr>').hide().append($('<td>', { colspan: 6 }).append($details)).appendTo($body);
        var job = { dfId: row.dfId, target: $details, loading: false, loaded: false };
        var expanded = false;
        function toggleDetails() {
            expanded = !expanded;
            $detailRow.toggle(expanded);
            $toggle.attr('aria-expanded', String(expanded)).text((expanded ? '− ' : '+ ') + row.dfNo);
            if (expanded) { loadDepartmentOverdueTasks(job, departmentId, generation); }
        }
        $toggle.on('click', function(event) { event.stopPropagation(); toggleDetails(); });
        $tr.on('click', toggleDetails);
    });
    if (!rows.length) {
        $('<tr>').append($('<td>', { colspan: 6, text: 'No delayed DFs match the current filters.' })).appendTo($body);
    }
    $('#departmentDfDelayModal').modal('show');

}

$(document).on('hidden.bs.modal', '#departmentDfDelayModal', function() {
    departmentDetailGeneration++;
});

function updateDepartmentDelayChart() {
    var summaries = [];
    table.rows({ search: 'applied', page: 'all' }).every(function() {
        summaries.push(getDepartmentSummaryList($(this.node())));
    });
    var departments = consolidateDepartmentDelays(summaries, getSelectedDepartmentId());
    $('#departmentDelayScope').text(summaries.length + ' matching DFs · ' + departments.length + ' delayed departments');
    var $bars = $('#departmentDelayBars').empty();
    if (!departments.length) {
        $('<p>', { 'class': 'summary-hint', text: 'No overdue pending tasks match the current filters.' }).appendTo($bars);
        return;
    }
    var maximum = departments[0].days;
    departments.forEach(function(department) {
        var $row = $('<button>', { type: 'button', 'class': 'department-delay-bar-row', 'aria-haspopup': 'dialog', 'aria-controls': 'departmentDfDelayModal', 'aria-label': department.name + ': ' + department.days + ' days. Show DF-wise delays.' }).on('click', function() { showDepartmentDfDelays(department.id, department.name); });
        $('<span>', { 'class': 'department-delay-name', text: department.name }).appendTo($row);
        var $track = $('<div>', { 'class': 'department-delay-track', 'aria-hidden': 'true' }).appendTo($row);
        $('<div>', { 'class': 'department-delay-fill' }).css('width', (department.days / maximum * 100) + '%').appendTo($track);
        $('<span>', { 'class': 'department-delay-value', text: department.days + ' days' }).appendTo($row);
        $('<div>', { role: 'listitem' }).append($row).appendTo($bars);
    });
}

function updateSerialNumbers() {
    var pageInfo = table.page.info();

    table.column(0, { search: 'applied', order: 'applied', page: 'current' }).nodes().each(function(cell, index) {
        cell.innerHTML = pageInfo.start + index + 1;
    });
}

$(document).on('click', '.view-task-details', function(event) {
    event.stopPropagation();
    var df_id = $(this).data('dfid');
    var dept_id = $(this).data('deptid');
    var df_no = $(this).data('dfno');
    var department = $(this).data('department');
    var task_status = $(this).data('status');

    $('#modalDfNo').text(df_no);
    $('#modalDepartment').text(department);
    $('#modalTaskFilter').text(({all: 'All tasks', done: 'Done tasks', pending: 'Pending tasks', delayed: 'Delayed tasks', 'on-time': 'Pending tasks on time'})[task_status] || 'Tasks');
    $('#taskData').html('<div style="text-align:center; padding:20px;"><i class="fa fa-spinner fa-spin"></i> Loading task details...</div>');
    $('#taskModal').modal('show');

    $.ajax({
        url: '<?php echo page_url . "Task/get_task_details"; ?>',
        type: 'POST',
        data: {
            df_id: df_id,
            department_id: dept_id,
            task_status: task_status,
            detail_scope: task_status === 'all' ? 'all' : 'filtered'
        },
        success: function(response) {
            $('#taskData').html(response);
        },
        error: function() {
            $('#taskData').html('<div class="alert alert-danger">Unable to load task details.</div>');
        }
    });
});

$(document).ready(function() {

    table = $('#exampleS').DataTable({
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
                title: 'Running DF Task Wise Delay Report',
                action: function(e, dt, button, config) {
                    exportSelectedDepartmentDetails.call(this, dt.button(button), config, button, dt, e);
                },
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'Running DF Task Wise Delay Report',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'exampleS') {
            return true;
        }

        var rowNode = table.row(dataIndex).node();
        var $rowNode = $(rowNode);

        var ownerFilter = $('#marketingPersonFilter').val();
        var departmentFilter = getSelectedDepartmentId();
        var delayFilter = $('#delayFilter').val();
        var progressFilter = $('#progressFilter').val();

        var rowOwner = $rowNode.data('owner');
        var activeMetrics = getActiveRowMetrics($rowNode);
        var rowDelay = activeMetrics.delayStatus;
        var rowProgress = parseFloat(activeMetrics.progress) || 0;

        if (departmentFilter !== '') {
            var availableDepartments = String($rowNode.data('departments') || '').split(',');

            if ($.inArray(String(departmentFilter), availableDepartments) === -1) {
                return false;
            }
        }

        if (ownerFilter !== '' && rowOwner !== ownerFilter) {
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

    $('#customSearch').on('keyup change', function() {
        table.search(this.value).draw();
    });

    $('#marketingPersonFilter, #departmentFilter, #delayFilter, #progressFilter').on('change', function() {
        table.draw();
    });

    $('#resetFilters').on('click', function() {
        $('#customSearch').val('');
        $('#marketingPersonFilter').val('');
        $('#departmentFilter').val('');
        $('#delayFilter').val('');
        $('#progressFilter').val('');
        lastOpenedDfId = null;

        table.search('');
        table.columns().search('');
        table.draw();
    });

    $('#exampleS tbody').on('click', 'tr.df-summary-row', function(event) {
        if ($(event.target).closest('a, button, input, select, textarea').length) {
            return;
        }

        if (getSelectedDepartmentId()) {
            return;
        }

        var $row = $(this);
        var rowApi = table.row($row);

        if (rowApi.child.isShown()) {
            rowApi.child.hide();
            $row.removeClass('shown');
            lastOpenedDfId = null;
            return;
        }

        closeAllChildRows();
        openRowDetails($row, true);
    });

    table.on('draw', function() {
        updateDepartmentDelayChart();
        updateSerialNumbers();
        updateVisibleRowSummaries();
        syncExpandedRows();
    });

    updateDepartmentDelayChart();
    updateSerialNumbers();
    updateVisibleRowSummaries();
    syncExpandedRows();
});
</script>

</body>
</html>
