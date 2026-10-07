<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Forward cash view over a date window: invoices falling due inside it,
| milestones that will become billable inside it, and what has been carried in
| already billable or already overdue.
*/

$calendar = isset($calendar) && is_array($calendar) ? $calendar : array();
$filters = isset($filters) && is_array($filters) ? $filters : array();
$df_options = isset($df_options) && is_array($df_options) ? $df_options : array();

$summary = isset($calendar['summary']) && is_array($calendar['summary']) ? $calendar['summary'] : array();
$insights = isset($calendar['insights']) && is_array($calendar['insights']) ? $calendar['insights'] : array();
$due_invoices = isset($calendar['due_invoices']) ? $calendar['due_invoices'] : array();
$becoming = isset($calendar['becoming_claimable']) ? $calendar['becoming_claimable'] : array();
$ready_now = isset($calendar['ready_now']) ? $calendar['ready_now'] : array();
$overdue_carry = isset($calendar['overdue_carry_in']) ? $calendar['overdue_carry_in'] : array();

$start_date_value = isset($filters['start_date']) ? $filters['start_date'] : date('Y-m-d');
$end_date_value = isset($filters['end_date']) ? $filters['end_date'] : date('Y-m-d', strtotime('+7 days'));
$current_df_filter = isset($filters['df_id']) ? (string) $filters['df_id'] : 'ALL';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | Payment Calendar</title>

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
                        <div class="fin-title">Payment Calendar</div>
                        <div class="fin-lede">
                            What can be collected in this window, and what will become billable inside it.
                            Collection comes from invoices already raised; billing comes from payment milestones
                            whose triggering work completes. Both are shown so the week can be planned, not guessed.
                        </div>
                        <div class="fin-chip-row">
                            <span class="fin-chip"><i class="fa fa-calendar"></i> <?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'window_label')); ?></span>
                            <span class="fin-chip"><i class="fa fa-clock-o"></i> <?php echo (int) accounts_master_report_value($summary, 'window_days', 0); ?> day window</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="fin-hero-actions">
                            <a href="<?php echo page_url; ?>Accounts/allrunningdf/ALL/ALL/ALL" class="fin-btn"><i class="fa fa-sitemap"></i> Running DF Control</a>
                            <a href="<?php echo page_url; ?>Accounts/overduepaymentdashboard" class="fin-btn"><i class="fa fa-exclamation-triangle"></i> Receivables &amp; Overdue</a>
                            <a href="<?php echo page_url; ?>Machine/mcsdispatchreport" class="fin-btn"><i class="fa fa-truck"></i> Dispatch Ledger</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('message')): ?>
                <div style="margin-bottom:18px;"><?php echo $this->session->flashdata('message'); ?></div>
            <?php endif; ?>

            <div class="fin-card fin-filter">
                <h3 class="fin-section-title">Window</h3>
                <div class="fin-section-copy">Pick the period you are planning collections for.</div>
                <form method="post" action="<?php echo page_url; ?>Accounts/filterbydateanddf" class="fin-filter-grid">
                    <div>
                        <label class="fin-label" for="start_date">From</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo accounts_master_report_escape($start_date_value); ?>">
                    </div>
                    <div>
                        <label class="fin-label" for="end_date">To</label>
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
                            <a href="<?php echo page_url; ?>Accounts/paymentdashboard/<?php echo date('Y-m-d'); ?>/<?php echo date('Y-m-d', strtotime('+30 days')); ?>/ALL" class="fin-btn">Next 30 days</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="fin-kpi-grid">
                <div class="fin-card fin-kpi good">
                    <div class="fin-kpi-label">Collectable in this window</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'expected_collection_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Invoices falling due here plus anything already overdue.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Falling due</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'due_in_window_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note"><?php echo (int) accounts_master_report_value($summary, 'due_in_window_count', 0); ?> invoice(s) reach their due date inside this window.</div>
                </div>
                <div class="fin-card fin-kpi danger">
                    <div class="fin-kpi-label">Already overdue</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'overdue_carry_in_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note"><?php echo (int) accounts_master_report_value($summary, 'overdue_carry_in_count', 0); ?> invoice(s) carried in past their due date.</div>
                </div>
                <div class="fin-card fin-kpi headline">
                    <div class="fin-kpi-label">Ready to invoice now</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'ready_now_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Milestones already complete on <?php echo (int) accounts_master_report_value($summary, 'ready_now_count', 0); ?> DF(s) with no invoice raised. Bill these to create next month's collection.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Becoming billable</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'becoming_claimable_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note"><?php echo (int) accounts_master_report_value($summary, 'becoming_claimable_count', 0); ?> milestone(s) fall due inside this window, if the work lands on time.</div>
                </div>
            </div>

            <div class="fin-panel-grid">
                <div class="fin-card fin-panel">
                    <h3 class="fin-section-title">Chase these</h3>
                    <div class="fin-section-copy">Invoices due in the window, then anything already past due.</div>
                    <div class="fin-list">
                        <?php if (!empty($due_invoices) || !empty($overdue_carry)): ?>
                            <?php foreach ($overdue_carry as $item): ?>
                                <div class="fin-item red">
                                    <div class="fin-item-top">
                                        <div class="fin-item-title"><?php echo accounts_master_report_escape($item['df_no']); ?> &middot; <?php echo accounts_master_report_escape($item['invoice_no']); ?></div>
                                        <div class="fin-item-amount"><?php echo accounts_master_report_escape($item['amount_display']); ?></div>
                                    </div>
                                    <div class="fin-item-copy"><?php echo accounts_master_report_escape($item['company_name']); ?> &mdash; overdue by <?php echo (int) $item['days_overdue']; ?> day(s), due <?php echo accounts_master_report_escape($item['due_date']); ?>.</div>
                                </div>
                            <?php endforeach; ?>
                            <?php foreach ($due_invoices as $item): ?>
                                <div class="fin-item amber">
                                    <div class="fin-item-top">
                                        <div class="fin-item-title"><?php echo accounts_master_report_escape($item['df_no']); ?> &middot; <?php echo accounts_master_report_escape($item['invoice_no']); ?></div>
                                        <div class="fin-item-amount"><?php echo accounts_master_report_escape($item['amount_display']); ?></div>
                                    </div>
                                    <div class="fin-item-copy"><?php echo accounts_master_report_escape($item['company_name']); ?> &mdash; due <?php echo accounts_master_report_escape($item['due_date']); ?>.</div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="fin-empty">No invoice falls due in this window and nothing is carried in overdue.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="fin-card fin-panel">
                    <h3 class="fin-section-title">Invoice these now</h3>
                    <div class="fin-section-copy">Work is finished and the money is claimable, but no invoice exists yet.</div>
                    <div class="fin-list">
                        <?php if (!empty($ready_now)): ?>
                            <?php foreach (array_slice($ready_now, 0, 10) as $item): ?>
                                <div class="fin-item amber">
                                    <div class="fin-item-top">
                                        <div class="fin-item-title"><?php echo accounts_master_report_escape($item['df_no']); ?></div>
                                        <div class="fin-item-amount"><?php echo accounts_master_report_escape($item['amount_display']); ?></div>
                                    </div>
                                    <div class="fin-item-copy"><?php echo accounts_master_report_escape($item['company_name']); ?> &mdash; <?php echo accounts_master_report_escape($item['detail']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="fin-empty">Nothing is claimable and uninvoiced right now.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="fin-card fin-panel">
                    <h3 class="fin-section-title">Readout</h3>
                    <div class="fin-section-copy">What this window says about cash.</div>
                    <ul class="fin-bullets">
                        <?php foreach ($insights as $insight): ?>
                            <li><?php echo accounts_master_report_escape($insight); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="fin-card fin-report">
                <div class="fin-toolbar">
                    <div>
                        <h3 class="fin-section-title">Milestones becoming billable in this window</h3>
                        <div class="fin-section-copy">Earliest first. Each becomes claimable when its triggering task is signed off, so a slipped task pushes the cash out with it.</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="calendarTable" class="table fin-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th style="width:14%;">Trigger date</th>
                                <th style="width:22%;">DF &amp; customer</th>
                                <th style="width:24%;">Milestone</th>
                                <th style="width:14%;">Amount</th>
                                <th style="width:14%;">Status</th>
                                <th style="width:12%;">Owner</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($becoming)): ?>
                            <?php foreach ($becoming as $index => $item): ?>
                                <tr class="<?php echo $item['claim_status_key'] === 'slipped' ? 'row-overdue' : ''; ?>">
                                    <td data-order="<?php echo (int) $index; ?>"><span class="fin-idx"><?php echo $index + 1; ?></span></td>
                                    <td data-order="<?php echo accounts_master_report_escape($item['trigger_date_raw']); ?>">
                                        <div class="cell-title"><?php echo accounts_master_report_escape($item['trigger_date']); ?></div>
                                    </td>
                                    <td>
                                        <div class="cell-title"><?php echo accounts_master_report_escape($item['company_name']); ?></div>
                                        <div class="cell-copy">DF <?php echo accounts_master_report_escape($item['df_no']); ?> &middot; <?php echo accounts_master_report_escape($item['po_no']); ?></div>
                                        <div class="cell-copy"><a href="<?php echo accounts_master_report_escape($item['df_detail_url']); ?>" target="_blank">DF detail</a></div>
                                    </td>
                                    <td>
                                        <div class="cell-title"><?php echo accounts_master_report_escape($item['task_name']); ?></div>
                                        <div><span class="tag"><?php echo accounts_master_report_escape($item['percentage_display']); ?> of order</span></div>
                                    </td>
                                    <td data-order="<?php echo (float) $item['amount']; ?>">
                                        <div class="cell-title"><?php echo accounts_master_report_escape($item['amount_display']); ?></div>
                                    </td>
                                    <td>
                                        <span class="pill <?php echo accounts_master_report_escape($item['claim_status_key']); ?>"><?php echo accounts_master_report_escape($item['claim_status_label']); ?></span>
                                    </td>
                                    <td><div class="cell-copy"><?php echo accounts_master_report_escape($item['marketing_person']); ?></div></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7"><div class="fin-empty">No payment milestone falls due between <?php echo accounts_master_report_escape($start_date_value); ?> and <?php echo accounts_master_report_escape($end_date_value); ?>.<br>Widen the window with <strong>Next 30 days</strong>.</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
            $('#calendarTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excelHtml5', text: 'Excel', title: 'Payment calendar' },
                    { extend: 'csvHtml5', text: 'CSV', title: 'Payment calendar' },
                    { extend: 'print', text: 'Print', title: 'Payment calendar' }
                ]
            });
        });
    </script>
</body>
</html>
