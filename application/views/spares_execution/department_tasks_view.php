<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$workflow_labels = !empty($workflows) ? $workflows : array();
$filters = !empty($filters) ? $filters : array();
$show_filter = !empty($filters['show']) ? $filters['show'] : 'active';
$workflow_type_filter = !empty($filters['workflow_type']) ? $filters['workflow_type'] : '';
$task_status_filter = !empty($filters['task_status']) ? $filters['task_status'] : '';
$assigned_to_filter = isset($filters['assigned_to']) ? $filters['assigned_to'] : '';
$search_filter = !empty($filters['search']) ? $filters['search'] : '';
$mrp_orders = !empty($mrp_orders) ? $mrp_orders : array();
$show_mrp_window = !empty($show_mrp_window);
$mrp_pending_count = 0;
$mrp_shortage_order_count = 0;
$mrp_total_shortage_qty = 0;

foreach ($mrp_orders as $mrp_order) {
    if (empty($mrp_order->mrp_run_id)) {
        $mrp_pending_count++;
    }
    if (!empty($mrp_order->shortage_items) && (int) $mrp_order->shortage_items > 0) {
        $mrp_shortage_order_count++;
        $mrp_total_shortage_qty += (float) $mrp_order->total_shortage_qty;
    }
}

if (!function_exists('spares_execution_department_status_class')) {
    function spares_execution_department_status_class($status)
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

if (!function_exists('spares_execution_department_filter_url')) {
    function spares_execution_department_filter_url($filters, $overrides = array())
    {
        $params = array_merge($filters, $overrides);

        foreach ($params as $key => $value) {
            if ($key === 'limit' || $value === '' || $value === null || ($key === 'show' && $value === 'active')) {
                unset($params[$key]);
            }
        }

        $query = http_build_query($params);

        return page_url . 'Spares_execution/department_tasks' . (!empty($query) ? '?' . $query : '');
    }
}

if (!function_exists('spares_execution_department_export_url')) {
    function spares_execution_department_export_url($filters)
    {
        $params = array();

        foreach ($filters as $key => $value) {
            if ($key === 'limit' || $value === '' || $value === null || ($key === 'show' && $value === 'active')) {
                continue;
            }

            $params[$key] = $value;
        }

        $query = http_build_query($params);

        return page_url . 'Spares_execution/export_department_tasks' . (!empty($query) ? '?' . $query : '');
    }
}

if (!function_exists('spares_execution_department_tracker_url')) {
    function spares_execution_department_tracker_url($order_id, $execution_task_id = 0)
    {
        $query = (int) $execution_task_id > 0 ? '?focus_task_id=' . (int) $execution_task_id : '';

        return page_url . 'Spares_execution/order/' . (int) $order_id . $query;
    }
}

if (!function_exists('spares_execution_department_short_date')) {
    function spares_execution_department_short_date($date)
    {
        return !empty($date) ? date('d M Y', strtotime($date)) : '-';
    }
}

if (!function_exists('spares_execution_department_short_datetime')) {
    function spares_execution_department_short_datetime($date)
    {
        return !empty($date) ? date('d M Y, h:i A', strtotime($date)) : '-';
    }
}
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
        .metric-bar { padding: 18px; margin-bottom: 20px; }
        .queue-metric { min-height: 76px; border-right: 1px solid #edf1f5; }
        .queue-metric:last-child { border-right: 0; }
        .queue-metric-label { font-size: 12px; color: #7f8a9a; text-transform: uppercase; letter-spacing: 0.4px; }
        .queue-metric-value { font-size: 24px; font-weight: 700; color: #243447; line-height: 1.2; margin-top: 8px; }
        .queue-metric-danger .queue-metric-value { color: #b42318; }
        .queue-metric-warning .queue-metric-value { color: #9a6700; }
        .filter-panel { padding: 18px; margin-bottom: 20px; }
        .filter-chip { display: inline-block; padding: 8px 14px; border: 1px solid #d8e2ee; border-radius: 999px; color: #425466; background: #fff; margin-right: 8px; margin-bottom: 10px; text-decoration: none !important; font-size: 12px; font-weight: 600; }
        .filter-chip:hover { border-color: #2b7cff; color: #2b7cff; }
        .filter-chip.active { background: #2b7cff; border-color: #2b7cff; color: #fff; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .status-pending { background: #edf2f7; color: #4a5568; }
        .status-open { background: #e7f2ff; color: #1e5aa7; }
        .status-in-progress { background: #fff4dd; color: #9a6700; }
        .status-completed { background: #e4f9ef; color: #18794e; }
        .status-blocked { background: #fdecec; color: #b42318; }
        .status-on-hold { background: #f1edff; color: #6b46c1; }
        .status-cancelled { background: #f5f5f5; color: #6c757d; }
        .workflow-chip { display: inline-block; padding: 3px 10px; border-radius: 999px; background: #eef5ff; color: #1f5aa6; font-size: 11px; font-weight: 600; }
        .priority-chip { display: inline-block; padding: 3px 10px; border-radius: 999px; background: #fff3dd; color: #9a6700; font-size: 11px; font-weight: 600; }
        .priority-chip.priority-critical { background: #fdebec; color: #b42318; }
        .priority-chip.priority-low { background: #eef2f6; color: #5f6f81; }
        .mrp-window { margin-bottom: 20px; }
        .mrp-window-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 15px; margin-bottom: 14px; }
        .mrp-window-title { margin: 0; color: #223247; font-weight: 700; }
        .mrp-window-note { color: #7f8a9a; font-size: 12px; margin-top: 4px; }
        .mrp-mini-card { border: 1px solid #edf1f5; border-left: 4px solid #2b7cff; border-radius: 8px; padding: 12px; margin-bottom: 14px; background: #fbfcff; min-height: 78px; }
        .mrp-mini-card.warning { border-left-color: #f59e0b; }
        .mrp-mini-card.danger { border-left-color: #dc2626; }
        .mrp-mini-label { font-size: 11px; color: #7f8a9a; text-transform: uppercase; font-weight: 700; }
        .mrp-mini-value { font-size: 22px; line-height: 1.2; font-weight: 700; color: #223247; margin-top: 5px; }
        .mrp-status { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .mrp-status.pending { background: #fff4dd; color: #9a6700; }
        .mrp-status.shortage { background: #fdecec; color: #b42318; }
        .mrp-status.available { background: #e4f9ef; color: #18794e; }
        .mrp-action-stack .btn, .mrp-action-stack form { display: inline-block; margin: 0 4px 4px 0; }
        .owner-chip { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: #eef2f6; color: #425466; }
        .owner-chip.unassigned { background: #fff4dd; color: #9a6700; }
        .queue-title { font-weight: 600; color: #223247; }
        .queue-subtitle { font-size: 12px; color: #7f8a9a; }
        .overdue-note { display: inline-block; margin-top: 6px; font-size: 12px; color: #b42318; font-weight: 600; }
        .task-update-row { background: #fbfcfe; }
        .progress { height: 8px; margin-bottom: 6px; background: #edf1f7; box-shadow: none; }
        .progress-bar { background: #2b7cff; }
        .table > thead > tr > th,
        .table > tbody > tr > td { vertical-align: middle; }
        @media (max-width: 991px) {
            .queue-metric { border-right: 0; border-bottom: 1px solid #edf1f5; padding-bottom: 15px; margin-bottom: 15px; }
            .queue-metric:last-child { border-bottom: 0; margin-bottom: 0; padding-bottom: 0; }
            .mrp-window-header { display: block; }
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
                            <a href="<?php echo page_url; ?>Spares_execution/my_tasks" class="btn btn-success waves-effect waves-light"><i class="fa fa-check-square-o"></i> My Tasks</a>
                            <a href="<?php echo page_url; ?>Spares_execution/alerts" class="btn btn-info waves-effect waves-light"><i class="fa fa-bell"></i> My Alerts <?php if (!empty($unread_execution_alerts)): ?>(<?php echo (int) $unread_execution_alerts; ?>)<?php endif; ?></a>
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-dashboard"></i> Execution Dashboard</a>
                            <a href="<?php echo page_url; ?>Spares/running_orders_list" class="btn btn-default waves-effect waves-light"><i class="fa fa-list"></i> Running Orders</a>
                        </div>
                        <h4 class="page-title"><?php echo htmlspecialchars($department->department); ?> Spares Tasks</h4>
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
                <div class="col-lg-12">
                    <div class="card-box metric-bar">
                        <div class="row">
                            <div class="col-md-2 col-sm-6 queue-metric">
                                <div class="queue-metric-label">Department Tasks</div>
                                <div class="queue-metric-value"><?php echo (int) $queue_counts->total_tasks; ?></div>
                            </div>
                            <div class="col-md-2 col-sm-6 queue-metric">
                                <div class="queue-metric-label">Open</div>
                                <div class="queue-metric-value"><?php echo (int) $queue_counts->open_tasks; ?></div>
                            </div>
                            <div class="col-md-2 col-sm-6 queue-metric">
                                <div class="queue-metric-label">In Progress</div>
                                <div class="queue-metric-value"><?php echo (int) $queue_counts->in_progress_tasks; ?></div>
                            </div>
                            <div class="col-md-2 col-sm-6 queue-metric">
                                <div class="queue-metric-label">Completed</div>
                                <div class="queue-metric-value"><?php echo (int) $queue_counts->completed_tasks; ?></div>
                            </div>
                            <div class="col-md-2 col-sm-6 queue-metric queue-metric-danger">
                                <div class="queue-metric-label">Overdue</div>
                                <div class="queue-metric-value"><?php echo (int) $queue_counts->overdue_tasks; ?></div>
                            </div>
                            <div class="col-md-2 col-sm-6 queue-metric queue-metric-warning">
                                <div class="queue-metric-label">Unassigned</div>
                                <div class="queue-metric-value"><?php echo (int) $queue_counts->unassigned_tasks; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($show_mrp_window): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box mrp-window">
                            <div class="mrp-window-header">
                                <div>
                                    <h4 class="mrp-window-title">BOM MRP & Shortage Window</h4>
                                    <div class="mrp-window-note">Released SF orders assigned to PPC for MRP run and shortage review.</div>
                                </div>
                                <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages" class="btn btn-default btn-sm"><i class="fa fa-external-link"></i> Full MRP Queue</a>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mrp-mini-card">
                                        <div class="mrp-mini-label">Released SF For PPC</div>
                                        <div class="mrp-mini-value"><?php echo count($mrp_orders); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mrp-mini-card warning">
                                        <div class="mrp-mini-label">Pending MRP</div>
                                        <div class="mrp-mini-value"><?php echo (int) $mrp_pending_count; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mrp-mini-card danger">
                                        <div class="mrp-mini-label">Orders With Shortage</div>
                                        <div class="mrp-mini-value"><?php echo (int) $mrp_shortage_order_count; ?> <small><?php echo number_format($mrp_total_shortage_qty, 3); ?> qty</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="min-width:180px;">Order / Customer</th>
                                            <th style="min-width:130px;">MRP Status</th>
                                            <th style="min-width:150px;">Shortage</th>
                                            <th style="min-width:120px;">MRP Due</th>
                                            <th style="min-width:120px;">Shortage Report</th>
                                            <th style="min-width:190px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($mrp_orders)): ?>
                                            <?php foreach ($mrp_orders as $mrp_order): ?>
                                                <?php
                                                $has_shortage = !empty($mrp_order->shortage_items) && (int) $mrp_order->shortage_items > 0;
                                                $mrp_status_class = empty($mrp_order->mrp_run_id) ? 'pending' : ($has_shortage ? 'shortage' : 'available');
                                                $mrp_status_text = empty($mrp_order->mrp_run_id) ? 'MRP Pending' : ($has_shortage ? 'Shortage' : 'Available');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="queue-title"><?php echo htmlspecialchars($mrp_order->company_name); ?></div>
                                                        <div class="queue-subtitle">SO-<?php echo (int) $mrp_order->order_id; ?><?php if (!empty($mrp_order->op_no)): ?> | OP <?php echo htmlspecialchars($mrp_order->op_no); ?><?php endif; ?></div>
                                                        <div class="queue-subtitle"><?php echo !empty($mrp_order->sf_no) ? 'SF: ' . htmlspecialchars($mrp_order->sf_no) : 'SF released'; ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="mrp-status <?php echo $mrp_status_class; ?>"><?php echo $mrp_status_text; ?></span>
                                                        <div class="queue-subtitle" style="margin-top:6px;"><?php echo spares_execution_department_short_datetime($mrp_order->latest_mrp_run_at); ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="queue-title"><?php echo (int) $mrp_order->shortage_items; ?> item(s)</div>
                                                        <div class="queue-subtitle"><?php echo number_format((float) $mrp_order->total_shortage_qty, 3); ?> qty shortage</div>
                                                        <?php if ((int) $mrp_order->missing_master_items > 0): ?>
                                                            <div class="overdue-note"><?php echo (int) $mrp_order->missing_master_items; ?> not in master</div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo spares_execution_department_short_date($mrp_order->run_mrp_due_date); ?></td>
                                                    <td>
                                                        <span class="status-badge <?php echo spares_execution_department_status_class($mrp_order->shortage_task_status ?: 'Pending'); ?>"><?php echo htmlspecialchars($mrp_order->shortage_task_status ?: 'Pending'); ?></span>
                                                        <div class="queue-subtitle" style="margin-top:6px;"><?php echo spares_execution_department_short_date($mrp_order->shortage_due_date); ?></div>
                                                    </td>
                                                    <td class="mrp-action-stack">
                                                        <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages/<?php echo (int) $mrp_order->order_id; ?>" class="btn btn-primary btn-xs">Open Report</a>
                                                        <form method="post" action="<?php echo page_url; ?>Spares_execution/run_mrp/<?php echo (int) $mrp_order->order_id; ?>">
                                                            <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Run MRP with current inventory qty?');">Run MRP</button>
                                                        </form>
                                                        <a href="<?php echo spares_execution_department_tracker_url((int) $mrp_order->order_id, (int) $mrp_order->run_mrp_task_id); ?>" class="btn btn-default btn-xs">Tracker</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No released SF assigned to PPC for MRP right now.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box filter-panel">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="<?php echo spares_execution_department_filter_url($filters, array('show' => 'active')); ?>" class="filter-chip <?php echo $show_filter === 'active' ? 'active' : ''; ?>">Active</a>
                                <a href="<?php echo spares_execution_department_filter_url($filters, array('show' => 'overdue')); ?>" class="filter-chip <?php echo $show_filter === 'overdue' ? 'active' : ''; ?>">Overdue</a>
                                <a href="<?php echo spares_execution_department_filter_url($filters, array('show' => 'stale')); ?>" class="filter-chip <?php echo $show_filter === 'stale' ? 'active' : ''; ?>">Stale (<?php echo (int) $queue_counts->stale_tasks; ?>)</a>
                                <a href="<?php echo spares_execution_department_filter_url($filters, array('show' => 'blocked')); ?>" class="filter-chip <?php echo $show_filter === 'blocked' ? 'active' : ''; ?>">Blocked (<?php echo (int) $queue_counts->blocked_tasks; ?>)</a>
                                <a href="<?php echo spares_execution_department_filter_url($filters, array('show' => 'on_hold')); ?>" class="filter-chip <?php echo $show_filter === 'on_hold' ? 'active' : ''; ?>">On Hold (<?php echo (int) $queue_counts->on_hold_tasks; ?>)</a>
                                <a href="<?php echo spares_execution_department_filter_url($filters, array('show' => 'unassigned')); ?>" class="filter-chip <?php echo $show_filter === 'unassigned' ? 'active' : ''; ?>">Unassigned (<?php echo (int) $queue_counts->unassigned_tasks; ?>)</a>
                                <a href="<?php echo spares_execution_department_filter_url($filters, array('show' => 'completed')); ?>" class="filter-chip <?php echo $show_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
                                <a href="<?php echo spares_execution_department_filter_url($filters, array('show' => 'all')); ?>" class="filter-chip <?php echo $show_filter === 'all' ? 'active' : ''; ?>">All Department Tasks</a>
                            </div>
                        </div>
                        <form method="get" action="<?php echo page_url; ?>Spares_execution/department_tasks" class="m-t-10">
                            <input type="hidden" name="show" value="<?php echo htmlspecialchars($show_filter); ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search_filter); ?>" placeholder="Customer, opportunity, task, order">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Workflow</label>
                                        <select name="workflow_type" class="form-control">
                                            <option value="">All Workflows</option>
                                            <?php foreach ($workflows as $workflow_key => $workflow_label): ?>
                                                <option value="<?php echo $workflow_key; ?>" <?php echo $workflow_type_filter === $workflow_key ? 'selected' : ''; ?>><?php echo htmlspecialchars($workflow_label); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Task Status</label>
                                        <select name="task_status" class="form-control">
                                            <option value="">All Statuses</option>
                                            <?php foreach ($task_status_options as $task_status_option): ?>
                                                <option value="<?php echo htmlspecialchars($task_status_option); ?>" <?php echo $task_status_filter === $task_status_option ? 'selected' : ''; ?>><?php echo htmlspecialchars($task_status_option); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Owner</label>
                                        <select name="assigned_to" class="form-control">
                                            <option value="">All Owners</option>
                                            <option value="unassigned" <?php echo $assigned_to_filter === 'unassigned' ? 'selected' : ''; ?>>Unassigned</option>
                                            <?php foreach ($department_users as $department_user): ?>
                                                <?php $department_user_name = trim($department_user->title . ' ' . $department_user->first_name . ' ' . $department_user->last_name); ?>
                                                <option value="<?php echo (int) $department_user->user_id; ?>" <?php echo (string) $assigned_to_filter === (string) $department_user->user_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($department_user_name); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label><br>
                                        <button type="submit" class="btn btn-primary">Apply</button>
                                        <a href="<?php echo page_url; ?>Spares_execution/department_tasks" class="btn btn-default">Reset</a>
                                        <a href="<?php echo spares_execution_department_export_url($filters); ?>" class="btn btn-success">Export CSV</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="min-width:180px;">Order / Customer</th>
                                        <th style="min-width:110px;">Workflow</th>
                                        <th style="min-width:220px;">Task</th>
                                        <th style="min-width:170px;">Owner</th>
                                        <th style="min-width:120px;">Planned End</th>
                                        <th style="min-width:120px;">Commit Date</th>
                                        <th style="min-width:120px;">Status</th>
                                        <th style="min-width:130px;">Progress</th>
                                        <th style="min-width:180px;">Dependency</th>
                                        <th style="min-width:150px;">Last Activity</th>
                                        <th style="min-width:110px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tasks)): ?>
                                        <?php foreach ($tasks as $task): ?>
                                            <?php
                                            $planned_end_label = !empty($task->planned_end_date) ? date('d M Y', strtotime($task->planned_end_date)) : '-';
                                            $commit_date_label = !empty($task->commit_date) ? date('d M Y', strtotime($task->commit_date)) : '-';
                                            $progress = max(0, min(100, (int) $task->completion_percent));
                                            $delay_days = 0;
                                            if ((int) $task->live_is_overdue === 1 && !empty($task->planned_end_date)) {
                                                $delay_days = max(1, (int) floor((strtotime(date('Y-m-d')) - strtotime($task->planned_end_date)) / 86400));
                                            }
                                            $priority_class = 'priority-medium';
                                            if ($task->priority === 'Critical') {
                                                $priority_class = 'priority-critical';
                                            } elseif ($task->priority === 'Low') {
                                                $priority_class = 'priority-low';
                                            }
                                            $owner_name = !empty($task->owner_name) ? $task->owner_name : 'Unassigned';
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="queue-title"><?php echo htmlspecialchars($task->company_name); ?></div>
                                                    <div class="queue-subtitle">Order #<?php echo (int) $task->order_id; ?><?php if (!empty($task->op_no)): ?> | OP <?php echo htmlspecialchars($task->op_no); ?><?php endif; ?></div>
                                                    <div class="queue-subtitle">Execution: <?php echo htmlspecialchars($task->execution_status); ?></div>
                                                </td>
                                                <td>
                                                    <span class="workflow-chip"><?php echo htmlspecialchars(isset($workflow_labels[$task->workflow_type]) ? $workflow_labels[$task->workflow_type] : $task->workflow_type); ?></span>
                                                    <div style="margin-top:8px;">
                                                        <span class="priority-chip <?php echo $priority_class; ?>"><?php echo htmlspecialchars($task->priority); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="queue-title"><?php echo (int) $task->sequence_no; ?>. <?php echo htmlspecialchars($task->task_name); ?></div>
                                                    <?php if ((int) $task->live_is_overdue === 1): ?>
                                                        <span class="overdue-note">Overdue by <?php echo $delay_days; ?> day<?php echo $delay_days > 1 ? 's' : ''; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="owner-chip <?php echo empty($task->owner_name) ? 'unassigned' : ''; ?>"><?php echo htmlspecialchars($owner_name); ?></span>
                                                </td>
                                                <td><?php echo $planned_end_label; ?></td>
                                                <td><?php echo $commit_date_label; ?></td>
                                                <td><span class="status-badge <?php echo spares_execution_department_status_class($task->task_status); ?>"><?php echo htmlspecialchars($task->task_status); ?></span></td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress; ?>%;"></div>
                                                    </div>
                                                    <span class="queue-subtitle"><?php echo $progress; ?>%</span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($task->dependency_task_name)): ?>
                                                        <div class="queue-subtitle"><?php echo htmlspecialchars($task->dependency_task_name); ?></div>
                                                        <?php if ((int) $task->dependency_blocked === 1): ?>
                                                            <div class="overdue-note" style="margin-top:4px;">Waiting for dependency completion</div>
                                                        <?php elseif ((int) $task->can_start_parallel === 1): ?>
                                                            <div class="queue-subtitle text-info" style="margin-top:4px;">Parallel start allowed</div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Ready to start</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($task->last_activity_at)): ?>
                                                        <div class="queue-subtitle"><?php echo date('d M Y', strtotime($task->last_activity_at)); ?></div>
                                                        <div class="queue-subtitle"><?php echo date('h:i A', strtotime($task->last_activity_at)); ?></div>
                                                    <?php else: ?>
                                                        <span class="text-muted">No activity</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo spares_execution_department_tracker_url((int) $task->order_id, (int) $task->execution_task_id); ?>" class="btn btn-primary btn-xs">Open Tracker</a>
                                                    <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo (int) $task->order_id; ?>" class="btn btn-info btn-xs">Gantt</a>
                                                </td>
                                            </tr>
                                            <tr class="task-update-row">
                                                <td colspan="11">
                                                    <form method="post" action="<?php echo page_url; ?>Spares_execution/update_task" enctype="multipart/form-data">
                                                        <input type="hidden" name="order_id" value="<?php echo (int) $task->order_id; ?>">
                                                        <input type="hidden" name="execution_task_id" value="<?php echo (int) $task->execution_task_id; ?>">
                                                        <input type="hidden" name="redirect_to" value="department_tasks">
                                                        <input type="hidden" name="redirect_show" value="<?php echo htmlspecialchars($show_filter); ?>">
                                                        <input type="hidden" name="redirect_workflow_type" value="<?php echo htmlspecialchars($workflow_type_filter); ?>">
                                                        <input type="hidden" name="redirect_task_status" value="<?php echo htmlspecialchars($task_status_filter); ?>">
                                                        <input type="hidden" name="redirect_assigned_to" value="<?php echo htmlspecialchars($assigned_to_filter); ?>">
                                                        <input type="hidden" name="redirect_search" value="<?php echo htmlspecialchars($search_filter); ?>">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Status</label>
                                                                    <select name="task_status" class="form-control input-sm">
                                                                        <?php foreach ($task_status_options as $task_status_option): ?>
                                                                            <option value="<?php echo htmlspecialchars($task_status_option); ?>" <?php echo $task->task_status === $task_status_option ? 'selected' : ''; ?>><?php echo htmlspecialchars($task_status_option); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">
                                                                    <label>Progress %</label>
                                                                    <input type="number" name="completion_percent" class="form-control input-sm" min="0" max="100" value="<?php echo $progress; ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label>Owner</label>
                                                                    <select name="assigned_to" class="form-control input-sm">
                                                                        <option value="">Unassigned</option>
                                                                        <?php foreach ($department_users as $department_user): ?>
                                                                            <?php $department_user_name = trim($department_user->title . ' ' . $department_user->first_name . ' ' . $department_user->last_name); ?>
                                                                            <option value="<?php echo (int) $department_user->user_id; ?>" <?php echo ((int) $task->assigned_to === (int) $department_user->user_id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($department_user_name); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <textarea name="remarks" class="form-control input-sm" rows="2" placeholder="Add update, blocker, or assignment note."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="form-group">
                                                                    <label>&nbsp;</label><br>
                                                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
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
                                                        <?php if (!empty($task->last_remark)): ?>
                                                            <div class="queue-subtitle">Last note: <?php echo htmlspecialchars($task->last_remark); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ((int) $task->dependency_blocked === 1): ?>
                                                            <div class="queue-subtitle text-warning">Status cannot move forward until "<?php echo htmlspecialchars($task->dependency_task_name); ?>" is completed.</div>
                                                        <?php elseif (!empty($task->dependency_task_name) && (int) $task->can_start_parallel === 1): ?>
                                                            <div class="queue-subtitle text-info">This task can run in parallel even though it references "<?php echo htmlspecialchars($task->dependency_task_name); ?>".</div>
                                                        <?php endif; ?>
                                                    </form>
                                                    <?php if ($task->task_status !== 'Completed' && $task->task_status !== 'Cancelled' && (int) $task->extension_allowed === 1): ?>
                                                        <div style="margin-top:12px; padding-top:12px; border-top:1px dashed #dbe3ec;">
                                                            <?php if ((int) $task->pending_extension_count > 0): ?>
                                                                <div class="queue-subtitle text-warning">
                                                                    Extension request already pending<?php echo !empty($task->pending_extension_requested_due_date) ? ' until ' . date('d M Y', strtotime($task->pending_extension_requested_due_date)) : ''; ?>.
                                                                </div>
                                                            <?php else: ?>
                                                                <?php $extension_min_date = !empty($task->planned_end_date) ? date('Y-m-d', strtotime($task->planned_end_date . ' +1 day')) : ''; ?>
                                                                <form method="post" action="<?php echo page_url; ?>Spares_execution/request_extension">
                                                                    <input type="hidden" name="order_id" value="<?php echo (int) $task->order_id; ?>">
                                                                    <input type="hidden" name="execution_task_id" value="<?php echo (int) $task->execution_task_id; ?>">
                                                                    <input type="hidden" name="redirect_to" value="department_tasks">
                                                                    <input type="hidden" name="redirect_show" value="<?php echo htmlspecialchars($show_filter); ?>">
                                                                    <input type="hidden" name="redirect_workflow_type" value="<?php echo htmlspecialchars($workflow_type_filter); ?>">
                                                                    <input type="hidden" name="redirect_task_status" value="<?php echo htmlspecialchars($task_status_filter); ?>">
                                                                    <input type="hidden" name="redirect_assigned_to" value="<?php echo htmlspecialchars($assigned_to_filter); ?>">
                                                                    <input type="hidden" name="redirect_search" value="<?php echo htmlspecialchars($search_filter); ?>">
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <div class="form-group">
                                                                                <label>Request New Due Date</label>
                                                                                <input type="date" name="requested_due_date" class="form-control input-sm" <?php echo $extension_min_date !== '' ? 'min="' . htmlspecialchars($extension_min_date) . '"' : ''; ?>>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-7">
                                                                            <div class="form-group">
                                                                                <label>Extension Reason</label>
                                                                                <textarea name="reason" class="form-control input-sm" rows="2" placeholder="Explain why extra time is needed."></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>&nbsp;</label><br>
                                                                                <button type="submit" class="btn btn-warning btn-sm">Raise Extension</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="text-center text-muted">No Spares execution tasks found for this department filter.</td>
                                        </tr>
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
</body>
</html>
