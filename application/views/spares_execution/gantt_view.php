<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$workflow_labels = array(
    'IN_STOCK' => 'In Stock',
    'STANDARD' => 'Standard Procurement',
    'CUSTOM' => 'Custom Production',
);

if (!function_exists('spares_execution_gantt_status_class')) {
    function spares_execution_gantt_status_class($status)
    {
        $map = array(
            'Pending' => 'status-pending',
            'Open' => 'status-open',
            'In Progress' => 'status-in-progress',
            'Completed' => 'status-completed',
            'Blocked' => 'status-blocked',
            'On Hold' => 'status-on-hold',
            'Cancelled' => 'status-cancelled',
        );

        return isset($map[$status]) ? $map[$status] : 'status-pending';
    }
}

if (!function_exists('spares_execution_gantt_date_only')) {
    function spares_execution_gantt_date_only($value)
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return null;
        }

        return date('Y-m-d', strtotime($value));
    }
}

if (!function_exists('spares_execution_gantt_offset_days')) {
    function spares_execution_gantt_offset_days($date_value, $timeline_start)
    {
        if (empty($date_value)) {
            return 0;
        }

        return (int) floor((strtotime($date_value) - strtotime($timeline_start)) / 86400);
    }
}

if (!function_exists('spares_execution_gantt_day_span')) {
    function spares_execution_gantt_day_span($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        return max(1, (int) floor((strtotime($end_date) - strtotime($start_date)) / 86400) + 1);
    }
}

$today_iso = date('Y-m-d');
$date_points = array($today_iso);

if (!empty($execution_order->commit_date)) {
    $date_points[] = spares_execution_gantt_date_only($execution_order->commit_date);
}

foreach ($tasks as $task) {
    foreach (array('planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date') as $field_name) {
        $clean_date = spares_execution_gantt_date_only($task->$field_name);
        if (!empty($clean_date)) {
            $date_points[] = $clean_date;
        }
    }
}

$date_points = array_values(array_filter(array_unique($date_points)));
sort($date_points);

$timeline_start = new DateTime(!empty($date_points[0]) ? $date_points[0] : $today_iso);
$timeline_end = new DateTime(!empty($date_points[count($date_points) - 1]) ? $date_points[count($date_points) - 1] : $today_iso);
$timeline_start->modify('-1 day');
$timeline_end->modify('+1 day');

$timeline_start_iso = $timeline_start->format('Y-m-d');
$timeline_end_iso = $timeline_end->format('Y-m-d');
$timeline_days = (int) $timeline_start->diff($timeline_end)->days + 1;
$cell_width = 38;
if ($timeline_days > 45) {
    $cell_width = 30;
}
if ($timeline_days > 75) {
    $cell_width = 24;
}
if ($timeline_days > 110) {
    $cell_width = 18;
}
$timeline_width = $timeline_days * $cell_width;
$today_left = spares_execution_gantt_offset_days($today_iso, $timeline_start_iso) * $cell_width;
$commit_left = !empty($execution_order->commit_date) ? spares_execution_gantt_offset_days(spares_execution_gantt_date_only($execution_order->commit_date), $timeline_start_iso) * $cell_width : null;

$timeline_dates = array();
$month_segments = array();
$cursor = clone $timeline_start;
while ($cursor <= $timeline_end) {
    $date_iso = $cursor->format('Y-m-d');
    $month_key = $cursor->format('Y-m');
    if (!isset($month_segments[$month_key])) {
        $month_segments[$month_key] = array(
            'label' => $cursor->format('M Y'),
            'days' => 0,
        );
    }
    $month_segments[$month_key]['days']++;
    $timeline_dates[] = array(
        'date' => $date_iso,
        'day' => $cursor->format('d'),
        'weekday' => $cursor->format('D'),
        'is_weekend' => in_array((int) $cursor->format('N'), array(6, 7), true),
        'is_today' => $date_iso === $today_iso,
    );
    $cursor->modify('+1 day');
}

