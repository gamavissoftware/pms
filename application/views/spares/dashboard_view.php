<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$workflow_labels = array(
    'IN_STOCK' => 'In Stock',
    'STANDARD' => 'Standard Procurement',
    'CUSTOM' => 'Custom Production',
);
$current_user_id = (int) $this->session->userdata['logged_in']['user_id'];
$filters = !empty($filters) ? $filters : array();
$view_mode = !empty($filters['view_mode']) ? $filters['view_mode'] : 'all';
$workflow_type_filter = !empty($filters['workflow_type']) ? $filters['workflow_type'] : '';
$execution_status_filter = !empty($filters['execution_status']) ? $filters['execution_status'] : '';
$priority_filter = !empty($filters['priority']) ? $filters['priority'] : '';
$search_filter = !empty($filters['search']) ? $filters['search'] : '';

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

function spares_execution_task_badge_class($status)
{
    $map = array(
        'Pending' => 'badge-neutral',
        'Open' => 'badge-info',
        'In Progress' => 'badge-warning',
        'Completed' => 'badge-success',
        'Blocked' => 'badge-danger',
        'On Hold' => 'badge-hold',
        'Cancelled' => 'badge-neutral',
    );

    return isset($map[$status]) ? $map[$status] : 'badge-neutral';
}

