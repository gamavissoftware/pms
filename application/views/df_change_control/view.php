<?php
if (!function_exists('changeDetailStatusClass')) {
    function changeDetailStatusClass($status)
    {
        $status = strtoupper((string)$status);
        if ($status === 'COMPLETED') {
            return 'tone-success';
        }
        if ($status === 'IN_PROGRESS') {
            return 'tone-info';
        }
        if ($status === 'ASSIGNED') {
            return 'tone-sky';
        }
        if ($status === 'PENDING_HEAD_ACTION' || $status === 'OPEN') {
            return 'tone-warning';
        }
        return 'tone-neutral';
    }
}

if (!function_exists('changeDetailPriorityClass')) {
    function changeDetailPriorityClass($priority)
    {
        $priority = strtoupper((string)$priority);
        if ($priority === 'CRITICAL') {
            return 'tone-danger';
        }
        if ($priority === 'HIGH') {
            return 'tone-warning';
        }
        if ($priority === 'MEDIUM') {
            return 'tone-info';
        }
        return 'tone-neutral';
    }
}

if (!function_exists('changeDetailReadable')) {
    function changeDetailReadable($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return '-';
        }

        return ucwords(strtolower(str_replace('_', ' ', $value)));
    }
}

if (!function_exists('changeDetailDate')) {
    function changeDetailDate($value, $with_time = false)
    {
        $value = trim((string)$value);
        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = strtotime($value);
        if (empty($timestamp)) {
            return '-';
        }

        return $with_time ? date('d M Y, h:i A', $timestamp) : date('d M Y', $timestamp);
    }
}

if (!function_exists('changeDetailSafeColor')) {
    function changeDetailSafeColor($color)
    {
        $color = trim((string)$color);
        if ($color !== '' && preg_match('/^#[0-9a-fA-F]{3,6}$/', $color)) {
            return $color;
        }

        return '#17365d';
    }
}

if (!function_exists('changeDetailHistoryTitle')) {
    function changeDetailHistoryTitle($action_type)
    {
        $action_type = strtoupper(trim((string)$action_type));

        switch ($action_type) {
            case 'REQUEST_CREATED':
                return 'Request Created';
            case 'DEPARTMENT_NOTIFIED':
                return 'Department Notified';
            case 'TASK_ASSIGNED':
                return 'Task Assigned';
            case 'TASK_IN_PROGRESS':
                return 'Progress Updated';
            case 'TASK_COMPLETED':
                return 'Task Completed';
            case 'REQUESTER_MESSAGE':
                return 'Requester Update';
            case 'ASSIGNEE_MESSAGE':
                return 'Assignee Update';
            case 'REQUEST_STATUS_UPDATED':
                return 'Request Status Updated';
            default:
                return changeDetailReadable($action_type);
        }
    }
}

if (!function_exists('changeDetailHistoryTone')) {
    function changeDetailHistoryTone($action_type)
    {
        $action_type = strtoupper(trim((string)$action_type));

        switch ($action_type) {
            case 'TASK_COMPLETED':
                return 'tone-success';
            case 'TASK_IN_PROGRESS':
            case 'ASSIGNEE_MESSAGE':
                return 'tone-info';
            case 'TASK_ASSIGNED':
                return 'tone-sky';
            case 'REQUEST_CREATED':
            case 'DEPARTMENT_NOTIFIED':
                return 'tone-warning';
            case 'REQUEST_STATUS_UPDATED':
                return 'tone-neutral';
            default:
                return 'tone-neutral';
        }
    }
}

$attachment_url = '';
if (!empty($change['attachment'])) {
    $attachment_url = site_http_root . 'image_bank/df_change_control/' . rawurlencode($change['attachment']);
}

$request_title = trim((string)$change['title']);
$request_title = $request_title !== '' ? $request_title : 'Untitled change request';
$requester_department = trim((string)$change['requestor_department_name']);
$requester_department = $requester_department !== '' ? $requester_department : 'Not mapped';
$reference_no = trim((string)$change['reference_no']);
$reference_no = $reference_no !== '' ? $reference_no : '-';
$revision_no = trim((string)$change['revision_no']);
$revision_no = $revision_no !== '' ? $revision_no : '-';
$change_summary = trim((string)$change['change_summary']);
$change_summary = $change_summary !== '' ? $change_summary : 'No summary added yet.';
$impact_note = trim((string)$change['impact_note']);

$today = date('Y-m-d');
$total_actions = count($actions);
$completed_actions = 0;
$pending_actions = 0;
$active_actions = 0;
$overdue_actions = 0;
$user_pending_actions = 0;

foreach ($actions as $action_stat) {
    $action_status = strtoupper((string)$action_stat['status']);
    if ($action_status === 'COMPLETED') {
        $completed_actions++;
    } elseif ($action_status === 'PENDING_HEAD_ACTION') {
        $pending_actions++;
    } else {
        $active_actions++;
    }

    $is_overdue = $action_status !== 'COMPLETED'
        && !empty($action_stat['target_date'])
        && $action_stat['target_date'] !== '0000-00-00'
        && $action_stat['target_date'] < $today;

    if ($is_overdue) {
        $overdue_actions++;
    }

    $head_pending_for_me = ($is_admin || ((int)$action_stat['department_head_id'] === (int)$current_user_id)) && $action_status !== 'COMPLETED';
    $execution_pending_for_me = ($is_admin || ((int)$action_stat['assigned_user_id'] === (int)$current_user_id))
        && in_array($action_status, array('ASSIGNED', 'IN_PROGRESS'), true);

    if ($head_pending_for_me || $execution_pending_for_me) {
        $user_pending_actions++;
    }
}

$completion_percent = $total_actions > 0 ? (int)round(($completed_actions / $total_actions) * 100) : 0;
$open_actions = $total_actions - $completed_actions;
$action_history_map = array();
$history_timeline_rows = array();

