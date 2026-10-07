<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$workflow_label = isset($workflows[$execution_order->workflow_type]) ? $workflows[$execution_order->workflow_type] : $execution_order->workflow_type;
$sf_released = $sf_form && $sf_form->form_status === 'Released';
$report_items = $latest_run ? $latest_run_items : $preview['items'];
$summary = $latest_run ? $latest_run : $preview['summary'];
$report_label = $latest_run ? 'Last MRP Snapshot' : 'Current Inventory Preview';

if (!function_exists('spares_mrp_qty')) {
    function spares_mrp_qty($qty)
    {
        return number_format((float) $qty, 3);
    }
}

if (!function_exists('spares_mrp_detail_date')) {
    function spares_mrp_detail_date($date)
    {
        return !empty($date) ? date('d M Y', strtotime($date)) : '-';
    }
}

if (!function_exists('spares_mrp_detail_datetime')) {
    function spares_mrp_detail_datetime($date)
    {
        return !empty($date) ? date('d M Y, h:i A', strtotime($date)) : '-';
    }
}

if (!function_exists('spares_mrp_status_class')) {
    function spares_mrp_status_class($status)
    {
        if ($status === 'Available') {
            return 'badge-success';
        }
        if ($status === 'Not In Master') {
            return 'badge-danger';
        }
        return 'badge-warning';
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
        .metric-value { color: #1f2937; font-size: 24px; font-weight: 700; line-height: 1.2; margin-top: 6px; }
        .table > thead > tr > th { background: #f8fafc; color: #334155; font-size: 12px; text-transform: uppercase; vertical-align: middle; }
        .table > tbody > tr > td { vertical-align: middle; }
        .item-title { color: #1f2937; font-weight: 700; }
        .item-subtitle { color: #64748b; font-size: 12px; margin-top: 3px; }
        .badge-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-neutral { background: #edf2f7; color: #475569; }
        .badge-warning { background: #fff7df; color: #9a6700; }
        .badge-danger { background: #fee2e2; color: #b42318; }
        .badge-success { background: #dcfce7; color: #167747; }
        .report-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; }
        .report-head h4 { margin: 0; }
        .empty-state { padding: 26px; text-align: center; color: #64748b; }
        @media print {
            #topnav, .page-title-box .btn-group, .alert, .btn, form { display: none !important; }
            body { background: #fff; }
            .wrapper, .container-fluid { padding: 0; margin: 0; }
            .card-box, .metric-card { box-shadow: none; border-color: #111; }
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
                            <a href="<?php echo page_url; ?>Spares_execution/mrp_shortages" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> MRP Queue</a>
                            <a href="<?php echo page_url; ?>Spares_execution/sf_form/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-purple waves-effect waves-light"><i class="fa fa-file-text-o"></i> SF Form</a>
                            <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-tasks"></i> Execution Tracker</a>
                            <a href="#" onclick="window.print(); return false;" class="btn btn-default waves-effect waves-light"><i class="fa fa-print"></i> Print</a>
                            <?php if ($sf_released && !empty($can_run_mrp)): ?>
                                <form method="post" action="<?php echo page_url; ?>Spares_execution/run_mrp/<?php echo (int) $order_snapshot->order_id; ?>" style="display:inline-block;">
                                    <button type="submit" class="btn btn-success waves-effect waves-light" onclick="return confirm('Run MRP with current inventory qty?');"><i class="fa fa-cogs"></i> Run MRP</button>
                                </form>
                            <?php endif; ?>
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

            <?php if (!$sf_released): ?>
                <div class="alert alert-warning">
                    SF is not released yet. Please release the SF form first, then PPC can run MRP.
                    <a href="<?php echo page_url; ?>Spares_execution/sf_form/<?php echo (int) $order_snapshot->order_id; ?>" class="alert-link">Open SF Form</a>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Order</div>
                        <div class="metric-value">SO-<?php echo (int) $order_snapshot->order_id; ?></div>
                        <div class="item-subtitle"><?php echo html_escape($order_snapshot->company_name); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Quotation Type</div>
                        <div class="metric-value"><?php echo html_escape($workflow_label); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">SF Release</div>
                        <div class="metric-value"><?php echo $sf_form ? html_escape($sf_form->sf_no) : '-'; ?></div>
                        <div class="item-subtitle"><?php echo $sf_form ? spares_mrp_detail_date($sf_form->release_date) : '-'; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Last MRP Run</div>
                        <div class="metric-value"><?php echo $latest_run ? spares_mrp_detail_datetime($latest_run->run_at) : 'Not Run'; ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">BOM Items</div>
                        <div class="metric-value"><?php echo (int) $summary->total_items; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Shortage Items</div>
                        <div class="metric-value text-danger"><?php echo (int) $summary->shortage_items; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Not In Master</div>
                        <div class="metric-value text-warning"><?php echo (int) $summary->missing_master_items; ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="metric-label">Total Shortage Qty</div>
                        <div class="metric-value"><?php echo spares_mrp_qty($summary->total_shortage_qty); ?></div>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="report-head">
                    <h4 class="m-t-0 header-title"><b><?php echo html_escape($report_label); ?></b></h4>
                    <?php if (!$latest_run && $sf_released): ?>
                        <span class="badge-pill badge-neutral">Preview only until MRP is run</span>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>ERP Part No.</th>
                                <th>Required Qty</th>
                                <th>Available Qty</th>
                                <th>Shortage Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($report_items)): ?>
                                <?php foreach ($report_items as $item): ?>
                                    <tr>
                                        <td><?php echo (int) $item->line_no; ?></td>
                                        <td>
                                            <div class="item-title"><?php echo html_escape($item->item_description); ?></div>
                                            <?php if (!empty($item->spare_part_code) && $item->spare_part_code !== $item->part_no_erp): ?>
                                                <div class="item-subtitle">Matched: <?php echo html_escape($item->spare_part_code); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo !empty($item->part_no_erp) ? html_escape($item->part_no_erp) : '<span class="text-muted">Not provided</span>'; ?></td>
                                        <td><?php echo spares_mrp_qty($item->required_qty); ?></td>
                                        <td><?php echo spares_mrp_qty($item->available_qty); ?></td>
                                        <td><strong><?php echo spares_mrp_qty($item->shortage_qty); ?></strong></td>
                                        <td><span class="badge-pill <?php echo spares_mrp_status_class($item->shortage_status); ?>"><?php echo html_escape($item->shortage_status); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-state">No BOM items available for shortage check.</td>
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
