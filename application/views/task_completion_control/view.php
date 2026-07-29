<?php
if (!function_exists('taskControlSafe')) {
    function taskControlSafe($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('taskControlDate')) {
    function taskControlDate($value, $with_time = false)
    {
        $value = trim((string) $value);
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

if (!function_exists('taskControlStatusMeta')) {
    function taskControlStatusMeta($status)
    {
        $status = (int) $status;
        if ($status === 1) {
            return array('label' => 'Completed', 'class' => 'label-success');
        }
        if ($status === 2) {
            return array('label' => 'Pending For Approval', 'class' => 'label-warning');
        }

        return array('label' => 'Pending', 'class' => 'label-default');
    }
}

if (!function_exists('taskControlName')) {
    function taskControlName($row, $prefix)
    {
        $parts = array();
        foreach (array('title', 'first_name', 'last_name') as $field) {
            $key = $prefix . '_' . $field;
            if (!empty($row[$key])) {
                $parts[] = trim((string) $row[$key]);
            }
        }

        if (empty($parts)) {
            return '-';
        }

        return ucwords(strtolower(implode(' ', $parts)));
    }
}

if (!function_exists('taskControlHistoryAction')) {
    function taskControlHistoryAction($action_type)
    {
        $action_type = strtoupper(trim((string) $action_type));
        if ($action_type === 'ROLLED_BACK_TO_PENDING') {
            return 'Rolled Back To Pending';
        }
        if ($action_type === 'UPDATED_COMPLETION_DATE') {
            return 'Completion Date Updated';
        }
        if ($action_type === 'UPDATED_COMPLETED_TASK') {
            return 'Completed Task Edited';
        }

        return ucwords(strtolower(str_replace('_', ' ', $action_type)));
    }
}

$status_meta = taskControlStatusMeta(isset($task['task_status']) ? $task['task_status'] : 0);
$owner_name = taskControlName($task, 'owner');
$completed_by_name = taskControlName($task, 'completer');
$changed_by_name = taskControlName($task, 'changer');
$task_is_completed = isset($task['task_status']) && (int) $task['task_status'] === 1;
$last_changed_on = !empty($task['changedOn']) && $task['changedOn'] !== '0000-00-00 00:00:00' ? $task['changedOn'] : $task['task_completed_on'];
$last_changed_by = $changed_by_name !== '-' ? $changed_by_name : $completed_by_name;
$task_completed_input = (!empty($task['task_completed_on']) && $task['task_completed_on'] !== '0000-00-00 00:00:00')
    ? date('Y-m-d', strtotime($task['task_completed_on']))
    : date('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Completed Task Review</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body {
            background: #eef3f9;
        }

        .task-detail-hero {
            background: linear-gradient(135deg, #17365d 0%, #0f172a 100%);
            color: #fff;
            border-radius: 20px;
            padding: 24px 26px;
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.18);
            margin-bottom: 20px;
        }

        .task-detail-hero h3 {
            margin: 0 0 6px;
            font-size: 28px;
            font-weight: 700;
        }

        .task-detail-hero p {
            margin: 0;
            line-height: 1.8;
            max-width: 980px;
            opacity: 0.96;
        }

        .hero-actions {
            margin-top: 18px;
        }

        .hero-actions .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .panel-card {
            background: #fff;
            border: 1px solid #d8e1ec;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            margin-bottom: 22px;
            overflow: hidden;
        }

        .panel-head {
            padding: 18px 22px;
            border-bottom: 1px solid #e7edf4;
            background: #f7fbff;
        }

        .panel-head h4 {
            margin: 0;
            color: #17365d;
            font-size: 18px;
            font-weight: 700;
        }

        .panel-head p {
            margin: 6px 0 0;
            color: #64748b;
        }

        .panel-body {
            padding: 20px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .summary-card {
            background: #f8fbff;
            border: 1px solid #dce7f2;
            border-radius: 16px;
            padding: 16px 18px;
            min-height: 110px;
        }

        .summary-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        .summary-value {
            margin-top: 8px;
            color: #17365d;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.5;
        }

        .detail-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .detail-list li {
            padding: 10px 0;
            border-bottom: 1px solid #edf2f7;
        }

        .detail-list li:last-child {
            border-bottom: none;
        }

        .detail-key {
            display: block;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-value {
            display: block;
            margin-top: 4px;
            color: #17365d;
            font-weight: 600;
            line-height: 1.7;
        }

        .remarks-box {
            background: #f8fafc;
            border: 1px solid #d9e2ec;
            border-radius: 14px;
            padding: 16px;
            white-space: pre-wrap;
            color: #334155;
        }

        .rollback-card {
            border: 1px solid #f3c49a;
            background: #fff7ed;
        }

        .table > thead > tr > th {
            background: #17365d;
            color: #fff;
            border-color: #17365d !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .helper-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-center">Completed Task Review</h4>
                    </div>
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            </div>

            <div class="task-detail-hero">
                <h3><?php echo taskControlSafe(strtoupper($task['df_no'] . ' | ' . $task['task_name'])); ?></h3>
                <p>Use this page to review the completed-task record, edit controlled completion details, or roll the task back to pending with a captured audit trail. Every action from this module records who made the change and when it happened.</p>
                <div class="hero-actions">
                    <a href="<?php echo page_url; ?>Task_completion_control" class="btn btn-default">Back to Control List</a>
                    <a href="<?php echo page_url; ?>Dashboard" class="btn btn-default">Open Dashboard</a>
                    <span class="label <?php echo taskControlSafe($status_meta['class']); ?>" style="font-size:13px;padding:10px 14px;"><?php echo taskControlSafe($status_meta['label']); ?></span>
                </div>
            </div>

            <?php if (!$module_ready) { ?>
                <div class="alert alert-danger">
                    <strong>Migration pending:</strong> Please run <code><?php echo taskControlSafe($migration_file); ?></code> before using edit or rollback actions.
                </div>
            <?php } ?>

            <?php if (!$task_is_completed) { ?>
                <div class="alert alert-warning">
                    This task is no longer in completed status. The audit history remains available here, but edit and rollback actions are disabled because the task is currently <strong><?php echo taskControlSafe($status_meta['label']); ?></strong>.
                </div>
            <?php } ?>

            <div class="panel-card">
                <div class="panel-head">
                    <h4>Task snapshot</h4>
                    <p>Current state of the selected task record in the live system.</p>
                </div>
                <div class="panel-body">
                    <div class="summary-grid">
                        <div class="summary-card">
                            <div class="summary-label">DF Reference</div>
                            <div class="summary-value"><?php echo taskControlSafe(strtoupper($task['df_no'])); ?><br><small><?php echo taskControlSafe($task['df_description']); ?></small></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Department</div>
                            <div class="summary-value"><?php echo taskControlSafe(strtoupper($task['department'])); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Accountable Person</div>
                            <div class="summary-value"><?php echo taskControlSafe(strtoupper($owner_name)); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Completed By</div>
                            <div class="summary-value"><?php echo taskControlSafe(strtoupper($completed_by_name)); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Planned Completion</div>
                            <div class="summary-value"><?php echo taskControlSafe(taskControlDate($task['end_date'])); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Actual Completion</div>
                            <div class="summary-value"><?php echo taskControlSafe(taskControlDate($task['task_completed_on'], true)); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Last Updated</div>
                            <div class="summary-value"><?php echo taskControlSafe(taskControlDate($last_changed_on, true)); ?><br><small><?php echo taskControlSafe($last_changed_by); ?></small></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">DF Status</div>
                            <div class="summary-value"><?php echo (int) $task['df_status'] === 1 ? 'Closed' : 'Running'; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="panel-card">
                        <div class="panel-head">
                            <h4>Task details</h4>
                            <p>Current remarks and metadata tied to this task record.</p>
                        </div>
                        <div class="panel-body">
                            <ul class="detail-list">
                                <li>
                                    <span class="detail-key">Task Record ID</span>
                                    <span class="detail-value"><?php echo (int) $task['id']; ?></span>
                                </li>
                                <li>
                                    <span class="detail-key">Master Task</span>
                                    <span class="detail-value"><?php echo taskControlSafe(strtoupper($task['task_name'])); ?></span>
                                </li>
                                <li>
                                    <span class="detail-key">Task Status</span>
                                    <span class="detail-value"><?php echo taskControlSafe($status_meta['label']); ?></span>
                                </li>
                                <li>
                                    <span class="detail-key">Remarks</span>
                                    <span class="detail-value">
                                        <div class="remarks-box"><?php echo nl2br(taskControlSafe($task['remarks'])); ?></div>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="panel-card">
                        <div class="panel-head">
                            <h4>Edit completed task</h4>
                            <p>Use this form to correct the recorded completion date or the final remarks without reopening the task.</p>
                        </div>
                        <div class="panel-body">
                            <?php if (!$module_ready || !$task_is_completed) { ?>
                                <div class="helper-note">
                                    Edit controls are disabled because the history table is not ready or the task is no longer in completed status.
                                </div>
                            <?php } else { ?>
                                <form method="post" action="<?php echo page_url; ?>Task_completion_control/update/<?php echo (int) $task['id']; ?>">
                                    <div class="form-group">
                                        <label>Completion Date</label>
                                        <input type="date" name="task_completed_on" class="form-control" max="<?php echo date('Y-m-d'); ?>" value="<?php echo taskControlSafe($task_completed_input); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="6"><?php echo taskControlSafe($task['remarks']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Change Note</label>
                                        <textarea name="change_note" class="form-control" rows="3" placeholder="Optional note for the audit trail. Example: corrected completion date after review."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Controlled Changes</button>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-card rollback-card">
                <div class="panel-head">
                    <h4>Rollback task to pending</h4>
                    <p>Use rollback only when the completed task must be reopened in the live flow. This action changes the task status back to pending and records the reason in the audit log.</p>
                </div>
                <div class="panel-body">
                    <?php if (!$module_ready || !$task_is_completed) { ?>
                        <div class="helper-note">
                            Rollback is disabled because the history table is not ready or the task is already out of completed status.
                        </div>
                    <?php } else { ?>
                        <form method="post" action="<?php echo page_url; ?>Task_completion_control/rollback/<?php echo (int) $task['id']; ?>" onsubmit="return confirm('Are you sure you want to roll this completed task back to pending?');">
                            <div class="form-group">
                                <label>Rollback Reason</label>
                                <textarea name="rollback_note" class="form-control" rows="4" placeholder="Explain why this task is being reopened." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning">Roll Back To Pending</button>
                        </form>
                    <?php } ?>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head">
                    <h4>Audit history</h4>
                    <p>Every controlled edit and rollback performed through this module is listed here.</p>
                </div>
                <div class="panel-body">
                    <?php if (empty($history)) { ?>
                        <div class="helper-note">
                            No completed-task control history has been recorded for this task yet.
                        </div>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Changed On</th>
                                        <th>Action</th>
                                        <th>Changed By</th>
                                        <th>Status Change</th>
                                        <th>Completion Change</th>
                                        <th>Remarks Change</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $history_row) { ?>
                                        <tr>
                                            <td><?php echo taskControlSafe(taskControlDate($history_row['changed_on'], true)); ?></td>
                                            <td><?php echo taskControlSafe(taskControlHistoryAction($history_row['action_type'])); ?></td>
                                            <td><?php echo taskControlSafe($history_row['changed_by_name'] !== '' ? $history_row['changed_by_name'] : '-'); ?></td>
                                            <td>
                                                <?php echo taskControlSafe(taskControlStatusMeta($history_row['old_task_status'])['label']); ?>
                                                ->
                                                <?php echo taskControlSafe(taskControlStatusMeta($history_row['new_task_status'])['label']); ?>
                                            </td>
                                            <td>
                                                <?php echo taskControlSafe(taskControlDate($history_row['old_task_completed_on'], true)); ?>
                                                <br>
                                                ->
                                                <br>
                                                <?php echo taskControlSafe(taskControlDate($history_row['new_task_completed_on'], true)); ?>
                                            </td>
                                            <td>
                                                <strong>Old:</strong>
                                                <div class="remarks-box" style="margin-bottom:10px;"><?php echo nl2br(taskControlSafe($history_row['old_remarks'])); ?></div>
                                                <strong>New:</strong>
                                                <div class="remarks-box"><?php echo nl2br(taskControlSafe($history_row['new_remarks'])); ?></div>
                                            </td>
                                            <td><?php echo nl2br(taskControlSafe($history_row['action_note'])); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <footer class="footer text-right">
                <?php $this->load->view('common/footer'); ?>
            </footer>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
</body>
</html>
