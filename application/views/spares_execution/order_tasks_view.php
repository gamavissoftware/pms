<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$workflow_labels = array(
    'CONSUMABLE' => 'Consumable',
    'CRITICAL' => 'Critical',
    'CONS_CRITICAL' => 'Consumable + Critical',
    'CUSTOM_CHANGEOVER' => 'Custom Engg - Changeover',
    'CUSTOM_SPEED_UPGRADATION' => 'Custom Engg - Speed Upgradation',
    'IN_STOCK' => 'Legacy - In Stock',
    'STANDARD' => 'Legacy - Standard Procurement',
    'CUSTOM' => 'Legacy - Custom Production',
);
$focus_task_id = !empty($focus_task_id) ? (int) $focus_task_id : 0;
$focus_extension_request_id = !empty($focus_extension_request_id) ? (int) $focus_extension_request_id : 0;
$focus_update_id = !empty($focus_update_id) ? (int) $focus_update_id : 0;
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
        body { background: #f5f7fb; }
        .card-box { border-radius: 10px; border: 1px solid #e8edf3; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .summary-card { background: #fff; border: 1px solid #e8edf3; border-radius: 10px; padding: 18px; margin-bottom: 20px; }
        .summary-title { font-size: 12px; color: #7f8a9a; text-transform: uppercase; }
        .summary-value { font-size: 20px; font-weight: 700; color: #243447; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .status-pending { background: #edf2f7; color: #4a5568; }
        .status-open { background: #e7f2ff; color: #1e5aa7; }
        .status-in-progress { background: #fff4dd; color: #9a6700; }
        .status-completed { background: #e4f9ef; color: #18794e; }
        .status-blocked { background: #fdecec; color: #b42318; }
        .status-on-hold { background: #f1edff; color: #6b46c1; }
        .status-cancelled { background: #f5f5f5; color: #6c757d; }
        .task-update-row { background: #fbfcfe; }
        .focus-highlight td,
        .focus-highlight th { background: #fff9db !important; }
        .focus-highlight { box-shadow: inset 4px 0 0 #f59e0b; }
        @media print {
            #topnav,
            .page-title-box .btn-group,
            .task-update-row,
            .alert,
            .btn,
            form { display: none !important; }
            body { background: #fff; }
            .wrapper, .container-fluid { padding: 0; margin: 0; }
            .card-box, .summary-card { box-shadow: none; }
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
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-dashboard"></i> Execution Dashboard</a>
                            <a href="<?php echo page_url; ?>Spares_execution/sf_form/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-purple waves-effect waves-light"><i class="fa fa-file-text-o"></i> SF Form</a>
                            <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-info waves-effect waves-light"><i class="fa fa-cogs"></i> MRP Report</a>
                            <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-info waves-effect waves-light"><i class="fa fa-bar-chart"></i> Gantt Chart</a>
                            <a href="<?php echo page_url; ?>Spares_execution/export_order_tracker/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-success waves-effect waves-light"><i class="fa fa-download"></i> Export Tracker CSV</a>
                            <a href="#" onclick="window.print(); return false;" class="btn btn-default waves-effect waves-light"><i class="fa fa-print"></i> Print</a>
                            <a href="<?php echo page_url; ?>Spares_execution/department_tasks" class="btn btn-default waves-effect waves-light"><i class="fa fa-users"></i> My Department</a>
                            <a href="<?php echo page_url; ?>Spares_execution/my_tasks" class="btn btn-success waves-effect waves-light"><i class="fa fa-check-square-o"></i> My Tasks</a>
                            <?php if (!empty($can_manage_schedule)): ?>
                                <a href="<?php echo page_url; ?>Spares_execution/edit_schedule/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-warning waves-effect waves-light"><i class="fa fa-calendar"></i> Edit Schedule</a>
                            <?php endif; ?>
                            <a href="<?php echo page_url; ?>Spares_execution/task_master" class="btn btn-primary waves-effect waves-light">Task Master</a>
                        </div>
                        <h4 class="page-title">Spares Execution Tracker</h4>
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
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">Workflow</div>
                        <div class="summary-value"><?php echo htmlspecialchars(isset($workflow_labels[$execution_order->workflow_type]) ? $workflow_labels[$execution_order->workflow_type] : $execution_order->workflow_type); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">Commit Date</div>
                        <div class="summary-value"><?php echo date('d M Y', strtotime($execution_order->commit_date)); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">Execution Status</div>
                        <div class="summary-value"><?php echo htmlspecialchars($execution_order->execution_status); ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">Total Tasks</div>
                        <div class="summary-value"><?php echo (int) $task_counters->total_tasks; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">Completed</div>
                        <div class="summary-value"><?php echo (int) $task_counters->completed_tasks; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">In Progress</div>
                        <div class="summary-value"><?php echo (int) $task_counters->in_progress_tasks; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-title">Overdue</div>
                        <div class="summary-value"><?php echo (int) $task_counters->overdue_tasks; ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">Priority</div>
                        <div class="summary-value"><?php echo htmlspecialchars($execution_order->priority); ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">Open</div>
                        <div class="summary-value"><?php echo (int) $task_counters->open_tasks; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">Pending</div>
                        <div class="summary-value"><?php echo (int) $task_counters->pending_tasks; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">Blocked</div>
                        <div class="summary-value"><?php echo (int) $task_counters->blocked_tasks; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">On Hold</div>
                        <div class="summary-value"><?php echo (int) $task_counters->on_hold_tasks; ?></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="summary-card">
                        <div class="summary-title">Cancelled</div>
                        <div class="summary-value"><?php echo (int) $task_counters->cancelled_tasks; ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="summary-card">
                        <div class="summary-title">Scheduling Notes</div>
                        <div class="summary-value" style="font-size: 14px; font-weight: 500;">
                            <?php echo !empty($execution_order->schedule_notes) ? nl2br(htmlspecialchars($execution_order->schedule_notes)) : '<span class="text-muted">No schedule notes added.</span>'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($can_manage_schedule)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-box">
                            <h4 class="m-t-0 header-title"><b>Order Controls</b></h4>
                            <p class="text-muted">Use this when the full execution order needs to be paused, resumed, or cancelled in one action.</p>
                            <form method="post" action="<?php echo page_url; ?>Spares_execution/bulk_order_action">
                                <input type="hidden" name="order_id" value="<?php echo (int) $order_snapshot->order_id; ?>">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Action</label>
                                            <select name="action_key" class="form-control">
                                                <option value="hold_remaining">Put Remaining Tasks On Hold</option>
                                                <option value="resume_held">Resume Held Tasks</option>
                                                <option value="cancel_remaining">Cancel Remaining Tasks</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <input type="text" name="remarks" class="form-control" placeholder="Explain why this order-wide action is needed.">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-warning">Apply Action</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Order Task Tracking</b></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Seq</th>
                                        <th>Task</th>
                                        <th>Department</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                        <th>Planned Window</th>
                                        <th>Actual Finish</th>
                                        <th>Last Activity</th>
                                        <th>Dependency</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tasks as $task): ?>
                                        <?php $status_class = 'status-' . strtolower(str_replace(' ', '-', $task->task_status)); ?>
                                        <tr id="task-row-<?php echo (int) $task->execution_task_id; ?>">
                                            <td><?php echo (int) $task->sequence_no; ?></td>
                                            <td>
                                                <b><?php echo htmlspecialchars($task->task_name); ?></b><br>
                                                <small><?php echo htmlspecialchars($task->task_code); ?></small>
                                            </td>
                                            <td><?php echo !empty($task->department) ? htmlspecialchars($task->department) : '<span class="text-muted">Not set</span>'; ?></td>
                                            <td>
                                                <?php
                                                if (!empty($task->first_name)) {
                                                    echo htmlspecialchars(trim($task->title . ' ' . $task->first_name . ' ' . $task->last_name));
                                                } else {
                                                    echo '<span class="text-muted">Unassigned</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($task->task_status); ?></span>
                                                <?php if ((int) $task->live_is_overdue === 1): ?>
                                                    <br><small class="text-danger">Overdue</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo !empty($task->planned_start_date) ? date('d M Y', strtotime($task->planned_start_date)) : '-'; ?>
                                                <br>
                                                to
                                                <br>
                                                <?php echo !empty($task->planned_end_date) ? date('d M Y', strtotime($task->planned_end_date)) : '-'; ?>
                                            </td>
                                            <td><?php echo !empty($task->actual_end_date) ? date('d M Y h:i A', strtotime($task->actual_end_date)) : '<span class="text-muted">Pending</span>'; ?></td>
                                            <td><?php echo !empty($task->last_activity_at) ? date('d M Y h:i A', strtotime($task->last_activity_at)) : '<span class="text-muted">No activity</span>'; ?></td>
                                            <td>
                                                <?php if (!empty($task->dependency_task_name)): ?>
                                                    <?php echo htmlspecialchars($task->dependency_task_name); ?>
                                                    <?php if ((int) $task->dependency_blocked === 1): ?>
                                                        <br><small class="text-warning">Waiting for dependency completion</small>
                                                    <?php elseif ((int) $task->can_start_parallel === 1): ?>
                                                        <br><small class="text-info">Parallel start allowed</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">None</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo (int) $task->completion_percent; ?>%</td>
                                        </tr>
                                        <tr id="task-update-row-<?php echo (int) $task->execution_task_id; ?>" class="task-update-row">
                                            <td colspan="10">
                                                <div class="row">
                                                    <div class="col-md-7">
                                                        <form method="post" action="<?php echo page_url; ?>Spares_execution/update_task" enctype="multipart/form-data">
                                                            <input type="hidden" name="order_id" value="<?php echo (int) $order_snapshot->order_id; ?>">
                                                            <input type="hidden" name="execution_task_id" value="<?php echo (int) $task->execution_task_id; ?>">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>Status</label>
                                                                        <select name="task_status" class="form-control input-sm">
                                                                            <?php foreach (array('Pending', 'Open', 'In Progress', 'Completed', 'Blocked', 'On Hold', 'Cancelled') as $status): ?>
                                                                                <option value="<?php echo $status; ?>" <?php echo $task->task_status === $status ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label>Progress %</label>
                                                                        <input type="number" name="completion_percent" class="form-control input-sm" min="0" max="100" value="<?php echo (int) $task->completion_percent; ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>Owner</label>
                                                                        <select name="assigned_to" class="form-control input-sm task-owner-select" data-department-id="<?php echo (int) $task->department_id; ?>" data-selected-user-id="<?php echo !empty($task->assigned_to) ? (int) $task->assigned_to : ''; ?>" disabled>
                                                                            <option value="">Select Department User</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label>Remarks</label>
                                                                        <textarea name="remarks" class="form-control input-sm" rows="2" placeholder="Progress, blocker, or completion note."></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label>Attachment</label>
                                                                        <input type="file" name="task_attachment" class="form-control input-sm">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php if ((int) $task->dependency_blocked === 1): ?>
                                                                <div class="text-warning" style="padding:0 15px 10px;">
                                                                    This task cannot move to Open, In Progress, or Completed until "<?php echo htmlspecialchars($task->dependency_task_name); ?>" is completed.
                                                                </div>
                                                            <?php elseif (!empty($task->dependency_task_name) && (int) $task->can_start_parallel === 1): ?>
                                                                <div class="text-info" style="padding:0 15px 10px;">
                                                                    This task references "<?php echo htmlspecialchars($task->dependency_task_name); ?>" but is allowed to run in parallel.
                                                                </div>
                                                            <?php endif; ?>
                                                            <button type="submit" class="btn btn-primary btn-sm">Update Task</button>
                                                        </form>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <?php if ($task->task_status !== 'Completed' && $task->task_status !== 'Cancelled' && (int) $task->extension_allowed === 1): ?>
                                                            <?php if ((int) $task->pending_extension_count > 0): ?>
                                                                <div class="text-warning" style="padding-top:8px;">
                                                                    An extension request is already pending<?php echo !empty($task->pending_extension_requested_due_date) ? ' until ' . date('d M Y', strtotime($task->pending_extension_requested_due_date)) : ''; ?>.
                                                                </div>
                                                            <?php else: ?>
                                                                <?php $extension_min_date = !empty($task->planned_end_date) ? date('Y-m-d', strtotime($task->planned_end_date . ' +1 day')) : ''; ?>
                                                                <form method="post" action="<?php echo page_url; ?>Spares_execution/request_extension">
                                                                    <input type="hidden" name="order_id" value="<?php echo (int) $order_snapshot->order_id; ?>">
                                                                    <input type="hidden" name="execution_task_id" value="<?php echo (int) $task->execution_task_id; ?>">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>New Due Date</label>
                                                                                <input type="date" name="requested_due_date" class="form-control input-sm" <?php echo $extension_min_date !== '' ? 'min="' . htmlspecialchars($extension_min_date) . '"' : ''; ?>>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <div class="form-group">
                                                                                <label>Extension Reason</label>
                                                                                <textarea name="reason" class="form-control input-sm" rows="2" placeholder="Why more time is needed."></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-warning btn-sm">Raise Extension Request</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        <?php elseif ((int) $task->extension_allowed !== 1): ?>
                                                            <span class="text-muted">Extension request not allowed for this task.</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Extension Requests</b></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Current Due</th>
                                        <th>Requested Due</th>
                                        <th>Reason</th>
                                        <th>Requested By</th>
                                        <th>Status</th>
                                        <th>Review</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($extension_requests)): ?>
                                        <?php foreach ($extension_requests as $request): ?>
                                            <tr id="extension-row-<?php echo (int) $request->extension_request_id; ?>">
                                                <td><?php echo htmlspecialchars($request->task_name); ?></td>
                                                <td><?php echo date('d M Y', strtotime($request->current_due_date)); ?></td>
                                                <td><?php echo date('d M Y', strtotime($request->requested_due_date)); ?></td>
                                                <td><?php echo nl2br(htmlspecialchars($request->reason)); ?></td>
                                                <td><?php echo htmlspecialchars(trim($request->requested_title . ' ' . $request->requested_first_name . ' ' . $request->requested_last_name)); ?></td>
                                                <td><span class="label label-<?php echo $request->request_status === 'Approved' ? 'success' : ($request->request_status === 'Rejected' ? 'danger' : 'warning'); ?>"><?php echo htmlspecialchars($request->request_status); ?></span></td>
                                                <td>
                                                    <?php if ($request->request_status === 'Pending' && $can_review_extensions): ?>
                                                        <form method="post" action="<?php echo page_url; ?>Spares_execution/review_extension">
                                                            <input type="hidden" name="order_id" value="<?php echo (int) $order_snapshot->order_id; ?>">
                                                            <input type="hidden" name="extension_request_id" value="<?php echo (int) $request->extension_request_id; ?>">
                                                            <div class="form-group">
                                                                <select name="decision" class="form-control input-sm">
                                                                    <option value="Approved">Approve</option>
                                                                    <option value="Rejected">Reject</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea name="review_remarks" class="form-control input-sm" rows="2" placeholder="Marketing review note"></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-success btn-sm">Submit Review</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <?php if (!empty($request->reviewed_first_name)): ?>
                                                            <small>
                                                                Reviewed by <?php echo htmlspecialchars(trim($request->reviewed_title . ' ' . $request->reviewed_first_name . ' ' . $request->reviewed_last_name)); ?><br>
                                                                <?php echo !empty($request->review_remarks) ? nl2br(htmlspecialchars($request->review_remarks)) : 'No remarks'; ?>
                                                            </small>
                                                        <?php else: ?>
                                                            <span class="text-muted">Awaiting marketing review</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center text-muted">No extension requests yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>Recent Task Updates</b></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>When</th>
                                        <th>Task</th>
                                        <th>Type</th>
                                        <th>Status Change</th>
                                        <th>Remarks</th>
                                        <th>Attachment</th>
                                        <th>By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($updates)): ?>
                                        <?php foreach ($updates as $update): ?>
                                            <tr id="update-row-<?php echo (int) $update->update_id; ?>">
                                                <td><?php echo date('d M Y h:i A', strtotime($update->added_on)); ?></td>
                                                <td><?php echo htmlspecialchars($update->task_name); ?></td>
                                                <td><?php echo htmlspecialchars($update->update_type); ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($update->previous_status) || !empty($update->new_status)) {
                                                        echo htmlspecialchars($update->previous_status . ' -> ' . $update->new_status);
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo !empty($update->remarks) ? nl2br(htmlspecialchars($update->remarks)) : '<span class="text-muted">-</span>'; ?></td>
                                                <td>
                                                    <?php if (!empty($update->attachment_name)): ?>
                                                        <a href="<?php echo page_url; ?>Spares_execution/view_attachment/<?php echo (int) $update->update_id; ?>" target="_blank">View File</a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars(trim($update->title . ' ' . $update->first_name . ' ' . $update->last_name)); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center text-muted">No task updates recorded yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/footer'); ?>
    <script>
        (function() {
            var focusTaskId = <?php echo $focus_task_id; ?>;
            var focusExtensionRequestId = <?php echo $focus_extension_request_id; ?>;
            var focusUpdateId = <?php echo $focus_update_id; ?>;
            var endpoint = '<?php echo page_url; ?>Spares_execution/get_department_users';
            var ownerSelects = document.querySelectorAll('.task-owner-select');
            var departmentUserCache = {};

            function scrollToElement(element) {
                if (!element) {
                    return;
                }

                if (typeof element.scrollIntoView === 'function') {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            function highlightElement(element) {
                if (!element) {
                    return;
                }

                element.classList.add('focus-highlight');
            }

            function applyFocusState() {
                if (focusTaskId > 0) {
                    var taskRow = document.getElementById('task-row-' + focusTaskId);
                    var taskUpdateRow = document.getElementById('task-update-row-' + focusTaskId);
                    highlightElement(taskRow);
                    highlightElement(taskUpdateRow);
                    scrollToElement(taskRow || taskUpdateRow);
                    return;
                }

                if (focusExtensionRequestId > 0) {
                    var extensionRow = document.getElementById('extension-row-' + focusExtensionRequestId);
                    highlightElement(extensionRow);
                    scrollToElement(extensionRow);
                    return;
                }

                if (focusUpdateId > 0) {
                    var updateRow = document.getElementById('update-row-' + focusUpdateId);
                    highlightElement(updateRow);
                    scrollToElement(updateRow);
                }
            }

            function setSelectOptions(select, users, selectedUserId) {
                select.innerHTML = '';

                var defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select User';
                select.appendChild(defaultOption);

                users.forEach(function(user) {
                    var option = document.createElement('option');
                    option.value = user.user_id;
                    option.textContent = user.name;
                    select.appendChild(option);
                });

                select.disabled = false;

                if (selectedUserId) {
                    select.value = String(selectedUserId);
                    if (select.value !== String(selectedUserId)) {
                        select.value = '';
                    }
                } else {
                    select.value = '';
                }
            }

            function setSelectPlaceholder(select, message) {
                select.innerHTML = '';
                var option = document.createElement('option');
                option.value = '';
                option.textContent = message;
                select.appendChild(option);
                select.disabled = true;
            }

            function requestDepartmentUsers(departmentId, callback) {
                if (!departmentId) {
                    callback([]);
                    return;
                }

                if (Object.prototype.hasOwnProperty.call(departmentUserCache, departmentId)) {
                    callback(departmentUserCache[departmentId]);
                    return;
                }

                var xhr = new XMLHttpRequest();
                xhr.open('GET', endpoint + '?department_id=' + encodeURIComponent(departmentId), true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState !== 4) {
                        return;
                    }

                    if (xhr.status >= 200 && xhr.status < 300) {
                        var response = { users: [] };

                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (error) {
                            response = { users: [] };
                        }

                        departmentUserCache[departmentId] = response.users || [];
                        callback(departmentUserCache[departmentId]);
                        return;
                    }

                    callback(null);
                };
                xhr.send();
            }

            document.addEventListener('DOMContentLoaded', function() {
                Array.prototype.forEach.call(ownerSelects, function(select) {
                    var departmentId = select.getAttribute('data-department-id');
                    var selectedUserId = select.getAttribute('data-selected-user-id');

                    if (!departmentId) {
                        setSelectPlaceholder(select, 'No Department Assigned');
                        return;
                    }

                    setSelectPlaceholder(select, 'Loading users...');
                    requestDepartmentUsers(departmentId, function(users) {
                        if (users === null) {
                            setSelectPlaceholder(select, 'Unable to load users');
                            return;
                        }

                        setSelectOptions(select, users, selectedUserId);
                    });
                });

                applyFocusState();
            });
        })();
    </script>
</body>
</html>
