<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Running DF finance control room.
|
| Every figure on this page comes from one of three registers: the PO book, the
| payment-term claim schedule, and the DF dispatch ledger. The view only
| presents what the controller has already reconciled.
*/

$report = isset($report) && is_array($report) ? $report : array();
$filters = isset($filters) && is_array($filters) ? $filters : array();
$df_options = isset($df_options) && is_array($df_options) ? $df_options : array();

$rows = isset($report['rows']) && is_array($report['rows']) ? $report['rows'] : array();
$summary = isset($report['summary']) && is_array($report['summary']) ? $report['summary'] : array();
$insights = isset($report['insights']) && is_array($report['insights']) ? $report['insights'] : array();
$action_rows = isset($report['action_rows']) && is_array($report['action_rows']) ? $report['action_rows'] : array();
$config_issues = isset($report['config_issues']) && is_array($report['config_issues']) ? $report['config_issues'] : array();
$marketing_summary = isset($report['marketing_summary']) && is_array($report['marketing_summary']) ? $report['marketing_summary'] : array();
$filter_summary = isset($report['filter_summary']) && is_array($report['filter_summary']) ? $report['filter_summary'] : array();

$has_date_filter = !empty($filters['has_date_filter']);
$start_date_value = $has_date_filter && isset($filters['start_date']) ? $filters['start_date'] : '';
$end_date_value = $has_date_filter && isset($filters['end_date']) ? $filters['end_date'] : '';
$current_df_filter = isset($filters['df_id']) ? (string) $filters['df_id'] : 'ALL';
$filter_path = ($has_date_filter ? $start_date_value . '/' . $end_date_value : 'ALL/ALL') . '/' . $current_df_filter;

if (!function_exists('accounts_master_report_escape')) {
    function accounts_master_report_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('accounts_master_report_value')) {
    function accounts_master_report_value($source, $key, $fallback = '')
    {
        return isset($source[$key]) ? $source[$key] : $fallback;
    }
}

