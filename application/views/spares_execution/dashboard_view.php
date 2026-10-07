<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$workflow_labels = !empty($workflows) ? $workflows : array();
$current_user_id = (int) $this->session->userdata['logged_in']['user_id'];
$filters = !empty($filters) ? $filters : array();
$view_mode = !empty($filters['view_mode']) ? $filters['view_mode'] : 'all';
$workflow_type_filter = !empty($filters['workflow_type']) ? $filters['workflow_type'] : '';
$execution_status_filter = !empty($filters['execution_status']) ? $filters['execution_status'] : '';
$search_filter = !empty($filters['search']) ? $filters['search'] : '';

if (!function_exists('spares_execution_status_class')) {
    function spares_execution_status_class($status)
    {
        $map = array(
            'Draft' => 'badge-neutral',
            'Scheduled' => 'badge-info',
            'In Progress' => 'badge-warning',
            'On Hold' => 'badge-hold',
            'Completed' => 'badge-success',
            'Cancelled' => 'badge-neutral',
        );

        return isset($map[$status]) ? $map[$status] : 'badge-neutral';
    }
}

if (!function_exists('spares_execution_priority_class')) {
    function spares_execution_priority_class($priority)
    {
        $map = array(
            'Low' => 'badge-neutral',
            'Medium' => 'badge-info',
            'High' => 'badge-warning',
            'Critical' => 'badge-danger',
        );

        return isset($map[$priority]) ? $map[$priority] : 'badge-neutral';
    }
}

if (!function_exists('spares_execution_filter_url')) {
    function spares_execution_filter_url($filters, $overrides = array())
    {
        $params = array_merge($filters, $overrides);
        foreach ($params as $key => $value) {
            if ($value === '' || $value === null || ($key === 'view_mode' && $value === 'all')) {
                unset($params[$key]);
            }
        }

        $query = http_build_query($params);
        return page_url . 'Spares_execution/dashboard' . (!empty($query) ? '?' . $query : '');
    }
}

if (!function_exists('spares_execution_export_url')) {
    function spares_execution_export_url($filters, $export_type)
    {
        $params = array();
        foreach ($filters as $key => $value) {
            if ($value === '' || $value === null || ($key === 'view_mode' && $value === 'all')) {
                continue;
            }

            $params[$key] = $value;
        }

        $query = http_build_query($params);
        return page_url . 'Spares_execution/export_csv/' . rawurlencode($export_type) . (!empty($query) ? '?' . $query : '');
    }
}

if (!function_exists('spares_execution_tracker_url')) {
    function spares_execution_tracker_url($order_id, $focus_task_id = 0, $focus_extension_request_id = 0)
    {
        $params = array();
        if ((int) $focus_task_id > 0) {
            $params['focus_task_id'] = (int) $focus_task_id;
        }
        if ((int) $focus_extension_request_id > 0) {
            $params['focus_extension_request_id'] = (int) $focus_extension_request_id;
        }

        $query = http_build_query($params);
        return page_url . 'Spares_execution/order/' . (int) $order_id . (!empty($query) ? '?' . $query : '');
    }
}