$total_tasks = !empty($task_counters->total_tasks) ? (int) $task_counters->total_tasks : 0;
$completed_tasks = !empty($task_counters->completed_tasks) ? (int) $task_counters->completed_tasks : 0;
$progress_percent = $total_tasks > 0 ? (int) round(($completed_tasks * 100) / $total_tasks) : 0;
$timeline_span_label = $timeline_start->format('d M Y') . ' to ' . $timeline_end->format('d M Y');
$marketing_owner_name = trim(($order_snapshot->marketing_title ?? '') . ' ' . ($order_snapshot->marketing_first_name ?? '') . ' ' . ($order_snapshot->marketing_last_name ?? ''));
$first_execution_task_id = !empty($tasks[0]->execution_task_id) ? (int) $tasks[0]->execution_task_id : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; <?php echo $page_title; ?></title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <style>
        body { background: #f4f7fb; }
        .card-box { border-radius: 12px; border: 1px solid #e4eaf2; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05); }
        .summary-card { background: #fff; border: 1px solid #e4eaf2; border-radius: 12px; padding: 18px; margin-bottom: 20px; min-height: 112px; }
        .summary-title { font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: #7b8794; }
        .summary-value { font-size: 22px; font-weight: 700; color: #1f2937; margin-top: 8px; line-height: 1.25; }
        .summary-note { color: #7b8794; font-size: 12px; margin-top: 8px; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .status-pending { background: #edf2f7; color: #4a5568; }
        .status-open { background: #e8f1ff; color: #1e5aa7; }
        .status-in-progress { background: #fff3dd; color: #9a6700; }
        .status-completed { background: #e5f7ed; color: #157347; }
        .status-blocked { background: #fdecec; color: #b42318; }
        .status-on-hold { background: #f3ecff; color: #6f42c1; }
        .status-cancelled { background: #f3f4f6; color: #6b7280; }
        .legend-chip { display: inline-flex; align-items: center; margin-right: 14px; margin-bottom: 10px; font-size: 12px; color: #4b5563; }
        .legend-chip span { display: inline-block; width: 20px; height: 10px; border-radius: 999px; margin-right: 8px; }
        .legend-planned { background: rgba(30, 90, 167, 0.16); border: 1px solid rgba(30, 90, 167, 0.45); }
        .legend-live { background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); }
        .legend-complete { background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%); }
        .legend-delayed { background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); }
        .legend-hold { background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%); }
        .legend-line { position: relative; width: 20px; height: 10px; margin-right: 8px; }
        .legend-line::before { content: ''; position: absolute; top: -2px; bottom: -2px; left: 9px; width: 2px; border-radius: 999px; }
        .legend-line.today::before { background: #ef4444; }
        .legend-line.commit::before { background: #0f766e; }
        .gantt-scroll { overflow-x: auto; overflow-y: hidden; border: 1px solid #e4eaf2; border-radius: 12px; background: #fff; }
        .gantt-board { min-width: 100%; }
        .gantt-header,
        .gantt-row { display: flex; align-items: stretch; min-width: 100%; }
        .gantt-meta-col { width: 360px; min-width: 360px; max-width: 360px; position: sticky; left: 0; z-index: 8; background: #fff; border-right: 1px solid #e4eaf2; }
        .gantt-header .gantt-meta-col { z-index: 12; background: #f8fafc; }
        .gantt-meta-head { padding: 18px; border-bottom: 1px solid #e4eaf2; }
        .gantt-meta-title { font-size: 15px; font-weight: 700; color: #1f2937; }
        .gantt-meta-subtitle { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .gantt-timeline-col { position: relative; }
        .gantt-month-row,
        .gantt-day-row { display: flex; }
        .gantt-month-cell { border-right: 1px solid #dbe4ef; border-bottom: 1px solid #e4eaf2; background: #f8fafc; padding: 10px 8px; font-size: 12px; font-weight: 700; color: #1f2937; text-align: center; }
        .gantt-day-cell { border-right: 1px solid #edf2f7; border-bottom: 1px solid #e4eaf2; text-align: center; padding: 8px 0; background: #fff; }
        .gantt-day-cell.weekend { background: #f8fafc; }
        .gantt-day-cell.today { background: #fff1f2; }
        .gantt-day-num { display: block; font-size: 12px; font-weight: 700; color: #334155; line-height: 1.1; }
        .gantt-day-name { display: block; font-size: 10px; color: #94a3b8; margin-top: 2px; }
        .gantt-row { border-bottom: 1px solid #edf2f7; }
        .gantt-row:last-child { border-bottom: 0; }
        .gantt-task-meta { padding: 16px 18px; min-height: 84px; }
        .gantt-task-title { font-size: 14px; font-weight: 700; color: #1f2937; }
        .gantt-task-note { font-size: 12px; color: #6b7280; margin-top: 6px; }
        .gantt-task-flags { margin-top: 8px; }
        .gantt-flag { display: inline-block; font-size: 11px; border-radius: 999px; padding: 3px 9px; margin-right: 6px; margin-top: 4px; }
        .flag-overdue { background: #fdecec; color: #b42318; }
        .flag-blocked { background: #fff3dd; color: #9a6700; }
        .flag-parallel { background: #e8f1ff; color: #1e5aa7; }
        .flag-complete { background: #e5f7ed; color: #157347; }
        .gantt-track-wrap { position: relative; height: 84px; background: #fff; }
        .gantt-track { position: relative; height: 84px; }
        .gantt-grid-cell { position: absolute; top: 0; bottom: 0; border-right: 1px solid #edf2f7; }
        .gantt-grid-cell.weekend { background: rgba(148, 163, 184, 0.08); }
        .gantt-line { position: absolute; top: 0; bottom: 0; width: 2px; z-index: 5; }
        .gantt-line.today { background: #ef4444; }
        .gantt-line.commit { background: #0f766e; }
        .gantt-bar { position: absolute; height: 14px; border-radius: 999px; z-index: 6; }
        .gantt-bar-planned { top: 20px; background: rgba(30, 90, 167, 0.16); border: 1px solid rgba(30, 90, 167, 0.55); }
        .gantt-bar-actual { top: 42px; color: #fff; font-size: 10px; font-weight: 700; padding: 0 8px; display: flex; align-items: center; white-space: nowrap; overflow: hidden; }
        .actual-live { background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); }
        .actual-complete { background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%); }
        .actual-delayed { background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); }
        .actual-hold { background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%); }
        .actual-cancelled { background: linear-gradient(90deg, #9ca3af 0%, #6b7280 100%); }
        .milestone-label { position: absolute; top: 6px; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 999px; z-index: 7; white-space: nowrap; }
        .milestone-label.today { right: 8px; background: #fff1f2; color: #b42318; }
        .milestone-label.commit { left: 8px; background: #ecfeff; color: #0f766e; }
        .empty-state { padding: 50px 20px; text-align: center; color: #6b7280; }
        @media print {
            #topnav,
            .page-title-box .btn-group,
            .legend-block .btn { display: none !important; }
            body { background: #fff; }
            .wrapper, .container-fluid { padding: 0; margin: 0; }
            .card-box, .summary-card, .gantt-scroll { box-shadow: none; }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">
                            <a href="<?php echo page_url; ?>Spares/order_detail/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Order Detail</a>
                            <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-primary waves-effect waves-light"><i class="fa fa-tasks"></i> Open Tracker</a>
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-dashboard"></i> Execution Dashboard</a>
                            <a href="<?php echo page_url; ?>Spares_execution/export_order_tracker/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-success waves-effect waves-light"><i class="fa fa-download"></i> Export Tracker CSV</a>
                            <?php if (!empty($can_manage_schedule)): ?>
                                <a href="<?php echo page_url; ?>Spares_execution/edit_schedule/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-warning waves-effect waves-light"><i class="fa fa-calendar"></i> Edit Schedule</a>
                            <?php endif; ?>
                            <a href="#" onclick="window.print(); return false;" class="btn btn-success waves-effect waves-light"><i class="fa fa-print"></i> Print</a>
                        </div>
                        <h4 class="page-title">Spares Execution Gantt</h4>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">Company</div>
                        <div class="summary-value"><?php echo htmlspecialchars($order_snapshot->company_name); ?></div>
                        <div class="summary-note">SO-<?php echo (int) $order_snapshot->order_id; ?><?php if (!empty($order_snapshot->po_no)): ?> | PO <?php echo htmlspecialchars($order_snapshot->po_no); ?><?php endif; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">Workflow</div>
                        <div class="summary-value"><?php echo htmlspecialchars(isset($workflow_labels[$execution_order->workflow_type]) ? $workflow_labels[$execution_order->workflow_type] : $execution_order->workflow_type); ?></div>
                        <div class="summary-note">Priority: <?php echo htmlspecialchars($execution_order->priority); ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">Commit Date</div>
                        <div class="summary-value"><?php echo !empty($execution_order->commit_date) ? date('d M Y', strtotime($execution_order->commit_date)) : '-'; ?></div>
                        <div class="summary-note">Execution status: <?php echo htmlspecialchars($execution_order->execution_status); ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">Progress</div>
                        <div class="summary-value"><?php echo $progress_percent; ?>%</div>
                        <div class="summary-note"><?php echo $completed_tasks; ?>/<?php echo $total_tasks; ?> tasks completed</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">Timeline Span</div>
                        <div class="summary-value"><?php echo htmlspecialchars($timeline_span_label); ?></div>
                        <div class="summary-note">Overdue: <?php echo (int) $task_counters->overdue_tasks; ?> | Blocked: <?php echo (int) $task_counters->blocked_tasks; ?> | On Hold: <?php echo (int) $task_counters->on_hold_tasks; ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box" style="padding: 18px 20px;">
                        <div class="row">
                            <div class="col-md-8 legend-block">
                                <div class="legend-chip"><span class="legend-planned"></span> Planned window</div>
                                <div class="legend-chip"><span class="legend-live"></span> Live work</div>
                                <div class="legend-chip"><span class="legend-complete"></span> Completed on time</div>
                                <div class="legend-chip"><span class="legend-delayed"></span> Delayed / overdue</div>
                                <div class="legend-chip"><span class="legend-hold"></span> On hold</div>
                                <div class="legend-chip"><span class="legend-line today"></span> Today</div>
                                <div class="legend-chip"><span class="legend-line commit"></span> Commit date</div>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="summary-note" style="margin-top: 4px;">
                                    Marketing owner: <?php echo !empty($marketing_owner_name) ? htmlspecialchars($marketing_owner_name) : '<span class="text-muted">Not mapped</span>'; ?>
                                </div>
                                <?php if (!empty($execution_order->schedule_notes)): ?>
                                    <div class="summary-note" style="margin-top: 8px;">Schedule notes are available in the tracker and edit schedule screen.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <?php if (!empty($tasks)): ?>
                            <div class="gantt-scroll">
                                <div class="gantt-board">
                                    <div class="gantt-header" style="min-width: <?php echo 360 + $timeline_width; ?>px;">
                                        <div class="gantt-meta-col">
                                            <div class="gantt-meta-head">
                                                <div class="gantt-meta-title">Execution Task Timeline</div>
                                                <div class="gantt-meta-subtitle">Track planned and actual movement for each execution task on the same order.</div>
                                            </div>
                                        </div>
                                        <div class="gantt-timeline-col" style="width: <?php echo $timeline_width; ?>px;">
                                            <div class="gantt-month-row">
                                                <?php foreach ($month_segments as $month_segment): ?>
                                                    <div class="gantt-month-cell" style="width: <?php echo (int) $month_segment['days'] * $cell_width; ?>px;"><?php echo htmlspecialchars($month_segment['label']); ?></div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="gantt-day-row">
                                                <?php foreach ($timeline_dates as $timeline_date): ?>
                                                    <div class="gantt-day-cell <?php echo $timeline_date['is_weekend'] ? 'weekend' : ''; ?> <?php echo $timeline_date['is_today'] ? 'today' : ''; ?>" style="width: <?php echo $cell_width; ?>px;">
                                                        <span class="gantt-day-num"><?php echo htmlspecialchars($timeline_date['day']); ?></span>
                                                        <span class="gantt-day-name"><?php echo htmlspecialchars($timeline_date['weekday']); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php foreach ($tasks as $task): ?>
                                        <?php
                                        $planned_start = spares_execution_gantt_date_only($task->planned_start_date);
                                        $planned_end = spares_execution_gantt_date_only($task->planned_end_date);
                                        $actual_start = spares_execution_gantt_date_only($task->actual_start_date);
                                        $actual_end = spares_execution_gantt_date_only($task->actual_end_date);
                                        $progress_value = max(0, min(100, (int) $task->completion_percent));
                                        $owner_name = !empty($task->first_name) ? trim($task->title . ' ' . $task->first_name . ' ' . $task->last_name) : 'Unassigned';

                                        if (empty($actual_start) && ($progress_value > 0 || in_array($task->task_status, array('In Progress', 'Completed'), true))) {
                                            $actual_start = !empty($planned_start) ? $planned_start : $today_iso;
                                        }

                                        if (empty($actual_end) && !empty($actual_start) && !in_array($task->task_status, array('Pending', 'Cancelled'), true)) {
                                            $actual_end = $task->task_status === 'Completed' ? $today_iso : $today_iso;
                                        }

                                        $planned_left = !empty($planned_start) ? spares_execution_gantt_offset_days($planned_start, $timeline_start_iso) * $cell_width : 0;
                                        $planned_width = !empty($planned_start) && !empty($planned_end) ? spares_execution_gantt_day_span($planned_start, $planned_end) * $cell_width : 0;
                                        $actual_left = !empty($actual_start) ? spares_execution_gantt_offset_days($actual_start, $timeline_start_iso) * $cell_width : 0;
                                        $actual_width = !empty($actual_start) && !empty($actual_end) ? spares_execution_gantt_day_span($actual_start, $actual_end) * $cell_width : 0;
                                        $actual_delayed = !empty($planned_end) && !empty($actual_end) && $actual_end > $planned_end;
                                        $actual_class = 'actual-live';
                                        if ($task->task_status === 'Completed') {
                                            $actual_class = $actual_delayed ? 'actual-delayed' : 'actual-complete';
                                        } elseif ($task->task_status === 'On Hold') {
                                            $actual_class = 'actual-hold';
                                        } elseif ($task->task_status === 'Cancelled') {
                                            $actual_class = 'actual-cancelled';
                                        } elseif ((int) $task->live_is_overdue === 1 || $actual_delayed) {
                                            $actual_class = 'actual-delayed';
                                        }
                                        ?>
                                        <div class="gantt-row" style="min-width: <?php echo 360 + $timeline_width; ?>px;">
                                            <div class="gantt-meta-col">
                                                <div class="gantt-task-meta">
                                                    <div class="gantt-task-title"><?php echo (int) $task->sequence_no; ?>. <?php echo htmlspecialchars($task->task_name); ?></div>
                                                    <div class="gantt-task-note">
                                                        <?php echo !empty($task->department) ? htmlspecialchars($task->department) : 'Department not set'; ?> | <?php echo htmlspecialchars($owner_name); ?>
                                                    </div>
                                                    <div class="gantt-task-note">
                                                        Planned: <?php echo !empty($planned_start) ? date('d M', strtotime($planned_start)) : '-'; ?> to <?php echo !empty($planned_end) ? date('d M', strtotime($planned_end)) : '-'; ?>
                                                        <?php if (!empty($actual_end) && $task->task_status === 'Completed'): ?>
                                                            | Finished: <?php echo date('d M', strtotime($actual_end)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="gantt-task-flags">
                                                        <span class="status-badge <?php echo spares_execution_gantt_status_class($task->task_status); ?>"><?php echo htmlspecialchars($task->task_status); ?></span>
                                                        <?php if ((int) $task->live_is_overdue === 1): ?>
                                                            <span class="gantt-flag flag-overdue">Overdue</span>
                                                        <?php endif; ?>
                                                        <?php if ((int) $task->dependency_blocked === 1): ?>
                                                            <span class="gantt-flag flag-blocked">Blocked by <?php echo htmlspecialchars($task->dependency_task_name); ?></span>
                                                        <?php elseif (!empty($task->dependency_task_name) && (int) $task->can_start_parallel === 1): ?>
                                                            <span class="gantt-flag flag-parallel">Parallel with <?php echo htmlspecialchars($task->dependency_task_name); ?></span>
                                                        <?php endif; ?>
                                                        <?php if ($task->task_status === 'Completed' && (int) $task->live_is_overdue !== 1): ?>
                                                            <span class="gantt-flag flag-complete">Closed</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="gantt-timeline-col" style="width: <?php echo $timeline_width; ?>px;">
                                                <div class="gantt-track-wrap">
                                                    <div class="gantt-track" style="width: <?php echo $timeline_width; ?>px;">
                                                        <?php foreach ($timeline_dates as $timeline_index => $timeline_date): ?>
                                                            <span class="gantt-grid-cell <?php echo $timeline_date['is_weekend'] ? 'weekend' : ''; ?>" style="left: <?php echo $timeline_index * $cell_width; ?>px; width: <?php echo $cell_width; ?>px;"></span>
                                                        <?php endforeach; ?>

                                                        <?php if ($commit_left !== null): ?>
                                                            <span class="gantt-line commit" style="left: <?php echo (int) $commit_left; ?>px;"></span>
                                                        <?php endif; ?>
                                                        <span class="gantt-line today" style="left: <?php echo (int) $today_left; ?>px;"></span>

                                                        <?php if ((int) $task->execution_task_id === $first_execution_task_id && $commit_left !== null): ?>
                                                            <span class="milestone-label commit" style="left: <?php echo min(max((int) $commit_left + 6, 6), max($timeline_width - 88, 6)); ?>px;">Commit</span>
                                                        <?php endif; ?>
                                                        <?php if ((int) $task->execution_task_id === $first_execution_task_id): ?>
                                                            <span class="milestone-label today" style="left: <?php echo min(max((int) $today_left + 6, 6), max($timeline_width - 72, 6)); ?>px;">Today</span>
                                                        <?php endif; ?>

                                                        <?php if ($planned_width > 0): ?>
                                                            <span class="gantt-bar gantt-bar-planned" style="left: <?php echo (int) $planned_left; ?>px; width: <?php echo max($cell_width, (int) $planned_width); ?>px;"></span>
                                                        <?php endif; ?>
                                                        <?php if ($actual_width > 0): ?>
                                                            <span class="gantt-bar gantt-bar-actual <?php echo $actual_class; ?>" style="left: <?php echo (int) $actual_left; ?>px; width: <?php echo max($cell_width, (int) $actual_width); ?>px;">
                                                                <?php echo $progress_value; ?>%
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                No execution tasks are available for this order yet. Create or edit the schedule first to render the Gantt chart.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/footer'); ?>
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>
</html>
