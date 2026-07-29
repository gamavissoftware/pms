<?php
$CI =& get_instance();
$CI->load->model('Task_model');

$df_id = $this->uri->segment(3);
$department = $this->uri->segment(4);

function safeDayGanttDate($date, $format = 'd M Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }

    return date($format, strtotime($date));
}

function safeDayGanttText($value)
{
    $value = trim((string)$value);

    if ($value == '') {
        return '-';
    }

    return ucwords(strtolower($value));
}

function safeDayGanttUpper($value)
{
    $value = trim((string)$value);

    if ($value == '') {
        return '-';
    }

    return strtoupper($value);
}

/*
|--------------------------------------------------------------------------
| Date Range
|--------------------------------------------------------------------------
*/
$date_range = $CI->Task_model->getWeekStartDates($df_id, $department);

if (count($date_range) > 0) {
    $stdate = $date_range[0];
    $etdate = $date_range[1];

    $Dates = $CI->Task_model->GetallDates($stdate, $etdate);
    $days_count = $CI->Task_model->getDaysCountForEachMonth($stdate, $etdate);
} else {
    echo "Date Not Found";
    exit;
}

/*
|--------------------------------------------------------------------------
| Main Data
|--------------------------------------------------------------------------
*/
$tasks = $CI->Task_model->getDFTaskScheduled($df_id, $department);

$df_data = $CI->Task_model->getallrunningdfByID($df_id);
$department_name = $CI->Task_model->textFormatting($CI->Task_model->getDepartmentBYID($department));

$work_done_per = $CI->Task_model->workCompleted($df_id, $department);
$work_delayed_per = $CI->Task_model->workDelayed($df_id, $department);

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
    $runningdfno = safeDayGanttUpper($df_data[1]);
    $companyname = $CI->Task_model->textFormatting($df_data[3]);
    $pono = $CI->Task_model->textFormatting($df_data[4]);
    $podate = $CI->Task_model->textFormatting($df_data[5]);
    $df_addedOn = safeDayGanttDate($df_data[7], 'd-m-Y');
    $marketing = $CI->Task_model->textFormatting($df_data[8]);
}

/*
|--------------------------------------------------------------------------
| Department Filter Data
|--------------------------------------------------------------------------
*/
$departmentOptions = $this->db
    ->select('a.department_id, b.department')
    ->from('task_department_wise_scheduling a')
    ->join('departments b', 'a.department_id = b.department_id', 'left')
    ->where('a.df_id', $df_id)
    ->where('b.status', 1)
    ->where('b.business_loc_id', 2)
    ->group_by('a.department_id')
    ->order_by('b.department', 'ASC')
    ->get();

/*
|--------------------------------------------------------------------------
| Completion / Delay Summary
|--------------------------------------------------------------------------
*/
$pendingTaskCountQuery = $this->db
    ->select('id')
    ->from('task_department_wise_scheduling')
    ->where('task_status', 0)
    ->where('df_id', $df_id);

if (!empty($department)) {
    $pendingTaskCountQuery->where('department_id', $department);
}

$pendingTaskCount = $pendingTaskCountQuery->get()->num_rows();

$delayDaysDisplay = '-';
$actualEndDateDisplay = '-';
$estimatedEndDateDisplay = '-';

if ($pendingTaskCount > 0) {
    $delayDaysDisplay = ((int)$work_delayed_per > 0) ? $work_delayed_per . ' Days' : 'On Time';

    if (!empty($planned_end_date) && $planned_end_date != '0000-00-00') {
        $plannedDateObj = date('Y-m-d', strtotime($planned_end_date));

        if ((int)$work_delayed_per > 0) {
            $estimatedRaw = date('Y-m-d', strtotime($plannedDateObj . ' +' . (int)$work_delayed_per . ' days'));
            $estimatedEndDateDisplay = safeDayGanttDate($estimatedRaw);
        } else {
            $estimatedEndDateDisplay = safeDayGanttDate($plannedDateObj);
        }
    }
} else {
    $completed_q = $this->db
        ->select_max('task_completed_on')
        ->from('task_department_wise_scheduling')
        ->where('df_id', $df_id)
        ->where('task_status', 1);

    if (!empty($department)) {
        $completed_q->where('department_id', $department);
    }

    $completedResult = $completed_q->get();

    if ($completedResult->num_rows() > 0) {
        $highest_date = $completedResult->row()->task_completed_on;

        if (!empty($highest_date) && $highest_date != '0000-00-00 00:00:00') {
            $finalcompletiondate = date('Y-m-d', strtotime($highest_date));
            $actualEndDateDisplay = safeDayGanttDate($highest_date);

            if (!empty($planned_end_date) && $planned_end_date != '0000-00-00' && $finalcompletiondate > date('Y-m-d', strtotime($planned_end_date))) {
                $start = new DateTime($finalcompletiondate);
                $end = new DateTime($planned_end_date);
                $interval = $start->diff($end);
                $delayDaysDisplay = $interval->days . ' Days';
            } else {
                $delayDaysDisplay = 'Early / On Time';
            }
        }
    }
}

