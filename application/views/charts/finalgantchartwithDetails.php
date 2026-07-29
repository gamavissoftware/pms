<?php
$CI = &get_instance();
$CI->load->model('Task_model');
if (file_exists(APPPATH . 'models/Df_change_control_model.php')) {
    $CI->load->model('Df_change_control_model');
}

$df_id = $this->uri->segment(3);
$department = $this->uri->segment(4);

function safeGanttDate($date, $format = 'd-M-Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }

    return date($format, strtotime($date));
}

function safeGanttText($value)
{
    $value = trim((string)$value);

    if ($value == '') {
        return '-';
    }

    return ucwords(strtolower($value));
}

$date_range = $CI->Task_model->getmaintaskenddate($df_id);

if (count($date_range) > 0) {
    if ($CI->Task_model->checkdfclosed($df_id) > 0) {
        $etdate = date('Y-m-d');
    } else {
        $etdate = $date_range[1];
    }

    $stdate = $date_range[0];
    $Dates = $CI->Task_model->getMondays($stdate, $etdate);
    $days_count = $CI->Task_model->getDaysCountForEachMonthFromArray($Dates);
} else {
    echo "Date Not Found";
    exit;
}

$df_data = $CI->Task_model->getallrunningdfByID($df_id);
$department_name = $CI->Task_model->textFormatting($CI->Task_model->getDepartmentBYID($department));

$planned_end_date = $CI->Task_model->getPlannedEndDate($df_id, $department);
$estimated_end_date = $CI->Task_model->GetEstimatedWithDelay($df_id, $department);
$ActualDelay = $CI->Task_model->getActualDelay($df_id, $department);

$runningdfno = '-';
$companyname = '-';
$pono = '-';
$podate = '-';
$df_addedOn = '-';
$marketing = '-';

if (count($df_data) > 0) {
    $runningdfno = strtoupper($df_data[1]);
    $companyname = $CI->Task_model->textFormatting($df_data[3]);
    $pono = $CI->Task_model->textFormatting($df_data[4]);
    $podate = $CI->Task_model->textFormatting($df_data[5]);
    $df_addedOn = safeGanttDate($df_data[7], 'd-m-Y');
    $marketing = $CI->Task_model->textFormatting($df_data[8]);
}

$changeControlSummary = array();
$ganttChangeDepartmentMap = array();
$activeDfChangeCount = 0;
$impactedChangeDepartments = 0;

if (isset($CI->Df_change_control_model) && method_exists($CI->Df_change_control_model, 'module_ready') && $CI->Df_change_control_model->module_ready()) {
    $changeControlSummary = $CI->Df_change_control_model->get_gantt_change_summary($df_id);
    $ganttChangeDepartmentMap = $CI->Df_change_control_model->get_gantt_department_map($df_id);
    $activeDfChangeCount = 0;
    foreach ($changeControlSummary as $changeRow) {
        if (isset($changeRow['status']) && $changeRow['status'] !== 'COMPLETED') {
            $activeDfChangeCount++;
        }
    }
    foreach ($ganttChangeDepartmentMap as $departmentChangeRow) {
        if (!empty($departmentChangeRow['has_requests'])) {
            $impactedChangeDepartments++;
        }
    }
}

$totalMainRows = 0;
$totalCompletedMain = 0;
$totalDelayedMain = 0;
$totalSubTasks = 0;

$mainRows = array();

$q = $this->db
    ->select('a.id, a.department_id, a.taskname, a.main_task_id, b.department, b.color')
    ->from('mdgantchartmaster a')
    ->join('departments b', 'a.department_id = b.department_id', 'left')
    ->order_by('a.sortorder', 'asc')
    ->get();

