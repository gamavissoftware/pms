<?php 
$CIA =& get_instance();
$CIA->load->model('Task_model');

$administrator_user_ids = [161, 139, 61, 162, 167];
$current_user_id = (int) $this->session->userdata['logged_in']['user_id'];
$is_df_admin = in_array($current_user_id, $administrator_user_ids, true);
$is_team_leader = $this->db->select('department_id')
    ->from('prestogroup_teams')
    ->where('team_leader', $current_user_id)
    ->limit(1)
    ->get()
    ->num_rows() > 0;
$can_view_all_running_df = ($is_df_admin || $is_team_leader);
$today = date('Y-m-d');
$this->load->helper('df_delay');
$currentDfDelays = df_current_delay_counts($this->db, $today);

$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

function safeDateShowDfDelayReport($date, $format = 'd-m-Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '';
    }

    return date($format, strtotime($date));
}

$reportid = (string) $this->uri->segment(3);
$isDelayReport = ($reportid === '1');
$customisedDfActivity = isset($customised_df_activity) && is_array($customised_df_activity)
    ? $customised_df_activity
    : array();

$rowsData = array();

$totalDf = 0;
$totalCustomisedDf = 0;
$totalDelayedDf = 0;
$totalCriticalDf = 0;
$totalOpenTickets = 0;
$totalProgress = 0;
$totalOnTimeDf = 0;

$this->db->select(
    "df.id, 
    df.df_no, 
    df.added_on, 
    df.df_upload, 
    df.on_hold, 
    df.priority_marked,
    df.machine_id,
    po.podate, 
    po.po_attachment, 
    po.lead_id, 
    po.id as po_id,
    po.df_number as po_df_number,
    (
        SELECT SUM(IFNULL(po_penalty.penalityamount, 0))
        FROM poreceived po_penalty
        WHERE po_penalty.df_id = df.id
    ) as penalty_amount,
    CONCAT(u.title, ' ', u.first_name, ' ', u.last_name) as dfowner,
    u.user_id as owner_id,
    MIN(tasks.start_date) as df_start_date,
    MAX(tasks.end_date) as projected_completion_date,
    COUNT(DISTINCT tasks.id) as total_tasks,
    SUM(CASE WHEN tasks.task_status = 1 THEN 1 ELSE 0 END) as completed_tasks,
    (
        SELECT COUNT(DISTINCT id) 
        FROM communication_ticket_system 
        WHERE df_id = df.id 
        AND ticket_status = 0
    ) as open_ticket_count,
    GROUP_CONCAT(
        CASE 
            WHEN tasks.task_status = 1 
            THEN CONCAT(tasks.task_completed_on, '|', tasks.end_date) 
            ELSE NULL 
        END SEPARATOR ';'
    ) as completed_tasks_data,
    MAX(CASE WHEN tasks.task_status = 1 THEN tasks.task_completed_on ELSE NULL END) as max_completion_date",
    false
);
$this->db->from('df_release df');
$this->db->join('poreceived po', 'po.df_id = df.id', 'left');
$this->db->join('system_users u', 'po.added_by = u.user_id', 'left');
$this->db->join('task_department_wise_scheduling tasks', 'tasks.df_id = df.id', 'left');
$this->db->where('df.df_status', 0);
$this->db->where('df.on_hold', 0);

if ($isDelayReport) {
    $delayed_df_visibility_query = 'EXISTS (
        SELECT 1 FROM task_department_wise_scheduling delayed_tasks
        WHERE delayed_tasks.df_id = df.id AND ' . df_open_overdue_sql($this->db, 'delayed_tasks', $today) . '
    )';

    if ($can_view_all_running_df) {
        $this->db->where($delayed_df_visibility_query, null, false);
    } else {
        // A marketing user's dashboard must contain only DFs owned by that user.
        // Task assignment alone must not expose another marketing person's DF.
        $this->db->group_start();
        $this->db->where('po.added_by', $current_user_id);
        $this->db->where($delayed_df_visibility_query, null, false);
        $this->db->group_end();
    }
} elseif (!$can_view_all_running_df) {
    $this->db->where('po.added_by', $current_user_id);
}

$this->db->group_by('df.id');
$this->db->order_by('df.id', 'DESC');

$query = $this->db->get();