$plannedEndDateDisplay = safeDayGanttDate($planned_end_date);

$totalTasks = count($tasks);
$totalCompletedTasks = 0;
$totalDelayedTasks = 0;
$totalTicketsVisible = 0;

$preparedTasks = array();

if ($totalTasks > 0) {
    foreach ($tasks as $row) {
        $taskid = $row['task_id'];
        $depid = $row['department_id'];

        $HOD = $CI->Task_model->getHOD($depid);
        $actualDone = $CI->Task_model->checkActualDoneStatus($taskid, $df_id);

        $delayDays = '';

        if (!empty($actualDone) && !empty($row['end_date']) && $row['end_date'] != '0000-00-00') {
            if (strtotime($actualDone) > strtotime($row['end_date'])) {
                $earlier = new DateTime($actualDone);
                $later = new DateTime($row['end_date']);
                $delayDays = $later->diff($earlier)->format("%a") . " Days";
            }
        } else {
            if (
                empty($actualDone) &&
                !empty($row['end_date']) &&
                $row['end_date'] != '0000-00-00' &&
                strtotime(date('Y-m-d')) > strtotime($row['end_date'])
            ) {
                $todayObj = new DateTime(date('Y-m-d'));
                $endObj = new DateTime($row['end_date']);
                $delayDays = $endObj->diff($todayObj)->format("%a") . " Days Pending";
            }
        }

        $actualData = $CI->Task_model->checkActualStatus($taskid, $df_id);

        if (!empty($actualDone)) {
            $totalCompletedTasks++;
        }

        if (!empty($delayDays)) {
            $totalDelayedTasks++;
        }

        $ticketCount = $this->db
            ->select('a.id')
            ->from('communication_ticket_system a')
            ->where('a.df_id', $df_id)
            ->where('a.task_id', $taskid)
            ->get()
            ->num_rows();

        if ($ticketCount > 0) {
            $totalTicketsVisible++;
        }

        $preparedTasks[] = array(
            'task_id' => $taskid,
            'department_id' => $depid,
            'department' => $row['department'],
            'task_name' => $row['task_name'],
            'hod' => $HOD,
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'actual_done' => $actualDone,
            'actual_data' => $actualData,
            'delay_days' => $delayDays,
            'ticket_count' => $ticketCount
        );
    }
}