/*
| Split an order into the four states its money can be in. Segments are clamped
| so a DF billed ahead of its milestones still renders a sane bar.
*/
if (!function_exists('accounts_master_report_flow')) {
    function accounts_master_report_flow($row)
    {
        $order = max(0, (float) accounts_master_report_value($row, 'order_value', 0));
        if ($order <= 0) {
            return array();
        }

        $received = min(max(0, (float) accounts_master_report_value($row, 'received_amount', 0)), $order);
        $unpaid = min(max(0, (float) accounts_master_report_value($row, 'balance_amount', 0)), $order - $received);
        $ready = min(max(0, (float) accounts_master_report_value($row, 'unbilled_amount', 0)), $order - $received - $unpaid);
        $pipeline = max(0, $order - $received - $unpaid - $ready);

        $segments = array();
        foreach (array(
            array('received', 'Received', $received),
            array('unpaid', 'Invoiced, awaiting payment', $unpaid),
            array('ready', 'Ready to invoice', $ready),
            array('pipeline', 'Not yet earned', $pipeline)
        ) as $segment) {
            if ($segment[2] > 0.01) {
                $segments[] = array(
                    'key' => $segment[0],
                    'label' => $segment[1],
                    'width' => round(($segment[2] / $order) * 100, 2)
                );
            }
        }

        return $segments;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Running DF finance control room">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | Running DF Finance Control</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <?php $this->load->view('accounts/_finance_theme'); ?>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <?php $this->load->view('common/info-section.php'); ?>

    <div class="wrapper">
        <div class="container-fluid fin-shell">

            <div class="fin-card fin-hero">
                <div class="row">
                    <div class="col-lg-8">
                        <span class="fin-kicker">Finance Control Room</span>
                        <div class="fin-title">Running DF Finance Control</div>
                        <div class="fin-lede">
                            Every running DF, read across three registers: the purchase orders booked against it,
                            the payment milestones those orders are claimable under, and the dispatch ledger that
                            records what was actually invoiced and collected. The gap between what has become
                            claimable and what has been invoiced is the cash available to raise today.
                        </div>
                        <div class="fin-chip-row">
                            <span class="fin-chip"><i class="fa fa-calendar"></i> <?php echo accounts_master_report_escape(accounts_master_report_value($filter_summary, 'date_range_label', 'All running DFs')); ?></span>
                            <span class="fin-chip"><i class="fa fa-sitemap"></i> <?php echo accounts_master_report_escape(accounts_master_report_value($filter_summary, 'df_label', 'All running DFs')); ?></span>
                            <span class="fin-chip"><i class="fa fa-clock-o"></i> Generated <?php echo accounts_master_report_escape(accounts_master_report_value($filter_summary, 'generated_on')); ?></span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="fin-hero-actions">
                            <a href="<?php echo page_url; ?>Accounts/paymentdashboard/<?php echo date('Y-m-d'); ?>/<?php echo date('Y-m-d', strtotime('+7 days')); ?>/ALL" class="fin-btn"><i class="fa fa-line-chart"></i> Upcoming Payments</a>
                            <a href="<?php echo page_url; ?>Accounts/overduepaymentdashboard" class="fin-btn"><i class="fa fa-exclamation-triangle"></i> Overdue Payments</a>
                            <a href="<?php echo page_url; ?>Machine/mcsdispatchreport" class="fin-btn"><i class="fa fa-truck"></i> Dispatch Ledger</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('message')): ?>
                <div style="margin-bottom:18px;"><?php echo $this->session->flashdata('message'); ?></div>
            <?php endif; ?>

            <div class="fin-card fin-filter">
                <h3 class="fin-section-title">Scope</h3>
                <div class="fin-section-copy"><?php echo accounts_master_report_escape(accounts_master_report_value($filter_summary, 'date_scope_note')); ?></div>

                <form method="post" action="<?php echo page_url; ?>Accounts/filterbydate" class="fin-filter-grid">
                    <div>
                        <label class="fin-label" for="start_date">DF released from</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo accounts_master_report_escape($start_date_value); ?>">
                    </div>
                    <div>
                        <label class="fin-label" for="end_date">DF released up to</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo accounts_master_report_escape($end_date_value); ?>">
                    </div>
                    <div>
                        <label class="fin-label" for="df_no">DF</label>
                        <select id="df_no" name="df_no" class="form-control">
                            <option value="ALL">All running DFs</option>
                            <?php foreach ($df_options as $df_option): ?>
                                <option value="<?php echo (int) $df_option['id']; ?>" <?php echo $current_df_filter === (string) $df_option['id'] ? 'selected' : ''; ?>>
                                    <?php echo accounts_master_report_escape(trim($df_option['df_no'] . ' ' . $df_option['df_description'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="fin-label">&nbsp;</label>
                        <div class="fin-filter-actions">
                            <button type="submit" class="fin-btn primary">Apply</button>
                            <a href="<?php echo page_url; ?>Accounts/allrunningdf/ALL/ALL/<?php echo accounts_master_report_escape($current_df_filter); ?>" class="fin-btn">All dates</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="fin-kpi-grid">
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Order book</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'order_value_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note"><?php echo (int) accounts_master_report_value($summary, 'po_count', 0); ?> PO(s) across <?php echo (int) accounts_master_report_value($summary, 'df_count', 0); ?> running DF(s).</div>
                </div>
                <div class="fin-card fin-kpi headline">
                    <div class="fin-kpi-label">Ready to invoice</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'unbilled_amount_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Claimable under the payment terms but not yet invoiced, across <?php echo (int) accounts_master_report_value($summary, 'unbilled_df_count', 0); ?> DF(s). This is the fastest cash available.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Claimable to date</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'claimable_amount_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Milestones whose trigger task is already complete.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Billing blocked by late work</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'slipped_amount_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Milestones past their target date whose work is still open, across <?php echo (int) accounts_master_report_value($summary, 'slipped_df_count', 0); ?> DF(s). Clearing the work is what turns this into an invoice.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Not yet earned</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'pipeline_amount_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Order value sitting behind milestones that have not been reached.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Invoiced</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'invoiced_amount_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Recorded on the dispatch ledger. Each invoice number is counted once.</div>
                </div>
                <div class="fin-card fin-kpi good">
                    <div class="fin-kpi-label">Received</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'received_amount_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note"><?php echo (int) accounts_master_report_value($summary, 'collection_percentage', 0); ?>% of the visible order book is collected.</div>
                </div>
                <div class="fin-card fin-kpi danger">
                    <div class="fin-kpi-label">Overdue</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'overdue_amount_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Invoiced, unpaid and past the ledger due date, across <?php echo (int) accounts_master_report_value($summary, 'overdue_df_count', 0); ?> DF(s).</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Balance due</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'balance_amount_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Invoiced and still awaiting payment, due or not.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Payment terms to fix</div>
                    <div class="fin-kpi-value"><?php echo (int) accounts_master_report_value($summary, 'config_df_count', 0); ?></div>
                    <div class="fin-kpi-note">DF(s) whose payment-term setup needs correction before their numbers can be relied on.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">No ledger entry</div>
                    <div class="fin-kpi-value"><?php echo (int) accounts_master_report_value($summary, 'no_ledger_df_count', 0); ?></div>
                    <div class="fin-kpi-note">Running DF(s) with nothing invoiced or collected recorded against them yet.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">No PO recorded</div>
                    <div class="fin-kpi-value"><?php echo (int) accounts_master_report_value($summary, 'no_po_df_count', 0); ?></div>
                    <div class="fin-kpi-note">Running DF(s) with no purchase order booked against them.</div>
                </div>
            </div>

            <div class="fin-panel-grid">
                <div class="fin-card fin-panel">
                    <h3 class="fin-section-title">Act on these first</h3>
                    <div class="fin-section-copy">Ranked by amount. Each row names the single next step.</div>
                    <div class="fin-list">
                        <?php if (!empty($action_rows)): ?>
                            <?php foreach ($action_rows as $action): ?>
                                <div class="fin-item <?php echo accounts_master_report_escape($action['tone']); ?>">
                                    <div class="fin-item-top">
                                        <div class="fin-item-title"><?php echo accounts_master_report_escape($action['action']); ?> &middot; <?php echo accounts_master_report_escape($action['df_no']); ?></div>
                                        <div class="fin-item-amount"><?php echo accounts_master_report_escape($action['amount_display']); ?></div>
                                    </div>
                                    <div class="fin-item-copy"><?php echo accounts_master_report_escape($action['company_name']); ?> &mdash; <?php echo accounts_master_report_escape($action['detail']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="fin-empty">Nothing needs chasing or invoicing in this view.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="fin-card fin-panel">
                    <h3 class="fin-section-title">Payment terms to correct</h3>
                    <div class="fin-section-copy">
                        Faults in the payment-term master, not in a single order. Fixing one term repairs every DF using it.
                        Duplicate milestone generations are already excluded from the figures above so orders are not billed twice.
                    </div>
                    <div class="fin-list">
                        <?php if (!empty($config_issues)): ?>
                            <?php foreach ($config_issues as $config_issue): ?>
                                <div class="fin-item slate">
                                    <div class="fin-item-top">
                                        <div class="fin-item-title"><?php echo accounts_master_report_escape($config_issue['problem']); ?></div>
                                        <div class="fin-item-amount"><?php echo accounts_master_report_escape($config_issue['order_value_display']); ?></div>
                                    </div>
                                    <div class="fin-item-copy">
                                        <?php if ((int) $config_issue['payment_term_id'] > 0): ?>
                                            Term #<?php echo (int) $config_issue['payment_term_id']; ?> &mdash;
                                        <?php endif; ?>
                                        <?php echo accounts_master_report_escape($config_issue['payment_term_name']); ?>
                                        <br><?php echo (int) $config_issue['df_count']; ?> running DF(s) affected.
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="fin-empty">Every payment term in this view is configured correctly.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="fin-card fin-panel">
                    <h3 class="fin-section-title">Readout</h3>
                    <div class="fin-section-copy">What the current view says about the running order book.</div>
                    <ul class="fin-bullets">
                        <?php foreach ($insights as $insight): ?>
                            <li><?php echo accounts_master_report_escape($insight); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (!empty($marketing_summary)): ?>
                        <h3 class="fin-section-title" style="margin-top:18px;">By owner</h3>
                        <div class="fin-list">
                            <?php foreach ($marketing_summary as $marketing_row): ?>
                                <div class="fin-item slate">
                                    <div class="fin-item-top">
                                        <div class="fin-item-title"><?php echo accounts_master_report_escape($marketing_row['label']); ?></div>
                                        <div class="fin-item-amount"><?php echo accounts_master_report_escape($marketing_row['unbilled_amount_display']); ?></div>
                                    </div>
                                    <div class="fin-item-copy">
                                        <?php echo (int) $marketing_row['df_count']; ?> DF(s) &middot;
                                        order <?php echo accounts_master_report_escape($marketing_row['order_value_display']); ?> &middot;
                                        ready to invoice <?php echo accounts_master_report_escape($marketing_row['unbilled_amount_display']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="fin-card fin-report">
                <div class="fin-toolbar">
                    <div>
                        <h3 class="fin-section-title">Running DF register</h3>
                        <div class="fin-section-copy">One row per running DF: its purchase orders, the claim schedule those orders sit on, and the dispatch ledger holding the money.</div>
                    </div>
                    <div class="fin-toolbar-right">
                        <div>
                            <label class="fin-label" for="healthFilter">Show</label>
                            <select id="healthFilter" class="fin-select">
                                <option value="">Everything</option>
                                <option value="overdue">Payment overdue</option>
                                <option value="unbilled">Ready to invoice</option>
                                <option value="config">Configuration gap</option>
                                <option value="nopo">No PO recorded</option>
                                <option value="watch">Awaiting payment</option>
                                <option value="collected">Fully collected</option>
                                <option value="ontrack">On track</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="finRegister" class="table fin-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th style="width:17%;">DF &amp; customer</th>
                                <th style="width:16%;">Purchase order</th>
                                <th style="width:21%;">Money position</th>
                                <th style="width:15%;">Status</th>
                                <th style="width:16%;">Claim schedule</th>
                                <th style="width:15%;">Dispatch ledger</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $index => $row): ?>
                                <?php
                                $row_class = '';
                                if ($row['health_key'] === 'overdue' || $row['health_key'] === 'nopo') {
                                    $row_class = 'row-overdue';
                                } elseif ($row['health_key'] === 'unbilled') {
                                    $row_class = 'row-unbilled';
                                } elseif ($row['health_key'] === 'config') {
                                    $row_class = 'row-config';
                                }
                                $flow_segments = accounts_master_report_flow($row);
                                ?>
                                <tr class="<?php echo $row_class; ?>" data-health="<?php echo accounts_master_report_escape($row['health_key']); ?>">
                                    <td data-order="<?php echo (int) $index; ?>"><span class="fin-idx"><?php echo $index + 1; ?></span></td>

                                    <td>
                                        <div class="cell-title"><?php echo accounts_master_report_escape($row['company_name']); ?></div>
                                        <div class="cell-copy">
                                            <strong><?php echo accounts_master_report_escape($row['df_no']); ?></strong><br>
                                            <?php echo accounts_master_report_escape($row['df_description'] !== '' ? $row['df_description'] : 'No DF description'); ?>
                                        </div>
                                        <div>
                                            <span class="tag">Released <?php echo accounts_master_report_escape($row['df_release_date']); ?></span>
                                            <?php foreach ($row['currencies'] as $currency): ?>
                                                <span class="tag <?php echo $currency !== 'INR' ? 'export' : ''; ?>"><?php echo accounts_master_report_escape($currency); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="cell-copy" style="margin-top:6px;">
                                            <a href="<?php echo accounts_master_report_escape($row['df_detail_url']); ?>" target="_blank">DF detail</a> &middot;
                                            <a href="<?php echo accounts_master_report_escape($row['gantt_url']); ?>" target="_blank">Gantt</a>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if (!empty($row['pos'])): ?>
                                            <?php foreach ($row['pos'] as $po): ?>
                                                <div style="margin-bottom:9px;">
                                                    <div class="cell-title"><?php echo accounts_master_report_escape($po['po_no'] !== '' ? $po['po_no'] : 'PO not numbered'); ?></div>
                                                    <div class="cell-copy">
                                                        Dated <?php echo accounts_master_report_escape($po['po_date']); ?><br>
                                                        <strong><?php echo accounts_master_report_escape($po['order_value_display']); ?></strong>
                                                        <?php if ($po['currency_amount_display'] !== ''): ?>
                                                            <br><span class="tag export"><?php echo accounts_master_report_escape($po['currency_amount_display']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="cell-copy" style="margin-top:4px;">
                                                        <?php echo accounts_master_report_escape($po['payment_term_name'] !== '' ? $po['payment_term_name'] : 'No payment term linked'); ?>
                                                    </div>
                                                    <?php if ($po['download_url'] !== ''): ?>
                                                        <div class="cell-copy"><a href="<?php echo accounts_master_report_escape($po['download_url']); ?>" target="_blank">Download PO</a></div>
                                                    <?php endif; ?>
                                                    <?php if ($po['order_on_hold']): ?>
                                                        <span class="tag">Order on hold</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="cell-copy">No purchase order is recorded against this DF.</div>
                                        <?php endif; ?>
                                    </td>

                                    <td data-order="<?php echo (float) $row['unbilled_amount']; ?>">
                                        <div class="money-grid">
                                            <div class="money-stat"><div class="m-label">Order</div><div class="m-value"><?php echo accounts_master_report_escape($row['order_value_display']); ?></div></div>
                                            <div class="money-stat"><div class="m-label">Claimable</div><div class="m-value"><?php echo accounts_master_report_escape($row['claimable_amount_display']); ?></div></div>
                                            <div class="money-stat ready"><div class="m-label">Ready to invoice</div><div class="m-value"><?php echo accounts_master_report_escape($row['unbilled_amount_display']); ?></div></div>
                                            <div class="money-stat"><div class="m-label">Invoiced</div><div class="m-value"><?php echo accounts_master_report_escape($row['invoiced_amount_display']); ?></div></div>
                                            <div class="money-stat received"><div class="m-label">Received</div><div class="m-value"><?php echo accounts_master_report_escape($row['received_amount_display']); ?></div></div>
                                            <div class="money-stat <?php echo $row['overdue_balance'] > 0 ? 'overdue' : ''; ?>"><div class="m-label">Balance</div><div class="m-value"><?php echo accounts_master_report_escape($row['balance_amount_display']); ?></div></div>
                                            <?php if ($row['slipped_amount'] > 0.01): ?>
                                                <div class="money-stat overdue"><div class="m-label">Blocked by late work</div><div class="m-value"><?php echo accounts_master_report_escape($row['slipped_amount_display']); ?></div></div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($flow_segments)): ?>
                                            <div class="flow">
                                                <?php foreach ($flow_segments as $segment): ?>
                                                    <span class="<?php echo accounts_master_report_escape($segment['key']); ?>" style="width:<?php echo (float) $segment['width']; ?>%;" title="<?php echo accounts_master_report_escape($segment['label']); ?>"></span>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="flow-key">
                                                <?php foreach ($flow_segments as $segment): ?>
                                                    <span><i class="<?php echo accounts_master_report_escape($segment['key']); ?>" style="background:<?php
                                                        echo $segment['key'] === 'received' ? '#0a8f5b' : ($segment['key'] === 'unpaid' ? '#0f4fb7' : ($segment['key'] === 'ready' ? '#e2a13b' : '#cdd7e3')); ?>;"></i><?php echo accounts_master_report_escape($segment['label']); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td data-order="<?php echo (float) $row['overdue_balance']; ?>">
                                        <span class="pill <?php echo accounts_master_report_escape($row['health_key']); ?>"><?php echo accounts_master_report_escape($row['health_label']); ?></span>
                                        <div class="cell-copy" style="margin-top:9px;"><?php echo accounts_master_report_escape($row['focus']); ?></div>
                                        <?php if (!empty($row['issues'])): ?>
                                            <ul class="issue-list">
                                                <?php foreach ($row['issues'] as $issue): ?>
                                                    <li><?php echo accounts_master_report_escape($issue); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($row['milestone_count'] > 0): ?>
                                            <?php foreach ($row['pos'] as $po): ?>
                                                <?php foreach ($po['milestones'] as $milestone): ?>
                                                    <div class="ms-card">
                                                        <div class="ms-head">
                                                            <div class="ms-name"><?php echo accounts_master_report_escape($milestone['task_name']); ?></div>
                                                            <span class="tag"><?php echo accounts_master_report_escape($milestone['percentage_display']); ?></span>
                                                        </div>
                                                        <div class="ms-meta">
                                                            <strong><?php echo accounts_master_report_escape($milestone['amount_display']); ?></strong>
                                                            &middot; <?php echo accounts_master_report_escape($milestone['trigger_date']); ?>
                                                        </div>
                                                        <div style="margin-top:5px;">
                                                            <span class="pill <?php echo accounts_master_report_escape($milestone['claim_status_key']); ?>"><?php echo accounts_master_report_escape($milestone['claim_status_label']); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="ledger-empty">No claim schedule can be built for this DF. Fix the payment term first.</div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="ledger-box">
                                            <div style="display:flex; justify-content:space-between; gap:8px; align-items:center;">
                                                <strong style="font-size:11.5px;">Ledger</strong>
                                                <span class="pill <?php echo accounts_master_report_escape($row['due_status_key']); ?>"><?php echo accounts_master_report_escape($row['due_status_label']); ?></span>
                                            </div>
                                            <?php if ($row['ledger_available']): ?>
                                                <div class="ms-meta" style="margin-top:7px;">
                                                    Invoice <strong><?php echo accounts_master_report_escape($row['invoice_no'] !== '' ? $row['invoice_no'] : 'not numbered'); ?></strong>
                                                    dated <?php echo accounts_master_report_escape($row['invoice_date']); ?><br>
                                                    Due <?php echo accounts_master_report_escape($row['due_date']); ?>
                                                    <?php if ($row['days_overdue'] > 0): ?>
                                                        &mdash; <span style="color:#c22f3d;font-weight:700;"><?php echo (int) $row['days_overdue']; ?> day(s) late</span>
                                                    <?php endif; ?>
                                                    <?php if ($row['nos_of_machines'] !== ''): ?><br>Machines: <?php echo accounts_master_report_escape($row['nos_of_machines']); ?><?php endif; ?>
                                                    <?php if ($row['remarks'] !== ''): ?><br>Note: <?php echo accounts_master_report_escape($row['remarks']); ?><?php endif; ?>
                                                    <br>Updated by <?php echo accounts_master_report_escape($row['last_updated_by']); ?> on <?php echo accounts_master_report_escape($row['last_updated_on']); ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="ledger-empty" style="margin-top:7px;">Nothing invoiced or collected is recorded for this DF yet.</div>
                                            <?php endif; ?>
                                            <button type="button" class="fin-btn primary ledger-edit"
                                                    style="margin-top:9px; padding:8px 10px; font-size:12px; width:100%;"
                                                    data-df-id="<?php echo (int) $row['df_id']; ?>"
                                                    data-label="<?php echo accounts_master_report_escape($row['df_no'] . ' - ' . $row['company_name']); ?>"
                                                    data-invoice-no="<?php echo accounts_master_report_escape($row['invoice_no']); ?>"
                                                    data-invoice-date="<?php echo accounts_master_report_escape($row['invoice_date_raw']); ?>"
                                                    data-invoice-amount="<?php echo (float) $row['invoiced_amount']; ?>"
                                                    data-taxable-sale="<?php echo (float) $row['taxable_sale']; ?>"
                                                    data-received="<?php echo (float) $row['received_amount']; ?>"
                                                    data-due-date="<?php echo accounts_master_report_escape($row['due_date_raw']); ?>"
                                                    data-due-status="<?php echo accounts_master_report_escape($row['due_status_label']); ?>"
                                                    data-remarks="<?php echo accounts_master_report_escape($row['remarks']); ?>"
                                                    data-claimable="<?php echo accounts_master_report_escape($row['claimable_amount_display']); ?>"
                                                    data-unbilled="<?php echo accounts_master_report_escape($row['unbilled_amount_display']); ?>">
                                                <?php echo $row['ledger_available'] ? 'Update invoice / receipt' : 'Record invoice / receipt'; ?>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="fin-empty">
                                        No running DF matched this filter.<br>
                                        Switch the DF selection back to <strong>All running DFs</strong> or press <strong>All dates</strong>.
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="ledgerModal" class="modal fade" role="dialog">
        <form method="post" action="<?php echo page_url; ?>Accounts/update_df_ledger/<?php echo accounts_master_report_escape($filter_path); ?>">
            <input type="hidden" name="df_id" id="ledger_df_id" value="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Invoice &amp; receipt</h4>
                        <div class="fin-section-copy" id="ledger_context"></div>
                    </div>
                    <div class="modal-body">
                        <div class="modal-note" id="ledger_helper"></div>

                        <div class="row" style="margin-top:14px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fin-label" for="ledger_invoice_no">Invoice number</label>
                                    <input type="text" class="form-control" name="invoice_no" id="ledger_invoice_no" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="fin-label" for="ledger_invoice_date">Invoice date</label>
                                    <input type="date" class="form-control" name="invoice_date" id="ledger_invoice_date">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fin-label" for="ledger_invoice_amount">Invoice amount</label>
                                    <input type="number" step="any" min="0" class="form-control" name="invoice_amount" id="ledger_invoice_amount" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fin-label" for="ledger_taxable_sale">Taxable sale</label>
                                    <input type="number" step="any" min="0" class="form-control" name="taxable_sale" id="ledger_taxable_sale" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fin-label" for="ledger_received">Payment received</label>
                                    <input type="number" step="any" min="0" class="form-control" name="payment_received" id="ledger_received" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fin-label" for="ledger_due_date">Payment due date</label>
                                    <input type="date" class="form-control" name="due_date" id="ledger_due_date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fin-label" for="ledger_due_status">Due status</label>
                                    <select class="form-control" name="payment_due_status" id="ledger_due_status">
                                        <option value="">Not updated</option>
                                        <option value="Due">Due</option>
                                        <option value="Not Due">Not Due</option>
                                        <option value="Received">Received</option>
                                        <option value="Hold">Hold</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="fin-label">Balance</label>
                                    <input type="text" class="form-control" id="ledger_balance" readonly value="0.00">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="fin-label" for="ledger_remarks">Remarks</label>
                            <textarea class="form-control" name="remarks" id="ledger_remarks" rows="2"></textarea>
                        </div>

                        <div class="text-center" style="margin-top:16px;">
                            <button type="submit" class="fin-btn primary" style="min-width:210px;">Save to dispatch ledger</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        $(function () {
            var register = $('#finRegister').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excelHtml5', text: 'Excel', title: 'Running DF finance control', exportOptions: { columns: ':visible' } },
                    { extend: 'csvHtml5', text: 'CSV', title: 'Running DF finance control', exportOptions: { columns: ':visible' } },
                    { extend: 'print', text: 'Print', title: 'Running DF finance control', exportOptions: { columns: ':visible' } }
                ],
                columnDefs: [{ orderable: false, targets: [5, 6] }]
            });

            $('#healthFilter').on('change', function () {
                var wanted = $(this).val();
                $.fn.dataTable.ext.search = [];
                if (wanted !== '') {
                    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                        return $(register.row(dataIndex).node()).attr('data-health') === wanted;
                    });
                }
                register.draw();
            });

            function recalcBalance() {
                var invoiced = parseFloat($('#ledger_invoice_amount').val()) || 0;
                var received = parseFloat($('#ledger_received').val()) || 0;
                $('#ledger_balance').val((invoiced - received).toFixed(2));
            }
            $('#ledger_invoice_amount, #ledger_received').on('input', recalcBalance);

            $(document).on('click', '.ledger-edit', function () {
                var b = $(this);
                $('#ledger_df_id').val(b.data('df-id'));
                $('#ledger_context').text(b.data('label'));
                $('#ledger_invoice_no').val(b.data('invoice-no'));
                $('#ledger_invoice_date').val(b.data('invoice-date'));
                $('#ledger_invoice_amount').val(b.data('invoice-amount'));
                $('#ledger_taxable_sale').val(b.data('taxable-sale'));
                $('#ledger_received').val(b.data('received'));
                $('#ledger_due_date').val(b.data('due-date'));
                $('#ledger_due_status').val(b.data('due-status') === 'Not updated' ? '' : b.data('due-status'));
                $('#ledger_remarks').val(b.data('remarks'));
                $('#ledger_helper').text(
                    'Claimable to date on this DF is ' + b.data('claimable') +
                    ', of which ' + b.data('unbilled') + ' is not invoiced yet. ' +
                    'This writes to the same dispatch ledger the dispatch report screen reads.'
                );
                recalcBalance();
                $('#ledgerModal').modal('show');
            });
        });
    </script>
</body>
</html>