if (!function_exists('spares_execution_short_date')) {
    function spares_execution_short_date($date)
    {
        return !empty($date) ? date('d M Y', strtotime($date)) : '-';
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
        body { background: #f5f7fb; color: #243447; }
        .card-box { border-radius: 8px; border: 1px solid #e3eaf2; box-shadow: none; }
        .page-title-box .btn { margin-bottom: 4px; }
        .metric-card { background: #fff; border: 1px solid #e3eaf2; border-left: 4px solid #2b7cff; border-radius: 8px; padding: 16px; margin-bottom: 18px; min-height: 108px; }
        .metric-label { font-size: 12px; color: #6f7d90; text-transform: uppercase; font-weight: 700; }
        .metric-value { font-size: 30px; font-weight: 700; color: #1f2d3d; line-height: 1.2; margin-top: 7px; }
        .metric-note { font-size: 12px; color: #7c8898; margin-top: 6px; }
        .simple-tabs { margin-bottom: 14px; }
        .simple-tab { display: inline-block; padding: 7px 12px; border: 1px solid #d8e2ee; border-radius: 6px; color: #425466; background: #fff; margin-right: 6px; margin-bottom: 8px; text-decoration: none !important; font-size: 12px; font-weight: 700; }
        .simple-tab.active { background: #2b7cff; border-color: #2b7cff; color: #fff; }
        .table > thead > tr > th { background: #f8fafc; color: #314154; font-size: 12px; text-transform: uppercase; vertical-align: middle; }
        .table > tbody > tr > td { vertical-align: middle; }
        .badge-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-neutral { background: #eef2f6; color: #5f6f81; }
        .badge-info { background: #e8f2ff; color: #1f5aa6; }
        .badge-warning { background: #fff3dd; color: #9a6700; }
        .badge-success { background: #e6f7ef; color: #1f7a4c; }
        .badge-danger { background: #fdebec; color: #b42318; }
        .badge-hold { background: #f1ebff; color: #6941c6; }
        .workflow-chip { display: inline-block; padding: 4px 9px; border-radius: 999px; background: #eef5ff; color: #1f5aa6; font-size: 11px; font-weight: 700; }
        .item-title { font-weight: 700; color: #243447; }
        .item-subtitle { font-size: 12px; color: #7c8898; margin-top: 3px; }
        .section-title { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .section-title h4 { margin: 0; }
        .filter-panel { padding: 16px; margin-bottom: 18px; }
        .filter-panel label { font-size: 12px; color: #64748b; text-transform: uppercase; }
        .progress { height: 8px; margin-bottom: 5px; background: #edf1f7; box-shadow: none; }
        .progress-bar { background: #2b7cff; }
        .action-stack .btn { margin: 0 4px 4px 0; }
        .attention-card { min-height: 360px; }
        .empty-state { padding: 22px; text-align: center; color: #7c8898; }
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
                            <a href="<?php echo page_url; ?>Spares_execution/department_tasks" class="btn btn-default waves-effect waves-light"><i class="fa fa-users"></i> Department Tasks</a>
                            <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages" class="btn btn-info waves-effect waves-light"><i class="fa fa-cogs"></i> MRP Shortages</a>
                            <a href="<?php echo page_url; ?>Spares/running_orders_list" class="btn btn-default waves-effect waves-light"><i class="fa fa-list"></i> Running Orders</a>
                            <a href="<?php echo page_url; ?>Spares_execution/task_master" class="btn btn-primary waves-effect waves-light"><i class="fa fa-sitemap"></i> Task Master</a>
                        </div>
                        <h4 class="page-title">Spares Execution</h4>
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
                    <div class="metric-card">
                        <div class="metric-label">Orders To Plan</div>
                        <div class="metric-value text-info"><?php echo (int) $metrics->unscheduled_orders; ?></div>
                        <div class="metric-note">New running orders pending execution setup</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Active Orders</div>
                        <div class="metric-value"><?php echo (int) $metrics->active_orders; ?></div>
                        <div class="metric-note">Scheduled or in progress</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Due This Week</div>
                        <div class="metric-value"><?php echo (int) $metrics->due_this_week; ?></div>
                        <div class="metric-note">Commit dates in next 7 days</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Needs Attention</div>
                        <div class="metric-value text-danger"><?php echo (int) $metrics->overdue_tasks + (int) $metrics->pending_extensions; ?></div>
                        <div class="metric-note"><?php echo (int) $metrics->overdue_tasks; ?> overdue, <?php echo (int) $metrics->pending_extensions; ?> extension requests</div>
                    </div>
                </div>
            </div>

            <div class="card-box filter-panel">
                <div class="simple-tabs">
                    <a href="<?php echo spares_execution_filter_url($filters, array('view_mode' => 'all')); ?>" class="simple-tab <?php echo $view_mode === 'all' ? 'active' : ''; ?>">All Orders</a>
                    <a href="<?php echo spares_execution_filter_url($filters, array('view_mode' => 'my')); ?>" class="simple-tab <?php echo $view_mode === 'my' ? 'active' : ''; ?>">My Orders</a>
                    <a href="<?php echo spares_execution_filter_url($filters, array('view_mode' => 'overdue')); ?>" class="simple-tab <?php echo $view_mode === 'overdue' ? 'active' : ''; ?>">Overdue</a>
                    <a href="<?php echo spares_execution_filter_url($filters, array('view_mode' => 'pending_extensions')); ?>" class="simple-tab <?php echo $view_mode === 'pending_extensions' ? 'active' : ''; ?>">Extensions</a>
                </div>
                <form method="get" action="<?php echo page_url; ?>Spares_execution/dashboard">
                    <input type="hidden" name="view_mode" value="<?php echo htmlspecialchars($view_mode); ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search_filter); ?>" placeholder="Customer, SO, PO, opportunity">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Quotation Type</label>
                                <select name="workflow_type" class="form-control">
                                    <option value="">All Types</option>
                                    <?php foreach ($workflow_labels as $workflow_key => $workflow_label): ?>
                                        <option value="<?php echo htmlspecialchars($workflow_key); ?>" <?php echo $workflow_type_filter === $workflow_key ? 'selected' : ''; ?>><?php echo htmlspecialchars($workflow_label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="execution_status" class="form-control">
                                    <option value="">All Status</option>
                                    <?php foreach ($status_options as $status_option): ?>
                                        <option value="<?php echo htmlspecialchars($status_option); ?>" <?php echo $execution_status_filter === $status_option ? 'selected' : ''; ?>><?php echo htmlspecialchars($status_option); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-default">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="section-title">
                            <h4 class="m-t-0 header-title"><b>Orders To Plan</b></h4>
                            <a href="<?php echo spares_execution_export_url($filters, 'pending_setup'); ?>" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Export</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>PO</th>
                                        <th>Order Date</th>
                                        <th>Owner</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($unscheduled_orders)): ?>
                                        <?php foreach ($unscheduled_orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <div class="item-title">SO-<?php echo (int) $order->order_id; ?></div>
                                                    <div class="item-subtitle"><?php echo !empty($order->op_no) ? 'Opp: ' . htmlspecialchars($order->op_no) : ''; ?></div>
                                                </td>
                                                <td>
                                                    <div class="item-title"><?php echo htmlspecialchars($order->company_name); ?></div>
                                                    <div class="item-subtitle"><?php echo number_format((float) $order->order_value, 2); ?></div>
                                                </td>
                                                <td><?php echo !empty($order->po_no) ? htmlspecialchars($order->po_no) : '<span class="text-muted">Not linked</span>'; ?></td>
                                                <td><?php echo spares_execution_short_date($order->order_date); ?></td>
                                                <td><?php echo !empty($order->marketing_person_name) ? htmlspecialchars($order->marketing_person_name) : '<span class="text-muted">Not mapped</span>'; ?></td>
                                                <td class="action-stack">
                                                    <a href="<?php echo page_url; ?>Spares_execution/schedule/<?php echo (int) $order->order_id; ?>" class="btn btn-primary btn-xs">Plan Flow</a>
                                                    <a href="<?php echo page_url; ?>Spares/order_detail/<?php echo (int) $order->order_id; ?>" class="btn btn-default btn-xs">Order</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="empty-state">No orders are waiting for execution planning.</td></tr>
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
                        <div class="section-title">
                            <h4 class="m-t-0 header-title"><b>Active Execution Orders</b></h4>
                            <a href="<?php echo spares_execution_export_url($filters, 'execution_orders'); ?>" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Export</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Commit Date</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Next Step</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($execution_orders)): ?>
                                        <?php foreach ($execution_orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <div class="item-title">SO-<?php echo (int) $order->order_id; ?></div>
                                                    <div class="item-subtitle"><?php echo !empty($order->po_no) ? 'PO: ' . htmlspecialchars($order->po_no) : 'PO not linked'; ?></div>
                                                </td>
                                                <td>
                                                    <div class="item-title"><?php echo htmlspecialchars($order->company_name); ?></div>
                                                    <div class="item-subtitle"><?php echo !empty($order->marketing_person_name) ? htmlspecialchars($order->marketing_person_name) : 'Owner not mapped'; ?></div>
                                                </td>
                                                <td><span class="workflow-chip"><?php echo htmlspecialchars(isset($workflow_labels[$order->workflow_type]) ? $workflow_labels[$order->workflow_type] : $order->workflow_type); ?></span></td>
                                                <td><?php echo spares_execution_short_date($order->commit_date); ?></td>
                                                <td>
                                                    <span class="badge-pill <?php echo spares_execution_status_class($order->execution_status); ?>"><?php echo htmlspecialchars($order->execution_status); ?></span>
                                                    <div class="item-subtitle"><span class="badge-pill <?php echo spares_execution_priority_class($order->priority); ?>"><?php echo htmlspecialchars($order->priority); ?></span></div>
                                                </td>
                                                <td style="min-width:150px;">
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" style="width: <?php echo (int) $order->progress_percent; ?>%;"></div>
                                                    </div>
                                                    <small><?php echo (int) $order->progress_percent; ?>%</small>
                                                </td>
                                                <td>
                                                    <?php if (!empty($order->next_task_name)): ?>
                                                        <div class="item-title"><?php echo htmlspecialchars($order->next_task_name); ?></div>
                                                        <div class="item-subtitle"><?php echo !empty($order->next_task_owner_name) ? htmlspecialchars($order->next_task_owner_name) : 'Owner not assigned'; ?></div>
                                                    <?php else: ?>
                                                        <span class="text-muted">No pending step</span>
                                                    <?php endif; ?>
                                                    <?php if ((int) $order->overdue_tasks > 0): ?>
                                                        <div class="item-subtitle text-danger"><?php echo (int) $order->overdue_tasks; ?> overdue</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="action-stack">
                                                    <a href="<?php echo spares_execution_tracker_url((int) $order->order_id); ?>" class="btn btn-primary btn-xs">Open</a>
                                                    <a href="<?php echo page_url; ?>Spares_execution/sf_form/<?php echo (int) $order->order_id; ?>" class="btn btn-purple btn-xs">SF</a>
                                                    <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages/<?php echo (int) $order->order_id; ?>" class="btn btn-info btn-xs">MRP</a>
                                                    <?php if ((int) $order->marketing_owner_id === $current_user_id || (int) $order->created_by === $current_user_id): ?>
                                                        <a href="<?php echo page_url; ?>Spares_execution/edit_schedule/<?php echo (int) $order->order_id; ?>" class="btn btn-warning btn-xs">Edit</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="empty-state">No active execution orders found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card-box attention-card">
                        <div class="section-title">
                            <h4 class="m-t-0 header-title"><b>Overdue Tasks</b></h4>
                            <a href="<?php echo spares_execution_export_url($filters, 'overdue_tasks'); ?>" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Export</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Task</th>
                                        <th>Owner</th>
                                        <th>Delay</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($overdue_tasks)): ?>
                                        <?php foreach ($overdue_tasks as $task): ?>
                                            <tr>
                                                <td>
                                                    <div class="item-title">SO-<?php echo (int) $task->order_id; ?></div>
                                                    <div class="item-subtitle"><?php echo htmlspecialchars($task->company_name); ?></div>
                                                </td>
                                                <td>
                                                    <div class="item-title"><?php echo htmlspecialchars($task->task_name); ?></div>
                                                    <div class="item-subtitle">Due: <?php echo spares_execution_short_date($task->planned_end_date); ?></div>
                                                </td>
                                                <td><?php echo !empty($task->owner_name) ? htmlspecialchars($task->owner_name) : '<span class="text-muted">Unassigned</span>'; ?></td>
                                                <td><span class="badge-pill badge-danger"><?php echo (int) $task->delay_days; ?> day(s)</span></td>
                                                <td><a href="<?php echo spares_execution_tracker_url((int) $task->order_id, (int) $task->execution_task_id); ?>" class="btn btn-primary btn-xs">Open</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="empty-state">No overdue tasks.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-box attention-card">
                        <div class="section-title">
                            <h4 class="m-t-0 header-title"><b>Extension Requests</b></h4>
                            <a href="<?php echo spares_execution_export_url($filters, 'pending_extensions'); ?>" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Export</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Task</th>
                                        <th>Requested Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pending_extensions)): ?>
                                        <?php foreach ($pending_extensions as $request): ?>
                                            <?php $can_review = ((int) $request->marketing_owner_id === $current_user_id) || ((int) $request->created_by === $current_user_id); ?>
                                            <tr>
                                                <td>
                                                    <div class="item-title">SO-<?php echo (int) $request->order_id; ?></div>
                                                    <div class="item-subtitle"><?php echo htmlspecialchars($request->company_name); ?></div>
                                                </td>
                                                <td>
                                                    <div class="item-title"><?php echo htmlspecialchars($request->task_name); ?></div>
                                                    <div class="item-subtitle"><?php echo !empty($request->requested_by_name) ? htmlspecialchars($request->requested_by_name) : 'Requester not mapped'; ?></div>
                                                </td>
                                                <td>
                                                    <div class="item-title"><?php echo spares_execution_short_date($request->requested_due_date); ?></div>
                                                    <div class="item-subtitle">Current: <?php echo spares_execution_short_date($request->current_due_date); ?></div>
                                                </td>
                                                <td>
                                                    <?php if ($can_review): ?>
                                                        <form method="post" action="<?php echo page_url; ?>Spares_execution/review_extension" style="min-width:210px;">
                                                            <input type="hidden" name="order_id" value="<?php echo (int) $request->order_id; ?>">
                                                            <input type="hidden" name="extension_request_id" value="<?php echo (int) $request->extension_request_id; ?>">
                                                            <input type="hidden" name="redirect_to" value="dashboard">
                                                            <input type="hidden" name="redirect_view_mode" value="<?php echo htmlspecialchars($view_mode); ?>">
                                                            <input type="hidden" name="redirect_workflow_type" value="<?php echo htmlspecialchars($workflow_type_filter); ?>">
                                                            <input type="hidden" name="redirect_execution_status" value="<?php echo htmlspecialchars($execution_status_filter); ?>">
                                                            <input type="hidden" name="redirect_search" value="<?php echo htmlspecialchars($search_filter); ?>">
                                                            <select name="decision" class="form-control input-sm" style="margin-bottom:6px;">
                                                                <option value="Approved">Approve</option>
                                                                <option value="Rejected">Reject</option>
                                                            </select>
                                                            <button type="submit" class="btn btn-success btn-xs">Submit</button>
                                                            <a href="<?php echo spares_execution_tracker_url((int) $request->order_id, 0, (int) $request->extension_request_id); ?>" class="btn btn-default btn-xs">Open</a>
                                                        </form>
                                                    <?php else: ?>
                                                        <a href="<?php echo spares_execution_tracker_url((int) $request->order_id, 0, (int) $request->extension_request_id); ?>" class="btn btn-primary btn-xs">Open</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="empty-state">No extension requests pending.</td></tr>
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
