<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$theme_color = '#4872b8';
$logo_query = $this->db->select('colorcode')->from('company_information')->get()->row();
if ($logo_query && !empty($logo_query->colorcode)) {
    $theme_color = $logo_query->colorcode;
}

$fy_start = (int) substr($financial_year, 0, 4);
$months = array();
for ($offset = 0; $offset < 12; $offset++) {
    $timestamp = mktime(0, 0, 0, 4 + $offset, 1, $fy_start);
    $months[date('Y-m', $timestamp)] = date('M Y', $timestamp);
}

$summary_total = $summary ? (int) $summary->total : 0;
$summary_dispatched = $summary ? (int) $summary->dispatched : 0;
$summary_at_risk = $summary ? (int) $summary->at_risk : 0;
$summary_overdue = $summary ? (int) $summary->overdue : 0;

$dispatch_value = function ($value) {
    return $value !== null && trim((string) $value) !== ''
        ? nl2br(htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'))
        : '-';
};
$dispatch_attr = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$dispatch_date = function ($value) {
    if (!$value || $value === '0000-00-00') {
        return '-';
    }
    $timestamp = strtotime($value);
    return $timestamp ? date('d M Y', $timestamp) : '-';
};
$shortage_content = function ($value, $items, $plan_id, $category) use ($dispatch_value) {
    $html = '<div class="shortage-summary">' . $dispatch_value($value) . '</div>';
    if ($items) {
        $html .= '<ul class="shortage-items">';
        foreach (explode('||', $items) as $item) {
            $parts = explode('~~', $item);
            $title = isset($parts[0]) ? $parts[0] : '';
            $item_qty = isset($parts[1]) ? $parts[1] : '';
            $pendency_from = isset($parts[2]) ? $parts[2] : '';
            $owner = isset($parts[3]) ? $parts[3] : '';
            $task_id = isset($parts[4]) ? (int) $parts[4] : 0;
            $task_code = isset($parts[5]) ? $parts[5] : '';
            $status = isset($parts[6]) ? ucwords(strtolower(str_replace('_', ' ', $parts[6]))) : 'Submitted';
            $label = htmlspecialchars(($item_qty !== '' ? 'Qty ' . $item_qty . ' · ' : '') . $title, ENT_QUOTES, 'UTF-8');
            if ($task_id > 0) {
                $label = '<a href="' . page_url . 'Task_management/view/' . $task_id . '" target="_blank">' . $label . '</a>';
            }
            $meta = trim(($pendency_from !== '' ? 'Pending from ' . date('d M Y', strtotime($pendency_from)) : '') . ($owner !== '' ? ' · ' . $owner : '') . ($task_code !== '' ? ' · ' . $task_code : '') . ($status !== '' ? ' · ' . $status : ''));
            $html .= '<li>' . $label . ($meta !== '' ? '<small class="shortage-task-meta">' . htmlspecialchars($meta, ENT_QUOTES, 'UTF-8') . '</small>' : '') . '</li>';
        }
        $html .= '</ul>';
    }
    return $html . '<button type="button" class="btn btn-xs btn-link add-shortage-line" onclick="event.stopPropagation(); openShortageTasks(' . (int) $plan_id . ', \'' . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . '\')"><i class="fa fa-plus"></i> Add line</button>';
};
$shortage_users = array();
foreach ($users as $user) {
    $shortage_users[] = array(
        'id' => (int) $user->user_id,
        'name' => ucwords(strtolower(trim($user->first_name . ' ' . $user->last_name)))
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> DF Dispatch Morning Meeting</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <style>
        :root { --dispatch-primary: <?php echo $theme_color; ?>; --dispatch-navy: #1f3f7a; }
        .dispatch-shell { margin-top: 20px; }
        .dispatch-hero {
            background: linear-gradient(135deg, var(--dispatch-primary), var(--dispatch-navy));
            color: #fff; border-radius: 14px; padding: 22px; margin-bottom: 18px;
            box-shadow: 0 10px 25px rgba(31, 63, 122, .18);
        }
        .dispatch-hero h2 { color: #fff; margin: 0 0 4px; font-size: 23px; font-weight: 700; }
        .dispatch-hero p { margin: 0; opacity: .9; }
        .hero-actions { text-align: right; }
        .hero-actions .btn { border-radius: 22px; font-weight: 600; margin-left: 6px; }
        .kpi-row { margin-bottom: 18px; }
        .kpi-card {
            background: #fff; border-radius: 12px; padding: 17px 18px; min-height: 104px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .08); border-left: 4px solid var(--dispatch-primary);
        }
        .kpi-card.success { border-color: #16a34a; }
        .kpi-card.warning { border-color: #f59e0b; }
        .kpi-card.danger { border-color: #dc2626; }
        .kpi-label { text-transform: uppercase; font-size: 11px; color: #64748b; letter-spacing: .6px; font-weight: 700; }
        .kpi-value { font-size: 30px; line-height: 1.2; color: #172554; font-weight: 800; }
        .kpi-note { color: #94a3b8; font-size: 11px; }
        .month-strip { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px; margin-bottom: 12px; }
        .month-chip {
            display: inline-block; min-width: 88px; padding: 9px 10px; border-radius: 10px;
            background: #fff; border: 1px solid #dbe3ef; color: #334155; text-align: center;
            font-size: 12px; font-weight: 700;
        }
        .month-chip:hover, .month-chip.active { background: var(--dispatch-primary); color: #fff; border-color: var(--dispatch-primary); }
        .month-chip small { display: block; opacity: .75; margin-top: 2px; }
        .filter-card, .dispatch-table-card {
            background: #fff; border-radius: 12px; padding: 16px; box-shadow: 0 4px 16px rgba(15, 23, 42, .08);
        }
        .filter-card { margin-bottom: 16px; }
        .missing-df-card { border-radius: 12px; margin-bottom: 16px; }
        .missing-df-note { margin: 6px 0 10px; font-size: 12px; }
        .missing-df-list { list-style: none; padding: 0; margin: 0; max-height: 190px; overflow-y: auto; }
        .missing-df-list li { padding: 6px 0; border-top: 1px solid rgba(0, 0, 0, 0.08); }
        .missing-df-list li:first-child { border-top: 0; }
        .missing-df-list small { display: block; opacity: 0.8; }
        .filter-card .form-control { border-radius: 8px; }
        .dispatch-table-card { border-top: 4px solid var(--dispatch-primary); }
        table.dispatch-table { width: 100% !important; white-space: nowrap; font-size: 12px; }
        table.dispatch-table thead th {
            background: var(--dispatch-primary); color: #fff; text-align: center;
            vertical-align: middle; padding: 10px 8px; border-color: #d9e2f2;
        }
        table.dispatch-table tbody td { vertical-align: middle; border-color: #e7edf5; padding: 8px; }
        table.dispatch-table tbody td.shortage-cell { text-align: center; font-weight: 700; }
        .shortage-items { margin: 5px 0; padding-left: 17px; text-align: left; font-weight: 400; white-space: normal; min-width: 150px; }
        .shortage-items li { margin-bottom: 3px; }
        .shortage-task-meta { display: block; color: #64748b; font-size: 10px; }
        .add-shortage-line { padding: 2px 4px; font-weight: 700; }
        table.dispatch-table tbody td.status-cell { min-width: 90px; white-space: normal; }
        table.dispatch-table tbody td.inline-editable { cursor: pointer; position: relative; transition: background .15s ease, box-shadow .15s ease; }
        table.dispatch-table tbody td.inline-editable:hover { background: #eef5ff; box-shadow: inset 0 0 0 1px var(--dispatch-primary); }
        table.dispatch-table tbody td.inline-editing { padding: 3px; background: #fff; box-shadow: inset 0 0 0 2px var(--dispatch-primary); }
        table.dispatch-table tbody td.inline-saving { background: #fff8db; cursor: wait; }
        table.dispatch-table tbody td.inline-saved { background: #dcfce7; }
        table.dispatch-table tbody td.inline-error { background: #fee2e2; box-shadow: inset 0 0 0 1px #dc2626; }
        .inline-editor { width: 100%; min-width: 95px; border: 0; outline: 0; padding: 5px 6px; background: transparent; color: #1e293b; }
        textarea.inline-editor { min-width: 220px; min-height: 70px; resize: vertical; }
        .inline-save-indicator { margin-left: 5px; color: #b7791f; }
        .dispatch-toast { display: none; position: fixed; right: 24px; bottom: 24px; z-index: 100000; padding: 11px 16px; border-radius: 8px; color: #fff; background: #166534; box-shadow: 0 8px 25px rgba(15,23,42,.25); font-weight: 600; }
        .dispatch-toast.error { background: #b91c1c; }
        .df-number { color: var(--dispatch-primary); font-weight: 800; }
        .model-cell { min-width: 230px; max-width: 320px; white-space: normal; font-weight: 600; color: #27364d; }
        .status-badge { display: inline-block; padding: 5px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .status-planned { background: #e0e7ff; color: #3730a3; }
        .status-in-progress { background: #dbeafe; color: #1d4ed8; }
        .status-at-risk, .status-overdue { background: #fee2e2; color: #b91c1c; }
        .status-ready { background: #dcfce7; color: #166534; }
        .status-dispatched { background: #d1fae5; color: #065f46; }
        .status-on-hold { background: #fef3c7; color: #92400e; }
        .dependency-pill { border: 0; border-radius: 18px; padding: 5px 10px; font-size: 11px; font-weight: 700; }
        .dependency-pill.open { background: #fff1f2; color: #be123c; }
        .dependency-pill.clear { background: #f1f5f9; color: #475569; }
        .progress { margin-bottom: 0; height: 8px; border-radius: 8px; min-width: 90px; }
        .action-btn { border-radius: 18px; margin: 1px; }
        .modal-content { border-radius: 14px; border: 0; overflow: hidden; }
        .modal-header { background: linear-gradient(135deg, var(--dispatch-primary), var(--dispatch-navy)); color: #fff; }
        .modal-title { color: #fff; font-weight: 700; }
        .modal-dialog.dispatch-modal { width: 94%; max-width: 1320px; }
        .dependency-item { border: 1px solid #e2e8f0; border-radius: 10px; padding: 11px; margin-bottom: 9px; }
        .dependency-item.blocked { border-left: 4px solid #dc2626; }
        .dependency-item.closed { opacity: .7; background: #f8fafc; }
        .dispatch-table-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .dispatch-table-toolbar h4 { margin: 0; color: #334155; }
        .shortage-task-row { border: 1px solid #dbe3ef; border-left: 4px solid var(--dispatch-primary); border-radius: 9px; padding: 12px 8px 2px; margin-bottom: 10px; background: #f8fafc; }
        .shortage-task-number { display: inline-block; margin: 7px 0 0; font-weight: 800; color: var(--dispatch-primary); }
        .audit-log-item { border-left: 3px solid var(--dispatch-primary); padding: 9px 12px; margin-bottom: 10px; background: #f8fafc; border-radius: 0 8px 8px 0; }
        .audit-log-meta { color: #64748b; font-size: 11px; margin-bottom: 5px; }
        .audit-value { display: inline-block; max-width: 46%; padding: 3px 6px; border-radius: 4px; white-space: pre-wrap; word-break: break-word; vertical-align: top; }
        .audit-old { background: #fee2e2; color: #991b1b; }
        .audit-new { background: #dcfce7; color: #166534; }
        .empty-state { padding: 48px 20px; text-align: center; color: #64748b; }
        .dispatch-loader {
            display: none; position: fixed; z-index: 99999; inset: 0;
            background: rgba(248, 250, 252, .78); align-items: center; justify-content: center;
        }
        .dispatch-loader.active { display: flex; }
        .dispatch-loader-box {
            background: #fff; border-radius: 12px; padding: 18px 24px; color: var(--dispatch-primary);
            box-shadow: 0 12px 35px rgba(15, 23, 42, .2); font-weight: 700;
        }
        @media (max-width: 767px) {
            .hero-actions { text-align: left; margin-top: 14px; }
            .hero-actions .btn { margin: 3px 4px 3px 0; }
        }
    </style>
</head>
<body>
<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<?php $this->load->view('common/info-section.php'); ?>

<div class="wrapper">
    <div class="container-fluid dispatch-shell">
        <div class="dispatch-hero">
            <div class="row">
                <div class="col-md-8">
                    <h2><i class="fa fa-calendar-check-o"></i> DF Dispatch Morning Meeting</h2>
                    <p>Financial Year <?php echo htmlspecialchars($financial_year, ENT_QUOTES, 'UTF-8'); ?> · Month-wise planning, dependency tracking and ownership</p>
                </div>
                <div class="col-md-4 hero-actions">
                    <?php if ($tables_ready): ?>
                        <button class="btn btn-default" id="syncScheduleBtn"><i class="fa fa-refresh"></i> Sync PMS Schedule</button>
                        <button class="btn btn-light" onclick="openPlanModal()"><i class="fa fa-plus"></i> Add DF Plan</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!$tables_ready): ?>
            <div class="alert alert-warning">
                <strong>Database setup required.</strong>
                For a new setup run <code>Database/df_dispatch_plan_001.sql</code>. For an existing setup run migrations <code>002</code> through <code>006</code>, then reload this page.
            </div>
        <?php else: ?>
            <div class="row kpi-row">
                <div class="col-md-3 col-sm-6"><div class="kpi-card"><div class="kpi-label">FY Planned DFs</div><div class="kpi-value"><?php echo $summary_total; ?></div><div class="kpi-note">Across April to March</div></div></div>
                <div class="col-md-3 col-sm-6"><div class="kpi-card success"><div class="kpi-label">Dispatched</div><div class="kpi-value"><?php echo $summary_dispatched; ?></div><div class="kpi-note">Completed dispatch plans</div></div></div>
                <div class="col-md-3 col-sm-6"><div class="kpi-card warning"><div class="kpi-label">At Risk</div><div class="kpi-value"><?php echo $summary_at_risk; ?></div><div class="kpi-note">Needs meeting attention</div></div></div>
                <div class="col-md-3 col-sm-6"><div class="kpi-card danger"><div class="kpi-label">Overdue</div><div class="kpi-value"><?php echo $summary_overdue; ?></div><div class="kpi-note">Past plan date, not dispatched</div></div></div>
            </div>

            <div class="month-strip">
                <a class="month-chip <?php echo $selected_month === 'all' ? 'active' : ''; ?>" href="?fy=<?php echo urlencode($financial_year); ?>&month=all">All<small><?php echo $summary_total; ?> DFs</small></a>
                <?php foreach ($months as $month_key => $month_label): ?>
                    <?php $month_count = isset($summary->months[$month_key]) ? $summary->months[$month_key] : 0; ?>
                    <a class="month-chip <?php echo $selected_month === $month_key ? 'active' : ''; ?>" href="?fy=<?php echo urlencode($financial_year); ?>&month=<?php echo urlencode($month_key); ?>">
                        <?php echo $month_label; ?><small><?php echo $month_count; ?> DFs</small>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($missing_dfs)): ?>
                <div class="alert alert-warning missing-df-card">
                    <strong><i class="fa fa-exclamation-triangle"></i> <?php echo count($missing_dfs); ?> released DF(s) cannot be placed on this board.</strong>
                    <div class="missing-df-note">A DF appears here only once its Dispatch task carries a date inside <?php echo htmlspecialchars($financial_year, ENT_QUOTES, 'UTF-8'); ?>. Schedule the Dispatch task for these DFs, or add them manually with <em>Add DF Plan</em>.</div>
                    <ul class="missing-df-list">
                        <?php foreach ($missing_dfs as $missing): ?>
                            <?php
                            $missing_date = $missing->dispatch_task_date;
                            if (!$missing_date || $missing_date === '0000-00-00') {
                                $reason = 'Dispatch task has no date scheduled';
                            } else {
                                $reason = 'Dispatch task is dated ' . date('d M Y', strtotime($missing_date)) . ', outside this financial year';
                            }
                            ?>
                            <li>
                                <strong>DF <?php echo htmlspecialchars($missing->df_no, ENT_QUOTES, 'UTF-8'); ?></strong>
                                <?php if (trim((string) $missing->df_description) !== ''): ?>
                                    &middot; <?php echo htmlspecialchars($missing->df_description, ENT_QUOTES, 'UTF-8'); ?>
                                <?php endif; ?>
                                <small>Released <?php echo date('d M Y', strtotime($missing->released_on)); ?> &middot; <?php echo htmlspecialchars($reason, ENT_QUOTES, 'UTF-8'); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form class="filter-card" method="get">
                <div class="row">
                    <div class="col-md-3">
                        <label>Financial Year</label>
                        <select name="fy" class="form-control">
                            <?php for ($year = $fy_start - 2; $year <= $fy_start + 2; $year++): ?>
                                <?php $fy_label = $year . '-' . substr((string) ($year + 1), -2); ?>
                                <option value="<?php echo $fy_label; ?>" <?php echo $financial_year === $fy_label ? 'selected' : ''; ?>><?php echo $fy_label; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Month</label>
                        <select name="month" class="form-control">
                            <option value="all" <?php echo $selected_month === 'all' ? 'selected' : ''; ?>>All months</option>
                            <?php foreach ($months as $month_key => $month_label): ?>
                                <option value="<?php echo $month_key; ?>" <?php echo $selected_month === $month_key ? 'selected' : ''; ?>><?php echo $month_label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All statuses</option>
                            <?php foreach (array('Planned', 'In Progress', 'At Risk', 'Ready', 'Dispatched', 'On Hold') as $option): ?>
                                <option value="<?php echo $option; ?>" <?php echo $selected_status === $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>DF / Model / Owner</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Search...">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block"><i class="fa fa-filter"></i></button>
                    </div>
                </div>
            </form>

            <div class="dispatch-table-card table-responsive">
                <div class="dispatch-table-toolbar">
                    <h4><i class="fa fa-table"></i> Dispatch Plan</h4>
                    <button type="button" class="btn btn-warning" id="openShortageTasksBtn"><i class="fa fa-user-plus"></i> Delegate Shortage Tasks</button>
                </div>
                <table id="dispatchTable" class="table table-bordered table-striped dispatch-table">
                    <thead>
                        <tr>
                            <th>S.No.</th><th>DF No.</th><th>Priority</th><th>Model</th><th>C / I</th><th>Product</th><th>Automation</th>
                            <th>DF Date</th><th>BOM Date</th><th>Loading</th><th>Completion Status</th><th>Completion Date</th>
                            <th>Actual Planned Completion Date</th><th>FAT Date</th><th>Dispatch Plan Date</th><th>Design</th><th>Marketing</th><th>Frame Status</th>
                            <th>Sec. 6 Shortage</th><th>Sec. 6 EOL</th><th>Purchase Shortage</th><th>Purchase EOL</th>
                            <th>BOP Shortage</th><th>BOP EOL</th><th>Automation Status</th><th>Electrical</th><th>Gear Box</th>
                            <th>Plan Status</th><th>Dependencies</th><th>Remarks</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($plans as $plan_index => $plan): ?>
                        <?php
                        $is_overdue = $plan->status !== 'Dispatched' && $plan->planned_dispatch_date < date('Y-m-d');
                        $status_class = $is_overdue ? 'status-overdue' : 'status-' . strtolower(str_replace(' ', '-', $plan->status));
                        $plan_payload = htmlspecialchars(json_encode($plan), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $plan_index + 1; ?></td>
                            <td class="df-number"><?php echo htmlspecialchars($plan->df_no, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-center inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="priority" data-type="number" data-value="<?php echo (int) $plan->priority; ?>"><?php echo (int) $plan->priority; ?></td>
                            <td class="model-cell"><?php echo $dispatch_value($plan->model); ?></td>
                            <td class="inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="machine_type" data-value="<?php echo $dispatch_attr($plan->machine_type); ?>"><?php echo $dispatch_value($plan->machine_type); ?></td>
                            <td class="inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="product" data-value="<?php echo $dispatch_attr($plan->product); ?>"><?php echo $dispatch_value($plan->product); ?></td>
                            <td class="inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="automation" data-value="<?php echo $dispatch_attr($plan->automation); ?>"><?php echo $dispatch_value($plan->automation); ?></td>
                            <td><?php echo $dispatch_date($plan->source_df_date); ?></td>
                            <td><?php echo $dispatch_date($plan->source_bom_date); ?></td>
                            <td class="model-cell inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="loading_status" data-type="textarea" data-value="<?php echo $dispatch_attr($plan->loading_status); ?>"><?php echo $dispatch_value($plan->loading_status); ?></td>
                            <td class="inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="completion_percent" data-type="number" data-value="<?php echo (int) $plan->completion_percent; ?>">
                                <strong><?php echo (int) $plan->completion_percent; ?>%</strong>
                                <div class="progress"><div class="progress-bar" style="width:<?php echo (int) $plan->completion_percent; ?>%; background:var(--dispatch-primary);"></div></div>
                            </td>
                            <td><?php echo $dispatch_date($plan->source_completion_date); ?></td>
                            <?php $actual_planned_value = !empty($plan->actual_planned_completion_date) ? $plan->actual_planned_completion_date : ''; ?>
                            <td class="inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="actual_planned_completion_date" data-type="date" data-value="<?php echo $dispatch_attr($actual_planned_value); ?>"><?php echo $dispatch_date($actual_planned_value); ?></td>
                            <?php $fat_date_value = !empty($plan->fat_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $plan->fat_date) ? $plan->fat_date : ''; ?>
                            <td class="inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="fat_date" data-type="date" data-value="<?php echo $dispatch_attr($fat_date_value); ?>"><?php echo $dispatch_date($fat_date_value); ?></td>
                            <td class="inline-editable" data-order="<?php echo $plan->planned_dispatch_date; ?>" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="planned_dispatch_date" data-type="date" data-value="<?php echo $dispatch_attr($plan->planned_dispatch_date); ?>"><?php echo $dispatch_date($plan->planned_dispatch_date); ?><?php if ($is_overdue): ?><br><span class="status-badge status-overdue">Overdue</span><?php endif; ?></td>
                            <td><?php echo $dispatch_value(ucwords(strtolower((string) ($plan->source_design_owner ?: $plan->design_owner)))); ?></td>
                            <td><?php echo $dispatch_value(ucwords(strtolower((string) ($plan->source_marketing_owner ?: $plan->marketing_owner)))); ?></td>
                            <td class="status-cell inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="frame_status" data-value="<?php echo $dispatch_attr($plan->frame_status); ?>"><?php echo $dispatch_value($plan->frame_status); ?></td>
                            <td class="shortage-cell"><?php echo $shortage_content($plan->section_6_shortage, $plan->section_6_items, $plan->id, 'Section 6'); ?></td>
                            <td class="shortage-cell"><?php echo $shortage_content($plan->section_6_eol, $plan->section_6_eol_items, $plan->id, 'Section 6 EOL'); ?></td>
                            <td class="shortage-cell"><?php echo $shortage_content($plan->purchase_shortage, $plan->purchase_items, $plan->id, 'Purchase'); ?></td>
                            <td class="shortage-cell"><?php echo $shortage_content($plan->purchase_eol, $plan->purchase_eol_items, $plan->id, 'Purchase EOL'); ?></td>
                            <td class="shortage-cell"><?php echo $shortage_content($plan->bop_shortage, $plan->bop_items, $plan->id, 'BOP'); ?></td>
                            <td class="shortage-cell"><?php echo $shortage_content($plan->bop_eol, $plan->bop_eol_items, $plan->id, 'BOP EOL'); ?></td>
                            <td class="status-cell inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="automation_status" data-value="<?php echo $dispatch_attr($plan->automation_status); ?>"><?php echo $dispatch_value($plan->automation_status); ?></td>
                            <td class="status-cell inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="electrical_status" data-value="<?php echo $dispatch_attr($plan->electrical_status); ?>"><?php echo $dispatch_value($plan->electrical_status); ?></td>
                            <td class="status-cell inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="gear_box_status" data-value="<?php echo $dispatch_attr($plan->gear_box_status); ?>"><?php echo $dispatch_value($plan->gear_box_status); ?></td>
                            <td class="inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="status" data-type="select" data-value="<?php echo $dispatch_attr($plan->status); ?>"><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($plan->status, ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td class="text-center">
                                <button class="dependency-pill <?php echo (int) $plan->open_dependency_count > 0 ? 'open' : 'clear'; ?>" onclick="openDependencies(<?php echo (int) $plan->id; ?>, '<?php echo htmlspecialchars($plan->df_no, ENT_QUOTES, 'UTF-8'); ?>')">
                                    <?php echo (int) $plan->open_dependency_count; ?> open / <?php echo (int) $plan->dependency_count; ?>
                                </button>
                            </td>
                            <td class="model-cell inline-editable" data-plan-id="<?php echo (int) $plan->id; ?>" data-field="remarks" data-type="textarea" data-value="<?php echo $dispatch_attr($plan->remarks); ?>"><?php echo $dispatch_value($plan->remarks); ?></td>
                            <td>
                                <button class="btn btn-xs btn-primary action-btn edit-plan" data-plan="<?php echo $plan_payload; ?>"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-xs btn-warning action-btn" onclick="openDependencies(<?php echo (int) $plan->id; ?>, '<?php echo htmlspecialchars($plan->df_no, ENT_QUOTES, 'UTF-8'); ?>')"><i class="fa fa-tasks"></i></button>
                                <button class="btn btn-xs btn-info action-btn" onclick="openPlanHistory(<?php echo (int) $plan->id; ?>)" title="View update history"><i class="fa fa-history"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($plans)): ?><div class="empty-state"><i class="fa fa-calendar-o fa-3x"></i><h4>No dispatch plans found</h4><p>Add the first DF plan or change the selected filters.</p></div><?php endif; ?>
            </div>
        <?php endif; ?>
        <?php $this->load->view('common/footer'); ?>
    </div>
</div>

<?php if ($tables_ready): ?>
<div id="planModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog dispatch-modal"><div class="modal-content">
        <form id="planForm">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">DF Dispatch Plan</h4></div>
            <div class="modal-body">
                <input type="hidden" name="plan_id" id="plan_id">
                <div class="row">
                    <div class="col-md-4 form-group"><label>DF from Master</label><select name="df_id" id="df_id" class="form-control"><option value="">Manual / Basic Machine</option><?php foreach ($available_dfs as $df): ?><option value="<?php echo (int) $df->id; ?>" data-df-no="<?php echo htmlspecialchars($df->df_no, ENT_QUOTES, 'UTF-8'); ?>" data-model="<?php echo htmlspecialchars($df->df_description, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($df->df_no . ' - ' . $df->df_description, ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4 form-group"><label>DF No. *</label><input name="df_no" id="df_no" class="form-control" required></div>
                    <div class="col-md-2 form-group"><label>Priority</label><input type="number" min="0" name="priority" id="priority" value="0" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>Completion %</label><input type="number" min="0" max="100" name="completion_percent" id="completion_percent" value="0" class="form-control"></div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group"><label>Model *</label><input name="model" id="model" class="form-control" required></div>
                    <div class="col-md-2 form-group"><label>C / I</label><input name="machine_type" id="machine_type" class="form-control" placeholder="Continuous / Intermittent"></div>
                    <div class="col-md-3 form-group"><label>Product</label><input name="product" id="product" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>Automation</label><input name="automation" id="automation" class="form-control"></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group"><label>DF Release Date</label><input id="df_date" class="form-control" readonly></div>
                    <div class="col-md-3 form-group"><label>BOM Completion Date</label><input id="bom_date" class="form-control" readonly></div>
                    <div class="col-md-3 form-group"><label>Loading</label><input name="loading_status" id="loading_status" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>Completion Date</label><input id="completion_date" class="form-control" readonly></div>
                </div>
                <div class="row">
                    <input type="hidden" name="dispatch_schedule" id="dispatch_schedule">
                    <div class="col-md-3 form-group"><label>Actual Planned Completion Date</label><input type="date" name="actual_planned_completion_date" id="actual_planned_completion_date" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>Dispatch Plan Date *</label><input type="date" name="planned_dispatch_date" id="planned_dispatch_date" class="form-control" required><small class="text-muted">Change this date to move the DF to another month.</small></div>
                    <div class="col-md-3 form-group"><label>FAT Date</label><input type="date" name="fat_date" id="fat_date" class="form-control"></div>
                    <div class="col-md-3 form-group"><label>Frame Status</label><input name="frame_status" id="frame_status" class="form-control"></div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group"><label>Design</label><input name="design_owner" id="design_owner" class="form-control"></div>
                    <div class="col-md-4 form-group"><label>Marketing</label><input name="marketing_owner" id="marketing_owner" class="form-control"></div>
                    <div class="col-md-4 form-group"><label>Plan Status</label><select name="status" id="plan_status" class="form-control"><?php foreach (array('Planned', 'In Progress', 'At Risk', 'Ready', 'Dispatched', 'On Hold') as $option): ?><option><?php echo $option; ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="row">
                    <div class="col-md-2 form-group"><label>Sec. 6 Shortage</label><input name="section_6_shortage" id="section_6_shortage" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>Sec. 6 EOL</label><input name="section_6_eol" id="section_6_eol" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>Purchase Shortage</label><input name="purchase_shortage" id="purchase_shortage" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>Purchase EOL</label><input name="purchase_eol" id="purchase_eol" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>BOP Shortage</label><input name="bop_shortage" id="bop_shortage" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>BOP EOL</label><input name="bop_eol" id="bop_eol" class="form-control"></div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group"><label>Automation Status</label><input name="automation_status" id="automation_status" class="form-control"></div>
                    <div class="col-md-4 form-group"><label>Electrical</label><input name="electrical_status" id="electrical_status" class="form-control"></div>
                    <div class="col-md-4 form-group"><label>Gear Box</label><input name="gear_box_status" id="gear_box_status" class="form-control"></div>
                </div>
                <div class="form-group"><label>Meeting Remarks</label><textarea name="remarks" id="plan_remarks" rows="3" class="form-control"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="savePlanBtn">Save Plan</button></div>
        </form>
    </div></div>
</div>

<div id="dependencyModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Dependencies · <span id="dependencyDf"></span></h4></div>
        <div class="modal-body">
            <div id="dependencyList"></div>
            <hr>
            <h5><i class="fa fa-user-plus"></i> Add / Delegate Dependency</h5>
            <form id="dependencyForm">
                <input type="hidden" name="plan_id" id="dependency_plan_id">
                <div class="row">
                    <div class="col-md-3 form-group"><label>Department</label><select name="department" class="form-control"><?php foreach (array('Design','Section 6','Section 6 EOL','Purchase','Purchase EOL','BOP','BOP EOL','Automation','Electrical','Gear Box','Production','Marketing','Other') as $department): ?><option><?php echo $department; ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-5 form-group"><label>Dependency *</label><input name="title" class="form-control" required></div>
                    <div class="col-md-4 form-group"><label>Owner</label><select name="owner_user_id" class="form-control searchable-user"><option value="0">Unassigned</option><?php foreach ($users as $user): ?><option value="<?php echo (int) $user->user_id; ?>"><?php echo htmlspecialchars(ucwords(strtolower(trim($user->first_name . ' ' . $user->last_name))), ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group"><label>Due Date *</label><input type="date" name="due_date" class="form-control" required></div>
                    <div class="col-md-3 form-group"><label>Priority</label><select name="priority" class="form-control"><option>Low</option><option selected>Medium</option><option>High</option><option>Critical</option></select></div>
                    <div class="col-md-4 form-group"><label>Remarks</label><input name="remarks" class="form-control"></div>
                    <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-warning btn-block">Delegate</button></div>
                </div>
            </form>
        </div>
    </div></div>
</div>

<div id="shortageTasksModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog dispatch-modal"><div class="modal-content">
        <form id="shortageTasksForm">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-list-alt"></i> Add Shortage Items</h4></div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7 form-group">
                        <label>DF Plan *</label>
                        <select id="shortage_plan_id" class="form-control" required>
                            <option value="">Select DF plan</option>
                            <?php foreach ($plans as $plan): ?>
                                <option value="<?php echo (int) $plan->id; ?>"><?php echo htmlspecialchars($plan->df_no . ' - ' . $plan->model, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5 form-group">
                        <label>Shortage Category *</label>
                        <select id="shortage_category" class="form-control" required>
                            <option value="Section 6">Sec. 6 Shortage</option>
                            <option value="Section 6 EOL">Sec. 6 EOL</option>
                            <option value="Purchase">Purchase Shortage</option>
                            <option value="Purchase EOL">Purchase EOL</option>
                            <option value="BOP">BOP Shortage</option>
                            <option value="BOP EOL">BOP EOL</option>
                        </select>
                    </div>
                </div>
                <div id="shortageTaskRows"></div>
                <button type="button" class="btn btn-default" id="addShortageTaskRow"><i class="fa fa-plus"></i> Add Another Item</button>
            </div>
            <div class="modal-footer">
                <span class="text-muted pull-left">All items are saved together. Assigning an item creates a linked Task Management task due on the DF plan date.</span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="delegateShortageTasksBtn"><i class="fa fa-check"></i> Submit Items</button>
            </div>
        </form>
    </div></div>
</div>

<div id="planHistoryModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-history"></i> Update History · <span id="historyDfNo"></span></h4></div>
        <div class="modal-body"><div id="planHistoryList"></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
    </div></div>
</div>
<?php endif; ?>

<div class="dispatch-loader" id="dispatchLoader">
    <div class="dispatch-loader-box"><i class="fa fa-spinner fa-spin"></i> Loading dispatch plans...</div>
</div>
<div class="dispatch-toast" id="dispatchToast"></div>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<?php if ($tables_ready): ?>
<script>
var dependencyPlanId = 0;
var dispatchDataTable = null;
var shortageUsers = <?php echo json_encode($shortage_users, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
var loggedInUserId = <?php $dispatch_session = $this->session->userdata('logged_in'); echo isset($dispatch_session['user_id']) ? (int) $dispatch_session['user_id'] : 0; ?>;
$(function() {
    if ($.fn.select2) {
        $('#dependencyForm .searchable-user').select2({placeholder: 'Search user', allowClear: true, dropdownParent: $('#dependencyModal')});
    }
    $('.month-chip').on('click', function() {
        showDispatchLoader();
    });

    $('.filter-card').on('submit', function() {
        showDispatchLoader();
    });

    $('.filter-card select[name="fy"], .filter-card select[name="month"], .filter-card select[name="status"]').on('change', function() {
        showDispatchLoader();
        $(this).closest('form').submit();
    });

    <?php if (!empty($plans)): ?>
    dispatchDataTable = $('#dispatchTable').DataTable({
        scrollX: true, pageLength: 50, order: [[14, 'asc'], [2, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {extend:'excelHtml5', text:'Export Excel', className:'btn btn-success btn-sm', title:'DF Dispatch Plan FY <?php echo $financial_year; ?>'},
            {extend:'print', text:'Print Meeting Sheet', className:'btn btn-primary btn-sm', title:'DF Dispatch Morning Meeting'}
        ],
        columnDefs: [{targets:[28,30], orderable:false}]
    });
    <?php endif; ?>

    $('#df_id').change(function() {
        var option = $(this).find('option:selected');
        if (option.val()) {
            $('#df_no').val(option.data('df-no'));
            $('#model').val(option.data('model'));
        }
    });

    $('#openShortageTasksBtn').click(function() { openShortageTasks('', 'Section 6'); });

    $('#addShortageTaskRow').click(function() {
        addShortageTaskRow();
    });

    $(document).on('click', '.remove-shortage-task', function() {
        if ($('#shortageTaskRows .shortage-task-row').length <= 1) {
            showDispatchToast('At least one item is required.', true);
            return;
        }
        $(this).closest('.shortage-task-row').remove();
        renumberShortageTaskRows();
        updateShortageSubmitButton();
    });

    $('#shortageTasksForm').submit(function(event) {
        event.preventDefault();
        var tasks = [];
        $('#shortageTaskRows .shortage-task-row').each(function(index) {
            var row = $(this);
            var ownerSelect = row.find('.shortage-task-owner').get(0);
            var selectedOwnerId = ownerSelect ? ownerSelect.value : '';
            if (!selectedOwnerId && $.fn.select2) {
                var selectedOwnerData = row.find('.shortage-task-owner').select2('data');
                selectedOwnerId = selectedOwnerData && selectedOwnerData.length ? selectedOwnerData[0].id : '';
            }
            var task = {
                item_qty: $.trim(row.find('.shortage-item-qty').val()),
                title: $.trim(row.find('.shortage-task-title').val()),
                owner_user_id: selectedOwnerId || '',
                pendency_from_date: row.find('.shortage-item-pendency-from').val() || '',
                priority: row.find('.shortage-task-priority').val(),
                remarks: $.trim(row.find('.shortage-task-remarks').val())
            };
            tasks.push(task);
        });

        if (!$('#shortage_plan_id').val()) {
            showDispatchToast('Select a DF plan.', true);
            return;
        }
        if (!tasks.length) {
            showDispatchToast('Add at least one shortage item.', true);
            return;
        }

        var hasAssignee = tasks.some(function(task) { return !!task.owner_user_id; });
        var button = $('#delegateShortageTasksBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + (hasAssignee ? 'Delegating...' : 'Submitting...'));
        $.post('<?php echo page_url; ?>Df_dispatch_plan/add_shortage_tasks', {
            plan_id: $('#shortage_plan_id').val(),
            category: $('#shortage_category').val(),
            tasks: JSON.stringify(tasks)
        }, function(response) {
            if (!response.status) {
                showDispatchToast(response.message, true);
                updateShortageSubmitButton();
                return;
            }
            $('#shortageTasksModal').modal('hide');
            showDispatchToast(response.message, false);
            window.setTimeout(function() { location.reload(); }, 700);
        }, 'json').fail(function(xhr) {
            var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Unable to save shortage items.';
            showDispatchToast(message, true);
            updateShortageSubmitButton();
        });
    });

    $(document).on('click', '#dispatchTable td.inline-editable', function(event) {
        if ($(event.target).is('.inline-editor') || $(this).hasClass('inline-saving')) {
            return;
        }
        beginInlineEdit($(this));
    });

    $(document).on('keydown', '#dispatchTable .inline-editor', function(event) {
        var editor = $(this);
        var cell = editor.closest('td');
        if (event.key === 'Escape') {
            event.preventDefault();
            cancelInlineEdit(cell);
            return;
        }
        if (event.key === 'Enter' && (!editor.is('textarea') || event.ctrlKey || event.metaKey)) {
            event.preventDefault();
            saveInlineEdit(cell);
            return;
        }
        if (event.key === 'Tab') {
            event.preventDefault();
            var cells = $('#dispatchTable td.inline-editable:visible');
            var currentIndex = cells.index(cell);
            var nextIndex = event.shiftKey ? currentIndex - 1 : currentIndex + 1;
            saveInlineEdit(cell, function() {
                if (nextIndex >= 0 && nextIndex < cells.length) {
                    beginInlineEdit($(cells.get(nextIndex)));
                }
            });
        }
    });

    $(document).on('blur', '#dispatchTable .inline-editor', function() {
        var cell = $(this).closest('td');
        window.setTimeout(function() {
            if (cell.hasClass('inline-editing')) {
                saveInlineEdit(cell);
            }
        }, 80);
    });

    $('#syncScheduleBtn').click(function() {
        var button = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
        $.post('<?php echo page_url; ?>Df_dispatch_plan/sync_schedule', {
            financial_year: '<?php echo htmlspecialchars($financial_year, ENT_QUOTES, 'UTF-8'); ?>'
        }, function(response) {
            alert(response.message);
            if (response.status) {
                location.reload();
            } else {
                button.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sync PMS Schedule');
            }
        }, 'json').fail(function() {
            alert('Unable to sync the PMS schedule.');
            button.prop('disabled', false).html('<i class="fa fa-refresh"></i> Sync PMS Schedule');
        });
    });

    $(document).on('click', '.edit-plan', function() {
        openPlanModal($(this).data('plan'));
    });

    $('#planForm').submit(function(event) {
        event.preventDefault();
        var button = $('#savePlanBtn').prop('disabled', true).text('Saving...');
        $.post('<?php echo page_url; ?>Df_dispatch_plan/save_plan', $(this).serialize(), function(response) {
            if (response.status) {
                location.reload();
            } else {
                alert(response.message);
                button.prop('disabled', false).text('Save Plan');
            }
        }, 'json').fail(function() {
            alert('Unable to save. Please try again.');
            button.prop('disabled', false).text('Save Plan');
        });
    });

    $('#dependencyForm').submit(function(event) {
        event.preventDefault();
        $.post('<?php echo page_url; ?>Df_dispatch_plan/add_dependency', $(this).serialize(), function(response) {
            if (response.status) {
                $('#dependencyForm')[0].reset();
                $('#dependency_plan_id').val(dependencyPlanId);
                loadDependencies();
            } else {
                alert(response.message);
            }
        }, 'json');
    });
});

function addShortageTaskRow() {
    if ($('#shortageTaskRows .shortage-task-row').length >= 25) {
        showDispatchToast('A maximum of 25 shortage items can be saved at once.', true);
        return;
    }
    var ownerOptions = '<option value="">Select assignee</option>';
    $.each(shortageUsers, function(_, user) {
        ownerOptions += '<option value="' + user.id + '">' + escapeHtml(user.name) + '</option>';
    });
    var row = $('<div class="shortage-task-row">' +
        '<div class="row">' +
            '<div class="col-md-1"><span class="shortage-task-number"></span></div>' +
            '<div class="col-md-2 form-group"><label>Item Qty *</label><input type="number" min="0.01" step="0.01" class="form-control shortage-item-qty" required></div>' +
            '<div class="col-md-3 form-group"><label>Item Description *</label><input type="text" class="form-control shortage-task-title" maxlength="500" required></div>' +
            '<div class="col-md-2 form-group"><label>Assign <small>(Optional)</small></label><select class="form-control shortage-task-owner searchable-user">' + ownerOptions + '</select></div>' +
            '<div class="col-md-2 form-group"><label>Item Pendency From *</label><input type="date" class="form-control shortage-item-pendency-from" required></div>' +
            '<div class="col-md-1 form-group"><label>Priority</label><select class="form-control shortage-task-priority"><option>Low</option><option selected>Medium</option><option>High</option><option>Critical</option></select></div>' +
            '<div class="col-md-1 form-group"><label>&nbsp;</label><button type="button" class="btn btn-danger btn-block remove-shortage-task" title="Remove task"><i class="fa fa-trash"></i></button></div>' +
        '</div>' +
        '<div class="row"><div class="col-md-offset-1 col-md-10 form-group"><label>Remarks</label><textarea class="form-control shortage-task-remarks" rows="2" maxlength="5000"></textarea></div></div>' +
    '</div>');
    $('#shortageTaskRows').append(row);
    if ($.fn.select2) {
        row.find('.shortage-task-owner').select2({placeholder: 'Search assignee', allowClear: true, dropdownParent: $('#shortageTasksModal')});
    }
    row.find('.shortage-task-owner').on('change', updateShortageSubmitButton);
    renumberShortageTaskRows();
    updateShortageSubmitButton();
    row.find('.shortage-item-qty').trigger('focus');
}

function openShortageTasks(planId, category) {
    $('#shortageTasksForm')[0].reset();
    $('#shortage_plan_id').val(planId || '');
    $('#shortage_category').val(category || 'Section 6');
    $('#shortageTaskRows').empty();
    addShortageTaskRow();
    updateShortageSubmitButton();
    $('#shortageTasksModal').modal('show');
}

function updateShortageSubmitButton() {
    var hasAssignee = false;
    $('#shortageTaskRows .shortage-task-owner').each(function() {
        if ($(this).val()) { hasAssignee = true; }
    });
    $('#delegateShortageTasksBtn').prop('disabled', false).toggleClass('btn-warning', hasAssignee).toggleClass('btn-primary', !hasAssignee)
        .html(hasAssignee ? '<i class="fa fa-user-plus"></i> Delegate Tasks' : '<i class="fa fa-check"></i> Submit Items');
}

function renumberShortageTaskRows() {
    $('#shortageTaskRows .shortage-task-row').each(function(index) {
        $(this).find('.shortage-task-number').text('#' + (index + 1));
    });
}

function beginInlineEdit(cell) {
    if (cell.hasClass('inline-editing') || cell.hasClass('inline-saving')) {
        return;
    }

    var openCell = $('#dispatchTable td.inline-editing');
    if (openCell.length && !openCell.is(cell)) {
        saveInlineEdit(openCell);
    }

    var value = cell.attr('data-value') || '';
    var type = cell.attr('data-type') || 'text';
    var editor;

    if (type === 'select') {
        editor = $('<select class="inline-editor form-control input-sm"></select>');
        $.each(['Planned', 'In Progress', 'At Risk', 'Ready', 'Dispatched', 'On Hold'], function(_, option) {
            editor.append($('<option></option>').val(option).text(option));
        });
        editor.val(value);
    } else if (type === 'textarea') {
        editor = $('<textarea class="inline-editor"></textarea>').val(value);
    } else {
        editor = $('<input class="inline-editor">').attr('type', type).val(value);
        if (cell.attr('data-field') === 'completion_percent') {
            editor.attr({min: 0, max: 100});
        } else if (type === 'number') {
            editor.attr({min: 0});
        }
    }

    cell.removeClass('inline-error inline-saved').addClass('inline-editing').empty().append(editor);
    editor.trigger('focus');
    if (editor.is('input[type="text"], textarea')) {
        var input = editor.get(0);
        input.setSelectionRange(value.length, value.length);
    }
}

function cancelInlineEdit(cell) {
    cell.removeClass('inline-editing');
    renderInlineCell(cell, cell.attr('data-value') || '');
}

function saveInlineEdit(cell, done) {
    if (!cell.hasClass('inline-editing')) {
        if (done) { done(); }
        return;
    }

    var editor = cell.find('.inline-editor');
    var oldValue = cell.attr('data-value') || '';
    var newValue = $.trim(editor.val() == null ? '' : String(editor.val()));
    if (newValue === oldValue) {
        cancelInlineEdit(cell);
        if (done) { done(); }
        return;
    }

    cell.removeClass('inline-editing').addClass('inline-saving').append('<span class="inline-save-indicator"><i class="fa fa-spinner fa-spin"></i></span>');

    $.ajax({
        url: '<?php echo page_url; ?>Df_dispatch_plan/update_field',
        type: 'POST',
        dataType: 'json',
        data: {
            plan_id: cell.attr('data-plan-id'),
            field: cell.attr('data-field'),
            value: newValue
        }
    }).done(function(response) {
        if (!response.status) {
            inlineSaveFailed(cell, oldValue, response.message || 'Unable to save this change.');
            return;
        }
        cell.attr('data-value', response.value).removeClass('inline-saving').addClass('inline-saved');
        renderInlineCell(cell, response.value);
        if (dispatchDataTable) {
            dispatchDataTable.cell(cell.get(0)).invalidate('dom');
        }
        window.setTimeout(function() { cell.removeClass('inline-saved'); }, 900);
        if (done) { done(); }
    }).fail(function(xhr) {
        var message = 'Network error. Your previous value has been restored.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        }
        inlineSaveFailed(cell, oldValue, message);
    });
}

function inlineSaveFailed(cell, oldValue, message) {
    cell.removeClass('inline-saving').addClass('inline-error');
    renderInlineCell(cell, oldValue);
    showDispatchToast(message, true);
    window.setTimeout(function() { cell.removeClass('inline-error'); }, 2200);
}

function renderInlineCell(cell, value) {
    var field = cell.attr('data-field');
    if (field === 'completion_percent') {
        var percent = Math.max(0, Math.min(100, parseInt(value, 10) || 0));
        cell.html('<strong>' + percent + '%</strong><div class="progress"><div class="progress-bar" style="width:' + percent + '%; background:var(--dispatch-primary);"></div></div>');
    } else if (field === 'status') {
        var statusClass = 'status-' + String(value).toLowerCase().replace(/\s+/g, '-');
        cell.html('<span class="status-badge ' + statusClass + '">' + escapeHtml(value) + '</span>');
    } else if (cell.attr('data-type') === 'date') {
        var parts = String(value || '').split('-');
        var monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        cell.text(parts.length === 3 ? parts[2] + ' ' + monthNames[parseInt(parts[1], 10) - 1] + ' ' + parts[0] : '-');
    } else {
        cell.html(value === '' ? '-' : escapeHtml(value).replace(/\n/g, '<br>'));
    }
}

function showDispatchToast(message, isError) {
    var toast = $('#dispatchToast');
    toast.stop(true, true).toggleClass('error', !!isError).text(message).fadeIn(120).delay(2200).fadeOut(250);
}

function openPlanModal(plan) {
    $('#planForm')[0].reset();
    $('#plan_id').val('');
    $('#priority').val(0);
    $('#completion_percent').val(0);
    $('#plan_status').val('Planned');
    if (plan) {
        $('#plan_id').val(plan.id);
        $('#df_id').val(plan.df_id || '');
        $('#df_no').val(plan.df_no);
        $('#priority').val(plan.priority);
        $('#model').val(plan.model);
        $('#machine_type').val(plan.machine_type);
        $('#product').val(plan.product);
        $('#automation').val(plan.automation);
        $('#df_date').val(plan.source_df_date || '');
        $('#bom_date').val(plan.source_bom_date || '');
        $('#loading_status').val(plan.loading_status);
        $('#planned_dispatch_date').val(plan.planned_dispatch_date);
        $('#dispatch_schedule').val(plan.dispatch_schedule);
        $('#fat_date').val(plan.fat_date);
        $('#completion_percent').val(plan.completion_percent);
        $('#completion_date').val(plan.source_completion_date || '');
        $('#actual_planned_completion_date').val(plan.actual_planned_completion_date || '');
        $('#frame_status').val(plan.frame_status);
        $('#design_owner').val(plan.design_owner);
        $('#marketing_owner').val(plan.marketing_owner);
        $('#section_6_shortage').val(plan.section_6_shortage);
        $('#section_6_eol').val(plan.section_6_eol);
        $('#purchase_shortage').val(plan.purchase_shortage);
        $('#purchase_eol').val(plan.purchase_eol);
        $('#bop_shortage').val(plan.bop_shortage);
        $('#bop_eol').val(plan.bop_eol);
        $('#automation_status').val(plan.automation_status);
        $('#electrical_status').val(plan.electrical_status);
        $('#gear_box_status').val(plan.gear_box_status);
        $('#plan_status').val(plan.status);
        $('#plan_remarks').val(plan.remarks);
    }
    $('#planModal').modal('show');
}

function openDependencies(planId, dfNo) {
    dependencyPlanId = planId;
    $('#dependency_plan_id').val(planId);
    $('#dependencyDf').text(dfNo);
    $('#dependencyModal').modal('show');
    loadDependencies();
}

function openPlanHistory(planId) {
    $('#historyDfNo').text('Loading...');
    $('#planHistoryList').html('<p class="text-muted"><i class="fa fa-spinner fa-spin"></i> Loading update history...</p>');
    $('#planHistoryModal').modal('show');

    $.getJSON('<?php echo page_url; ?>Df_dispatch_plan/history/' + planId, function(response) {
        if (!response.status) {
            $('#planHistoryList').html('<div class="alert alert-danger">' + escapeHtml(response.message) + '</div>');
            return;
        }
        $('#historyDfNo').text(response.plan.df_no);
        if (!response.logs.length) {
            $('#planHistoryList').html('<div class="alert alert-info">No update history has been recorded yet.</div>');
            return;
        }

        var html = '';
        $.each(response.logs, function(_, log) {
            var userName = sentenceCase($.trim((log.first_name || '') + ' ' + (log.last_name || ''))) || ('User #' + log.changed_by);
            var fieldLabel = log.field_name ? String(log.field_name).replace(/_/g, ' ').replace(/\b\w/g, function(letter) { return letter.toUpperCase(); }) : 'Record';
            var oldValue = log.old_value == null || log.old_value === '' ? 'Empty' : log.old_value;
            var newValue = log.new_value == null || log.new_value === '' ? 'Empty' : log.new_value;
            html += '<div class="audit-log-item">';
            html += '<div class="audit-log-meta"><strong>' + escapeHtml(userName) + '</strong> · ' + escapeHtml(log.changed_on) + ' · ' + escapeHtml(log.action) + '</div>';
            html += '<div><strong>' + escapeHtml(fieldLabel) + '</strong></div>';
            if (log.action === 'Created') {
                html += '<span class="audit-value audit-new">' + escapeHtml(newValue) + '</span>';
            } else {
                html += '<span class="audit-value audit-old">' + escapeHtml(oldValue) + '</span> <i class="fa fa-long-arrow-right text-muted"></i> <span class="audit-value audit-new">' + escapeHtml(newValue) + '</span>';
            }
            html += '</div>';
        });
        $('#planHistoryList').html(html);
    }).fail(function() {
        $('#planHistoryList').html('<div class="alert alert-danger">Unable to load update history.</div>');
    });
}

function loadDependencies() {
    $('#dependencyList').html('<p class="text-muted">Loading dependencies...</p>');
    $.getJSON('<?php echo page_url; ?>Df_dispatch_plan/dependencies/' + dependencyPlanId, function(response) {
        if (!response.status) {
            $('#dependencyList').html('<div class="alert alert-danger">' + escapeHtml(response.message) + '</div>');
            return;
        }
        if (!response.dependencies.length) {
            $('#dependencyList').html('<div class="alert alert-info">No dependencies recorded for this DF.</div>');
            return;
        }
        var html = '';
        $.each(response.dependencies, function(_, item) {
            var owner = sentenceCase($.trim((item.owner_name || '') + ' ' + (item.owner_last_name || ''))) || 'Unassigned';
            var liveStatus = item.effective_status || item.status;
            html += '<div class="dependency-item ' + (liveStatus === 'Blocked' ? 'blocked' : '') + ' ' + (liveStatus === 'Closed' ? 'closed' : '') + '">';
            html += '<div class="row"><div class="col-md-8"><strong>' + escapeHtml(item.title) + '</strong>';
            if (item.task_management_item_id) {
                html += ' <a class="btn btn-xs btn-link" target="_blank" href="<?php echo page_url; ?>Task_management/view/' + item.task_management_item_id + '">' + escapeHtml(item.task_code || 'View task') + '</a>';
            }
            html += '<br>';
            var itemQty = item.item_qty ? 'Qty: ' + item.item_qty + ' · ' : '';
            var pendencyFrom = item.pendency_from_date ? 'Pending from: ' + item.pendency_from_date + ' · ' : '';
            html += '<small>' + escapeHtml(itemQty + pendencyFrom + item.department + ' · Owner: ' + owner + ' · ' + item.priority) + '</small></div>';
            html += '<div class="col-md-4"><select class="form-control input-sm dependency-status" data-id="' + item.id + '">';
            $.each(['Open','In Progress','Blocked','Closed'], function(_, status) {
                html += '<option' + (liveStatus === status ? ' selected' : '') + '>' + status + '</option>';
            });
            html += '</select></div></div></div>';
        });
        $('#dependencyList').html(html);
    });
}

$(document).on('change', '.dependency-status', function() {
    var select = $(this);
    $.post('<?php echo page_url; ?>Df_dispatch_plan/update_dependency', {
        dependency_id: select.data('id'), status: select.val()
    }, function(response) {
        if (!response.status) {
            alert(response.message);
        } else {
            loadDependencies();
        }
    }, 'json');
});

function escapeHtml(value) {
    return $('<div>').text(value == null ? '' : value).html();
}

function sentenceCase(value) {
    return String(value || '').toLowerCase().replace(/\b[a-z]/g, function(letter) { return letter.toUpperCase(); });
}

function showDispatchLoader() {
    $('#dispatchLoader').addClass('active');
}
</script>
<?php endif; ?>
</body>
</html>
