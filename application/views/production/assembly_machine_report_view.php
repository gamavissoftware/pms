<?php defined('BASEPATH') or exit('No direct script access allowed');
$report_assembly_lines = isset($report_assembly_lines) ? $report_assembly_lines : $assembly_lines;
$df_shortages = isset($df_shortages) ? $df_shortages : array();
$total = count($machines);
$running = 0;
$with_updates = 0;
$progress_sum = 0;
foreach ($machines as $machine) {
    if ((int) $machine->is_active === 1) $running++;
    if (!empty($machine->log_id)) {
        $with_updates++;
        $progress_sum += (int) $machine->progress_percent;
    }
}
$average_progress = $with_updates > 0 ? round($progress_sum / $with_updates) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo html_escape($page_title); ?></title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f7fb; font-family: Poppins, sans-serif; color: #243447; }
        .report-shell { padding-top: 22px; padding-bottom: 30px; }
        .report-head { background: linear-gradient(135deg, #2563a8, #17324d); color: #fff; padding: 23px 25px; border-radius: 16px; box-shadow: 0 12px 28px rgba(23,50,77,.16); margin-bottom: 16px; }
        .report-head h2 { margin: 0 0 5px; font-size: 24px; font-weight: 700; color: #fff !important; }
        .report-head p { margin: 0; opacity: .88; font-size: 12px; color: #fff !important; }
        .report-head .btn { border-radius: 9px; margin-left: 7px; }
        .panel-card { background: #fff; border: 1px solid #e6ebf1; border-radius: 14px; box-shadow: 0 7px 20px rgba(35,52,70,.06); margin-bottom: 15px; }
        .filters { padding: 15px; }
        .filters label { color: #6d7f92; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
        .filters .form-control, .filters .btn { border-radius: 8px; }
        .kpi { padding: 15px 17px; min-height: 92px; }
        .kpi-label { color: #8090a1; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .kpi-value { font-size: 27px; font-weight: 700; color: #1d334a; line-height: 34px; }
        .kpi-note { color: #91a0ae; font-size: 10px; }
        .table-card { padding: 0 16px 15px; }
        .table-title { padding: 16px 0 12px; font-weight: 700; font-size: 16px; border-bottom: 1px solid #edf1f5; }
        .table thead th { background: #f7f9fc; color: #607286; font-size: 10px; text-transform: uppercase; white-space: nowrap; border-bottom: 1px solid #dce5ed; }
        .table td { vertical-align: middle !important; font-size: 12px; }
        .machine-name { font-weight: 700; color: #233b53; }
        .machine-hover-target { cursor: help; border-bottom: 1px dashed #7f93a7; }
        .sequence-handle { cursor: move; color: #8090a1; white-space: nowrap; }
        .sequence-handle i { margin-right: 5px; }
        .machine-row.dragging { opacity: .45; }
        .machine-row.drag-over { border-top: 3px solid #2f80ed; }
        .subtext { display: block; color: #8797a7; font-size: 10px; margin-top: 3px; }
        .df-no { color: #2364aa; font-weight: 700; }
        .shortage-report-cell { min-width: 270px; max-width: 380px; white-space: normal; }
        .shortage-group { border-left: 3px solid #f59e0b; background: #fff8e8; border-radius: 0 7px 7px 0; padding: 6px 8px; margin-bottom: 6px; }
        .shortage-group:last-child { margin-bottom: 0; }
        .shortage-category { color: #8a5800; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .shortage-number { display: inline-block; margin-left: 5px; padding: 1px 6px; border-radius: 10px; background: #fde6af; color: #7c4b00; font-size: 9px; font-weight: 700; }
        .shortage-report-items { margin: 4px 0 0; padding-left: 17px; }
        .shortage-report-items li { margin-bottom: 3px; color: #45586b; }
        .shortage-item-meta { display: block; color: #8493a2; font-size: 9px; }
        .shortage-clear { color: #15803d; font-weight: 600; }
        .progress-track { width: 105px; height: 7px; border-radius: 8px; background: #e9eef4; overflow: hidden; margin-top: 5px; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #2f80ed, #56cc9d); border-radius: 8px; }
        .photo-thumb { width: 58px; height: 46px; object-fit: cover; border-radius: 8px; border: 1px solid #dce5ed; }
        .photo-empty { width: 58px; height: 46px; border-radius: 8px; background: #eef2f6; color: #94a1af; display: flex; align-items: center; justify-content: center; }
        .remarks-cell { max-width: 260px; white-space: normal; color: #485c70; }
        .status { display: inline-block; border-radius: 14px; padding: 4px 8px; font-size: 9px; font-weight: 700; }
        .status-running { background: #dcfce7; color: #15803d; }
        .status-closed { background: #e9eef4; color: #536577; }
        .btn-update { border-radius: 8px; background: #eef4ff; color: #2463ad; }
        .line-update-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 12px; margin-bottom: 15px; }
        .line-update-card { background: #fff; border: 1px solid #e1e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 7px 18px rgba(35,52,70,.06); }
        .line-update-photo { height: 145px; background: #edf2f7; position: relative; }
        .line-update-photo img { width: 100%; height: 100%; object-fit: cover; }
        .line-update-grid.single-line .line-update-photo { height: auto; min-height: 145px; background: #edf2f7; }
        .line-update-grid.single-line .line-update-photo img { display: block; width: 100%; height: auto; max-height: 520px; object-fit: contain; }
        .line-update-placeholder { height: 100%; display: flex; align-items: center; justify-content: center; color: #9aa8b6; font-size: 28px; }
        .line-update-body { padding: 13px 14px; }
        .line-update-head { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 8px; }
        .line-update-name { font-size: 14px; font-weight: 700; color: #213a53; }
        .line-progress-pill { background: #e8f4ff; color: #2463ad; border-radius: 14px; padding: 4px 8px; font-size: 10px; font-weight: 700; white-space: nowrap; }
        .line-update-remark { color: #53677a; font-size: 11px; line-height: 17px; min-height: 34px; }
        .line-update-meta { color: #91a0ae; font-size: 9px; margin-top: 8px; }
        .line-update-action { margin-top: 10px; }
        .modal-content { border: 0; border-radius: 15px; overflow: hidden; }
        .modal-header { background: linear-gradient(135deg, #2563a8, #17324d); color: #fff; }
        .modal-header .close { color: #fff; opacity: .9; }
        .modal-body .form-control { border-radius: 9px; }
        .machine-info-dialog { width: 96%; max-width: 1500px; margin: 20px auto; }
        .machine-info-grid { display: grid; grid-template-columns: minmax(420px, 1.15fr) minmax(360px, .85fr); gap: 18px; }
        .machine-info-section { border: 1px solid #e1e8f0; border-radius: 10px; padding: 13px; background: #fff; }
        .machine-info-section h5 { margin: 0 0 11px; color: #213a53; font-weight: 700; }
        .machine-floor-photo { width: 100%; max-height: 470px; object-fit: contain; border-radius: 9px; background: #edf2f7; }
        .machine-floor-empty { min-height: 220px; display: flex; align-items: center; justify-content: center; background: #edf2f7; border-radius: 9px; color: #91a0ae; }
        .machine-floor-meta { margin-top: 8px; color: #718295; font-size: 11px; }
        #infoShortages .shortage-report-cell { max-width: none; width: 100%; }
        #infoShortages .shortage-group { margin-bottom: 8px; }
        @media (max-width: 767px) { .report-head .text-right { text-align: left; margin-top: 12px; } }
        @media (max-width: 900px) { .machine-info-dialog { width: 94%; } .machine-info-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<div class="wrapper">
    <div class="container-fluid report-shell">
        <div class="report-head">
            <div class="row">
                <div class="col-md-8">
                    <h2><?php echo html_escape($page_title); ?></h2>
                    <p>Machine status, latest assembly photograph, progress percentage and supervisor remarks in one report.</p>
                </div>
                <div class="col-md-4 text-right">
                    <a href="<?php echo page_url; ?>Masters/manage_jobs" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Job Dashboard</a>
                    <button type="button" onclick="window.print()" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
        </div>

        <div class="panel-card filters">
            <form method="get" action="<?php echo page_url; ?>Masters/assembly_machine_report">
                <div class="row">
                    <div class="col-md-5">
                        <label>Assembly Line</label>
                        <select name="line_id" class="form-control">
                            <option value="0">All Assembly Lines</option>
                            <?php foreach ($assembly_lines as $line): ?>
                                <option value="<?php echo (int) $line->line_id; ?>" <?php echo $selected_line_id === (int) $line->line_id ? 'selected' : ''; ?>>
                                    <?php echo html_escape(ucwords(strtolower($line->line_name))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Job Status</label>
                        <select name="status" class="form-control">
                            <option value="running" <?php echo $selected_status === 'running' ? 'selected' : ''; ?>>Running</option>
                            <option value="closed" <?php echo $selected_status === 'closed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="all" <?php echo $selected_status === 'all' ? 'selected' : ''; ?>>All Jobs</option>
                        </select>
                    </div>
                    <div class="col-md-4" style="padding-top:21px;">
                        <button class="btn btn-primary"><i class="fa fa-filter"></i> Apply</button>
                        <a href="<?php echo page_url; ?>Masters/assembly_machine_report" class="btn btn-default">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-title" style="border:0; padding-top:2px;">Latest Assembly Line Updates</div>
        <div class="line-update-grid<?php echo (int) $selected_line_id > 0 ? ' single-line' : ''; ?>">
            <?php foreach ($report_assembly_lines as $line): ?>
                <?php $line_update = isset($assembly_line_updates[(int) $line->line_id]) ? $assembly_line_updates[(int) $line->line_id] : null; ?>
                <div class="line-update-card">
                    <div class="line-update-photo">
                        <?php if (!empty($line_update->image_path)): ?>
                            <a href="#" class="image-popup-trigger" data-image-src="<?php echo uploadsurl.'assembly_line_progress/'.rawurlencode($line_update->image_path); ?>" data-image-title="<?php echo html_escape(ucwords(strtolower($line->line_name))); ?> update">
                                <img src="<?php echo uploadsurl.'assembly_line_progress/'.rawurlencode($line_update->image_path); ?>" alt="Assembly line update">
                            </a>
                        <?php else: ?>
                            <div class="line-update-placeholder"><i class="fa fa-picture-o"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="line-update-body">
                        <div class="line-update-head">
                            <span class="line-update-name"><?php echo html_escape(ucwords(strtolower($line->line_name))); ?></span>
                            <span class="line-progress-pill"><?php echo !empty($line_update) ? (int) $line_update->progress_percent.'%' : 'No Update'; ?></span>
                        </div>
                        <div class="line-update-remark">
                            <?php echo !empty($line_update->remarks) ? nl2br(html_escape($line_update->remarks)) : 'Upload the first assembly line picture and progress remarks.'; ?>
                        </div>
                        <?php if (!empty($line_update)): ?>
                            <div class="line-update-meta">
                                <?php echo date('d M Y, h:i A', strtotime($line_update->created_at)); ?>
                                · <?php echo html_escape(ucwords(strtolower(trim($line_update->updated_by_name)))); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($can_update): ?>
                            <div class="line-update-action">
                                <button type="button" class="btn btn-primary btn-sm" onclick='openLineUpdateModal(<?php echo (int) $line->line_id; ?>, <?php echo json_encode((string) $line->line_name, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>)'>
                                    <i class="fa fa-camera"></i> Upload Line Update
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <div class="col-md-3 col-xs-6"><div class="panel-card kpi"><div class="kpi-label">Machines</div><div class="kpi-value"><?php echo $total; ?></div><div class="kpi-note">In selected assembly scope</div></div></div>
            <div class="col-md-3 col-xs-6"><div class="panel-card kpi"><div class="kpi-label">Running</div><div class="kpi-value"><?php echo $running; ?></div><div class="kpi-note">Active assembly jobs</div></div></div>
            <div class="col-md-3 col-xs-6"><div class="panel-card kpi"><div class="kpi-label">Updated</div><div class="kpi-value"><?php echo $with_updates; ?></div><div class="kpi-note">Machines with progress logs</div></div></div>
            <div class="col-md-3 col-xs-6"><div class="panel-card kpi"><div class="kpi-label">Average Progress</div><div class="kpi-value"><?php echo $average_progress; ?>%</div><div class="kpi-note">Latest reported progress</div></div></div>
        </div>

        <div class="panel-card table-card">
            <div class="table-title clearfix">
                <span>Assembly Machine Register</span>
                <?php if ($can_update && (int) $selected_line_id > 0 && !empty($machines)): ?>
                    <button type="button" id="saveMachineSequence" class="btn btn-primary btn-sm pull-right"><i class="fa fa-save"></i> Save Sequence</button>
                    <span class="subtext pull-right" style="margin:5px 12px 0 0;">Drag rows into the required order</span>
                <?php elseif ($can_update && (int) $selected_line_id === 0): ?>
                    <span class="subtext pull-right">Select one assembly line to arrange machines</span>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <?php if ((int) $selected_line_id > 0): ?><th>Sequence</th><?php endif; ?>
                            <th>Assembly / Machine</th>
                            <th>DF</th>
                            <th>DF Shortages</th>
                            <th>Supervisor</th>
                            <th>Progress</th>
                            <th>Latest Picture</th>
                            <th>Progress Remarks</th>
                            <th>Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="machineSequenceBody">
                    <?php if (!empty($machines)): foreach ($machines as $machine): ?>
                        <?php
                        $machine_display_name = trim((string) $machine->machine_name);
                        if (!preg_match('/^DF\b/i', $machine_display_name)) {
                            $machine_display_name = 'DF ' . $machine_display_name;
                        }
                        $shortage_key = strtoupper(preg_replace('/[^A-Z0-9]/i', '', preg_replace('/^DF[\s\-_]*/i', '', trim((string) $machine->df_number))));
                        $machine_shortages = isset($df_shortages[$shortage_key]) ? $df_shortages[$shortage_key] : null;
                        $machine_line_update = isset($assembly_line_updates[(int) $machine->line_id]) ? $assembly_line_updates[(int) $machine->line_id] : null;
                        ?>
                        <tr class="machine-row" data-assignment-id="<?php echo (int) $machine->assignment_id; ?>" <?php echo ($can_update && (int) $selected_line_id > 0) ? 'draggable="true"' : ''; ?>>
                            <?php if ((int) $selected_line_id > 0): ?>
                                <td class="sequence-handle"><i class="fa fa-bars"></i><span class="sequence-number"><?php echo !empty($machine->sequence_no) ? (int) $machine->sequence_no : ''; ?></span></td>
                            <?php endif; ?>
                            <td>
                                <span class="machine-name machine-hover-target"
                                    data-machine="<?php echo html_escape($machine_display_name); ?>"
                                    data-line="<?php echo html_escape(ucwords(strtolower($machine->line_name))); ?>"
                                    data-code="<?php echo html_escape((string) $machine->machine_code); ?>"
                                    data-model="<?php echo html_escape((string) $machine->model_no); ?>"
                                    data-df="<?php echo html_escape((string) $machine->df_number); ?>"
                                    data-release="<?php echo !empty($machine->release_date) ? date('d M Y', strtotime($machine->release_date)) : '--'; ?>"
                                    data-supervisor="<?php echo html_escape(ucwords(strtolower((string) $machine->supervisor_name))); ?>"
                                    data-status="<?php echo (int) $machine->is_active === 1 ? 'Running' : 'Completed'; ?>"
                                    data-progress="<?php echo !empty($machine->log_id) ? (int) $machine->progress_percent.'%' : '--'; ?>"
                                    data-remarks="<?php echo html_escape((string) $machine->progress_remarks); ?>"
                                    data-allocation-remarks="<?php echo html_escape((string) $machine->allocation_remarks); ?>"
                                    data-updated="<?php echo !empty($machine->progress_updated_at) ? date('d M Y, h:i A', strtotime($machine->progress_updated_at)) : '--'; ?>"
                                    data-updated-by="<?php echo html_escape(ucwords(strtolower(trim((string) $machine->progress_updated_by)))); ?>"
                                    data-floor-image="<?php echo !empty($machine_line_update->image_path) ? uploadsurl.'assembly_line_progress/'.rawurlencode($machine_line_update->image_path) : ''; ?>"
                                    data-floor-uploaded="<?php echo !empty($machine_line_update->created_at) ? date('d M Y, h:i A', strtotime($machine_line_update->created_at)) : ''; ?>"
                                    data-floor-uploaded-by="<?php echo !empty($machine_line_update->updated_by_name) ? html_escape(ucwords(strtolower(trim((string) $machine_line_update->updated_by_name)))) : ''; ?>"
                                    data-timeline="<?php echo page_url.'Masters/view_job_timeline/'.(int) $machine->assignment_id; ?>"><?php echo html_escape($machine_display_name); ?></span>
                                <span class="subtext"><?php echo html_escape(ucwords(strtolower($machine->line_name))); ?><?php echo !empty($machine->machine_code) ? ' · '.$machine->machine_code : ''; ?></span>
                            </td>
                            <td><span class="df-no"><?php echo html_escape($machine->df_number); ?></span><span class="subtext"><?php echo date('d M Y', strtotime($machine->release_date)); ?></span></td>
                            <td class="shortage-report-cell">
                                <?php if (!empty($machine_shortages)): ?>
                                    <?php
                                    $shortage_categories = array('Section 6', 'Section 6 EOL', 'Purchase', 'Purchase EOL', 'BOP', 'BOP EOL');
                                    $has_shortage = false;
                                    foreach ($shortage_categories as $shortage_category):
                                        $shortage_number = isset($machine_shortages['numbers'][$shortage_category]) ? trim((string) $machine_shortages['numbers'][$shortage_category]) : '';
                                        $category_items = array_values(array_filter($machine_shortages['items'], function ($item) use ($shortage_category) {
                                            return isset($item['category']) && $item['category'] === $shortage_category;
                                        }));
                                        if ($shortage_number === '' && empty($category_items)) continue;
                                        $has_shortage = true;
                                    ?>
                                        <div class="shortage-group">
                                            <div class="shortage-category">
                                                <?php echo html_escape($shortage_category); ?>
                                                <span class="shortage-number">No.: <?php echo html_escape($shortage_number !== '' ? $shortage_number : count($category_items)); ?></span>
                                            </div>
                                            <?php if (!empty($category_items)): ?><ul class="shortage-report-items">
                                                <?php foreach ($category_items as $shortage_item): ?>
                                                    <li>
                                                        <?php if (!empty($shortage_item['task_id'])): ?><a target="_blank" href="<?php echo page_url . 'Task_management/view/' . (int) $shortage_item['task_id']; ?>"><?php endif; ?>
                                                        <?php echo html_escape($shortage_item['title']); ?>
                                                        <?php if (!empty($shortage_item['task_id'])): ?></a><?php endif; ?>
                                                        <span class="shortage-item-meta"><?php echo html_escape(trim((!empty($shortage_item['item_qty']) ? 'Qty: ' . $shortage_item['item_qty'] : '') . (!empty($shortage_item['pendency_from_date']) ? ' · Pending from: ' . date('d M Y', strtotime($shortage_item['pendency_from_date'])) : '') . (!empty($shortage_item['owner_name']) ? ' · ' . $shortage_item['owner_name'] : '') . (!empty($shortage_item['task_code']) ? ' · ' . $shortage_item['task_code'] : '') . (!empty($shortage_item['status']) ? ' · ' . $shortage_item['status'] : ''))); ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul><?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (!$has_shortage): ?><span class="shortage-clear"><i class="fa fa-check-circle"></i> No shortage</span><?php endif; ?>
                                <?php else: ?>
                                    <span class="shortage-clear"><i class="fa fa-check-circle"></i> No shortage</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo html_escape(ucwords(strtolower($machine->supervisor_name))); ?></td>
                            <td>
                                <strong><?php echo !empty($machine->log_id) ? (int) $machine->progress_percent.'%' : '--'; ?></strong>
                                <div class="progress-track"><div class="progress-fill" style="width:<?php echo !empty($machine->log_id) ? min(100, (int) $machine->progress_percent) : 0; ?>%"></div></div>
                                <span class="status <?php echo (int) $machine->is_active === 1 ? 'status-running' : 'status-closed'; ?>"><?php echo (int) $machine->is_active === 1 ? 'Running' : 'Completed'; ?></span>
                            </td>
                            <td>
                                <?php if (!empty($machine->image_path)): ?>
                                    <a href="#" class="image-popup-trigger" data-image-src="<?php echo uploadsurl.'daily_logs/'.rawurlencode($machine->image_path); ?>" data-image-title="<?php echo html_escape($machine_display_name); ?> latest picture"><img class="photo-thumb" src="<?php echo uploadsurl.'daily_logs/'.rawurlencode($machine->image_path); ?>" alt="Assembly progress"></a>
                                <?php else: ?><span class="photo-empty"><i class="fa fa-camera"></i></span><?php endif; ?>
                            </td>
                            <td class="remarks-cell"><?php echo !empty($machine->progress_remarks) ? nl2br(html_escape($machine->progress_remarks)) : '<span class="text-muted">No update yet</span>'; ?></td>
                            <td>
                                <?php if (!empty($machine->progress_updated_at)): ?>
                                    <?php echo date('d M Y', strtotime($machine->progress_updated_at)); ?>
                                    <span class="subtext"><?php echo html_escape(ucwords(strtolower(trim($machine->progress_updated_by)))); ?></span>
                                <?php else: ?>--<?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo page_url.'Masters/view_job_timeline/'.(int) $machine->assignment_id; ?>" class="btn btn-default btn-sm" title="Timeline"><i class="fa fa-history"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="<?php echo (int) $selected_line_id > 0 ? 10 : 9; ?>" class="text-center text-muted" style="padding:35px;">No assembly machines match the selected filters.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="imagePreviewModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="imagePreviewTitle">Machine Picture</h4></div>
            <div class="modal-body text-center" style="background:#f4f7fb;"><img id="imagePreview" src="" alt="Machine preview" style="max-width:100%; max-height:72vh; border-radius:8px;"></div>
        </div>
    </div>
</div>

<div id="machineInfoModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog machine-info-dialog">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-cogs"></i> <span id="machineInfoTitle"></span></h4></div>
            <div class="modal-body" style="background:#f4f7fb;">
                <div class="machine-info-grid">
                    <div>
                        <div class="machine-info-section">
                            <h5>Machine Information</h5>
                            <table class="table table-bordered" style="margin-bottom:0;">
                                <tr><th>Assembly Line</th><td id="infoLine"></td></tr>
                                <tr><th>Machine Code / Model</th><td id="infoCodeModel"></td></tr>
                                <tr><th>DF No.</th><td id="infoDf"></td></tr>
                                <tr><th>Release Date</th><td id="infoRelease"></td></tr>
                                <tr><th>Supervisor</th><td id="infoSupervisor"></td></tr>
                                <tr><th>Status / Progress</th><td id="infoStatusProgress"></td></tr>
                                <tr><th>Progress Remarks</th><td id="infoRemarks"></td></tr>
                                <tr><th>Allocation Remarks</th><td id="infoAllocationRemarks"></td></tr>
                                <tr><th>Last Updated</th><td id="infoUpdated"></td></tr>
                            </table>
                        </div>
                        <div class="machine-info-section" style="margin-top:15px;">
                            <h5>DF Shortages</h5>
                            <div id="infoShortages"></div>
                        </div>
                    </div>
                    <div class="machine-info-section">
                        <h5>Latest Assembly Floor Picture</h5>
                        <a href="#" id="infoFloorImageLink" class="image-popup-trigger">
                            <img id="infoFloorImage" class="machine-floor-photo" src="" alt="Latest assembly floor">
                        </a>
                        <div id="infoFloorImageEmpty" class="machine-floor-empty"><span><i class="fa fa-picture-o"></i> No floor picture uploaded</span></div>
                        <div id="infoFloorImageMeta" class="machine-floor-meta"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><a id="infoTimeline" class="btn btn-primary" href="#"><i class="fa fa-history"></i> View Full Timeline</a><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<div id="lineUpdateModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="lineUpdateForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-camera"></i> Assembly Line Update</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="line_id" id="lineId">
                    <div class="alert alert-info" id="selectedLine"></div>
                    <div class="form-group">
                        <label>Assembly Line Picture <span class="text-danger">*</span> <small class="text-muted">(one JPG/PNG, up to 5 MB)</small></label>
                        <input type="file" name="assembly_image" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Progress Percentage</label>
                        <select name="progress_percent" class="form-control">
                            <option value="10">10% - Started</option><option value="25">25% - Assembly in Progress</option>
                            <option value="50">50% - Half Complete</option><option value="75">75% - Advanced Assembly</option>
                            <option value="90">90% - Final Assembly</option><option value="100">100% - Assembly Complete</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Progress Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" id="lineRemarks" class="form-control" rows="4" required placeholder="Describe overall line progress, machines covered, pending work or blockers..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveLineUpdate" class="btn btn-primary"><i class="fa fa-upload"></i> Save Line Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script>
$(document).on('click', '.image-popup-trigger', function (event) {
    event.preventDefault();
    $('#imagePreview').attr('src', $(this).data('image-src'));
    $('#imagePreviewTitle').text($(this).data('image-title') || 'Machine Picture');
    $('#imagePreviewModal').modal('show');
});
$('#imagePreviewModal').on('hidden.bs.modal', function () { $('#imagePreview').attr('src', ''); });

var machineHoverTimer = null;
$('.machine-hover-target').on('mouseenter', function () {
    var machine = $(this);
    clearTimeout(machineHoverTimer);
    machineHoverTimer = setTimeout(function () {
        $('#machineInfoTitle').text(machine.data('machine'));
        $('#infoLine').text(machine.data('line') || '--');
        $('#infoCodeModel').text([machine.data('code'), machine.data('model')].filter(Boolean).join(' / ') || '--');
        $('#infoDf').text(machine.data('df') || '--');
        $('#infoRelease').text(machine.data('release') || '--');
        $('#infoSupervisor').text(machine.data('supervisor') || '--');
        $('#infoStatusProgress').text((machine.data('status') || '--') + ' / ' + (machine.data('progress') || '--'));
        $('#infoRemarks').text(machine.data('remarks') || 'No update yet');
        $('#infoAllocationRemarks').text(machine.data('allocation-remarks') || '--');
        $('#infoUpdated').text((machine.data('updated') || '--') + (machine.data('updated-by') ? ' · ' + machine.data('updated-by') : ''));
        $('#infoShortages').html(machine.closest('tr').find('.shortage-report-cell').first().clone().removeClass('shortage-report-cell').html());
        var floorImage = machine.data('floor-image');
        if (floorImage) {
            $('#infoFloorImage').attr('src', floorImage).show();
            $('#infoFloorImageLink').data('image-src', floorImage).data('image-title', (machine.data('line') || 'Assembly floor') + ' latest picture').show();
            $('#infoFloorImageEmpty').hide();
            $('#infoFloorImageMeta').text('Uploaded ' + (machine.data('floor-uploaded') || '--') + (machine.data('floor-uploaded-by') ? ' · ' + machine.data('floor-uploaded-by') : '')).show();
        } else {
            $('#infoFloorImage').attr('src', '').hide();
            $('#infoFloorImageLink').hide();
            $('#infoFloorImageEmpty').show();
            $('#infoFloorImageMeta').text('').hide();
        }
        $('#infoTimeline').attr('href', machine.data('timeline'));
        $('#machineInfoModal').modal('show');
    }, 400);
}).on('mouseleave', function () { clearTimeout(machineHoverTimer); });

var draggedMachineRow = null;
function refreshSequenceNumbers() {
    $('#machineSequenceBody .machine-row').each(function (index) { $(this).find('.sequence-number').text(index + 1); });
}
$('#machineSequenceBody').on('dragstart', '.machine-row', function (event) {
    draggedMachineRow = this;
    $(this).addClass('dragging');
    event.originalEvent.dataTransfer.effectAllowed = 'move';
}).on('dragover', '.machine-row', function (event) {
    event.preventDefault();
    if (this !== draggedMachineRow) $(this).addClass('drag-over');
}).on('dragleave', '.machine-row', function () { $(this).removeClass('drag-over');
}).on('drop', '.machine-row', function (event) {
    event.preventDefault();
    if (draggedMachineRow && this !== draggedMachineRow) {
        var box = this.getBoundingClientRect();
        if (event.originalEvent.clientY < box.top + box.height / 2) $(this).before(draggedMachineRow);
        else $(this).after(draggedMachineRow);
        refreshSequenceNumbers();
    }
    $(this).removeClass('drag-over');
}).on('dragend', '.machine-row', function () {
    $('#machineSequenceBody .machine-row').removeClass('dragging drag-over');
    draggedMachineRow = null;
});
refreshSequenceNumbers();

$('#saveMachineSequence').on('click', function () {
    var assignmentIds = $('#machineSequenceBody .machine-row').map(function () { return $(this).data('assignment-id'); }).get();
    var button = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving');
    $.ajax({
        url: '<?php echo page_url; ?>Masters/save_assembly_machine_sequence',
        type: 'POST', dataType: 'json',
        data: { line_id: <?php echo (int) $selected_line_id; ?>, assignment_ids: assignmentIds }
    }).done(function (response) {
        if (response.status == 1) window.location.reload();
        else { alert(response.message || 'Unable to save sequence.'); button.prop('disabled', false).html('<i class="fa fa-save"></i> Save Sequence'); }
    }).fail(function () {
        alert('Server error while saving the sequence.');
        button.prop('disabled', false).html('<i class="fa fa-save"></i> Save Sequence');
    });
});

function openLineUpdateModal(lineId, lineName) {
    $('#lineUpdateForm')[0].reset();
    $('#lineId').val(lineId);
    $('#selectedLine').text(lineName);
    $('#lineUpdateModal').modal('show');
}
$('#lineUpdateForm').on('submit', function (event) {
    event.preventDefault();
    if ($.trim($('#lineRemarks').val()) === '') {
        alert('Please enter progress remarks.');
        return;
    }
    var button = $('#saveLineUpdate');
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving');
    $.ajax({
        url: '<?php echo page_url; ?>Masters/save_assembly_line_progress',
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        dataType: 'json'
    }).done(function (response) {
        if (response.status == 1) {
            window.location.reload();
        } else {
            alert(response.message || 'Unable to save progress.');
            button.prop('disabled', false).html('<i class="fa fa-upload"></i> Save Line Update');
        }
    }).fail(function () {
        alert('Server error while saving the update.');
        button.prop('disabled', false).html('<i class="fa fa-upload"></i> Save Line Update');
    });
});
</script>
</body>
</html>