foreach ($history as $history_row) {
    $department_action_id = isset($history_row['department_action_id']) ? (int)$history_row['department_action_id'] : 0;
    $history_timeline_rows[] = $history_row;

    if ($department_action_id > 0) {
        if (!isset($action_history_map[$department_action_id])) {
            $action_history_map[$department_action_id] = array();
        }

        $action_history_map[$department_action_id][] = $history_row;
    }
}

if (!empty($actions)) {
    $synthetic_history_id = -1;
    foreach ($actions as $action_row) {
        $action_id = isset($action_row['id']) ? (int)$action_row['id'] : 0;
        $action_status = strtoupper((string)$action_row['status']);
        $completed_on = trim((string)$action_row['completed_on']);

        if ($action_id <= 0 || $action_status !== 'COMPLETED' || $completed_on === '' || $completed_on === '0000-00-00 00:00:00') {
            continue;
        }

        $has_completion_history = false;
        if (!empty($action_history_map[$action_id])) {
            foreach ($action_history_map[$action_id] as $action_history_row) {
                if (strtoupper((string)$action_history_row['action_type']) === 'TASK_COMPLETED') {
                    $has_completion_history = true;
                    break;
                }
            }
        }

        if ($has_completion_history) {
            continue;
        }

        $synthetic_row = array(
            'id' => $synthetic_history_id,
            'change_id' => isset($change['id']) ? (int)$change['id'] : 0,
            'department_action_id' => $action_id,
            'action_by' => isset($action_row['assigned_user_id']) ? (int)$action_row['assigned_user_id'] : 0,
            'action_by_name' => trim((string)$action_row['assignee_name']) !== '' ? (string)$action_row['assignee_name'] : 'Assignee',
            'action_role' => 'ASSIGNEE',
            'action_type' => 'TASK_COMPLETED',
            'action_note' => trim((string)$action_row['assignee_remarks']) !== '' ? trim((string)$action_row['assignee_remarks']) : 'Task marked as completed.',
            'created_on' => $completed_on,
            'department' => isset($action_row['department']) ? (string)$action_row['department'] : ''
        );
        $synthetic_history_id--;

        if (!isset($action_history_map[$action_id])) {
            $action_history_map[$action_id] = array();
        }
        $action_history_map[$action_id][] = $synthetic_row;

        $history_timeline_rows[] = $synthetic_row;
    }
}

$history_sorter = function ($left, $right) {
    $left_time = isset($left['created_on']) ? strtotime((string)$left['created_on']) : 0;
    $right_time = isset($right['created_on']) ? strtotime((string)$right['created_on']) : 0;

    if ($left_time === $right_time) {
        $left_id = isset($left['id']) ? (int)$left['id'] : 0;
        $right_id = isset($right['id']) ? (int)$right['id'] : 0;
        return $left_id - $right_id;
    }

    return $left_time - $right_time;
};

foreach ($action_history_map as $department_action_id => $action_history_rows) {
    usort($action_history_rows, $history_sorter);
    $action_history_map[$department_action_id] = $action_history_rows;
}
unset($action_history_rows);

usort($history_timeline_rows, $history_sorter);
$timeline_rows_for_display = array_reverse($history_timeline_rows);

$visible_progress_actions = array();
$completed_departments = array();

foreach ($actions as $action_row) {
    if (strtoupper((string)$action_row['status']) === 'COMPLETED') {
        $completed_departments[] = $action_row;
        continue;
    }

    $visible_progress_actions[] = $action_row;
}

$show_completed_summary = !empty($completed_departments) && count($completed_departments) < count($actions);
$only_completed_view = !empty($actions) && count($completed_departments) === count($actions);

if (empty($visible_progress_actions) && !empty($completed_departments)) {
    $visible_progress_actions = $completed_departments;
}

if (!empty($visible_progress_actions)) {
    usort($visible_progress_actions, function ($left, $right) use ($current_user_id, $is_admin, $today) {
        $left_status = strtoupper((string)$left['status']);
        $right_status = strtoupper((string)$right['status']);

        $left_can_head = $is_admin || ((int)$left['department_head_id'] === (int)$current_user_id);
        $right_can_head = $is_admin || ((int)$right['department_head_id'] === (int)$current_user_id);
        $left_can_assignee = $is_admin || ((int)$left['assigned_user_id'] === (int)$current_user_id);
        $right_can_assignee = $is_admin || ((int)$right['assigned_user_id'] === (int)$current_user_id);

        $left_needs_action = ($left_can_head && $left_status !== 'COMPLETED')
            || ($left_can_assignee && in_array($left_status, array('ASSIGNED', 'IN_PROGRESS'), true));
        $right_needs_action = ($right_can_head && $right_status !== 'COMPLETED')
            || ($right_can_assignee && in_array($right_status, array('ASSIGNED', 'IN_PROGRESS'), true));

        if ($left_needs_action !== $right_needs_action) {
            return $left_needs_action ? -1 : 1;
        }

        $left_overdue = $left_status !== 'COMPLETED'
            && !empty($left['target_date'])
            && $left['target_date'] !== '0000-00-00'
            && $left['target_date'] < $today;
        $right_overdue = $right_status !== 'COMPLETED'
            && !empty($right['target_date'])
            && $right['target_date'] !== '0000-00-00'
            && $right['target_date'] < $today;

        if ($left_overdue !== $right_overdue) {
            return $left_overdue ? -1 : 1;
        }

        return strcasecmp((string)$left['department'], (string)$right['department']);
    });
}

$has_custom_summary = trim((string)$change['change_summary']) !== '';
$change_summary_preview = $has_custom_summary ? trim((string)$change['change_summary']) : '';
if ($change_summary_preview !== '' && strlen($change_summary_preview) > 180) {
    $change_summary_preview = substr($change_summary_preview, 0, 177) . '...';
}

$department_filter_options = array();
$department_action_status_map = array();
$status_filter_options = array();
$display_action_cards = array();