if ($query->num_rows() > 0) {
    foreach ($query->result() as $rows) {

        $displayDfNo = $rows->df_no;
        if (empty($displayDfNo) || $displayDfNo == 0) {
            $displayDfNo = $rows->po_df_number;
        }

        $poDate = safeDateShowDfDelayReport($rows->podate);
        $dfReleaseDate = safeDateShowDfDelayReport($rows->added_on);
        $dfStartDisplay = safeDateShowDfDelayReport($rows->df_start_date);
        $projectedDisplay = safeDateShowDfDelayReport($rows->projected_completion_date);

        $poAttachment = '';
        if ((int)$current_user_id == (int)$rows->owner_id && !empty($rows->po_attachment)) {
            $poAttachment = '<br><a href="'.sfdocument.'Taskdocument/'.$rows->po_attachment.'" download class="btn btn-info btn-xs btn-action"><i class="fa fa-download"></i> PO</a>';
        }

        $edit = '';
        if (!empty($rows->lead_id) && !empty($rows->po_id)) {
            $gg = $this->db
                ->select('id')
                ->from('df_design_form_table')
                ->where('po_id', $rows->po_id)
                ->where('lead_id', $rows->lead_id)
                ->get();

            if ($gg->num_rows() > 0) {
                $edit = "<a href='".page_url."Dashboard/edit_df_project_form/".$rows->po_id."/".$rows->lead_id."' class='btn btn-primary btn-xs btn-action'><i class='fa fa-pencil'></i> Edit</a>";
            } else {
                // A transferred DF may not have a legacy design-form row under
                // its current PO/lead. Open its DF-scoped workspace instead of
                // linking to an unrelated form from the previous lead.
                $edit = "<a href='".page_url."Dashboard/customised_df_workspace/".$rows->po_id."/".$rows->lead_id."/0' class='btn btn-primary btn-xs btn-action'><i class='fa fa-pencil'></i> Edit</a>";
            }
        }

        $totalTasks = (int)$rows->total_tasks;
        $completedTasks = (int)$rows->completed_tasks;

        $completionPercentage = ($totalTasks > 0) ? round(($completedTasks * 100) / $totalTasks) : 0;

        $maxDelayDays = 0;
        $delayCount = 0;
        $totalDelayDays = 0;

        if (!empty($rows->completed_tasks_data)) {
            $completedTasksList = explode(';', $rows->completed_tasks_data);

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
                    $completedDateObj = date('Y-m-d', strtotime($completedOn));

                    if ($completedDateObj > $endDate) {
                        $delayCount++;

                        if (method_exists($CIA->Task_model, 'getDays')) {
                            $delayDays = $CIA->Task_model->getDays($endDate, $completedDateObj, 1);
                        } else {
                            $delayDays = abs(round((strtotime($completedDateObj) - strtotime($endDate)) / 86400));
                        }

                        $totalDelayDays += (int)$delayDays;

                        if ($delayDays > $maxDelayDays) {
                            $maxDelayDays = (int)$delayDays;
                        }
                    }
                }
            }
        }

        $delayPercentage = ($completedTasks > 0) ? round(($delayCount * 100) / $completedTasks) : 0;
        $maxTaskDelayDays = $maxDelayDays;

        if (method_exists($CIA->Task_model, 'workDelayed')) {
            $maxTaskDelayDays = (int)$CIA->Task_model->workDelayed((int)$rows->id, 0);
        }

        $actualCompletionDate = '';

        if ($completionPercentage == 100 && !empty($rows->max_completion_date) && $rows->max_completion_date != '0000-00-00 00:00:00') {
            $actualCompletionDate = date('Y-m-d', strtotime($rows->max_completion_date));
        } elseif (!empty($rows->projected_completion_date) && $rows->projected_completion_date != '0000-00-00') {
            $tempDate = date('Y-m-d', strtotime($rows->projected_completion_date . ' +' . $maxTaskDelayDays . ' days'));

            if (method_exists($CIA->Task_model, 'SKIPsingle_holidays')) {
                $actualCompletionDate = $CIA->Task_model->SKIPsingle_holidays($tempDate);
            } else {
                $actualCompletionDate = $tempDate;
            }
        }

        $actualDisplay = safeDateShowDfDelayReport($actualCompletionDate);

        $dfDelayDays = 0;
        if (
            !empty($rows->projected_completion_date) &&
            $rows->projected_completion_date != '0000-00-00' &&
            !empty($actualCompletionDate) &&
            $actualCompletionDate != '0000-00-00'
        ) {
            if (date('Y-m-d', strtotime($actualCompletionDate)) > date('Y-m-d', strtotime($rows->projected_completion_date))) {
                if (method_exists($CIA->Task_model, 'getDays')) {
                    $dfDelayDays = $CIA->Task_model->getDays($rows->projected_completion_date, $actualCompletionDate, 1);
                } else {
                    $dfDelayDays = abs(round((strtotime($actualCompletionDate) - strtotime($rows->projected_completion_date)) / 86400));
                }
            }
        }

        $hasCurrentDelay = !empty($currentDfDelays[(int)$rows->id]);
        $delayStatus = $hasCurrentDelay ? 'Delayed' : 'On Time';
        $ticketStatus = ((int)$rows->open_ticket_count > 0) ? 'Open Tickets' : 'No Tickets';

        $riskLevel = 'Normal';
        $riskClass = 'success';

        if ($delayPercentage >= 50 || $dfDelayDays >= 10 || (int)$rows->open_ticket_count >= 3) {
            $riskLevel = 'Critical';
            $riskClass = 'danger';
        } elseif ($delayPercentage > 0 || $dfDelayDays > 0 || (int)$rows->open_ticket_count > 0) {
            $riskLevel = 'Attention';
            $riskClass = 'warning';
        }

        $show = 1;
        if ($isDelayReport) {
            $show = $hasCurrentDelay ? 1 : 0;
        }

        if ($show == 1) {
            $customisedActivity = isset($customisedDfActivity[(int) $rows->id])
                ? $customisedDfActivity[(int) $rows->id]
                : array();
            $isCustomisedDf = !empty($customisedActivity);
            $totalDf++;
            $totalProgress += $completionPercentage;
            $totalOpenTickets += (int)$rows->open_ticket_count;

            if ($isCustomisedDf) {
                $totalCustomisedDf++;
            }

            if ($delayStatus == 'Delayed') {
                $totalDelayedDf++;
            } else {
                $totalOnTimeDf++;
            }

            if ($riskLevel == 'Critical') {
                $totalCriticalDf++;
            }

            $rowsData[] = array(
                'id' => (int)$rows->id,
                'df_no' => $displayDfNo,
                'df_upload' => $rows->df_upload,
                'po_date' => $poDate,
                'po_attachment' => $poAttachment,
                'lead_id' => $rows->lead_id,
                'po_id' => $rows->po_id,
                'edit' => $edit,
                'df_owner' => trim($rows->dfowner),
                'owner_id' => $rows->owner_id,
                'priority_marked' => (int)$rows->priority_marked,
                'penalty_amount' => (float)$rows->penalty_amount,
                'df_release_date' => $dfReleaseDate,
                'df_start_date' => $dfStartDisplay,
                'projected_completion_date' => $projectedDisplay,
                'actual_completion_date' => $actualDisplay,
                'df_delay_days' => $dfDelayDays,
                'overdue_task_count' => isset($currentDfDelays[(int)$rows->id]) ? (int)$currentDfDelays[(int)$rows->id] : 0,
                'completion_percentage' => $completionPercentage,
                'delay_percentage' => $delayPercentage,
                'open_ticket_count' => (int)$rows->open_ticket_count,
                'machine_id' => $rows->machine_id,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'delay_count' => $delayCount,
                'delay_status' => $delayStatus,
                'ticket_status' => $ticketStatus,
                'risk_level' => $riskLevel,
                'risk_class' => $riskClass,
                'is_customised_df' => $isCustomisedDf,
                'customised_activity_count' => isset($customisedActivity['activity_count']) ? (int) $customisedActivity['activity_count'] : 0,
                'customised_last_scheduled_on' => isset($customisedActivity['last_scheduled_on']) ? $customisedActivity['last_scheduled_on'] : ''
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

    <title><?php echo sitetitle; ?> DF List with Delay Info</title>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

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
        h3 {
            color: #ffffff !important;
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

        .filter-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.05);
        }

        .df-view-tabs {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            margin-bottom: 16px;
            padding: 3px;
            border: 1px solid #d8dee9;
            border-radius: 7px;
            background: #f3f6fb;
        }

        .df-view-tab {
            min-height: 36px;
            border: 0;
            border-radius: 5px;
            padding: 7px 13px;
            background: transparent;
            color: #4b5563;
            font-size: 12px;
            font-weight: 800;
        }

        .df-view-tab:hover,
        .df-view-tab:focus {
            color: #111827;
            outline: none;
        }

        .df-view-tab.is-active {
            background: #fff;
            color: #0f766e;
            box-shadow: 0 1px 4px rgba(31,41,55,0.12);
        }

        .df-view-count {
            display: inline-block;
            min-width: 22px;
            margin-left: 5px;
            padding: 2px 6px;
            border-radius: 10px;
            background: #e5e7eb;
            color: #374151;
            text-align: center;
        }

        .df-view-tab.is-active .df-view-count {
            background: #d8f3ec;
            color: #0f766e;
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

        .customised-df-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 4px 7px;
            border: 1px solid #99d8c9;
            border-radius: 4px;
            background: #e8f7f2;
            color: #0f766e;
            font-size: 10px;
            font-weight: 900;
            white-space: nowrap;
        }

        table.manglesh tbody tr.customised-df-row > td {
            background-color: #f4fbf9 !important;
        }

        table.manglesh tbody tr.customised-df-row > td:first-child {
            box-shadow: inset 4px 0 0 #159477;
        }

        table.manglesh tbody tr.customised-df-row:hover > td {
            background-color: #eaf7f3 !important;
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

        .ticket-badge {
            background-color: #f59e0b;
            color: #fff;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 900;
            cursor: pointer;
            display: inline-block;
        }

        .ticket-badge:hover {
            background-color: #d97706;
        }

        .btn-action {
            border-radius: 20px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            margin: 2px;
        }

        .inline-action-form {
            display: inline-block;
            margin: 0;
        }

        .company-box {
            text-align: left;
            min-width: 180px;
        }

        .company-name {
            font-weight: 900;
            color: #111827;
        }

        .date-muted {
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
            margin-top: 2px;
        }

        .dataTables_wrapper .dt-buttons .btn {
            border-radius: 20px !important;
            margin-right: 5px;
            font-weight: 800;
        }

        .dataTables_filter input,
        .dataTables_length select,
        .filter-card input,
        .filter-card select {
            border-radius: 20px;
            border: 1px solid #d8dee9;
            padding: 6px 12px;
        }

        .modal-content {
            border-radius: 18px;
            border: none;
            box-shadow: 0 20px 55px rgba(0,0,0,.18);
        }

        .modal-header {
            background: linear-gradient(135deg, <?php echo $themeColor;?> 0%, #111827 100%);
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
                            <?php if ($isDelayReport) { ?>
                                <h3 class="report-title"><?php echo $is_df_admin ? 'DFs Running With Delay' : 'My Delayed DF'; ?></h3>
                                <div class="report-subtitle">
                                    <?php echo $is_df_admin ? 'Delay-focused DF report with task completion, open ticket and risk indicators.' : 'Delay-focused view of DFs owned by you as marketing person.'; ?>
                                </div>
                            <?php } else { ?>
                                <h3 class="report-title"><?php echo $is_df_admin ? 'All Running DF' : 'My Running DF'; ?></h3>
                                <div class="report-subtitle">
                                    <?php echo $is_df_admin ? 'Live DF execution report with progress, projected closure, delay status, tickets and machine mapping.' : 'Live DF execution report showing DFs owned by you as marketing person.'; ?>
                                </div>
                            <?php } ?>

                            <div class="report-pill">
                                <i class="fa fa-tasks"></i> <?php echo $is_df_admin ? 'Active and non-hold DF records' : 'Your active marketing DFs'; ?>
                            </div>
                        </div>

                        <div class="col-md-4 text-right">
                            <div style="margin-bottom:12px;">
                                <a href="<?php echo page_url; ?>Reporting/penalitydf" class="btn btn-danger waves-effect waves-light" style="border-radius:20px; font-weight:800;">
                                    <i class="fa fa-exclamation-triangle"></i> Penalty Report
                                </a>
                            </div>
                            <div style="font-size:13px; opacity:.85;">Visible DF Records</div>
                            <div style="font-size:28px; font-weight:900; margin-top:5px;">
                                <?php echo $totalDf; ?> DF
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

	        <?php if ($is_df_admin) { ?>
            <div class="filter-card">
                <div class="filter-title">
                    <i class="fa fa-file-text-o"></i> Management Report Filter
                </div>

                <form method="post" action="<?php echo page_url; ?>Dashboard/management_report" class="form-inline" target="_blank">
                    <div class="form-group m-r-10">
                        <label for="start_date" class="m-r-10">From:</label>
                        <input type="text" class="form-control datepicker" name="start_date" id="start_date" placeholder="Start Date" required>
                    </div>

                    <div class="form-group m-r-10">
                        <label for="end_date" class="m-r-10">To:</label>
                        <input type="text" class="form-control datepicker" name="end_date" id="end_date" placeholder="End Date" required>
                    </div>

                    <button type="submit" class="btn btn-primary waves-effect waves-light" style="border-radius:20px; font-weight:800;">
                        <i class="fa fa-search"></i> Generate Report
                    </button>
                </form>
            </div>
        <?php } ?>

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
                    <div class="kpi-hint">DF with overdue pending tasks</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-ticket"></i></div>
                    <div class="kpi-label">Open Tickets</div>
                    <div class="kpi-value"><?php echo $totalOpenTickets; ?></div>
                    <div class="kpi-hint">Total open tickets in visible DF</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-label">Average Progress</div>
                    <div class="kpi-value"><?php echo $avgProgress; ?>%</div>
                    <div class="kpi-hint">Overall completion percentage</div>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <div class="df-view-tabs" role="tablist" aria-label="DF list view">
                <button type="button" class="df-view-tab is-active" data-customised-filter="" role="tab" aria-selected="true">
                    All DF <span class="df-view-count"><?php echo $totalDf; ?></span>
                </button>
                <button type="button" class="df-view-tab" data-customised-filter="1" role="tab" aria-selected="false">
                    <i class="fa fa-sliders"></i> Customised DF <span class="df-view-count"><?php echo $totalCustomisedDf; ?></span>
                </button>
            </div>

            <div class="filter-title">
                <i class="fa fa-filter"></i> Smart Filters
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>Search</label>
                    <input type="text" id="customSearch" class="form-control" placeholder="Search DF, owner, dates...">
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Marketing Person</label>
                    <select id="ownerFilter" class="form-control">
                        <option value="">All Marketing Person</option>
                        <?php
                        $owners = array();
                        foreach ($rowsData as $row) {
                            if (!empty($row['df_owner'])) {
                                $owners[$row['df_owner']] = $row['df_owner'];
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
                    <label>Delay Status</label>
                    <select id="delayFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Delayed">Delayed</option>
                        <option value="On Time">On Time</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Ticket Status</label>
                    <select id="ticketFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Open Tickets">Open Tickets</option>
                        <option value="No Tickets">No Tickets</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Risk</label>
                    <select id="riskFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Critical">Critical</option>
                        <option value="Attention">Attention</option>
                        <option value="Normal">Normal</option>
                    </select>
                </div>

                <div class="col-md-1 col-sm-6">
                    <label>&nbsp;</label>
                    <button type="button" id="resetFilters" class="btn btn-default btn-block" style="border-radius:20px; font-weight:800;">
                        Reset
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
                        <th>DF / PO</th>
                        <th>PO Date</th>
                        <th>Marketing Person</th>
                        <th>DF Release</th>
                        <th>DF Start</th>
                        <th>Planned Closure</th>
                        <th>Actual / Expected</th>
                        <th>DF Delay</th>
                        <th>Progress</th>
                        <th>Delayed %</th>
                        <th>Risk</th>
                        <th>Open Tickets</th>
                        <th>Actions</th>
                        <?php if ((int)$current_user_id == 161) { ?>
                            <th>Machines</th>
                        <?php } ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($rowsData)) { ?>
                        <?php $m = 1; foreach ($rowsData as $row) {

                            $progressClass = 'progress-bar-success';
                            if ($row['completion_percentage'] < 50) {
                                $progressClass = 'progress-bar-danger';
                            } elseif ($row['completion_percentage'] < 80) {
                                $progressClass = 'progress-bar-warning';
                            }

                            $riskPillClass = 'pill-success';
                            if ($row['risk_class'] == 'danger') {
                                $riskPillClass = 'pill-danger';
                            } elseif ($row['risk_class'] == 'warning') {
                                $riskPillClass = 'pill-warning';
                            }

                            $canManagePenalty = ((int)$current_user_id === (int)$row['owner_id']);
                            $hasDfMarked = ((int)$row['priority_marked'] > 0);
                            $hasPenaltyMarked = ((float)$row['penalty_amount'] > 0);
                        ?>
                            <tr
                                class="<?php echo $row['is_customised_df'] ? 'customised-df-row' : ''; ?>"
                                data-owner="<?php echo htmlspecialchars($row['df_owner']); ?>"
                                data-delay="<?php echo $row['delay_status']; ?>"
                                data-ticket="<?php echo $row['ticket_status']; ?>"
                                data-risk="<?php echo $row['risk_level']; ?>"
                                data-customised="<?php echo $row['is_customised_df'] ? '1' : '0'; ?>"
                            >
                                <td><?php echo $m; ?></td>

                                <td>
                                    <span class="df-badge">
                                        <?php echo strtoupper($row['df_no']); ?>
                                    </span>
                                    <?php if ($row['is_customised_df']) { ?>
                                        <?php
                                        $lastCustomisedDate = !empty($row['customised_last_scheduled_on'])
                                            ? date('d-m-Y H:i', strtotime($row['customised_last_scheduled_on']))
                                            : '';
                                        ?>
                                        <br>
                                        <span class="customised-df-badge" title="Last Customised DF update: <?php echo htmlspecialchars($lastCustomisedDate); ?>">
                                            <i class="fa fa-sliders"></i> Customised DF
                                        </span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <?php if (!empty($row['df_upload'])) { ?>
                                        <a href="<?php echo sfdocument;?>Taskdocument/dfattachment/<?php echo $row['df_upload'];?>" download class="btn btn-primary btn-xs btn-action">
                                            <i class="fa fa-download"></i> DF
                                        </a>
                                    <?php } ?>
                                    <?php echo $row['edit']; ?>
                                </td>

                                <td>
                                    <?php echo !empty($row['po_date']) ? $row['po_date'] : '-'; ?>
                                    <?php echo $row['po_attachment']; ?>
                                </td>

                                <td><?php echo !empty($row['df_owner']) ? strtoupper($row['df_owner']) : '-'; ?></td>

                                <td><?php echo !empty($row['df_release_date']) ? $row['df_release_date'] : '-'; ?></td>

                                <td><?php echo !empty($row['df_start_date']) ? $row['df_start_date'] : '-'; ?></td>

                                <td><?php echo !empty($row['projected_completion_date']) ? $row['projected_completion_date'] : '-'; ?></td>

                                <td><?php echo !empty($row['actual_completion_date']) ? $row['actual_completion_date'] : '-'; ?></td>

                                <td>
                                    <?php if ($row['delay_status'] === 'Delayed') { ?>
                                        <span class="status-pill pill-danger">Delayed</span>
                                        <div class="date-muted"><?php echo (int)$row['overdue_task_count']; ?> overdue pending tasks</div>
                                    <?php } else { ?>
                                        <span class="status-pill pill-success">On Time</span>
                                    <?php } ?>
                                    <?php if ($row['df_delay_days'] > 0) { ?>
                                        <div class="date-muted">Projected closure delay: <?php echo (int)$row['df_delay_days']; ?> days</div>
                                    <?php } ?>
                                </td>

                                <td>
                                    <div class="progress-wrap">
                                        <div class="progress">
                                            <div class="progress-bar <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo $row['completion_percentage']; ?>%;"></div>
                                        </div>
                                        <strong><?php echo $row['completion_percentage']; ?>%</strong>
                                        <div class="date-muted">
                                            <?php echo $row['completed_tasks']; ?>/<?php echo $row['total_tasks']; ?> tasks
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?php if ($row['delay_percentage'] > 0) { ?>
                                        <span class="status-pill pill-danger"><?php echo $row['delay_percentage']; ?>%</span>
                                    <?php } else { ?>
                                        <span class="status-pill pill-success">0%</span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <span class="status-pill <?php echo $riskPillClass; ?>">
                                        <?php echo $row['risk_level']; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($row['open_ticket_count'] > 0) { ?>
                                        <span class="ticket-badge btn-view-tickets"
                                              data-df-id="<?php echo $row['id']; ?>"
                                              data-df-no="<?php echo htmlspecialchars(strtoupper($row['df_no'])); ?>">
                                            <i class="fa fa-ticket"></i> <?php echo $row['open_ticket_count']; ?> Open
                                        </span>
                                    <?php } else { ?>
                                        <span class="status-pill pill-success">No Tickets</span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <?php if ($canManagePenalty) { ?>
                                        <form method="post" action="<?php echo page_url; ?>Task/save_marked_df" class="inline-action-form">
                                            <input type="hidden" name="df_id" value="<?php echo (int)$row['id']; ?>">
                                            <input type="hidden" name="mark_value" value="<?php echo $hasDfMarked ? 0 : 1; ?>">
                                            <input type="hidden" name="return_url" value="<?php echo page_url . 'Task/dfreleasedashboard' . ($isDelayReport ? '/1' : '/'); ?>">
                                            <button type="submit" class="btn <?php echo $hasDfMarked ? 'btn-default' : 'btn-success'; ?> btn-xs btn-action">
                                                <i class="fa <?php echo $hasDfMarked ? 'fa-check-circle' : 'fa-thumb-tack'; ?>"></i> <?php echo $hasDfMarked ? 'Unmark DF' : 'Mark DF'; ?>
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="btn btn-danger btn-xs btn-action btn-manage-penalty"
                                            data-df-id="<?php echo (int)$row['id']; ?>"
                                            data-df-no="<?php echo htmlspecialchars(strtoupper($row['df_no'])); ?>"
                                            data-penalty-amount="<?php echo (float)$row['penalty_amount']; ?>"
                                        >
                                            <i class="fa fa-exclamation-triangle"></i> <?php echo $hasPenaltyMarked ? 'Update Penalty' : 'Mark Penalty'; ?>
                                        </button>
                                    <?php } else { ?>
                                        <?php if ($hasDfMarked) { ?>
                                            <span class="status-pill pill-info">DF Marked</span>
                                        <?php } ?>
                                        <?php if ($hasPenaltyMarked) { ?>
                                            <span class="status-pill pill-danger">Penalty Marked</span>
                                        <?php } ?>
                                    <?php } ?>

                                    <a href="<?php echo page_url; ?>Dashboard/df_delay_management_report?df_id=<?php echo (int) $row['id']; ?>" target="_blank" class="btn btn-info btn-xs btn-action">
                                        <i class="fa fa-line-chart"></i> Mgmt Report
                                    </a>

                                    <a href="<?php echo page_url;?>gantt/<?php echo $row['id'];?>" target="_blank" class="btn btn-warning btn-xs btn-action">
                                        <i class="fa fa-bar-chart"></i> Gantt
                                    </a>
                                </td>

                                <?php if ((int)$current_user_id == 161) { ?>
                                    <td>
                                        <select name="select_machine" id="select_machine<?php echo $row['id']; ?>" class="form-control" onchange="saveMachine(<?php echo $row['id']; ?>)">
                                            <option value="">--Select Machine--</option>
                                            <?php
                                            $query_machine = $this->db->select('*')->from('machine_master')->where('status', 1)->get();
                                            if ($query_machine->num_rows() > 0) {
                                                foreach ($query_machine->result() as $machine) {
                                                    $select = ($machine->id == $row['machine_id']) ? 'selected' : '';
                                                    echo '<option value="'.$machine->id.'" '.$select.'>'.$machine->name.'</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php $m++; } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php $this->load->view('common/footer');?>

    </div>
</div>

<div class="modal fade" id="ticket-modal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="ticketModalLabel">Open Tickets</h4>
            </div>

            <div class="modal-body">
                <div id="ticket-modal-loader" style="text-align: center; padding: 30px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                    <p>Loading tickets...</p>
                </div>

                <div id="ticket-modal-content" style="max-height: 60vh; overflow-y: auto;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" style="border-radius:20px; font-weight:800;">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="penalty-modal" tabindex="-1" role="dialog" aria-labelledby="penaltyModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo page_url; ?>Task/save_penality_df">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="penaltyModalLabel">Mark Penalty DF</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="df_id" id="penalty-df-id" value="">
                    <input type="hidden" name="return_url" value="<?php echo page_url . 'Task/dfreleasedashboard' . ($isDelayReport ? '/1' : '/'); ?>">

                    <div class="alert alert-info" style="border-radius:12px;">
                        Only the marketing person who owns this DF can update penalty. Enter <strong>0</strong> to remove it from the penalty report.
                    </div>

                    <div class="form-group">
                        <label>DF No.</label>
                        <input type="text" id="penalty-df-no" class="form-control" value="" readonly>
                    </div>

                    <div class="form-group">
                        <label>Penalty Amount</label>
                        <input type="number" step="0.01" min="0" name="penalityamount" id="penalty-amount" class="form-control" required>
                    </div>

                    <div style="font-size:12px; color:#6b7280;">
                        The saved amount will automatically reflect in the penalty DF reporting page.
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="<?php echo page_url; ?>Reporting/penalitydf" class="btn btn-default waves-effect" style="border-radius:20px; font-weight:800;">
                        <i class="fa fa-list"></i> Open Report
                    </a>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" style="border-radius:20px; font-weight:800;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light" style="border-radius:20px; font-weight:800;">
                        <i class="fa fa-save"></i> Save Penalty
                    </button>
                </div>
            </form>
        </div>
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

<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script>
$(document).ready(function () {

    $('.datepicker').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
    });

    var table = $('#example5').DataTable({
        processing: true,
        fixedHeader: true,
        pageLength: 25,
        responsive: false,
        scrollX: true,
        order: [],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'DF Delay Report'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'DF Delay Report'
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'example5') {
            return true;
        }

        var rowNode = table.row(dataIndex).node();

        var ownerFilter = $('#ownerFilter').val();
        var delayFilter = $('#delayFilter').val();
        var ticketFilter = $('#ticketFilter').val();
        var riskFilter = $('#riskFilter').val();
        var customisedFilter = $('.df-view-tab.is-active').data('customised-filter') || '';

        var rowOwner = $(rowNode).data('owner');
        var rowDelay = $(rowNode).data('delay');
        var rowTicket = $(rowNode).data('ticket');
        var rowRisk = $(rowNode).data('risk');
        var rowCustomised = String($(rowNode).data('customised') || '0');

        if (ownerFilter !== '' && rowOwner !== ownerFilter) {
            return false;
        }

        if (delayFilter !== '' && rowDelay !== delayFilter) {
            return false;
        }

        if (ticketFilter !== '' && rowTicket !== ticketFilter) {
            return false;
        }

        if (riskFilter !== '' && rowRisk !== riskFilter) {
            return false;
        }

        if (customisedFilter !== '' && rowCustomised !== String(customisedFilter)) {
            return false;
        }

        return true;
    });

    $('#customSearch').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#ownerFilter, #delayFilter, #ticketFilter, #riskFilter').on('change', function () {
        table.draw();
    });

    $('.df-view-tab').on('click', function () {
        $('.df-view-tab').removeClass('is-active').attr('aria-selected', 'false');
        $(this).addClass('is-active').attr('aria-selected', 'true');
        table.draw();
    });

    $('#resetFilters').on('click', function () {
        $('#customSearch').val('');
        $('#ownerFilter').val('');
        $('#delayFilter').val('');
        $('#ticketFilter').val('');
        $('#riskFilter').val('');
        $('.df-view-tab').removeClass('is-active').attr('aria-selected', 'false');
        $('.df-view-tab[data-customised-filter=""]').addClass('is-active').attr('aria-selected', 'true');

        table.search('');
        table.columns().search('');
        table.draw();
    });

    $('.wrapper').on('click', '.btn-view-tickets', function() {
        var $modal = $('#ticket-modal');
        var $loader = $('#ticket-modal-loader');
        var $content = $('#ticket-modal-content');

        var dfId = $(this).data('df-id');
        var dfNo = $(this).data('df-no');

        $modal.find('#ticketModalLabel').text('Open Tickets for DF: ' + dfNo);
        $content.empty();
        $loader.show();
        $modal.modal('show');

        $.ajax({
            url: '<?php echo page_url; ?>Task/ajax_get_df_tickets/' + dfId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $loader.hide();

                if (response.success && response.tickets.length > 0) {
                    var html = '';

                    $.each(response.tickets, function(index, ticket) {
                        html += '<div style="border-bottom:1px solid #eee; padding:12px 0;">';
                        html += '  <div style="color:#337ab7; font-weight:bold;">Task: ' + (ticket.task_name || 'N/A') + '</div>';
                        html += '  <div style="font-size:12px; color:#777;"><strong>Ticket #:</strong> ' + ticket.help_ticket_no + '</div>';
                        html += '  <div style="font-size:12px; color:#777;"><strong>By:</strong> ' + ticket.added_by_name + ' | <strong>On:</strong> ' + ticket.added_date + '</div>';
                        html += '  <div style="margin-top:6px; padding-left:10px; border-left:3px solid #f0ad4e;">' + ticket.remarks + '</div>';
                        html += '</div>';
                    });

                    $content.html(html);
                } else {
                    $content.html('<div class="alert alert-info">No open tickets found for this DF.</div>');
                }
            },
            error: function() {
                $loader.hide();
                $content.html('<div class="alert alert-danger">Error loading ticket details.</div>');
            }
        });
    });

    $('.wrapper').on('click', '.btn-manage-penalty', function() {
        var dfId = $(this).data('df-id');
        var dfNo = $(this).data('df-no');
        var penaltyAmount = $(this).data('penalty-amount');

        $('#penalty-df-id').val(dfId);
        $('#penalty-df-no').val(dfNo);
        $('#penalty-amount').val(penaltyAmount);
        $('#penaltyModalLabel').text('Mark Penalty DF: ' + dfNo);
        $('#penalty-modal').modal('show');
    });

});

function saveMachine(dfid) {
    var machineId = document.getElementById("select_machine" + dfid).value;

    if (machineId != "") {
        $.ajax({
            url: '<?php echo page_url;?>Task/save_machine',
            type: 'POST',
            data: {
                machine_id: machineId,
                dfid: dfid
            },
            success: function(response) {},
            error: function() {
                alert('Error saving the machine');
            }
        });
    } else {
        alert("Please select a machine.");
    }
}
</script>

</body>
</html>
