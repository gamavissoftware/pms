<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$workflow_labels = !empty($workflows) ? $workflows : array();
$filters = !empty($filters) ? $filters : array();
$mrp_status_filter = !empty($filters['mrp_status']) ? $filters['mrp_status'] : 'all';
$workflow_type_filter = !empty($filters['workflow_type']) ? $filters['workflow_type'] : '';
$search_filter = !empty($filters['search']) ? $filters['search'] : '';
$mrp_status_labels = array(
    'all' => 'All Released SF',
    'pending' => 'Pending MRP',
    'shortage' => 'Shortage',
    'available' => 'Available',
    'missing_master' => 'Not In Master',
);

if (!function_exists('spares_mrp_short_date')) {
    function spares_mrp_short_date($date)
    {
        return !empty($date) ? date('d M Y', strtotime($date)) : '-';
    }
}

if (!function_exists('spares_mrp_short_datetime')) {
    function spares_mrp_short_datetime($date)
    {
        return !empty($date) ? date('d M Y, h:i A', strtotime($date)) : '-';
    }
}

if (!function_exists('spares_mrp_queue_url')) {
    function spares_mrp_queue_url($filters, $overrides = array())
    {
        $params = array_merge($filters, $overrides);

        foreach ($params as $key => $value) {
            if ($key === 'limit' || $value === '' || $value === null || ($key === 'mrp_status' && $value === 'all')) {
                unset($params[$key]);
            }
        }

        $query = http_build_query($params);
        return page_url . 'Spares_execution/mrp_shortages' . (!empty($query) ? '?' . $query : '');
    }
}

if (!function_exists('spares_mrp_export_url')) {
    function spares_mrp_export_url($filters)
    {
        $params = array();

        foreach ($filters as $key => $value) {
            if ($key === 'limit' || $value === '' || $value === null || ($key === 'mrp_status' && $value === 'all')) {
                continue;
            }

            $params[$key] = $value;
        }

        $query = http_build_query($params);
        return page_url . 'Spares_execution/export_mrp_shortages' . (!empty($query) ? '?' . $query : '');
    }
}