foreach ($actions as $action_row) {
    $department_name = trim((string)$action_row['department']);
    if ($department_name !== '') {
        $department_filter_options[$department_name] = $department_name;
    }

    $action_id = isset($action_row['id']) ? (int)$action_row['id'] : 0;
    $action_status_key = strtoupper(trim((string)$action_row['status']));
    if ($action_id > 0) {
        $department_action_status_map[$action_id] = $action_status_key;
    }

    if ($action_status_key !== '') {
        $status_filter_options[$action_status_key] = changeDetailReadable($action_status_key);
    }
}

if (!empty($department_filter_options)) {
    natcasesort($department_filter_options);
}

$preferred_status_order = array('PENDING_HEAD_ACTION', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED', 'OPEN');
$ordered_status_filter_options = array();
foreach ($preferred_status_order as $preferred_status_key) {
    if (isset($status_filter_options[$preferred_status_key])) {
        $ordered_status_filter_options[$preferred_status_key] = $status_filter_options[$preferred_status_key];
        unset($status_filter_options[$preferred_status_key]);
    }
}
if (!empty($status_filter_options)) {
    foreach ($status_filter_options as $status_key => $status_label) {
        $ordered_status_filter_options[$status_key] = $status_label;
    }
}
$status_filter_options = $ordered_status_filter_options;

$display_action_cards = $visible_progress_actions;
if (!empty($completed_departments) && !$only_completed_view) {
    $display_action_cards = array_merge($visible_progress_actions, $completed_departments);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> <?php echo htmlspecialchars((string)$change['change_no']); ?></title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        :root {
            --cc-brand: #17365d;
            --cc-brand-dark: #0f172a;
            --cc-bg: #eef4f9;
            --cc-surface: #ffffff;
            --cc-surface-soft: #f7fbff;
            --cc-line: #d9e5f0;
            --cc-text: #1f2937;
            --cc-muted: #64748b;
            --cc-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
        }

        body {
            background: #f3f6fb;
            color: var(--cc-text);
        }

        .detail-page {
            padding-bottom: 28px;
        }

        .page-title-box {
            margin-bottom: 14px;
        }

        .page-title-box .page-title {
            color: var(--cc-brand);
            font-weight: 700;
        }

        .page-header-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 12px 18px;
            margin-bottom: 16px;
            min-height: 100px;
            border-radius: 18px;
            background: #ffffff;
            color: var(--cc-text);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            position: relative;
            border: 1px solid var(--cc-line);
        }

        .page-header-card:before,
        .page-header-card:after {
            display: none;
        }

        .page-header-copy,
        .page-header-side {
            position: relative;
            z-index: 1;
        }

        .page-header-copy {
            flex: 1 1 auto;
            min-width: 0;
            max-width: none;
        }

        .page-kicker {
            display: inline-block;
            margin-bottom: 4px;
            font-size: 10px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--cc-brand);
        }

        .header-code {
            margin: 0;
            font-size: 17px;
            line-height: 1;
            font-weight: 700;
            color: var(--cc-brand);
        }

        .page-header-copy h3 {
            margin: 3px 0 0;
            font-size: 16px;
            line-height: 1.35;
            font-weight: 700;
            color: var(--cc-brand-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-mainline {
            min-width: 0;
        }

        .page-pills,
        .page-actions,
        .panel-inline-pills,
        .action-badges,
        .history-tags,
        .completed-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .page-header-subline {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            line-height: 1.35;
            color: var(--cc-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .page-header-subline .divider {
            opacity: 0.7;
            margin: 0 6px;
        }

        .page-header-side {
            display: flex;
            align-items: center;
            width: auto;
            flex: 0 0 auto;
            max-width: 100%;
        }

        .page-pills {
            justify-content: flex-start;
            margin-top: 0;
        }

        .page-actions {
            justify-content: flex-end;
            margin-top: 0;
            gap: 8px;
        }

        .page-actions .btn {
            border-radius: 999px;
            padding: 7px 12px;
            border-width: 1px;
            box-shadow: none;
            min-width: 0;
            font-weight: 700;
            font-size: 12px;
        }

        .page-actions .btn-default {
            background: #ffffff;
            color: var(--cc-brand);
            border-color: #d5e2ef;
        }

        .page-actions .btn-default:hover,
        .page-actions .btn-default:focus {
            background: #f8fbff;
            color: var(--cc-brand);
        }

        .page-actions .btn-warning {
            background: #f59e0b;
            color: #ffffff;
            border-color: #f59e0b;
        }

        .page-actions .btn-primary {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .filter-panel {
            margin-bottom: 20px;
        }

        .filter-toolbar {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr)) auto;
            gap: 14px;
            align-items: end;
        }

        .filter-field label {
            display: block;
            margin-bottom: 8px;
            color: var(--cc-brand);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .filter-field .form-control {
            border-color: #cfddeb;
            border-radius: 12px;
            box-shadow: none;
            min-height: 44px;
        }

        .filter-actions {
            display: flex;
            align-items: end;
            gap: 10px;
            justify-content: flex-end;
        }

        .filter-actions .btn {
            min-height: 44px;
            border-radius: 999px;
            min-width: 140px;
        }

        .filter-help {
            margin-top: 10px;
            color: var(--cc-muted);
            line-height: 1.5;
            font-size: 12px;
        }

        .workspace-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            align-items: start;
        }

        .workspace-panel {
            background: var(--cc-surface);
            border: 1px solid var(--cc-line);
            border-radius: 24px;
            box-shadow: var(--cc-shadow);
            overflow: hidden;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 16px 20px;
            background: var(--cc-surface-soft);
            border-bottom: 1px solid var(--cc-line);
        }

        .panel-head h4 {
            margin: 0;
            color: var(--cc-brand);
            font-size: 18px;
            font-weight: 700;
        }

        .panel-head p {
            margin: 4px 0 0;
            color: var(--cc-muted);
            line-height: 1.5;
            font-size: 13px;
        }

        .panel-inline-pills {
            justify-content: flex-end;
        }

        .counter-pill-value {
            font-size: 15px;
            font-weight: 700;
        }

        .detail-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.2px;
            border: 1px solid transparent;
        }

        .tone-success {
            background: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .tone-info {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1d4ed8;
        }

        .tone-sky {
            background: #e0f2fe;
            border-color: #7dd3fc;
            color: #0369a1;
        }

        .tone-warning {
            background: #fef3c7;
            border-color: #fcd34d;
            color: #92400e;
        }

        .tone-danger {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #b91c1c;
        }

        .tone-neutral {
            background: #e2e8f0;
            border-color: #cbd5e1;
            color: #334155;
        }

        .panel-body {
            padding: 20px;
        }

        .timeline-panel {
            position: static;
        }

        .timeline-panel .panel-body {
            max-height: none;
            overflow: visible;
            padding-right: 20px;
        }

        .timeline-panel .panel-body::-webkit-scrollbar {
            width: 8px;
        }

        .timeline-panel .panel-body::-webkit-scrollbar-thumb {
            background: #c7d7e6;
            border-radius: 999px;
        }

        .empty-state {
            padding: 22px;
            border-radius: 18px;
            text-align: center;
            border: 1px dashed #cbd8e6;
            background: #f8fbff;
            color: var(--cc-muted);
            line-height: 1.8;
        }

        .timeline-list {
            position: relative;
            padding-left: 28px;
        }

        .timeline-list:before {
            content: '';
            position: absolute;
            left: 8px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: linear-gradient(180deg, #93c5fd 0%, #dbeafe 100%);
        }

        .timeline-item {
            position: relative;
            padding-left: 18px;
            margin-bottom: 18px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -2px;
            top: 12px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #17365d;
            border: 3px solid #ffffff;
            box-shadow: 0 0 0 3px #bfdbfe;
        }

        .history-card {
            padding: 16px 18px;
            border-radius: 16px;
            border: 1px solid #dbe7f3;
            background: #ffffff;
        }

        .history-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .history-title {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--cc-brand);
        }

        .history-time {
            margin-top: 6px;
            color: var(--cc-muted);
            line-height: 1.6;
            font-size: 13px;
        }

        .history-note {
            margin-top: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #f8fbff;
            border: 1px solid #e1ebf5;
            color: var(--cc-text);
            line-height: 1.7;
            word-break: break-word;
        }

        .history-note.empty-note {
            color: var(--cc-muted);
        }

        .progress-stack {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .workflow-table-wrap {
            border: 1px solid #dce8f3;
            border-radius: 18px;
            overflow: hidden;
            background: #ffffff;
        }

        .workflow-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .workflow-table thead th {
            padding: 12px 14px;
            background: #f8fbff;
            border-bottom: 1px solid #dce8f3;
            color: var(--cc-brand);
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
            white-space: nowrap;
        }

        .workflow-table tbody tr.workflow-summary-row td {
            padding: 14px;
            border-top: 1px solid #edf2f7;
            vertical-align: top;
            background: #ffffff;
        }

        .workflow-table tbody.workflow-row-group:first-child tr.workflow-summary-row td {
            border-top: none;
        }

        .workflow-table tbody.workflow-row-group.is-overdue-row tr.workflow-summary-row td {
            background: #fffafa;
        }

        .workflow-department-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--cc-brand);
            line-height: 1.35;
        }

        .workflow-secondary {
            margin-top: 4px;
            color: var(--cc-muted);
            font-size: 12px;
            line-height: 1.45;
        }

        .workflow-primary-value {
            font-size: 13px;
            font-weight: 700;
            color: var(--cc-brand-dark);
            line-height: 1.45;
        }

        .workflow-note-snippet {
            max-width: 320px;
            color: var(--cc-muted);
            font-size: 12px;
            line-height: 1.5;
            word-break: break-word;
        }

        .workflow-action-cell {
            white-space: nowrap;
            text-align: right;
        }

        .workflow-action-cell .btn {
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 700;
        }

        .workflow-status-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .workflow-status-stack .detail-pill {
            padding: 6px 10px;
            font-size: 11px;
        }

        .workflow-detail-row td {
            padding: 0 !important;
            border-top: none !important;
            background: #f8fbff !important;
        }

        .workflow-detail-shell {
            padding: 16px;
            border-top: 1px solid #e5edf5;
        }

        .workflow-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 14px;
        }

        .workflow-detail-shell .note-card {
            background: #ffffff;
        }

        .workflow-detail-shell .action-panel {
            margin-top: 12px;
        }

        .workflow-detail-shell .action-panel:first-of-type {
            margin-top: 0;
        }

        .progress-card {
            padding: 18px;
            border: 1px solid #d9e5f0;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .progress-card.needs-action {
            border-color: #bfdbfe;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08), 0 16px 30px rgba(15, 23, 42, 0.07);
        }

        .progress-card.is-overdue {
            border-color: #fecaca;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08), 0 16px 30px rgba(15, 23, 42, 0.07);
        }

        .progress-card-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }

        .progress-card-head h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--cc-brand);
        }

        .progress-card-head p {
            margin: 6px 0 0;
            line-height: 1.6;
            color: var(--cc-muted);
            font-size: 13px;
        }

        .progress-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .meta-chip,
        .note-card {
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #dbe7f3;
            background: #ffffff;
        }

        .meta-chip-label,
        .note-card .label-title {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--cc-brand);
            font-weight: 700;
        }

        .meta-chip strong {
            display: block;
            margin-top: 8px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.6;
            color: var(--cc-text);
            word-break: break-word;
        }

        .meta-chip small {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.6;
            color: var(--cc-muted);
        }

        .progress-note-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .inline-action-rail {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .inline-action-rail .btn {
            border-radius: 999px;
            min-width: 0;
            padding-left: 16px;
            padding-right: 16px;
        }

        .note-card .label-value {
            margin-top: 10px;
            color: var(--cc-text);
            line-height: 1.75;
            word-break: break-word;
        }

        .action-panel {
            margin-top: 16px;
            padding: 16px;
            background: #ffffff;
            border: 1px solid #dbe7f3;
            border-radius: 16px;
        }

        .action-panel + .action-panel {
            margin-top: 12px;
        }

        .action-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 14px;
        }

        .action-panel-title {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--cc-brand);
        }

        .action-panel-hint {
            margin-top: 4px;
            color: var(--cc-muted);
            line-height: 1.5;
            font-size: 12px;
        }

        .action-panel .form-group label {
            color: var(--cc-brand);
            font-weight: 700;
        }

        .action-panel .form-control {
            border-color: #cfddeb;
            border-radius: 12px;
            box-shadow: none;
            min-height: 42px;
        }

        .action-panel textarea.form-control {
            min-height: 96px;
            resize: vertical;
        }

        .action-panel .btn {
            min-width: 170px;
            border-radius: 999px;
        }

        .completed-summary {
            margin-top: 18px;
            padding: 18px;
            border-radius: 18px;
            border: 1px dashed #c7d7e7;
            background: #f8fbff;
        }

        .completed-summary h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: var(--cc-brand);
        }

        .completed-summary p {
            margin: 8px 0 0;
            color: var(--cc-muted);
            line-height: 1.6;
        }

        .is-hidden {
            display: none !important;
        }

        @media (max-width: 1199px) {
            .workspace-grid {
                grid-template-columns: 1fr;
            }

            .timeline-panel {
                position: static;
            }

            .timeline-panel .panel-body {
                max-height: none;
                overflow: visible;
                padding-right: 22px;
            }
        }

        @media (max-width: 991px) {
            .page-header-card,
            .panel-head,
            .history-head,
            .progress-card-head,
            .action-panel-head,
            .page-actions {
                flex-direction: column;
            }

            .page-actions {
                align-items: stretch;
            }

            .panel-inline-pills,
            .action-badges {
                justify-content: flex-start;
            }

            .progress-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .progress-note-grid,
            .workflow-detail-grid,
            .filter-toolbar {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                justify-content: flex-start;
            }

            .workflow-table {
                min-width: 860px;
            }

            .workflow-table-wrap {
                overflow-x: auto;
            }
        }

        @media (max-width: 767px) {
            .detail-page {
                padding-bottom: 18px;
            }

            .page-header-card {
                padding: 14px 16px;
                border-radius: 16px;
                min-height: auto;
            }

            .page-header-copy h3 {
                font-size: 14px;
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
            }

            .page-header-side {
                width: 100%;
                justify-content: flex-start;
            }

            .page-actions .btn,
            .action-panel .btn {
                width: 100%;
            }

            .filter-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-actions .btn {
                width: 100%;
            }

            .panel-head,
            .panel-body,
            .history-card {
                padding-left: 16px;
                padding-right: 16px;
            }

            .progress-card,
            .action-panel {
                padding: 16px;
            }

            .progress-meta-grid {
                grid-template-columns: 1fr;
            }

            .timeline-list {
                padding-left: 22px;
            }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid detail-page">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">DF Change Control Details</h4>
                    </div>
                    <?php echo $this->session->flashdata('message'); ?>
                    <?php $this->load->view('df_change_control/_module_nav', array('module_nav' => $module_nav)); ?>
                </div>
            </div>

            <div class="page-header-card">
                <div class="page-header-copy">
                    <div class="page-kicker">ECM / IOM Request</div>
                    <div class="header-code"><?php echo htmlspecialchars((string)$change['change_no']); ?></div>
                    <h3><?php echo htmlspecialchars($request_title); ?></h3>
                    <div class="page-header-subline">
                        <span><?php echo changeDetailReadable($change['request_type']); ?></span>
                        <span class="divider">•</span>
                        <span>DF <?php echo htmlspecialchars((string)$change['df_no']); ?></span>
                        <span class="divider">•</span>
                        <span><?php echo changeDetailReadable($change['change_category']); ?></span>
                        <span class="divider">•</span>
                        <span><?php echo htmlspecialchars($requester_department); ?></span>
                        <span class="divider">•</span>
                        <span><?php echo htmlspecialchars((string)$change['creator_name']); ?></span>
                        <span class="divider">•</span>
                        <span><?php echo changeDetailDate($change['created_on'], true); ?></span>
                    </div>
                </div>

                <div class="page-header-side">
                    <div class="page-actions">
                        <a href="<?php echo page_url; ?>Df_change_control" class="btn btn-default btn-sm">Dashboard</a>
                        <a href="<?php echo page_url; ?>Task/finalgantchartWithDetails/<?php echo (int)$change['df_id']; ?>" target="_blank" class="btn btn-warning btn-sm">Gantt</a>
                        <?php if ($attachment_url !== '') { ?>
                            <a href="<?php echo $attachment_url; ?>" target="_blank" class="btn btn-primary btn-sm">Attachment</a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="workspace-panel filter-panel">
                    <div class="panel-head">
                        <div>
                            <h4>Focus View</h4>
                            <p>Filter by department and status.</p>
                        </div>
                        <div class="panel-inline-pills">
                            <span class="detail-pill tone-neutral"><span class="counter-pill-value" id="visibleProgressCount"><?php echo count($visible_progress_actions); ?></span> Visible Departments</span>
                            <span class="detail-pill tone-info"><span class="counter-pill-value" id="visibleTimelineCount"><?php echo count($timeline_rows_for_display); ?></span> Visible Interactions</span>
                        </div>
                    </div>
                <div class="panel-body">
                    <div class="filter-toolbar">
                        <div class="filter-field">
                            <label for="changeDepartmentFilter">Department</label>
                            <select id="changeDepartmentFilter" class="form-control">
                                <option value="all">All Departments</option>
                                <?php foreach ($department_filter_options as $department_name) { ?>
                                    <option value="<?php echo htmlspecialchars(strtolower($department_name), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($department_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="filter-field">
                            <label for="changeStatusFilter">Status</label>
                            <select id="changeStatusFilter" class="form-control">
                                <option value="all">All Statuses</option>
                                <?php foreach ($status_filter_options as $status_key => $status_label) { ?>
                                    <option value="<?php echo htmlspecialchars(strtolower($status_key), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($status_label); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="filter-actions">
                            <button type="button" class="btn btn-default" id="clearChangeFilters">Clear Filters</button>
                        </div>
                    </div>
                    <div class="filter-help">Completed items stay hidden until you filter them.</div>
                </div>
            </div>

            <div class="workspace-grid">
                <div class="workspace-panel progress-panel">
                    <div class="panel-head">
                        <div>
                            <h4>Department Workboard</h4>
                            <p><?php echo $only_completed_view ? 'All department work is already closed.' : 'Current department actions.'; ?></p>
                        </div>
                        <div class="panel-inline-pills">
                            <span class="detail-pill tone-neutral"><?php echo $open_actions; ?> Open</span>
                            <span class="detail-pill tone-success"><?php echo $completed_actions; ?> Completed</span>
                            <?php if ($overdue_actions > 0) { ?>
                                <span class="detail-pill tone-danger"><?php echo $overdue_actions; ?> Overdue</span>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (empty($actions)) { ?>
                            <div class="empty-state">No department action has been added for this change request yet.</div>
                        <?php } else { ?>
                            <div class="workflow-table-wrap">
                                <table class="workflow-table">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Assigned To</th>
                                            <th>Status</th>
                                            <th>Deadline</th>
                                            <th>Latest Progress</th>
                                            <th>Latest Note</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($display_action_cards as $action) { ?>
                                        <?php
                                        $can_head_act = $is_admin || ((int)$action['department_head_id'] === (int)$current_user_id);
                                        $can_assignee_act = $is_admin || ((int)$action['assigned_user_id'] === (int)$current_user_id);
                                        $action_status = strtoupper((string)$action['status']);
                                        $is_active = $action_status !== 'COMPLETED';
                                        $is_completed_card = $action_status === 'COMPLETED';
                                        $is_overdue = $is_active
                                            && !empty($action['target_date'])
                                            && $action['target_date'] !== '0000-00-00'
                                            && $action['target_date'] < $today;
                                        $needs_my_action = ($can_head_act && $action_status !== 'COMPLETED')
                                            || ($can_assignee_act && in_array($action_status, array('ASSIGNED', 'IN_PROGRESS'), true));
                                        $members = !empty($member_map[$action['department_id']]) ? $member_map[$action['department_id']] : array();
                                        $action_history_rows = !empty($action_history_map[$action['id']]) ? $action_history_map[$action['id']] : array();
                                        $assigned_by_name = $action['head_name'] !== '' ? (string)$action['head_name'] : 'Pending HOD action';
                                        $assigned_by_time = '';
                                        $latest_progress_title = 'Pending Head Action';
                                        $latest_progress_note = 'Waiting for the department head to assign this task.';
                                        $latest_progress_time = '';
                                        $detail_panel_id = 'workflow-detail-' . (int)$action['id'];

                                        if (!empty($action_history_rows)) {
                                            foreach ($action_history_rows as $action_history_row) {
                                                if (strtoupper((string)$action_history_row['action_type']) === 'TASK_ASSIGNED') {
                                                    $assigned_by_name = trim((string)$action_history_row['action_by_name']) !== '' ? (string)$action_history_row['action_by_name'] : $assigned_by_name;
                                                    $assigned_by_time = (string)$action_history_row['created_on'];
                                                }
                                            }

                                            $latest_action_row = $action_history_rows[count($action_history_rows) - 1];
                                            $latest_progress_title = changeDetailHistoryTitle($latest_action_row['action_type']);
                                            $latest_progress_note = trim((string)$latest_action_row['action_note']);
                                            $latest_progress_time = (string)$latest_action_row['created_on'];
                                        }

                                        if ($latest_progress_note === '') {
                                            if ($action_status === 'COMPLETED') {
                                                $latest_progress_note = 'This department task has been completed.';
                                            } elseif ($action_status === 'IN_PROGRESS') {
                                                $latest_progress_note = 'Work is in progress and waiting for the next update.';
                                            } elseif ($action_status === 'ASSIGNED') {
                                                $latest_progress_note = 'Task is assigned and waiting for the assignee to update progress.';
                                            }
                                        }

                                        $latest_progress_note_plain = trim(preg_replace('/\s+/', ' ', strip_tags($latest_progress_note)));
                                        $latest_progress_note_snippet = strlen($latest_progress_note_plain) > 120
                                            ? substr($latest_progress_note_plain, 0, 117) . '...'
                                            : $latest_progress_note_plain;

                                        $row_action_label = 'Open';
                                        if ($needs_my_action && $can_head_act && (int)$action['assigned_user_id'] <= 0) {
                                            $row_action_label = 'Assign';
                                        } elseif ($needs_my_action) {
                                            $row_action_label = 'Update';
                                        }
                                        ?>
                                        <tbody
                                            class="workflow-row-group <?php echo $is_overdue ? 'is-overdue-row' : ''; ?> <?php echo $is_completed_card ? 'is-completed-row' : ''; ?>"
                                            id="action-<?php echo (int)$action['id']; ?>"
                                            data-filter-item="progress"
                                            data-department="<?php echo htmlspecialchars(strtolower(trim((string)$action['department'])), ENT_QUOTES, 'UTF-8'); ?>"
                                            data-status="<?php echo htmlspecialchars(strtolower($action_status), ENT_QUOTES, 'UTF-8'); ?>"
                                            data-default-hide-completed="<?php echo (!empty($completed_departments) && !$only_completed_view && $is_completed_card) ? '1' : '0'; ?>"
                                        >
                                            <tr class="workflow-summary-row">
                                                <td>
                                                    <div class="workflow-department-name"><?php echo htmlspecialchars((string)$action['department']); ?></div>
                                                    <div class="workflow-secondary"><?php echo $action['assignee_name'] !== '' ? 'Owner: ' . htmlspecialchars((string)$action['assignee_name']) : 'Owner not assigned yet'; ?></div>
                                                </td>
                                                <td>
                                                    <div class="workflow-primary-value"><?php echo $action['assignee_name'] !== '' ? htmlspecialchars((string)$action['assignee_name']) : 'Not assigned yet'; ?></div>
                                                    <div class="workflow-secondary"><?php echo $assigned_by_time !== '' ? 'Assigned by ' . htmlspecialchars($assigned_by_name) . ' on ' . changeDetailDate($assigned_by_time, true) : 'Waiting for department head assignment'; ?></div>
                                                </td>
                                                <td>
                                                    <div class="workflow-status-stack">
                                                        <span class="detail-pill <?php echo changeDetailStatusClass($action['status']); ?>">
                                                            <?php echo changeDetailReadable($action['status']); ?>
                                                        </span>
                                                        <?php if ($needs_my_action) { ?>
                                                            <span class="detail-pill tone-info">Needs Your Action</span>
                                                        <?php } ?>
                                                        <?php if ($is_overdue) { ?>
                                                            <span class="detail-pill tone-danger">Overdue</span>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="workflow-primary-value"><?php echo changeDetailDate($action['target_date']); ?></div>
                                                    <div class="workflow-secondary">
                                                        TAT: <?php echo !empty($action['planned_days']) ? (int)$action['planned_days'] . ' day(s)' : 'Not set'; ?>
                                                        <?php if (trim((string)$action['completed_on']) !== '' && (string)$action['completed_on'] !== '0000-00-00 00:00:00') { ?>
                                                            <br>Completed <?php echo changeDetailDate($action['completed_on'], true); ?>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="workflow-primary-value"><?php echo htmlspecialchars($latest_progress_title); ?></div>
                                                    <div class="workflow-secondary"><?php echo $latest_progress_time !== '' ? 'Updated ' . changeDetailDate($latest_progress_time, true) : 'No execution update recorded yet'; ?></div>
                                                </td>
                                                <td>
                                                    <div class="workflow-note-snippet"><?php echo htmlspecialchars($latest_progress_note_snippet); ?></div>
                                                </td>
                                                <td class="workflow-action-cell">
                                                    <button type="button" class="btn <?php echo $needs_my_action ? 'btn-primary' : 'btn-default'; ?> btn-sm" data-toggle="collapse" data-target="#<?php echo $detail_panel_id; ?>" aria-expanded="false">
                                                        <?php echo $row_action_label; ?>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="workflow-detail-row">
                                                <td colspan="7">
                                                    <div class="collapse" id="<?php echo $detail_panel_id; ?>">
                                                        <div class="workflow-detail-shell">
                                                            <div class="workflow-detail-grid">
                                                                <div class="note-card">
                                                                    <div class="label-title">HOD Note</div>
                                                                    <div class="label-value">
                                                                        <?php echo trim((string)$action['head_remarks']) !== '' ? nl2br(htmlspecialchars((string)$action['head_remarks'])) : 'No HOD instruction added yet.'; ?>
                                                                    </div>
                                                                </div>
                                                                <div class="note-card">
                                                                    <div class="label-title">Latest Note</div>
                                                                    <div class="label-value">
                                                                        <?php echo nl2br(htmlspecialchars($latest_progress_note)); ?>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <?php if ($can_head_act && $action_status !== 'COMPLETED') { ?>
                                                                <div class="action-panel">
                                                                    <?php if (empty($members)) { ?>
                                                                        <div class="alert alert-warning" style="margin-bottom:14px;">No active team member is mapped for this department right now. Please update the department users before assigning execution.</div>
                                                                    <?php } ?>
                                                                    <form method="post" action="<?php echo page_url; ?>Df_change_control/take_department_action/<?php echo (int)$action['id']; ?>">
                                                                        <div class="action-panel-head">
                                                                            <div class="action-panel-copy">
                                                                                <div class="action-panel-title">Assign Task</div>
                                                                                <div class="action-panel-hint">Choose owner and due days.</div>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" <?php echo empty($members) ? 'disabled' : ''; ?>>Save Assignment</button>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label>Completion Days</label>
                                                                                    <input type="number" min="1" class="form-control" name="planned_days" value="<?php echo !empty($action['planned_days']) ? (int)$action['planned_days'] : ''; ?>" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="form-group">
                                                                                    <label>Assign Team Member</label>
                                                                                    <select class="form-control" name="assigned_user_id" <?php echo empty($members) ? 'disabled' : 'required'; ?>>
                                                                                        <option value=""><?php echo empty($members) ? 'No active member available' : 'Choose team member'; ?></option>
                                                                                        <?php foreach ($members as $member) { ?>
                                                                                            <option value="<?php echo (int)$member['user_id']; ?>" <?php echo ((int)$action['assigned_user_id'] === (int)$member['user_id']) ? 'selected' : ''; ?>>
                                                                                                <?php echo htmlspecialchars(trim($member['title'] . ' ' . $member['first_name'] . ' ' . $member['last_name'])); ?>
                                                                                            </option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <div class="form-group">
                                                                                    <label>HOD Remarks</label>
                                                                                    <textarea class="form-control" name="head_remarks" rows="3" placeholder="Define what exactly this department needs to complete."><?php echo htmlspecialchars((string)$action['head_remarks']); ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            <?php } ?>

                                                            <?php if ($can_assignee_act && in_array($action_status, array('ASSIGNED', 'IN_PROGRESS'), true)) { ?>
                                                                <div class="action-panel">
                                                                    <form method="post" action="<?php echo page_url; ?>Df_change_control/update_department_execution/<?php echo (int)$action['id']; ?>">
                                                                        <div class="action-panel-head">
                                                                            <div class="action-panel-copy">
                                                                                <div class="action-panel-title">Update Progress</div>
                                                                                <div class="action-panel-hint">Post the latest execution update.</div>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-success">Update Execution</button>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <label>Execution Status</label>
                                                                                    <select class="form-control" name="execution_status" required>
                                                                                        <option value="IN_PROGRESS" <?php echo $action['status'] === 'IN_PROGRESS' ? 'selected' : ''; ?>>In Progress</option>
                                                                                        <option value="COMPLETED">Completed</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-9">
                                                                                <div class="form-group">
                                                                                    <label>Execution Remarks</label>
                                                                                    <textarea class="form-control" name="assignee_remarks" rows="3" required placeholder="Update the work progress, challenge, completion note, or delivery detail."><?php echo htmlspecialchars((string)$action['assignee_remarks']); ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            </div>

                            <div class="empty-state is-hidden" id="progressFilterEmpty">No department row matches the current filter.</div>
                        <?php } ?>
                    </div>
                </div>

                <div class="workspace-panel timeline-panel">
                    <div class="panel-head">
                        <div>
                            <h4>Interaction Panel</h4>
                            <p>Recent updates and communication.</p>
                        </div>
                        <div class="panel-inline-pills">
                            <span class="detail-pill tone-info"><span class="counter-pill-value" id="timelineEventCount"><?php echo count($timeline_rows_for_display); ?></span> Events</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (empty($timeline_rows_for_display)) { ?>
                            <div class="empty-state">No history has been recorded for this request yet.</div>
                        <?php } else { ?>
                            <div class="timeline-list">
                                <?php foreach ($timeline_rows_for_display as $row) { ?>
                                    <?php
                                    $timeline_department = trim((string)$row['department']);
                                    $timeline_status = 'open';
                                    $timeline_action_id = isset($row['department_action_id']) ? (int)$row['department_action_id'] : 0;
                                    if ($timeline_action_id > 0 && isset($department_action_status_map[$timeline_action_id])) {
                                        $timeline_status = strtolower((string)$department_action_status_map[$timeline_action_id]);
                                    } elseif (!empty($change['status'])) {
                                        $timeline_status = strtolower((string)$change['status']);
                                    }
                                    ?>
                                    <div
                                        class="timeline-item"
                                        data-filter-item="timeline"
                                        data-department="<?php echo htmlspecialchars(strtolower($timeline_department), ENT_QUOTES, 'UTF-8'); ?>"
                                        data-status="<?php echo htmlspecialchars($timeline_status, ENT_QUOTES, 'UTF-8'); ?>"
                                    >
                                        <span class="timeline-dot"></span>
                                        <div class="history-card">
                                            <div class="history-head">
                                                <div>
                                                    <div class="history-title"><?php echo htmlspecialchars((string)$row['action_by_name']); ?></div>
                                                    <div class="history-time"><?php echo changeDetailDate($row['created_on'], true); ?></div>
                                                </div>
                                                <div class="history-tags">
                                                    <span class="detail-pill <?php echo changeDetailHistoryTone($row['action_type']); ?>"><?php echo changeDetailHistoryTitle($row['action_type']); ?></span>
                                                    <span class="detail-pill tone-neutral"><?php echo changeDetailReadable($row['action_role']); ?></span>
                                                    <?php if (trim((string)$row['department']) !== '') { ?>
                                                        <span class="detail-pill tone-sky"><?php echo htmlspecialchars((string)$row['department']); ?></span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="history-note <?php echo trim((string)$row['action_note']) === '' ? 'empty-note' : ''; ?>">
                                                <?php echo trim((string)$row['action_note']) !== '' ? nl2br(htmlspecialchars((string)$row['action_note'])) : 'No additional note was added for this event.'; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="empty-state is-hidden" id="timelineFilterEmpty">No interaction matches the selected department or status.</div>
                        <?php } ?>
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
    <script>
        (function ($) {
            function normalizeValue(value) {
                return $.trim((value || '').toString()).toLowerCase();
            }

            function updateChangeControlFilters() {
                var selectedDepartment = normalizeValue($('#changeDepartmentFilter').val());
                var selectedStatus = normalizeValue($('#changeStatusFilter').val());
                var shouldTuckCompleted = selectedDepartment === 'all' && selectedStatus === 'all';
                var visibleProgressCount = 0;
                var visibleTimelineCount = 0;

                $('[data-filter-item="progress"]').each(function () {
                    var $card = $(this);
                    var cardDepartment = normalizeValue($card.data('department'));
                    var cardStatus = normalizeValue($card.data('status'));
                    var defaultHideCompleted = $card.data('default-hide-completed') === 1 || $card.data('default-hide-completed') === '1';
                    var matchesDepartment = selectedDepartment === 'all' || cardDepartment === selectedDepartment;
                    var matchesStatus = selectedStatus === 'all' || cardStatus === selectedStatus;
                    var isVisible = matchesDepartment && matchesStatus;

                    if (isVisible && shouldTuckCompleted && defaultHideCompleted) {
                        isVisible = false;
                    }

                    $card.toggleClass('is-hidden', !isVisible);
                    if (isVisible) {
                        visibleProgressCount++;
                    }
                });

                $('[data-filter-item="timeline"]').each(function () {
                    var $row = $(this);
                    var rowDepartment = normalizeValue($row.data('department'));
                    var rowStatus = normalizeValue($row.data('status'));
                    var matchesDepartment = selectedDepartment === 'all' || rowDepartment === selectedDepartment;
                    var matchesStatus = selectedStatus === 'all' || rowStatus === selectedStatus;
                    var isVisible = matchesDepartment && matchesStatus;

                    $row.toggleClass('is-hidden', !isVisible);
                    if (isVisible) {
                        visibleTimelineCount++;
                    }
                });

                $('#visibleProgressCount').text(visibleProgressCount);
                $('#visibleTimelineCount').text(visibleTimelineCount);
                $('#timelineEventCount').text(visibleTimelineCount);

                $('#progressFilterEmpty').toggleClass('is-hidden', visibleProgressCount !== 0);
                $('#timelineFilterEmpty').toggleClass('is-hidden', visibleTimelineCount !== 0);

                if ($('#completedSummary').length) {
                    $('#completedSummary').toggleClass('is-hidden', !shouldTuckCompleted);
                }
            }

            $('#changeDepartmentFilter, #changeStatusFilter').on('change', updateChangeControlFilters);

            $('#clearChangeFilters').on('click', function () {
                $('#changeDepartmentFilter').val('all');
                $('#changeStatusFilter').val('all');
                updateChangeControlFilters();
            });

            updateChangeControlFilters();
        })(jQuery);
    </script>
    <?php $this->load->view('df_change_control/_notification_poller'); ?>
</body>
</html>