if ($q->num_rows() > 0) {
    foreach ($q->result() as $row) {

        $tasksid = array();

        $q2 = $this->db
            ->select('id, task_id')
            ->from('sharmajitaskmapping')
            ->where('report_id', $row->id)
            ->get();

        if ($q2->num_rows() > 0) {
            foreach ($q2->result() as $row1) {
                $tasksid[] = (int)$row1->task_id;
            }
        }

        $min_start_date = '';
        $max_end_date = '';
        $main_task_end_date = '';
        $edt = '';
        $delaydays = '';
        $actual_completed_date = '';
        $percentage_work_done = 0;

        if (!empty($tasksid)) {
            $q3 = $this->db
                ->select('MIN(start_date) as min_start_date, MAX(end_date) as max_end_date')
                ->from('task_department_wise_scheduling')
                ->where_in('taskid', $tasksid, false)
                ->where('df_id', $df_id)
                ->where('department_id', $row->department_id)
                ->get();

            if ($q3->num_rows() > 0) {
                $row3 = $q3->row();

                if (!empty($row3->min_start_date) && $row3->min_start_date != '0000-00-00') {
                    $min_start_date = $row3->min_start_date;
                }

                if (!empty($row3->max_end_date) && $row3->max_end_date != '0000-00-00') {
                    $max_end_date = $row3->max_end_date;
                    $edt = $max_end_date;
                }
            }

            $q4 = $this->db
                ->select('end_date')
                ->from('task_department_wise_scheduling')
                ->where('taskid', $row->main_task_id)
                ->where('df_id', $df_id)
                ->where('department_id', $row->department_id)
                ->get();

            if ($q4->num_rows() > 0) {
                $main_task_end_date = $q4->row()->end_date;

                if (!empty($main_task_end_date) && $main_task_end_date != '0000-00-00' && !empty($max_end_date)) {
                    $edt = ($main_task_end_date == $max_end_date) ? $main_task_end_date : $max_end_date;
                }
            }

            $q5 = $this->db
                ->select('task_completed_on')
                ->from('task_department_wise_scheduling')
                ->where('taskid', $row->main_task_id)
                ->where('df_id', $df_id)
                ->get();

            if ($q5->num_rows() > 0) {
                $task_completed_on = $q5->row()->task_completed_on;

                if (!empty($task_completed_on) && $task_completed_on != '0000-00-00 00:00:00') {
                    $actual_completed_date = date('Y-m-d', strtotime($task_completed_on));

                    if (!empty($max_end_date)) {
                        $completed_date_obj = new DateTime($actual_completed_date);
                        $max_end_date_obj = new DateTime($max_end_date);
                        $delaydays = ($completed_date_obj > $max_end_date_obj) ? $completed_date_obj->diff($max_end_date_obj)->days . " Days" : "";
                    }
                } else {
                    if (!empty($max_end_date)) {
                        $today_obj = new DateTime(date('Y-m-d'));
                        $max_end_date_obj = new DateTime($max_end_date);
                        $delaydays = ($today_obj > $max_end_date_obj) ? $today_obj->diff($max_end_date_obj)->days . " Days Pending" : "Pending";
                    } else {
                        $delaydays = "Pending";
                    }
                }
            } else {
                if (!empty($max_end_date)) {
                    $today_obj = new DateTime(date('Y-m-d'));
                    $max_end_date_obj = new DateTime($max_end_date);
                    $delaydays = ($today_obj > $max_end_date_obj) ? $today_obj->diff($max_end_date_obj)->days . " Days Pending" : "Pending";
                } else {
                    $delaydays = "Pending";
                }
            }

            $percentage_work_done = $CI->Task_model->delaypercentagecount($row->id, $df_id, $tasksid);
        }

        $subTasks = $CI->Task_model->GetSharmajiTasks($row->id, $df_id);

        $totalMainRows++;

        if (number_format($percentage_work_done, 2) == '100.00') {
            $totalCompletedMain++;
        }

        if (!empty($delaydays) && stripos($delaydays, 'Pending') === false && stripos($delaydays, 'Days') !== false) {
            $totalDelayedMain++;
        }

        if (count($subTasks) > 0) {
            $totalSubTasks += count($subTasks);
        }

        $mainRows[] = array(
            'id' => $row->id,
            'department_id' => $row->department_id,
            'department' => $row->department,
            'color' => !empty($row->color) ? $row->color : '',
            'taskname' => $row->taskname,
            'main_task_id' => $row->main_task_id,
            'tasksid' => $tasksid,
            'min_start_date' => $min_start_date,
            'max_end_date' => $max_end_date,
            'edt' => $edt,
            'delaydays' => $delaydays,
            'actual_completed_date' => $actual_completed_date,
            'percentage_work_done' => $percentage_work_done,
            'subtasks' => $subTasks
        );
    }
}

