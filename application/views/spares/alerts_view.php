<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
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
        .filter-chip { display: inline-block; padding: 8px 14px; border: 1px solid #d8e2ee; border-radius: 999px; color: #425466; background: #fff; margin-right: 8px; text-decoration: none !important; font-size: 12px; font-weight: 600; }
        .filter-chip.active { background: #2b7cff; border-color: #2b7cff; color: #fff; }
        .badge-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-neutral { background: #eef2f6; color: #5f6f81; }
        .badge-danger { background: #fdebec; color: #b42318; }
        .table > thead > tr > th,
        .table > tbody > tr > td { vertical-align: middle; }
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
                            <?php if (!empty($unread_execution_alerts)): ?>
                                <a href="<?php echo page_url; ?>Spares_execution/mark_all_alerts_read<?php echo $show === 'all' ? '?show=all' : ''; ?>" class="btn btn-warning waves-effect waves-light"><i class="fa fa-check"></i> Mark All Read</a>
                            <?php endif; ?>
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Execution Dashboard</a>
                        </div>
                        <h4 class="page-title">Spares Execution Alerts</h4>
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
                    <div class="card-box" style="padding:18px; margin-bottom:20px;">
                        <a href="<?php echo page_url; ?>Spares_execution/alerts" class="filter-chip <?php echo $show !== 'all' ? 'active' : ''; ?>">Unread Only</a>
                        <a href="<?php echo page_url; ?>Spares_execution/alerts?show=all" class="filter-chip <?php echo $show === 'all' ? 'active' : ''; ?>">All Alerts</a>
                        <span class="pull-right text-muted" style="padding-top:8px;">Unread: <?php echo (int) $unread_execution_alerts; ?></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
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
                                    <?php if (!empty($alerts)): ?>
                                        <?php foreach ($alerts as $alert): ?>
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
                                                    <a href="<?php echo page_url; ?>Spares_execution/gantt/<?php echo (int) $alert->reference_id; ?>" class="btn btn-info btn-xs">Gantt</a>
                                                    <?php if ((int) $alert->is_read === 0): ?>
                                                        <a href="<?php echo page_url; ?>Spares_execution/mark_alert_read/<?php echo (int) $alert->id; ?><?php echo $show === 'all' ? '?show=all' : ''; ?>" class="btn btn-default btn-xs">Mark Read</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted">No Spares execution alerts found for this filter.</td></tr>
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