$released_count = 0;
$pending_mrp_count = 0;
$shortage_count = 0;
foreach ($orders as $order) {
    $released_count++;
    if (empty($order->mrp_run_id)) {
        $pending_mrp_count++;
    }
    if (!empty($order->shortage_items) && (int) $order->shortage_items > 0) {
        $shortage_count++;
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
        .card-box { border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: none; }
        .metric-card { background: #fff; border: 1px solid #e2e8f0; border-left: 4px solid #2563eb; border-radius: 8px; padding: 15px; min-height: 94px; margin-bottom: 18px; }
        .metric-label { color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; }
        .metric-value { color: #1f2937; font-size: 28px; font-weight: 700; line-height: 1.2; margin-top: 6px; }
        .table > thead > tr > th { background: #f8fafc; color: #334155; font-size: 12px; text-transform: uppercase; vertical-align: middle; }
        .table > tbody > tr > td { vertical-align: middle; }
        .item-title { color: #1f2937; font-weight: 700; }
        .item-subtitle { color: #64748b; font-size: 12px; margin-top: 3px; }
        .badge-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-neutral { background: #edf2f7; color: #475569; }
        .badge-info { background: #e8f2ff; color: #1d4ed8; }
        .badge-warning { background: #fff7df; color: #9a6700; }
        .badge-danger { background: #fee2e2; color: #b42318; }
        .badge-success { background: #dcfce7; color: #167747; }
        .workflow-chip { display: inline-block; padding: 4px 9px; border-radius: 999px; background: #eef5ff; color: #1f5aa6; font-size: 11px; font-weight: 700; }
        .filter-panel { padding: 16px; margin-bottom: 18px; }
        .filter-chip { display: inline-block; padding: 8px 13px; border: 1px solid #d8e2ee; border-radius: 999px; color: #425466; background: #fff; margin-right: 8px; margin-bottom: 10px; text-decoration: none !important; font-size: 12px; font-weight: 700; }
        .filter-chip:hover { border-color: #2563eb; color: #2563eb; }
        .filter-chip.active { background: #2563eb; border-color: #2563eb; color: #fff; }
        .empty-state { padding: 26px; text-align: center; color: #64748b; }
        .action-stack .btn, .action-stack form { display: inline-block; margin: 0 4px 4px 0; }
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
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-dashboard"></i> Execution Dashboard</a>
                            <a href="<?php echo page_url; ?>Spares_master" class="btn btn-default waves-effect waves-light"><i class="fa fa-cubes"></i> Inventory Master</a>
                        </div>
                        <h4 class="page-title">PPC MRP & Shortage Report</h4>
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
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-label">Released SF</div>
                        <div class="metric-value"><?php echo (int) $released_count; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-label">Pending MRP</div>
                        <div class="metric-value text-warning"><?php echo (int) $pending_mrp_count; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-label">Orders With Shortage</div>
                        <div class="metric-value text-danger"><?php echo (int) $shortage_count; ?></div>
                    </div>
                </div>
            </div>

            <div class="card-box filter-panel">
                <div class="row">
                    <div class="col-md-12">
                        <?php foreach ($mrp_status_labels as $status_key => $status_label): ?>
                            <a href="<?php echo spares_mrp_queue_url($filters, array('mrp_status' => $status_key)); ?>" class="filter-chip <?php echo $mrp_status_filter === $status_key ? 'active' : ''; ?>"><?php echo html_escape($status_label); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <form method="get" action="<?php echo page_url; ?>Spares_execution/mrp_shortages" class="m-t-10">
                    <input type="hidden" name="mrp_status" value="<?php echo html_escape($mrp_status_filter); ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" value="<?php echo html_escape($search_filter); ?>" placeholder="Customer, opportunity, PO, SF no.">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Quotation Type</label>
                                <select name="workflow_type" class="form-control">
                                    <option value="">All Quotation Types</option>
                                    <?php foreach ($workflow_labels as $workflow_key => $workflow_label): ?>
                                        <option value="<?php echo html_escape($workflow_key); ?>" <?php echo $workflow_type_filter === $workflow_key ? 'selected' : ''; ?>><?php echo html_escape($workflow_label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages" class="btn btn-default">Reset</a>
                                <a href="<?php echo spares_mrp_export_url($filters); ?>" class="btn btn-success"><i class="fa fa-download"></i> Export CSV</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Quotation Type</th>
                                <th>SF Release</th>
                                <th>Last MRP</th>
                                <th>Shortage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                    $has_shortage = !empty($order->shortage_items) && (int) $order->shortage_items > 0;
                                    $mrp_status_class = empty($order->mrp_run_id) ? 'badge-warning' : ($has_shortage ? 'badge-danger' : 'badge-success');
                                    $mrp_status_text = empty($order->mrp_run_id) ? 'Not Run' : ($has_shortage ? 'Shortage' : 'Available');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="item-title">SO-<?php echo (int) $order->order_id; ?></div>
                                            <div class="item-subtitle"><?php echo !empty($order->po_no) ? 'PO: ' . html_escape($order->po_no) : 'PO not linked'; ?></div>
                                        </td>
                                        <td>
                                            <div class="item-title"><?php echo html_escape($order->company_name); ?></div>
                                            <div class="item-subtitle"><?php echo !empty($order->op_no) ? 'Opp: ' . html_escape($order->op_no) : ''; ?></div>
                                        </td>
                                        <td><span class="workflow-chip"><?php echo html_escape(isset($workflow_labels[$order->workflow_type]) ? $workflow_labels[$order->workflow_type] : $order->workflow_type); ?></span></td>
                                        <td>
                                            <div class="item-title"><?php echo html_escape($order->sf_no); ?></div>
                                            <div class="item-subtitle"><?php echo spares_mrp_short_date($order->release_date); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge-pill <?php echo $mrp_status_class; ?>"><?php echo $mrp_status_text; ?></span>
                                            <div class="item-subtitle"><?php echo spares_mrp_short_datetime($order->latest_mrp_run_at); ?></div>
                                        </td>
                                        <td>
                                            <div class="item-title"><?php echo (int) $order->shortage_items; ?> item(s)</div>
                                            <div class="item-subtitle"><?php echo number_format((float) $order->total_shortage_qty, 3); ?> qty shortage</div>
                                            <?php if ((int) $order->missing_master_items > 0): ?>
                                                <div class="item-subtitle text-danger"><?php echo (int) $order->missing_master_items; ?> not in master</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-stack">
                                            <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages/<?php echo (int) $order->order_id; ?>" class="btn btn-primary btn-xs">Open</a>
                                            <?php if (!empty($can_run_mrp)): ?>
                                                <form method="post" action="<?php echo page_url; ?>Spares_execution/run_mrp/<?php echo (int) $order->order_id; ?>">
                                                    <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Run MRP with current inventory qty?');">Run MRP</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-state">No released SF is waiting for MRP.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/footer'); ?>
</body>
</html>