$overallProgress = ($totalMainRows > 0) ? round(($totalCompletedMain * 100) / $totalMainRows) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DF Wise Gantt Chart - <?php echo $runningdfno; ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            background: #f3f6fb;
            color: #111827;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
        }

        .page-wrapper {
            padding: 20px;
        }

        .report-hero {
            background: linear-gradient(135deg, #4872b8 0%, #111827 100%);
            border-radius: 18px;
            padding: 24px;
            color: #fff;
            box-shadow: 0 12px 35px rgba(0,0,0,0.14);
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
        }

        .report-hero:before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            right: -90px;
            top: -90px;
            background: rgba(255,255,255,0.08);
        }

        .report-title {
            font-size: 28px;
            font-weight: 900;
            margin: 0;
            letter-spacing: .3px;
        }

        .report-subtitle {
            margin-top: 8px;
            font-size: 13px;
            opacity: .9;
        }

        .switch-box {
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 14px;
            padding: 12px;
        }

        .switch-box label {
            font-size: 12px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .switch-box select {
            border-radius: 20px;
            font-weight: 800;
            height: 34px;
        }

        .kpi-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.07);
            padding: 16px;
            min-height: 104px;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:after {
            content: "";
            position: absolute;
            width: 85px;
            height: 85px;
            border-radius: 50%;
            right: -25px;
            bottom: -25px;
            background: rgba(72,114,184,0.08);
        }

        .kpi-label {
            font-size: 12px;
            font-weight: 900;
            color: #6b7280;
            letter-spacing: .4px;
        }

        .kpi-value {
            margin-top: 8px;
            font-size: 27px;
            font-weight: 900;
            color: #111827;
        }

        .kpi-hint {
            margin-top: 4px;
            font-size: 11px;
            color: #6b7280;
            font-weight: 700;
        }

        .info-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.06);
            margin-bottom: 16px;
            overflow: hidden;
        }

        .info-card table {
            margin-bottom: 0;
        }

        .info-card th {
            background: #f8fafc;
            color: #374151;
            font-size: 12px;
            text-align: center;
            vertical-align: middle;
            border-color: #e5e7eb;
        }

        .info-card span {
            color: #8b4513;
            font-weight: 900;
        }

        .legend-card {
            background: #fff;
            border-radius: 16px;
            padding: 14px;
            margin-bottom: 16px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.05);
        }

        .legend-title {
            font-size: 14px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 10px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            margin-right: 18px;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 800;
            color: #374151;
        }

        .change-board {
            background: #fff7ed;
            border: 1px solid #fdba74;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(194, 65, 12, 0.08);
        }

        .change-board-title {
            font-size: 14px;
            font-weight: 900;
            color: #9a3412;
            margin-bottom: 10px;
        }

        .change-stat {
            background: rgba(255,255,255,0.65);
            border: 1px solid rgba(251,146,60,0.28);
            border-radius: 14px;
            padding: 14px;
            min-height: 98px;
            margin-bottom: 12px;
        }

        .change-stat-label {
            font-size: 11px;
            font-weight: 900;
            color: #9a3412;
            letter-spacing: .5px;
        }

        .change-stat-value {
            font-size: 26px;
            font-weight: 900;
            color: #7c2d12;
            margin-top: 8px;
        }

        .change-item {
            background: rgba(255,255,255,0.72);
            border: 1px solid rgba(251,146,60,0.24);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 10px;
            text-transform: none;
        }

        .change-item strong,
        .change-item a {
            color: #9a3412;
        }

        .change-inline-wrap {
            margin-top: 6px;
            text-transform: none;
        }

        .change-inline-chip {
            display: inline-block;
            font-size: 9px;
            font-weight: 900;
            padding: 2px 7px;
            border-radius: 999px;
            margin: 0 4px 4px 0;
            background: rgba(255,255,255,0.82);
            border: 1px solid #fdba74;
            color: #9a3412;
            text-decoration: none !important;
        }

        .change-inline-chip.active {
            background: #f97316;
            color: #fff !important;
            border-color: #f97316;
        }

        .change-row-highlight .left-sticky,
        .change-row-highlight td {
            box-shadow: inset 0 0 0 9999px rgba(249, 115, 22, 0.06);
        }

        .change-row-soft .left-sticky,
        .change-row-soft td {
            box-shadow: inset 0 0 0 9999px rgba(251, 146, 60, 0.03);
        }

        .legend-box {
            width: 32px;
            height: 16px;
            border-radius: 4px;
            margin-right: 7px;
            border: 1px solid #d1d5db;
        }

        .blue,
        .planned-cell {
            background: #afcbe3 !important;
            color: #111827 !important;
            text-align: center;
            height: 20px;
        }

        .green,
        .actual-cell {
            background: #15803d !important;
            color: #fff !important;
            text-align: center;
            height: 20px;
        }

        .red,
        .delay-cell {
            background: #dc2626 !important;
            color: #fff !important;
            text-align: center;
            height: 20px;
        }

        .yellow {
            background: #facc15 !important;
            color: #111827 !important;
            text-align: center;
        }

        .light_grey {
            background-color: #f3f4f6;
            vertical-align: top;
            transform: rotate(180deg);
        }

        .lighter_grey {
            background-color: #e5e7eb;
        }

        .right div {
            writing-mode: vertical-rl;
            font-size: 11px;
            font-weight: 800;
            color: #374151;
        }

        .gantt-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 10px 30px rgba(31,41,55,0.08);
            padding: 14px;
            margin-bottom: 20px;
        }

        .gantt-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .gantt-toolbar-title {
            font-size: 15px;
            font-weight: 900;
            color: #111827;
        }

        .toolbar-btn {
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            padding: 6px 12px;
        }

        .table-scroll {
            position: relative;
            width: 100%;
            z-index: 1;
            overflow: auto;
            height: calc(100vh - 360px);
            min-height: 430px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            background: #fff;
        }

        .table-scroll table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .table-scroll th,
        .table-scroll td {
            font-size: 10px;
            text-align: center;
            border: 1px solid #d1d5db;
            padding: 0;
            vertical-align: middle;
        }

        .table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 7;
            background: #e5e7eb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .left-sticky {
            position: sticky !important;
            left: 0;
            z-index: 9 !important;
            background: #fff !important;
            box-shadow: 4px 0 10px rgba(0,0,0,0.08);
            min-width: 615px;
            max-width: 615px;
            width: 615px;
        }

        thead .left-sticky {
            z-index: 11 !important;
        }

        .task-meta-table {
            width: 615px;
            border-collapse: collapse;
            margin: 0;
        }

        .task-meta-table td,
        .task-meta-table th {
            border: 1px solid #d1d5db;
            padding: 3px;
            font-size: 10px;
            vertical-align: middle;
        }

        .task-index {
            width: 30px;
            font-weight: 900;
        }

        .task-department {
            width: 100px;
            text-align: left !important;
            font-weight: 800;
        }

        .task-name {
            width: 180px;
            text-align: left !important;
            font-weight: 800;
            color: #111827;
        }

        .task-label {
            width: 28px;
            font-weight: 900;
            text-align: left !important;
        }

        .task-date {
            width: 95px;
            font-weight: 800;
        }

        .task-small {
            width: 55px;
            font-weight: 800;
        }

        .toggle-dept {
            cursor: pointer;
            background: #f8fafc;
            border-radius: 8px;
            padding: 5px;
            display: block;
        }

        .toggle-dept:hover {
            background: #eff6ff;
        }

        .toggle-icon {
            color: #7e22ce;
            font-size: 13px;
        }

        .task-detail-row {
            display: none;
        }

        .highlight,
        .highlight .left-sticky {
            background-color: #e0ffff !important;
        }

        .ticket-link {
            color: #fff;
            font-size: 13px;
            display: inline-block;
            margin-top: 2px;
        }

        .ticket-link:hover {
            color: #fff;
            opacity: .85;
        }

        .modal-content {
            border-radius: 18px;
            border: none;
            box-shadow: 0 20px 55px rgba(0,0,0,.18);
        }

        .modal-header {
            background: linear-gradient(135deg, #4872b8 0%, #111827 100%);
            color: #fff;
            border-radius: 18px 18px 0 0;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 900;
        }

        .modal-body {
            text-transform: none;
        }

        @media print {
            .switch-box,
            .gantt-toolbar,
            .legend-card,
            .toolbar-btn {
                display: none !important;
            }

            .table-scroll {
                height: auto;
                overflow: visible;
            }

            body {
                background: #fff;
            }

            .report-hero,
            .kpi-card,
            .info-card,
            .gantt-card {
                box-shadow: none;
            }
        }

        @media(max-width: 767px) {
            .page-wrapper {
                padding: 10px;
            }

            .report-title {
                font-size: 21px;
            }

            .kpi-value {
                font-size: 23px;
            }

            .table-scroll {
                height: 500px;
            }
        }
    </style>
</head>

<body>

<div class="page-wrapper">

    <div class="report-hero">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="report-title">Progress Gantt Chart With Details</h1>
                <div class="report-subtitle">
                    DF-wise department and week-wise progress visualization with planned, actual and delay tracking.
                </div>
            </div>

            <div class="col-md-4">
                <div class="switch-box">
                    <label>Switch Gantt View</label>
                    <select id="switch" class="form-control" onchange="switchData();">
                        <option value="2">Day Wise</option>
                        <option value="1">Week Wise</option>
                        <option value="3">Department Wise</option>
                        <option value="4" selected>Department & Week Wise</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="info-card">
        <table class="table table-bordered">
            <tr>
                <th>DF No<br><span><?php echo $runningdfno; ?></span></th>
                <th>Company Name<br><span><?php echo $companyname; ?></span></th>
                <th>PO No<br><span><?php echo $pono; ?></span></th>
                <th>PO Date<br><span><?php echo $podate; ?></span></th>
                <th>DF Release Date<br><span><?php echo $df_addedOn; ?></span></th>
                <th>Marketing Person<br><span><?php echo $marketing; ?></span></th>
            </tr>
        </table>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-label">Main Milestones</div>
                <div class="kpi-value"><?php echo $totalMainRows; ?></div>
                <div class="kpi-hint">Total department-level rows</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-label">Completed</div>
                <div class="kpi-value"><?php echo $totalCompletedMain; ?></div>
                <div class="kpi-hint"><?php echo $overallProgress; ?>% overall completion</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-label">Delayed</div>
                <div class="kpi-value"><?php echo $totalDelayedMain; ?></div>
                <div class="kpi-hint">Milestones showing delay</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-label">Detailed Tasks</div>
                <div class="kpi-value"><?php echo $totalSubTasks; ?></div>
                <div class="kpi-hint">Expandable task-level records</div>
            </div>
        </div>
    </div>

    <div class="legend-card">
        <div class="legend-title">
            <i class="fa fa-info-circle"></i> Chart Indicators
        </div>

        <span class="legend-item">
            <span class="legend-box blue"></span> Planned
        </span>

        <span class="legend-item">
            <span class="legend-box green"></span> Actual / Completed
        </span>

        <span class="legend-item">
            <span class="legend-box red"></span> Delay
        </span>

        <span class="legend-item">
            <span class="legend-box" style="background:#fff;"></span> No Activity
        </span>

        <span class="legend-item">
            <i class="fa fa-ticket" style="color:#dc2626; margin-right:6px;"></i> Ticket Available
        </span>

        <span class="legend-item">
            <span class="legend-box" style="background:#fed7aa; border-color:#fdba74;"></span> Rework / ECN / IOM Linked
        </span>
    </div>

    <?php if (!empty($changeControlSummary)) { ?>
        <div class="change-board">
            <div class="change-board-title">
                <i class="fa fa-random"></i> DF Change Control Visibility
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="change-stat">
                        <div class="change-stat-label">Linked Requests</div>
                        <div class="change-stat-value"><?php echo count($changeControlSummary); ?></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="change-stat">
                        <div class="change-stat-label">Active Rework</div>
                        <div class="change-stat-value"><?php echo $activeDfChangeCount; ?></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="change-stat">
                        <div class="change-stat-label">Impacted Departments</div>
                        <div class="change-stat-value"><?php echo $impactedChangeDepartments; ?></div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="change-stat">
                        <div class="change-stat-label">Quick Actions</div>
                        <div style="margin-top:12px;">
                            <a href="<?php echo page_url; ?>Df_change_control/create/<?php echo $df_id; ?>" class="btn btn-warning btn-sm">Raise ECN / IOM</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php foreach (array_slice($changeControlSummary, 0, 4) as $changeRow) { ?>
                    <div class="col-md-6">
                        <div class="change-item">
                            <strong><?php echo $changeRow['change_no']; ?></strong>
                            | <?php echo $changeRow['request_type']; ?>
                            | <?php echo str_replace('_', ' ', $changeRow['change_category']); ?>
                            | <?php echo $changeRow['status']; ?><br>
                            <?php echo safeGanttText($changeRow['title']); ?><br>
                            <small>Departments: <?php echo (int)$changeRow['total_departments']; ?> | Active: <?php echo (int)$changeRow['active_departments']; ?></small><br>
                            <a href="<?php echo page_url; ?>Df_change_control/view/<?php echo $changeRow['id']; ?>" target="_blank">Open linked request</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="change-board" style="background:#f8fafc; border-color:#cbd5e1;">
            <div class="change-board-title" style="color:#334155;">
                <i class="fa fa-random"></i> No linked rework / ECN / IOM on this DF yet.
            </div>
            <a href="<?php echo page_url; ?>Df_change_control/create/<?php echo $df_id; ?>" class="btn btn-warning btn-sm">Raise ECN / IOM for this DF</a>
        </div>
    <?php } ?>

    <div class="gantt-card">
        <div class="gantt-toolbar">
            <div class="gantt-toolbar-title">
                <i class="fa fa-bar-chart"></i> Department & Week Wise Timeline
            </div>

            <div>
                <button type="button" class="btn btn-primary btn-sm toolbar-btn" onclick="expandAllRows();">
                    <i class="fa fa-plus-circle"></i> Expand All
                </button>

                <button type="button" class="btn btn-secondary btn-sm toolbar-btn" onclick="collapseAllRows();">
                    <i class="fa fa-minus-circle"></i> Collapse All
                </button>

                <button type="button" class="btn btn-success btn-sm toolbar-btn" onclick="window.print();">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="table-scroll">
            <table id="main-table" class="main-table" border="1">
                <thead>
                    <tr>
                        <th class="left-sticky" rowspan="3">
                            <table class="task-meta-table">
                                <tr>
                                    <td colspan="8" style="font-size:18px; font-weight:900; padding:8px;">
                                        Indicators
                                    </td>
                                </tr>
                                <tr>
                                    <td class="blue" style="width:35px;"></td>
                                    <td style="font-weight:800;">Planned</td>
                                    <td class="green" style="width:35px;"></td>
                                    <td style="font-weight:800;">Actual</td>
                                    <td class="red" style="width:35px;"></td>
                                    <td style="font-weight:800;">Delay</td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <th class="task-index">#</th>
                                    <th class="task-department">Department</th>
                                    <th class="task-name">Task</th>
                                    <th class="task-label">PLN<br>ACT</th>
                                    <th class="task-date">Start Date</th>
                                    <th class="task-date">End Date</th>
                                    <th class="task-small">Delay<br>Days</th>
                                    <th class="task-small">% Done</th>
                                </tr>
                            </table>
                        </th>

                        <?php if (count($days_count) > 0) { ?>
                            <?php foreach ($days_count as $month => $days) { ?>
                                <th class="text-center lighter_grey" colspan="<?php echo $days; ?>">
                                    <?php echo $month; ?>
                                </th>
                            <?php } ?>
                        <?php } ?>
                    </tr>

                    <tr>
                        <?php if (count($Dates) > 0) { ?>
                            <?php foreach ($Dates as $date) { 
                                $week = $CI->Task_model->getWeekNumberFromDate($date);
                            ?>
                                <th class="text-center lighter_grey">
                                    W<?php echo $week; ?>
                                </th>
                            <?php } ?>
                        <?php } ?>
                    </tr>

                    <tr>
                        <?php if (count($Dates) > 0) { ?>
                            <?php foreach ($Dates as $date) { ?>
                                <th scope="col" class="right light_grey">
                                    <div style="width:30px;">
                                        <?php echo date('d M Y', strtotime($date)); ?>
                                    </div>
                                </th>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;

                    foreach ($mainRows as $row) {

                        $tasksid = $row['tasksid'];
                        $min_start_date = $row['min_start_date'];
                        $edt = $row['edt'];
                        $delaydays = $row['delaydays'];
                        $percentage_work_done = $row['percentage_work_done'];
                        $color = $row['color'];
                        $departmentChangeData = isset($ganttChangeDepartmentMap[$row['department_id']]) ? $ganttChangeDepartmentMap[$row['department_id']] : array();
                        $hasDepartmentChangeRequests = !empty($departmentChangeData['has_requests']);
                        $hasActiveDepartmentChanges = !empty($departmentChangeData['has_active_requests']);
                        $mainRowClass = $hasActiveDepartmentChanges ? 'change-row-highlight' : ($hasDepartmentChangeRequests ? 'change-row-soft' : '');

                        $plannedWeeks = array();

                        if (!empty($min_start_date) && !empty($edt)) {
                            $plannedWeeks = $CI->Task_model->get_week_numbers_between_datesWithYearNew($min_start_date, $edt);
                        }

                        $actualWeeks = array();

                        if (!empty($tasksid)) {
                            $tdetail = $CI->Task_model->getDoneTaskMaxStart_EndDate($tasksid, $df_id);

                            if (count($tdetail) > 0) {
                                $actualWeeks = $CI->Task_model->get_week_numbers_between_datesWithYearNew($tdetail[0], $tdetail[1]);
                            }
                        }
                    ?>
                        <tr class="main-group-row main-group-<?php echo $row['main_task_id']; ?> <?php echo $mainRowClass; ?>">
                            <th class="left-sticky" rowspan="2">
                                <table class="task-meta-table">
                                    <tr>
                                        <td rowspan="2" class="task-index" style="background-color:<?php echo $color; ?>;">
                                            <?php echo $i; ?>
                                        </td>

                                        <td rowspan="2" class="task-department" style="background-color:<?php echo $color; ?>;">
                                            <span class="toggle-dept" onclick="toggleRow(<?php echo $row['main_task_id']; ?>)">
                                                <?php echo safeGanttText($row['department']); ?>
                                                <span class="toggle-icon toggle-icon-<?php echo $row['main_task_id']; ?>">
                                                    <i class="fa fa-plus-circle"></i>
                                                </span>
                                            </span>
                                        </td>

                                        <td rowspan="2" class="task-name" style="background-color:<?php echo $color; ?>;">
                                            <?php echo safeGanttText($row['taskname']); ?>
                                            <?php if ($hasDepartmentChangeRequests) { ?>
                                                <div class="change-inline-wrap">
                                                    <?php foreach (array_slice($departmentChangeData['items'], 0, 3) as $changeItem) { ?>
                                                        <a class="change-inline-chip <?php echo $changeItem['department_status'] !== 'COMPLETED' ? 'active' : ''; ?>" href="<?php echo page_url; ?>Df_change_control/view/<?php echo $changeItem['change_id']; ?>" target="_blank">
                                                            <?php echo $changeItem['change_no']; ?>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </td>

                                        <td class="task-label" style="background-color:<?php echo $color; ?>;">PLN</td>

                                        <td class="task-date" style="background-color:<?php echo $color; ?>;">
                                            <?php echo safeGanttDate($min_start_date); ?>
                                        </td>

                                        <td class="task-date" style="background-color:<?php echo $color; ?>;">
                                            <?php echo safeGanttDate($edt); ?>
                                        </td>

                                        <td rowspan="2" class="task-small" style="background-color:<?php echo $color; ?>;">
                                            <?php echo !empty($delaydays) ? $delaydays : '-'; ?>
                                        </td>

                                        <td rowspan="2" class="task-small" style="background-color:<?php echo $color; ?>;">
                                            <?php echo number_format($percentage_work_done, 2); ?>%
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="task-label" style="background-color:<?php echo $color; ?>;">ACT</td>
                                        <td colspan="2" style="background-color:<?php echo $color; ?>;">
                                            <?php
                                            if (number_format($percentage_work_done, 2) == '100.00') {
                                                $complete_q = $this->db
                                                    ->select('task_completed_on')
                                                    ->from('task_department_wise_scheduling')
                                                    ->where('taskid', $row['main_task_id'])
                                                    ->where('df_id', $df_id)
                                                    ->where('task_status', 1)
                                                    ->get();

                                                if ($complete_q->num_rows() > 0) {
                                                    echo safeGanttDate($complete_q->row()->task_completed_on);
                                                } else {
                                                    echo '-';
                                                }
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </th>

                            <?php foreach ($Dates as $date) {
                                $css = '';
                                $currentDateweek = $this->task->getYearForWeek($date) . $CI->Task_model->getWeekNumberFromDate($date);

                                if (!empty($plannedWeeks) && in_array($currentDateweek, $plannedWeeks)) {
                                    $css = 'blue';
                                }
                            ?>
                                <td class="<?php echo $css; ?>"></td>
                            <?php } ?>
                        </tr>

                        <tr class="main-group-row main-group-<?php echo $row['main_task_id']; ?> <?php echo $mainRowClass; ?>">
                            <?php foreach ($Dates as $date) {

                                $css = '';
                                $icon = '';

                                if (!empty($min_start_date) && !empty($edt) && !empty($tasksid)) {

                                    $CurrentWeekNo = $CI->Task_model->getWeekNumberFromDateWithYear($date);
                                    $tdetail = $CI->Task_model->getDoneTaskMaxStart_EndDate($tasksid, $df_id);

                                    if (count($tdetail) > 0) {
                                        $min_start = $tdetail[0];
                                        $max_end = $tdetail[1];

                                        $ActualWeeks = $CI->Task_model->get_week_numbers_between_datesWithYearNew($min_start, $max_end);
                                        $currentDateweek = $CI->Task_model->getWeekNumberFromDateWithYear($date);

                                        $alldone = $CI->Task_model->checkifalltasksaredone($tasksid, $df_id);

                                        if ($alldone == 1) {
                                            $completedDate = date('Y-m-d', strtotime($CI->Task_model->getTaskCompletedDate($row['main_task_id'], $df_id)));

                                            $plannedStartWeek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start_date);
                                            $plannedendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($edt);
                                            $ActualendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($completedDate);

                                            if ($ActualendWeek > $plannedendWeek) {
                                                $weekinbetween = range($plannedendWeek, $ActualendWeek);

                                                if (in_array($CurrentWeekNo, $weekinbetween) && $CurrentWeekNo <> $plannedendWeek) {
                                                    $css = 'red';
                                                }

                                                if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                    $css = 'green';
                                                }
                                            } else {
                                                if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                    $css = 'green';
                                                }
                                            }
                                        } else {
                                            $plannedStartWeek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start_date);
                                            $plannedendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($edt);
                                            $todayweekno = $CI->Task_model->getWeekNumberFromDateWithYear(date('Y-m-d'));

                                            if ($todayweekno > $plannedendWeek) {
                                                $weekinbetween = range($plannedendWeek, $todayweekno);

                                                if (in_array($CurrentWeekNo, $weekinbetween) && $CurrentWeekNo > $plannedendWeek) {
                                                    $css = 'red';
                                                } elseif ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                    $css = 'green';
                                                }
                                            } else {
                                                if ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $todayweekno) {
                                                    $css = 'green';
                                                }
                                            }
                                        }
                                    } else {
                                        $plannedStartWeek = $CI->Task_model->getWeekNumberFromDateWithYear($min_start_date);
                                        $plannedendWeek = $CI->Task_model->getWeekNumberFromDateWithYear($edt);
                                        $todayweekno = $CI->Task_model->getWeekNumberFromDateWithYear(date('Y-m-d'));

                                        if ($todayweekno > $plannedendWeek) {
                                            $weekinbetween = range($plannedendWeek, $todayweekno);

                                            if (in_array($CurrentWeekNo, $weekinbetween) && $CurrentWeekNo > $plannedendWeek) {
                                                $css = 'red';
                                            } elseif ($CurrentWeekNo >= $plannedStartWeek && $CurrentWeekNo <= $plannedendWeek) {
                                                $css = 'green';
                                            }
                                        } else {
                                            if ((int)$CurrentWeekNo >= (int)$plannedStartWeek && (int)$CurrentWeekNo <= (int)$todayweekno) {
                                                $css = 'green';
                                            }
                                        }
                                    }
                                }

                                if ($css == 'red') {
                                    $ticket_q = $this->db
                                        ->select('a.id')
                                        ->from('communication_ticket_system a')
                                        ->where('a.df_id', $df_id)
                                        ->where('a.department_id', $row['department_id'])
                                        ->get();

                                    if ($ticket_q->num_rows() > 0) {
                                        $icon = '<a class="ticket-link" href="javascript:;" onclick="open_ticket_popup(' . $df_id . ',' . $row['department_id'] . ');"><i class="fa fa-ticket"></i></a>';
                                    }
                                }
                            ?>
                                <td class="<?php echo $css; ?>"><?php echo $icon; ?></td>
                            <?php } ?>
                        </tr>

                        <?php
                        $ty = 1;

                        if (count($row['subtasks']) > 0) {
                            foreach ($row['subtasks'] as $tasked) {

                                $subPlannedWeeks = array();

                                if (!empty($tasked['start_date']) && !empty($tasked['end_date'])) {
                                    $subPlannedWeeks = $CI->Task_model->get_week_numbers_between_dates($tasked['start_date'], $tasked['end_date']);
                                }

                                $subPercentage = ($tasked['task_status'] == 1) ? '100.00' : '0.00';
                        ?>
                                <tr class="task-detail-row row-2-and-4-<?php echo $row['main_task_id']; ?>">
                                    <th class="left-sticky" rowspan="2">
                                        <table class="task-meta-table">
                                            <tr>
                                                <td rowspan="2" class="task-index" style="background-color:<?php echo $color; ?>;">
                                                    <?php echo $i . "." . $ty; ?>
                                                </td>

                                                <td rowspan="2" colspan="2" class="task-name" style="width:280px; text-align:center !important; background-color:<?php echo $color; ?>;">
                                                    <?php echo safeGanttText($tasked['task_name']); ?>
                                                </td>

                                                <td class="task-label" style="background-color:<?php echo $color; ?>;">PLN</td>

                                                <td class="task-date" style="background-color:<?php echo $color; ?>;">
                                                    <?php echo safeGanttDate($tasked['start_date']); ?>
                                                </td>

                                                <td class="task-date" style="background-color:<?php echo $color; ?>;">
                                                    <?php echo safeGanttDate($tasked['end_date']); ?>
                                                </td>

                                                <td rowspan="2" class="task-small" style="background-color:<?php echo $color; ?>;">
                                                    <?php echo !empty($tasked['delaydays']) ? $tasked['delaydays'] : '-'; ?>
                                                </td>

                                                <td rowspan="2" class="task-small" style="background-color:<?php echo $color; ?>;">
                                                    <?php echo $subPercentage; ?>%
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="task-label" style="background-color:<?php echo $color; ?>;">ACT</td>

                                                <td colspan="2" style="background-color:<?php echo $color; ?>;">
                                                    <?php
                                                    if ($tasked['task_status'] == 1) {
                                                        echo safeGanttDate($tasked['completionDate']);
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </th>

                                    <?php foreach ($Dates as $date) {
                                        $css = '';
                                        $holiday = $CI->Task_model->checkifholiday($date);

                                        if ($holiday == 0 && !empty($subPlannedWeeks)) {
                                            $currentDateweek = $CI->Task_model->getWeekNumberFromDate($date);

                                            if (in_array($currentDateweek, $subPlannedWeeks)) {
                                                $css = 'blue';
                                            }
                                        }
                                    ?>
                                        <td class="<?php echo $css; ?>"></td>
                                    <?php } ?>
                                </tr>

                                <tr class="task-detail-row row-2-<?php echo $row['main_task_id']; ?>">
                                    <?php foreach ($Dates as $date) {

                                        $css = '';
                                        $icon = '';
                                        $holiday = $CI->Task_model->checkifholiday($date);

                                        if ($holiday == 0) {
                                            $actual = $CI->Task_model->checkActualStatus($tasked['taskid'], $df_id);

                                            if (count($actual) > 0) {
                                                $task_status = $actual[0];
                                                $completedOn = $actual[2];

                                                if ($task_status == 1) {
                                                    $currentDateweek = $CI->Task_model->getWeekNumberFromDate($date);
                                                    $allplannedweeks = $CI->Task_model->get_week_numbers_between_dates($tasked['start_date'], $tasked['end_date']);
                                                    $allcompletedWeeks = $CI->Task_model->get_week_numbers_between_dates($tasked['start_date'], date('Y-m-d', strtotime($completedOn)));

                                                    $previousmonday = $CI->Task_model->get_previous_monday($tasked['start_date']);
                                                    $previousmondayEND = $CI->Task_model->get_previous_monday(date('Y-m-d', strtotime($completedOn)));

                                                    if (in_array($currentDateweek, $allplannedweeks)) {
                                                        $css = 'green';
                                                    } else {
                                                        if (count($allplannedweeks) < count($allcompletedWeeks) && strtotime($previousmonday) <= strtotime($date) && strtotime($previousmondayEND) >= strtotime($date)) {
                                                            $css = 'red';
                                                        }
                                                    }
                                                } else {
                                                    $previousmondaySTART = $CI->Task_model->get_previous_monday($tasked['start_date']);
                                                    $previousmondayEND = $CI->Task_model->get_previous_monday($tasked['end_date']);
                                                    $previousmondaytoday = $CI->Task_model->get_previous_monday(date('Y-m-d'));

                                                    if (strtotime($previousmondaytoday) > strtotime($previousmondayEND)) {
                                                        if (strtotime($date) >= strtotime($previousmondaySTART) && strtotime($date) <= strtotime($previousmondaytoday)) {
                                                            $css = 'red';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                        <td class="<?php echo $css; ?>"><?php echo $icon; ?></td>
                                    <?php } ?>
                                </tr>
                        <?php
                                $ty++;
                            }
                        }
                        ?>

                    <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <span id="stagrmk"></span>
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
            </div>
            <div class="modal-body" id="modalbody">
                Please wait...
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalTicket" tabindex="-1" aria-labelledby="exampleModalTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Department Raised Tickets
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
            </div>
            <div class="modal-body" id="modalbodyTicket" style="overflow-x:auto;">
                Please wait...
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js"></script>

<script>
var lastOpenedGroupId = null;

function open_popup(dfid, taskid, departmentid) {
    $("#exampleModal").modal('show');
    $("#modalbody").html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading progress report...</div>');
    $("#stagrmk").html(departmentid + ' Progress Report');

    $.ajax({
        type: "POST",
        url: "<?php echo page_url; ?>Task/getreportoftasks/",
        data: {
            dfid: dfid,
            taskid: taskid,
            departmentid: departmentid
        },
        success: function(data) {
            $("#modalbody").html(data);
        },
        error: function() {
            $("#modalbody").html('<div class="alert alert-danger">Unable to load progress report.</div>');
        }
    });
}

function open_ticket_popup(dfid, departmentid) {
    $("#exampleModalTicket").modal('show');
    $("#modalbodyTicket").html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading tickets...</div>');

    $.ajax({
        type: "POST",
        url: "<?php echo page_url; ?>Task/getTicktsDepartmentWise/",
        data: {
            dfid: dfid,
            departmentid: departmentid
        },
        success: function(data) {
            $("#modalbodyTicket").html(data);
        },
        error: function() {
            $("#modalbodyTicket").html('<div class="alert alert-danger">Unable to load tickets.</div>');
        }
    });
}

function switchData() {
    var swi = $("#switch").val();

    if (swi == 1) {
        window.location = "<?php echo page_url; ?>Task/dfgantchartSharmaji/<?php echo $df_id; ?>/";
    } else if (swi == 2) {
        window.location = "<?php echo page_url; ?>Task/dfgantchartNew/<?php echo $df_id; ?>/";
    } else if (swi == 3) {
        window.location = "<?php echo page_url; ?>Task/dfgantchartDepartmentwise/<?php echo $df_id; ?>";
    } else {
        window.location = "<?php echo page_url; ?>Task/finalgantchart/<?php echo $df_id; ?>";
    }
}

function toggleRow(groupId) {
    var rows = $('.row-2-and-4-' + groupId);
    var rows1 = $('.row-2-' + groupId);
    var icon = $('.toggle-icon-' + groupId);
    var groupRows = $('.main-group-' + groupId);

    if (lastOpenedGroupId && lastOpenedGroupId !== groupId) {
        collapseGroup(lastOpenedGroupId);
    }

    if (rows.is(':hidden') && rows1.is(':hidden')) {
        rows.show().addClass('highlight');
        rows1.show().addClass('highlight');
        groupRows.addClass('highlight');
        icon.html('<i class="fa fa-minus-circle"></i>');
        lastOpenedGroupId = groupId;
    } else {
        collapseGroup(groupId);
        lastOpenedGroupId = null;
    }
}

function collapseGroup(groupId) {
    $('.row-2-and-4-' + groupId).hide().removeClass('highlight');
    $('.row-2-' + groupId).hide().removeClass('highlight');
    $('.main-group-' + groupId).removeClass('highlight');
    $('.toggle-icon-' + groupId).html('<i class="fa fa-plus-circle"></i>');
}

function expandAllRows() {
    $('[class*="row-2-and-4-"]').show().addClass('highlight');
    $('[class*="row-2-"]').show().addClass('highlight');
    $('.toggle-icon').html('<i class="fa fa-minus-circle"></i>');
    lastOpenedGroupId = null;
}

function collapseAllRows() {
    $('[class*="row-2-and-4-"]').hide().removeClass('highlight');
    $('[class*="row-2-"]').hide().removeClass('highlight');
    $('.main-group-row').removeClass('highlight');
    $('.toggle-icon').html('<i class="fa fa-plus-circle"></i>');
    lastOpenedGroupId = null;
}
</script>

</body>
</html>