$overallProgress = ($totalTasks > 0) ? round(($totalCompletedTasks * 100) / $totalTasks) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DF Wise Day Gantt Chart - <?php echo $runningdfno; ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            background: #f3f6fb;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            text-transform: uppercase;
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

        .control-card {
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 14px;
            padding: 12px;
        }

        .control-card label {
            font-size: 12px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .control-card select {
            border-radius: 20px;
            font-weight: 800;
            height: 35px;
        }

        .info-card,
        .kpi-card,
        .legend-card,
        .gantt-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.06);
            margin-bottom: 16px;
        }

        .info-card {
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

        .kpi-card {
            padding: 16px;
            min-height: 105px;
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

        .legend-card {
            padding: 14px;
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

        .legend-box {
            width: 32px;
            height: 16px;
            border-radius: 4px;
            margin-right: 7px;
            border: 1px solid #d1d5db;
        }

        .blue {
            background-color: #afcbe3 !important;
            color: #111827 !important;
            text-align: center;
            height: 20px;
        }

        .green {
            background-color: #59ab77 !important;
            color: #fff !important;
            text-align: center;
            height: 20px;
        }

        .red {
            background-color: #e3696a !important;
            color: #fff !important;
            text-align: center;
            height: 20px;
        }

        .holiday {
            background-color: #ffc0cb !important;
            color: #111827 !important;
            text-align: center;
            height: 20px;
        }

        .empty-cell {
            background-color: #fff;
            height: 20px;
        }

        .lighter_grey {
            background-color: #e5e7eb;
        }

        .light_grey {
            background-color: #f3f4f6;
            vertical-align: top;
            transform: rotate(180deg);
        }

        .right div {
            writing-mode: vertical-rl;
            font-size: 11px;
            font-weight: 800;
            color: #374151;
        }

        .gantt-card {
            padding: 14px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(31,41,55,0.08);
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
            overflow: auto;
            height: calc(100vh - 380px);
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
            min-width: 790px;
            max-width: 790px;
            width: 790px;
        }

        thead .left-sticky {
            z-index: 11 !important;
        }

        .task-meta-table {
            width: 790px;
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
            width: 35px;
            font-weight: 900;
        }

        .task-department {
            width: 110px;
            font-weight: 800;
        }

        .task-name {
            width: 220px;
            font-weight: 800;
            text-align: left !important;
            color: #111827;
        }

        .task-hod {
            width: 130px;
            font-weight: 800;
        }

        .task-label {
            width: 55px;
            font-weight: 900;
        }

        .task-date {
            width: 95px;
            font-weight: 800;
        }

        .task-delay {
            width: 95px;
            font-weight: 800;
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
            .control-card,
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
            <div class="col-md-7">
                <h1 class="report-title">Progress Gantt Chart</h1>
                <div class="report-subtitle">
                    DF-wise day level task timeline with planned, actual, delay, holiday and ticket tracking.
                </div>
            </div>

            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <div class="control-card">
                            <label>Switch Gantt View</label>
                            <select id="switch" class="form-control" onchange="switchData();">
                                <option value="2" selected>Day Wise</option>
                                <option value="1">Week Wise</option>
                                <option value="3">Department Wise</option>
                                <option value="4">Department & Week Wise</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="control-card">
                            <label>Department Filter</label>
                            <select name="filter" id="filter" class="form-control" onchange="filter_Gantt();">
                                <option value="" <?php if ($department == '') { echo 'selected'; } ?>>All Departments</option>
                                <?php if ($departmentOptions->num_rows() > 0) { ?>
                                    <?php foreach ($departmentOptions->result() as $depRow) { ?>
                                        <option value="<?php echo $depRow->department_id; ?>" <?php if ($department == $depRow->department_id) { echo 'selected'; } ?>>
                                            <?php echo strtoupper($depRow->department); ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
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

    <div class="info-card">
        <table class="table table-bordered">
            <tr>
                <th>Department<br><span><?php echo !empty($department_name) ? $department_name : 'All Departments'; ?></span></th>
                <th>Work Completed<br><span><?php echo $work_done_per; ?>%</span></th>
                <th>Delay Days<br><span><?php echo $delayDaysDisplay; ?></span></th>
                <th>Planned End Date<br><span><?php echo $plannedEndDateDisplay; ?></span></th>
                <?php if ($pendingTaskCount > 0) { ?>
                    <th>Estimated End Date<br><span><?php echo $estimatedEndDateDisplay; ?></span></th>
                <?php } ?>
                <th>Actual End Date<br><span><?php echo $actualEndDateDisplay; ?></span></th>
            </tr>
        </table>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-label">Total Tasks</div>
                <div class="kpi-value"><?php echo $totalTasks; ?></div>
                <div class="kpi-hint">Visible task records</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-label">Completed</div>
                <div class="kpi-value"><?php echo $totalCompletedTasks; ?></div>
                <div class="kpi-hint"><?php echo $overallProgress; ?>% overall task completion</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-label">Delayed Tasks</div>
                <div class="kpi-value"><?php echo $totalDelayedTasks; ?></div>
                <div class="kpi-hint">Tasks showing delay</div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="kpi-card">
                <div class="kpi-label">Ticket Linked</div>
                <div class="kpi-value"><?php echo $totalTicketsVisible; ?></div>
                <div class="kpi-hint">Tasks having raised ticket</div>
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
            <span class="legend-box holiday"></span> Holiday
        </span>

        <span class="legend-item">
            <i class="fa fa-ticket" style="color:#dc2626; margin-right:6px;"></i> Ticket Available
        </span>
    </div>

    <div class="gantt-card">
        <div class="gantt-toolbar">
            <div class="gantt-toolbar-title">
                <i class="fa fa-calendar"></i> Day Wise Timeline
            </div>

            <button type="button" class="btn btn-success btn-sm toolbar-btn" onclick="window.print();">
                <i class="fa fa-print"></i> Print
            </button>
        </div>

        <div class="table-scroll">
            <table id="main-table" class="main-table" border="1">
                <thead>
                    <tr>
                        <th class="left-sticky" rowspan="2">
                            <table class="task-meta-table">
                                <tr>
                                    <td colspan="8" style="font-size:18px; font-weight:900; padding:8px;">
                                        Indicators
                                    </td>
                                </tr>

                                <tr>
                                    <td class="blue"></td>
                                    <td style="font-weight:800;">Planned</td>
                                    <td class="green"></td>
                                    <td style="font-weight:800;">Actual</td>
                                    <td class="red"></td>
                                    <td style="font-weight:800;">Delay</td>
                                    <td class="holiday"></td>
                                    <td style="font-weight:800;">Holiday</td>
                                </tr>

                                <tr>
                                    <th class="task-index">Sr. No.</th>
                                    <th class="task-department">Department</th>
                                    <th class="task-name">Task</th>
                                    <th class="task-hod">HOD</th>
                                    <th class="task-label">PLN<br>ACT</th>
                                    <th class="task-date">Start Date</th>
                                    <th class="task-date">End Date</th>
                                    <th class="task-delay">Delay Days</th>
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
                                $holiday = $CI->Task_model->checkifholiday($date);
                                $css = ($holiday == 0) ? '' : 'holiday';
                            ?>
                                <th scope="col" class="right light_grey <?php echo $css; ?>">
                                    <div style="width:50px;">
                                        <?php echo date('d M Y', strtotime($date)); ?>
                                    </div>
                                </th>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($preparedTasks) > 0) { ?>
                        <?php $i = 1; foreach ($preparedTasks as $row) {

                            $css_occur = array();

                            $taskid = $row['task_id'];
                            $depid = $row['department_id'];
                            $actualDone = $row['actual_done'];
                            $actual = $row['actual_data'];
                            $delayDays = $row['delay_days'];
                        ?>

                            <tr>
                                <th class="left-sticky" rowspan="2">
                                    <table class="task-meta-table">
                                        <tr>
                                            <td rowspan="2" class="task-index">
                                                <?php echo $i; ?>
                                            </td>

                                            <td rowspan="2" class="task-department">
                                                <?php echo safeDayGanttText($row['department']); ?>
                                            </td>

                                            <td rowspan="2" class="task-name">
                                                <?php echo safeDayGanttText($row['task_name']); ?>
                                            </td>

                                            <td rowspan="2" class="task-hod">
                                                <?php echo safeDayGanttText($row['hod']); ?>
                                            </td>

                                            <td class="task-label">PLN</td>

                                            <td class="task-date">
                                                <?php echo safeDayGanttDate($row['start_date']); ?>
                                            </td>

                                            <td class="task-date">
                                                <?php echo safeDayGanttDate($row['end_date']); ?>
                                            </td>

                                            <td rowspan="2" class="task-delay">
                                                <?php echo !empty($delayDays) ? $delayDays : '-'; ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="task-label">ACT</td>
                                            <td colspan="2">
                                                <?php echo !empty($actualDone) ? safeDayGanttDate($actualDone) : '-'; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </th>

                                <?php foreach ($Dates as $date) {

                                    $holiday = $CI->Task_model->checkifholiday($date);

                                    if ($holiday == 0) {
                                        $planned = $CI->Task_model->checkDateInBetween($date, $row['start_date'], $row['end_date']);
                                        $css = ($planned == 1) ? 'blue' : '';
                                    } else {
                                        $css = 'holiday';
                                    }
                                ?>
                                    <td class="<?php echo $css; ?>"></td>
                                <?php } ?>
                            </tr>

                            <tr>
                                <?php foreach ($Dates as $date) {

                                    $holiday = $CI->Task_model->checkifholiday($date);
                                    $css = '';
                                    $icon = '';

                                    if ($holiday == 0) {
                                        if (count($actual) > 0) {
                                            $task_status = $actual[0];
                                            $assigned_on = $actual[1];
                                            $completedOn = $actual[2];
                                            $tstdate = $actual[3];
                                            $tsetdate = $actual[4];

                                            $betweenDate = $CI->Task_model->checkDateInBetween($date, $row['start_date'], $row['end_date']);

                                            if ($task_status == 1) {
                                                if ($betweenDate == 1) {
                                                    $css = 'green';
                                                }

                                                if (
                                                    !empty($completedOn) &&
                                                    !empty($tsetdate) &&
                                                    strtotime(date('Y-m-d', strtotime($completedOn))) > strtotime($tsetdate)
                                                ) {
                                                    $allDatesExceed = $CI->Task_model->GetallDates($tsetdate, date('Y-m-d', strtotime($completedOn)));
                                                    array_shift($allDatesExceed);

                                                    if (in_array($date, $allDatesExceed)) {
                                                        $css = 'red';
                                                    }
                                                }
                                            } else {
                                                if (!empty($tsetdate) && strtotime($date) > strtotime($tsetdate) && strtotime($date) <= strtotime(date('Y-m-d'))) {
                                                    $css = 'red';
                                                }
                                            }
                                        }
                                    } else {
                                        $css = 'holiday';
                                    }

                                    if ($css == 'red') {
                                        $ticket_q = $this->db
                                            ->select('a.id')
                                            ->from('communication_ticket_system a')
                                            ->where('a.df_id', $df_id)
                                            ->where('a.task_id', $taskid)
                                            ->get();

                                        if ($ticket_q->num_rows() > 0) {
                                            if (!in_array('red', $css_occur)) {
                                                $icon = '<a class="ticket-link" href="javascript:;" onclick="open_ticket_popup(' . $df_id . ',' . $depid . ',' . $taskid . ');"><i class="fa fa-ticket"></i></a>';
                                                $css_occur[] = 'red';
                                            }
                                        }
                                    }
                                ?>
                                    <td class="<?php echo $css; ?>"><?php echo $icon; ?></td>
                                <?php } ?>
                            </tr>

                        <?php $i++; } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="<?php echo count($Dates) + 1; ?>" style="padding:20px; font-weight:900;">
                                No task data found for this DF.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="exampleModalTicket" tabindex="-1" aria-labelledby="exampleModalTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    Tickets Raised On Task
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
            </div>

            <div class="modal-body" id="modalbodyTicket" style="overflow-x:auto;">
                Please wait...
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:20px; font-weight:800;">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.min.js"></script>

<script>
function filter_Gantt() {
    var filter = $('#filter').val();
    window.location = "<?php echo page_url; ?>Task/dfgantchartNew/<?php echo $df_id; ?>/" + filter;
}

function switchData() {
    var swi = $("#switch").val();

    if (swi == 1) {
        window.location = "<?php echo page_url; ?>Task/dfgantchartSharmaji/<?php echo $df_id; ?>/<?php echo $department; ?>";
    } else if (swi == 2) {
        window.location = "<?php echo page_url; ?>Task/dfgantchartNew/<?php echo $df_id; ?>/<?php echo $department; ?>";
    } else if (swi == 3) {
        window.location = "<?php echo page_url; ?>Task/dfgantchartDepartmentwise/<?php echo $df_id; ?>";
    } else {
        window.location = "<?php echo page_url; ?>Task/finalgantchart/<?php echo $df_id; ?>";
    }
}

function open_ticket_popup(dfid, departmentid, taskid) {
    $("#exampleModalTicket").modal('show');
    $("#modalbodyTicket").html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading tickets...</div>');

    $.ajax({
        type: "POST",
        url: "<?php echo page_url; ?>Task/getTicktsTaskWise/",
        data: {
            dfid: dfid,
            departmentid: departmentid,
            taskid: taskid
        },
        success: function(data) {
            $("#modalbodyTicket").html(data);
        },
        error: function() {
            $("#modalbodyTicket").html('<div class="alert alert-danger">Unable to load ticket details.</div>');
        }
    });
}
</script>

</body>
</html>