function spares_execution_health_class($status)
{
    $map = array(
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
    );

    return isset($map[$status]) ? $map[$status] : 'badge-neutral';
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

if (!function_exists('spares_execution_extract_stale_count')) {
    function spares_execution_extract_stale_count($run_note)
    {
        if (preg_match('/Stale task alerts:\s*(\d+)/i', (string) $run_note, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}

if (!function_exists('spares_execution_run_stale_count')) {
    function spares_execution_run_stale_count($run)
    {
        if (isset($run->stale_sent_count)) {
            return (int) $run->stale_sent_count;
        }

        return spares_execution_extract_stale_count(!empty($run->run_note) ? $run->run_note : '');
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
        .metric-card { background: #fff; border: 1px solid #e8edf3; border-radius: 10px; padding: 18px; margin-bottom: 20px; min-height: 120px; }
        .metric-label { font-size: 12px; color: #7f8a9a; text-transform: uppercase; letter-spacing: 0.5px; }
        .metric-value { font-size: 28px; font-weight: 700; color: #223247; line-height: 1.2; margin-top: 8px; }
        .metric-note { font-size: 12px; color: #7f8a9a; margin-top: 8px; }
        .table > thead > tr > th,
        .table > tbody > tr > td { vertical-align: middle; }
        .workflow-chip { display: inline-block; padding: 3px 10px; border-radius: 999px; background: #eef5ff; color: #1f5aa6; font-size: 11px; font-weight: 600; }
        .badge-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-neutral { background: #eef2f6; color: #5f6f81; }
        .badge-info { background: #e8f2ff; color: #1f5aa6; }
        .badge-warning { background: #fff3dd; color: #9a6700; }
        .badge-success { background: #e6f7ef; color: #1f7a4c; }
        .badge-danger { background: #fdebec; color: #b42318; }
        .badge-hold { background: #f1ebff; color: #6941c6; }
        .progress-wrap { min-width: 140px; }
        .progress { height: 8px; margin-bottom: 6px; background: #edf1f7; box-shadow: none; }
        .progress-bar { background: #2b7cff; }
        .queue-card { min-height: 100%; }
        .queue-item-title { font-weight: 600; color: #223247; }
        .queue-item-subtitle { font-size: 12px; color: #7f8a9a; }
        .risk-line { display: block; font-size: 12px; color: #7f8a9a; margin-bottom: 4px; }
        .risk-alert { color: #b42318; font-weight: 600; }
        .review-box { min-width: 260px; }
        .section-actions { margin-bottom: 15px; }
        .filter-panel { padding: 18px; margin-bottom: 20px; }
        .filter-chip { display: inline-block; padding: 8px 14px; border: 1px solid #d8e2ee; border-radius: 999px; color: #425466; background: #fff; margin-right: 8px; margin-bottom: 10px; text-decoration: none !important; font-size: 12px; font-weight: 600; }
        .filter-chip:hover { border-color: #2b7cff; color: #2b7cff; }
        .filter-chip.active { background: #2b7cff; border-color: #2b7cff; color: #fff; }
        .filter-help { font-size: 12px; color: #7f8a9a; margin-top: 10px; }
        .code-box { background: #f8fafc; border: 1px solid #d8e2ee; border-radius: 8px; padding: 10px 12px; font-family: monospace; font-size: 12px; color: #243447; word-break: break-all; }
        .health-card { min-height: 148px; }
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
                            <a href="<?php echo page_url; ?>Spares_execution/department_tasks" class="btn btn-default waves-effect waves-light"><i class="fa fa-users"></i> My Department</a>
                            <a href="<?php echo page_url; ?>Spares_execution/my_tasks" class="btn btn-success waves-effect waves-light"><i class="fa fa-check-square-o"></i> My Tasks</a>
                            <a href="<?php echo page_url; ?>Spares_execution/alerts" class="btn btn-info waves-effect waves-light"><i class="fa fa-bell"></i> My Alerts <?php if (!empty($unread_execution_alerts)): ?>(<?php echo (int) $unread_execution_alerts; ?>)<?php endif; ?></a>
                            <form method="post" action="<?php echo page_url; ?>Spares_execution/send_alerts" style="display:inline-block; margin-right:8px;">
                                <button type="submit" class="btn btn-warning waves-effect waves-light"><i class="fa fa-bell"></i> Send Today's Alerts</button>
                            </form>
                            <a href="<?php echo page_url; ?>Dashboard/sparesdashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Spares Dashboard</a>
                            <a href="<?php echo page_url; ?>Spares/running_orders_list" class="btn btn-default waves-effect waves-light"><i class="fa fa-list"></i> Running Orders</a>
                            <a href="<?php echo page_url; ?>Spares_execution/task_master" class="btn btn-primary waves-effect waves-light"><i class="fa fa-sitemap"></i> Task Master</a>
                        </div>
                        <h4 class="page-title">Spares Execution Dashboard</h4>
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
                <div class="col-md-2">
                    <div class="metric-card">
                        <div class="metric-label">Total Execution Orders</div>
                        <div class="metric-value"><?php echo (int) $metrics->total_orders; ?></div>
                        <div class="metric-note"><?php echo (int) $metrics->completed_orders; ?> completed till now</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="metric-card">
                        <div class="metric-label">Active Orders</div>
                        <div class="metric-value"><?php echo (int) $metrics->active_orders; ?></div>
                        <div class="metric-note"><?php echo (int) $metrics->scheduled_orders; ?> scheduled, <?php echo (int) $metrics->in_progress_orders; ?> in progress, <?php echo (int) $metrics->on_hold_orders; ?> on hold</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="metric-card">
                        <div class="metric-label">Pending Setup</div>
                        <div class="metric-value text-info"><?php echo (int) $metrics->unscheduled_orders; ?></div>
                        <div class="metric-note">Won/running orders not yet scheduled</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="metric-card">
                        <div class="metric-label">Due This Week</div>
                        <div class="metric-value"><?php echo (int) $metrics->due_this_week; ?></div>
                        <div class="metric-note">Commit dates within 7 days</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="metric-card">
                        <div class="metric-label">Overdue Tasks</div>
                        <div class="metric-value text-danger"><?php echo (int) $metrics->overdue_tasks; ?></div>
                        <div class="metric-note">Pending task deadlines crossed</div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="metric-card">
                        <div class="metric-label">Pending Extensions</div>
                        <div class="metric-value text-warning"><?php echo (int) $metrics->pending_extensions; ?></div>
                        <div class="metric-note"><?php echo (int) $metrics->critical_orders; ?> critical orders live</div>
                    </div>
                </div>
            </div>

            <?php if (!empty($module_health)): ?>
                <div class="row">
                    <?php foreach ($module_health as $health_item): ?>
                        <div class="col-md-3">
                            <div class="metric-card health-card">
                                <div class="metric-label"><?php echo htmlspecialchars($health_item['label']); ?></div>
                                <div class="metric-value"><?php echo htmlspecialchars((string) $health_item['value']); ?></div>
                                <div class="metric-note"><?php echo htmlspecialchars($health_item['note']); ?></div>
                                <div style="margin-top:10px;">
                                    <span class="badge-pill <?php echo spares_execution_health_class($health_item['status']); ?>"><?php echo htmlspecialchars(ucfirst($health_item['status'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box filter-panel">
                        <div class="row">
                            <div class="col-md-12">
                                <a href="<?php echo spares_execution_filter_url($filters, array('view_mode' => 'all')); ?>" class="filter-chip <?php echo $view_mode === 'all' ? 'active' : ''; ?>">All Orders</a>
                                <a href="<?php echo spares_execution_filter_url($filters, array('view_mode' => 'my')); ?>" class="filter-chip <?php echo $view_mode === 'my' ? 'active' : ''; ?>">My Orders</a>
                                <a href="<?php echo spares_execution_filter_url($filters, array('view_mode' => 'overdue')); ?>" class="filter-chip <?php echo $view_mode === 'overdue' ? 'active' : ''; ?>">Overdue Only</a>
                                <a href="<?php echo spares_execution_filter_url($filters, array('view_mode' => 'pending_extensions')); ?>" class="filter-chip <?php echo $view_mode === 'pending_extensions' ? 'active' : ''; ?>">Pending Extensions</a>
                            </div>
                        </div>
                        <form method="get" action="<?php echo page_url; ?>Spares_execution/dashboard" class="m-t-10">
                            <input type="hidden" name="view_mode" value="<?php echo htmlspecialchars($view_mode); ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search_filter); ?>" placeholder="SO, PO, opportunity, customer">
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                                        <label>Status</label>
                                        <select name="execution_status" class="form-control">
                                            <option value="">All Statuses</option>
                                            <?php foreach ($status_options as $status_option): ?>
                                                <option value="<?php echo htmlspecialchars($status_option); ?>" <?php echo $execution_status_filter === $status_option ? 'selected' : ''; ?>><?php echo htmlspecialchars($status_option); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Priority</label>
                                        <select name="priority" class="form-control">
                                            <option value="">All Priorities</option>
                                            <?php foreach ($priority_options as $priority_option): ?>
                                                <option value="<?php echo htmlspecialchars($priority_option); ?>" <?php echo $priority_filter === $priority_option ? 'selected' : ''; ?>><?php echo htmlspecialchars($priority_option); ?></option>
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
                        <div class="filter-help">Quick views filter the execution list below. `My Orders` and search also narrow the pending setup and approval queues. `Send Today's Alerts` creates in-app alerts for pending setup, overdue tasks, stale tasks, and pending extensions, with same-day duplicate protection.</div>
                        <div class="section-actions" style="margin-top:15px;">
                            <a href="<?php echo spares_execution_export_url($filters, 'execution_orders'); ?>" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Export Execution Orders</a>
                            <a href="<?php echo spares_execution_export_url($filters, 'pending_setup'); ?>" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Export Pending Setup</a>
                            <a href="<?php echo spares_execution_export_url($filters, 'overdue_tasks'); ?>" class="btn btn-danger btn-sm"><i class="fa fa-download"></i> Export Overdue Tasks</a>
                            <a href="<?php echo spares_execution_export_url($filters, 'stale_tasks'); ?>" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Export Stale Tasks</a>
                            <a href="<?php echo spares_execution_export_url($filters, 'pending_extensions'); ?>" class="btn btn-warning btn-sm"><i class="fa fa-download"></i> Export Pending Extensions</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box" style="margin-bottom:20px;">
                        <div class="section-actions clearfix">
                            <h4 class="m-t-0 header-title pull-left"><b>Automation Setup</b></h4>
                        </div>
                        <p class="text-muted">Use the CLI command below in your server cron for daily automated Spares execution alerts, including pending setup, overdue, stale, and extension follow-ups. This is the recommended setup.</p>
                        <div class="code-box"><?php echo htmlspecialchars($cron_command_example); ?></div>
                        <?php if (!empty($cron_web_url_example)): ?>
                            <p class="text-muted" style="margin-top:15px;">
                                <?php if (!empty($cron_web_requires_key)): ?>
                                    If your hosting supports URL-based cron instead of CLI, this secured endpoint can also be used:
                                <?php else: ?>
                                    If your hosting supports URL-based cron instead of CLI, use this direct endpoint:
                                <?php endif; ?>
                            </p>
                            <div class="code-box"><?php echo htmlspecialchars($cron_web_url_example); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="section-actions clearfix">
                            <h4 class="m-t-0 header-title pull-left"><b>Recent Automation Runs</b></h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>When</th>
                                        <th>Mode</th>
                                        <th>Triggered By</th>
                                        <th>Sent</th>
                                        <th>Skipped</th>
                                        <th>Breakdown</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_alert_runs)): ?>
                                        <?php foreach ($recent_alert_runs as $run): ?>
                                            <tr>
                                                <td><?php echo date('d M Y h:i A', strtotime($run->created_at)); ?></td>
                                                <td><span class="badge-pill badge-info"><?php echo htmlspecialchars(str_replace('_', ' ', $run->run_mode)); ?></span></td>
                                                <td>
                                                    <?php
                                                    if (!empty($run->first_name)) {
                                                        echo htmlspecialchars(trim($run->title . ' ' . $run->first_name . ' ' . $run->last_name));
                                                    } else {
                                                        echo '<span class="text-muted">System</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo (int) $run->sent_count; ?></td>
                                                <td><?php echo (int) $run->skipped_count; ?></td>
                                                <td>
                                                    <?php $stale_run_count = spares_execution_run_stale_count($run); ?>
                                                    <span class="risk-line">Pending setup: <?php echo (int) $run->unscheduled_sent_count; ?></span>
                                                    <span class="risk-line">Overdue: <?php echo (int) $run->overdue_sent_count; ?></span>
                                                    <span class="risk-line">Stale: <?php echo $stale_run_count; ?></span>
                                                    <span class="risk-line">Extensions: <?php echo (int) $run->pending_extension_sent_count; ?></span>
                                                </td>
                                                <td><?php echo !empty($run->run_note) ? htmlspecialchars($run->run_note) : '<span class="text-muted">-</span>'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center text-muted">No automation run history recorded yet.</td></tr>
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
                        <div class="section-actions clearfix">
                            <h4 class="m-t-0 header-title pull-left"><b>My Execution Alerts</b></h4>
                            <a href="<?php echo page_url; ?>Spares_execution/alerts" class="pull-right btn btn-default btn-xs">Open Full Alert Inbox</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>When</th>
                                        <th>Title</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($my_execution_alerts)): ?>
                                        <?php foreach ($my_execution_alerts as $alert): ?>
                                            <tr>
                                                <td><?php echo date('d M Y h:i A', strtotime($alert->created_at)); ?></td>
                                                <td><?php echo htmlspecialchars($alert->title); ?></td>
                                                <td><?php echo htmlspecialchars($alert->message); ?></td>
                                                <td>
                                                    <?php if ((int) $alert->is_read === 1): ?>
                                                        <span class="badge-pill badge-neutral">Read</span>
                                                    <?php else: ?>
                                                        <span class="badge-pill badge-danger">Unread</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $alert->reference_id; ?>" class="btn btn-primary btn-xs">Open Order</a>
                                                    <?php if ((int) $alert->is_read === 0): ?>
                                                        <a href="<?php echo page_url; ?>Spares_execution/mark_alert_read/<?php echo (int) $alert->id; ?>?redirect=dashboard" class="btn btn-default btn-xs">Mark Read</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted">No Spares execution alerts for your user yet.</td></tr>
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
                        <div class="section-actions clearfix">
                            <h4 class="m-t-0 header-title pull-left"><b>Orders Awaiting Execution Setup</b></h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>SO Reference</th>
                                        <th>Customer</th>
                                        <th>Marketing</th>
                                        <th>Order Date</th>
                                        <th>Current Stage</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($unscheduled_orders)): ?>
                                        <?php foreach ($unscheduled_orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <div class="queue-item-title">SO-<?php echo (int) $order->order_id; ?></div>
                                                    <div class="queue-item-subtitle">
                                                        <?php echo !empty($order->po_no) ? 'PO: ' . htmlspecialchars($order->po_no) : 'PO not linked'; ?>
                                                        <?php if (!empty($order->op_no)): ?>
                                                            <br>Opp: <?php echo htmlspecialchars($order->op_no); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="queue-item-title"><?php echo htmlspecialchars($order->company_name); ?></div>
                                                    <div class="queue-item-subtitle">Order Value: <?php echo number_format((float) $order->order_value, 2); ?></div>
                                                </td>
                                                <td><?php echo !empty($order->marketing_person_name) ? htmlspecialchars($order->marketing_person_name) : '<span class="text-muted">Not mapped</span>'; ?></td>
                                                <td><?php echo !empty($order->order_date) ? date('d M Y', strtotime($order->order_date)) : '<span class="text-muted">Not set</span>'; ?></td>
                                                <td><?php echo !empty($order->current_progress_stage) ? htmlspecialchars($order->current_progress_stage) : '<span class="text-muted">Order Received</span>'; ?></td>
                                                <td><span class="badge-pill badge-warning"><?php echo htmlspecialchars($order->spare_order_status); ?></span></td>
                                                <td>
                                                    <a href="<?php echo page_url; ?>Spares_execution/schedule/<?php echo (int) $order->order_id; ?>" class="btn btn-primary btn-xs">Start Scheduling</a>
                                                    <a href="<?php echo page_url; ?>Spares/order_detail/<?php echo (int) $order->order_id; ?>" class="btn btn-default btn-xs">Order Detail</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center text-muted">All running spare orders already have execution schedules.</td></tr>
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
                        <div class="section-actions clearfix">
                            <h4 class="m-t-0 header-title pull-left"><b>Execution Orders</b></h4>
                            <span class="pull-right text-muted" style="padding-top: 4px;">View: <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $view_mode))); ?></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>SO Reference</th>
                                        <th>Customer</th>
                                        <th>Workflow</th>
                                        <th>Marketing</th>
                                        <th>Commit Date</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Risk</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($execution_orders)): ?>
                                        <?php foreach ($execution_orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <div class="queue-item-title">SO-<?php echo (int) $order->order_id; ?></div>
                                                    <div class="queue-item-subtitle">
                                                        <?php echo !empty($order->po_no) ? 'PO: ' . htmlspecialchars($order->po_no) : 'PO not linked'; ?>
                                                        <?php if (!empty($order->op_no)): ?>
                                                            <br>Opp: <?php echo htmlspecialchars($order->op_no); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="queue-item-title"><?php echo htmlspecialchars($order->company_name); ?></div>
                                                    <div class="queue-item-subtitle">Order Value: <?php echo number_format((float) $order->order_value, 2); ?></div>
                                                </td>
                                                <td><span class="workflow-chip"><?php echo htmlspecialchars(isset($workflow_labels[$order->workflow_type]) ? $workflow_labels[$order->workflow_type] : $order->workflow_type); ?></span></td>
                                                <td><?php echo !empty($order->marketing_person_name) ? htmlspecialchars($order->marketing_person_name) : '<span class="text-muted">Not mapped</span>'; ?></td>
                                                <td><?php echo !empty($order->commit_date) ? date('d M Y', strtotime($order->commit_date)) : '<span class="text-muted">Not set</span>'; ?></td>
                                                <td><span class="badge-pill <?php echo spares_execution_priority_class($order->priority); ?>"><?php echo htmlspecialchars($order->priority); ?></span></td>
                                                <td><span class="badge-pill <?php echo spares_execution_status_class($order->execution_status); ?>"><?php echo htmlspecialchars($order->execution_status); ?></span></td>
                                                <td class="progress-wrap">
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" style="width: <?php echo (int) $order->progress_percent; ?>%;"></div>
                                                    </div>
                                                    <small><?php echo (int) $order->progress_percent; ?>% complete, <?php echo (int) $order->completed_tasks; ?>/<?php echo (int) $order->total_tasks; ?> tasks completed</small>
                                                </td>
                                                <td>
                                                    <?php if ((int) $order->overdue_tasks > 0): ?>
                                                        <span class="risk-line risk-alert"><?php echo (int) $order->overdue_tasks; ?> overdue task(s)</span>
                                                    <?php endif; ?>
                                                    <?php if ((int) $order->blocked_tasks > 0): ?>
                                                        <span class="risk-line"><?php echo (int) $order->blocked_tasks; ?> blocked task(s)</span>
                                                    <?php endif; ?>
                                                    <?php if ((int) $order->on_hold_tasks > 0): ?>
                                                        <span class="risk-line"><?php echo (int) $order->on_hold_tasks; ?> on hold task(s)</span>
                                                    <?php endif; ?>
                                                    <?php if ((int) $order->pending_extensions > 0): ?>
                                                        <span class="risk-line"><?php echo (int) $order->pending_extensions; ?> extension request(s) pending</span>
                                                    <?php endif; ?>
                                                    <?php if ((int) $order->overdue_tasks === 0 && (int) $order->pending_extensions === 0 && (int) $order->blocked_tasks === 0 && (int) $order->on_hold_tasks === 0): ?>
                                                        <span class="risk-line text-success">On track</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $order->order_id; ?>" class="btn btn-primary btn-xs">Open Tracker</a>
                                                    <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo (int) $order->order_id; ?>" class="btn btn-info btn-xs">Gantt</a>
                                                    <?php if ((int) $order->marketing_owner_id === $current_user_id || (int) $order->created_by === $current_user_id): ?>
                                                        <a href="<?php echo page_url; ?>Spares_execution/edit_schedule/<?php echo (int) $order->order_id; ?>" class="btn btn-warning btn-xs">Edit Schedule</a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo page_url; ?>Spares/order_detail/<?php echo (int) $order->order_id; ?>" class="btn btn-default btn-xs">Order Detail</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="10" class="text-center text-muted">No execution orders matched the current filters.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card-box queue-card">
                        <h4 class="m-t-0 header-title"><b>Overdue Task Queue</b></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Task</th>
                                        <th>Owner</th>
                                        <th>Due Date</th>
                                        <th>Delay</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($overdue_tasks)): ?>
                                        <?php foreach ($overdue_tasks as $task): ?>
                                            <tr>
                                                <td>
                                                    <div class="queue-item-title">SO-<?php echo (int) $task->order_id; ?></div>
                                                    <div class="queue-item-subtitle"><?php echo htmlspecialchars($task->company_name); ?></div>
                                                </td>
                                                <td>
                                                    <div class="queue-item-title"><?php echo htmlspecialchars($task->task_name); ?></div>
                                                    <div class="queue-item-subtitle"><?php echo !empty($task->department) ? htmlspecialchars($task->department) : 'Department not set'; ?></div>
                                                </td>
                                                <td><?php echo !empty($task->owner_name) ? htmlspecialchars($task->owner_name) : '<span class="text-muted">Unassigned</span>'; ?></td>
                                                <td><?php echo date('d M Y', strtotime($task->planned_end_date)); ?></td>
                                                <td><span class="badge-pill badge-danger"><?php echo (int) $task->delay_days; ?> day(s)</span></td>
                                                <td>
                                                    <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $task->order_id; ?>" class="btn btn-primary btn-xs">Open</a>
                                                    <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo (int) $task->order_id; ?>" class="btn btn-info btn-xs">Gantt</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center text-muted">No overdue execution tasks.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-box queue-card">
                        <h4 class="m-t-0 header-title"><b>Pending Extension Approvals</b></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Task</th>
                                        <th>Requested By</th>
                                        <th>Due Change</th>
                                        <th>Review</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pending_extensions)): ?>
                                        <?php foreach ($pending_extensions as $request): ?>
                                            <?php $can_review = ((int) $request->marketing_owner_id === $current_user_id) || ((int) $request->created_by === $current_user_id); ?>
                                            <tr>
                                                <td>
                                                    <div class="queue-item-title">SO-<?php echo (int) $request->order_id; ?></div>
                                                    <div class="queue-item-subtitle"><?php echo htmlspecialchars($request->company_name); ?></div>
                                                </td>
                                                <td>
                                                    <div class="queue-item-title"><?php echo htmlspecialchars($request->task_name); ?></div>
                                                    <div class="queue-item-subtitle"><?php echo htmlspecialchars(isset($workflow_labels[$request->workflow_type]) ? $workflow_labels[$request->workflow_type] : $request->workflow_type); ?></div>
                                                </td>
                                                <td>
                                                    <?php echo !empty($request->requested_by_name) ? htmlspecialchars($request->requested_by_name) : '<span class="text-muted">Unknown</span>'; ?>
                                                    <div class="queue-item-subtitle"><?php echo date('d M Y h:i A', strtotime($request->requested_on)); ?></div>
                                                </td>
                                                <td>
                                                    <span class="risk-line"><?php echo date('d M Y', strtotime($request->current_due_date)); ?> to <?php echo date('d M Y', strtotime($request->requested_due_date)); ?></span>
                                                    <div class="queue-item-subtitle"><?php echo nl2br(htmlspecialchars($request->reason)); ?></div>
                                                </td>
                                                <td class="review-box">
                                                    <?php if ($can_review): ?>
                                                        <form method="post" action="<?php echo page_url; ?>Spares_execution/review_extension">
                                                            <input type="hidden" name="order_id" value="<?php echo (int) $request->order_id; ?>">
                                                            <input type="hidden" name="extension_request_id" value="<?php echo (int) $request->extension_request_id; ?>">
                                                            <div class="form-group">
                                                                <select name="decision" class="form-control input-sm">
                                                                    <option value="Approved">Approve</option>
                                                                    <option value="Rejected">Reject</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" name="review_remarks" class="form-control input-sm" placeholder="Review note">
                                                            </div>
                                                            <button type="submit" class="btn btn-success btn-xs">Submit</button>
                                                            <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $request->order_id; ?>" class="btn btn-default btn-xs">Open</a>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">Awaiting marketing owner review</span><br>
                                                        <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $request->order_id; ?>" class="btn btn-default btn-xs m-t-5">Open Tracker</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted">No pending extension approvals.</td></tr>
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
                        <div class="section-actions clearfix">
                            <h4 class="m-t-0 header-title pull-left"><b>Stale Follow-up Queue</b></h4>
                            <span class="pull-right text-muted" style="padding-top:4px;">No activity in the last <?php echo (int) $stale_after_days; ?> day(s)</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Task</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                        <th>Last Activity</th>
                                        <th>Stale Age</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($stale_tasks)): ?>
                                        <?php foreach ($stale_tasks as $task): ?>
                                            <tr>
                                                <td>
                                                    <div class="queue-item-title">SO-<?php echo (int) $task->order_id; ?></div>
                                                    <div class="queue-item-subtitle"><?php echo htmlspecialchars($task->company_name); ?></div>
                                                </td>
                                                <td>
                                                    <div class="queue-item-title"><?php echo htmlspecialchars($task->task_name); ?></div>
                                                    <div class="queue-item-subtitle"><?php echo !empty($task->department) ? htmlspecialchars($task->department) : 'Department not set'; ?></div>
                                                </td>
                                                <td><?php echo !empty($task->owner_name) ? htmlspecialchars($task->owner_name) : '<span class="text-muted">Unassigned</span>'; ?></td>
                                                <td><span class="badge-pill <?php echo spares_execution_task_badge_class($task->task_status); ?>"><?php echo htmlspecialchars($task->task_status); ?></span></td>
                                                <td>
                                                    <?php if (!empty($task->last_activity_at)): ?>
                                                        <?php echo date('d M Y h:i A', strtotime($task->last_activity_at)); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No activity</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="badge-pill badge-warning"><?php echo max(0, (int) $task->stale_days); ?> day(s)</span></td>
                                                <td>
                                                    <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $task->order_id; ?>" class="btn btn-primary btn-xs">Open Tracker</a>
                                                    <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo (int) $task->order_id; ?>" class="btn btn-info btn-xs">Gantt</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center text-muted">No stale active tasks found for the current dashboard filters.</td></tr>
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
                        <div class="section-actions clearfix">
                            <h4 class="m-t-0 header-title pull-left"><b>Recent Task Activity</b></h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>When</th>
                                        <th>Order</th>
                                        <th>Task</th>
                                        <th>Update</th>
                                        <th>By</th>
                                        <th>Note</th>
                                        <th>Attachment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_task_activity)): ?>
                                        <?php foreach ($recent_task_activity as $activity): ?>
                                            <tr>
                                                <td><?php echo date('d M Y h:i A', strtotime($activity->added_on)); ?></td>
                                                <td>
                                                    <div class="queue-item-title">SO-<?php echo (int) $activity->order_id; ?></div>
                                                    <div class="queue-item-subtitle"><?php echo htmlspecialchars($activity->company_name); ?></div>
                                                </td>
                                                <td>
                                                    <div class="queue-item-title"><?php echo htmlspecialchars($activity->task_name); ?></div>
                                                    <div class="queue-item-subtitle"><?php echo htmlspecialchars(isset($workflow_labels[$activity->workflow_type]) ? $workflow_labels[$activity->workflow_type] : $activity->workflow_type); ?></div>
                                                </td>
                                                <td>
                                                    <span class="badge-pill badge-info"><?php echo htmlspecialchars($activity->update_type); ?></span>
                                                    <?php if (!empty($activity->new_status)): ?>
                                                        <div class="queue-item-subtitle">Now: <?php echo htmlspecialchars($activity->new_status); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo !empty($activity->updated_by_name) ? htmlspecialchars($activity->updated_by_name) : '<span class="text-muted">Unknown</span>'; ?></td>
                                                <td><?php echo !empty($activity->remarks) ? nl2br(htmlspecialchars($activity->remarks)) : '<span class="text-muted">-</span>'; ?></td>
                                                <td>
                                                    <?php if (!empty($activity->attachment_name)): ?>
                                                        <a href="<?php echo page_url . 'uploads/spares_execution/' . rawurlencode($activity->attachment_name); ?>" target="_blank">View File</a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $activity->order_id; ?>" class="btn btn-primary btn-xs">Open Tracker</a>
                                                    <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo (int) $activity->order_id; ?>" class="btn btn-info btn-xs">Gantt</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="text-center text-muted">No recent task activity found for the current dashboard filters.</td></tr>
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
                        <h4 class="m-t-0 header-title"><b>Recent Alert Log</b></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>When</th>
                                        <th>Alert Type</th>
                                        <th>Recipient</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_alerts)): ?>
                                        <?php foreach ($recent_alerts as $alert): ?>
                                            <tr>
                                                <td><?php echo date('d M Y h:i A', strtotime($alert->created_at)); ?></td>
                                                <td><?php echo htmlspecialchars(str_replace('_', ' ', $alert->alert_type)); ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($alert->first_name)) {
                                                        echo htmlspecialchars(trim($alert->title . ' ' . $alert->first_name . ' ' . $alert->last_name));
                                                    } else {
                                                        echo '<span class="text-muted">User #' . (int) $alert->recipient_user_id . '</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo !empty($alert->alert_message) ? htmlspecialchars($alert->alert_message) : '<span class="text-muted">-</span>'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center text-muted">No execution alerts have been generated yet.</td></tr>
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
