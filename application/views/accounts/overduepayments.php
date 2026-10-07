<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Receivables, read straight from the dispatch ledger and grouped by invoice.
| Deliberately not limited to running DFs - a shipped machine closes its DF long
| before its money arrives, so most of the receivable sits on closed ones.
*/

$receivables = isset($receivables) && is_array($receivables) ? $receivables : array();
$invoices = isset($receivables['invoices']) && is_array($receivables['invoices']) ? $receivables['invoices'] : array();
$summary = isset($receivables['summary']) && is_array($receivables['summary']) ? $receivables['summary'] : array();
$buckets = isset($receivables['buckets']) && is_array($receivables['buckets']) ? $receivables['buckets'] : array();
$insights = isset($receivables['insights']) && is_array($receivables['insights']) ? $receivables['insights'] : array();

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

$overdue_buckets = array();
$bucket_peak = 0;
foreach ($buckets as $bucket) {
    if (strpos($bucket['key'], '_') !== false && $bucket['key'] !== 'no_due_date' && $bucket['key'] !== 'not_due') {
        $overdue_buckets[] = $bucket;
        $bucket_peak = max($bucket_peak, (float) $bucket['amount']);
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
    <title><?php echo sitetitle; ?> | Receivables &amp; Overdue</title>

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
    <style>
        .age-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:11px; margin-top:14px; }
        .age-card{ border:1px solid var(--fin-line); border-radius:10px; padding:13px 14px; }
        .age-card.hot{ border-color:#f0b3b8; background:#fffafa; }
        .age-amount{ font-size:17px; font-weight:800; margin:6px 0 4px; }
        .age-bar{ height:5px; border-radius:4px; background:#eef1f5; overflow:hidden; margin-top:8px; }
        .age-bar span{ display:block; height:100%; background:var(--fin-red); }
    </style>
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
                        <div class="fin-title">Receivables &amp; Overdue</div>
                        <div class="fin-lede">
                            Every invoice on the dispatch ledger that still carries a balance, aged against its due date
                            and grouped by invoice number so an invoice raised across two DFs is counted once.
                            Closed DFs are included on purpose: a machine ships and its DF closes long before the money lands.
                        </div>
                        <div class="fin-chip-row">
                            <span class="fin-chip"><i class="fa fa-file-text-o"></i> <?php echo (int) accounts_master_report_value($summary, 'invoice_count', 0); ?> open invoice(s)</span>
                            <span class="fin-chip"><i class="fa fa-clock-o"></i> Generated <?php echo date('d M Y h:i A'); ?></span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="fin-hero-actions">
                            <a href="<?php echo page_url; ?>Accounts/allrunningdf/ALL/ALL/ALL" class="fin-btn"><i class="fa fa-sitemap"></i> Running DF Control</a>
                            <a href="<?php echo page_url; ?>Accounts/paymentdashboard/<?php echo date('Y-m-d'); ?>/<?php echo date('Y-m-d', strtotime('+7 days')); ?>/ALL" class="fin-btn"><i class="fa fa-calendar"></i> Payment Calendar</a>
                            <a href="<?php echo page_url; ?>Machine/mcsdispatchreport" class="fin-btn"><i class="fa fa-truck"></i> Dispatch Ledger</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fin-kpi-grid">
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Total receivable</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'balance_total_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Invoiced <?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'invoiced_total_display', '₹0.00')); ?>, collected <?php echo (int) accounts_master_report_value($summary, 'collection_percentage', 0); ?>%.</div>
                </div>
                <div class="fin-card fin-kpi danger">
                    <div class="fin-kpi-label">Overdue</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'overdue_total_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note"><?php echo (int) accounts_master_report_value($summary, 'overdue_count', 0); ?> invoice(s) past due, oldest by <?php echo (int) accounts_master_report_value($summary, 'oldest_days', 0); ?> days.</div>
                </div>
                <div class="fin-card fin-kpi headline">
                    <div class="fin-kpi-label">Outstanding with no due date</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'no_due_date_total_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note"><?php echo (int) accounts_master_report_value($summary, 'no_due_date_count', 0); ?> invoice(s) cannot be aged or chased on a date until a due date is set on the ledger.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Not yet due</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'not_due_total_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Invoiced and still inside its credit period.</div>
                </div>
                <div class="fin-card fin-kpi good">
                    <div class="fin-kpi-label">Collected</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'received_total_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Against the invoices listed on this page.</div>
                </div>
                <div class="fin-card fin-kpi">
                    <div class="fin-kpi-label">Sitting on closed DFs</div>
                    <div class="fin-kpi-value"><?php echo accounts_master_report_escape(accounts_master_report_value($summary, 'closed_df_balance_display', '₹0.00')); ?></div>
                    <div class="fin-kpi-note">Money still owed on DFs that have already been closed out.</div>
                </div>
            </div>

            <div class="fin-panel-grid">
                <div class="fin-card fin-panel" style="grid-column:1 / -1;">
                    <h3 class="fin-section-title">Ageing</h3>
                    <div class="fin-section-copy">Balance by how far past its due date it has gone.</div>
                    <div class="age-grid">
                        <?php foreach ($overdue_buckets as $bucket): ?>
                            <div class="age-card <?php echo $bucket['amount'] > 0 ? 'hot' : ''; ?>">
                                <div class="fin-kpi-label"><?php echo accounts_master_report_escape($bucket['label']); ?></div>
                                <div class="age-amount"><?php echo accounts_master_report_escape($bucket['amount_display']); ?></div>
                                <div class="fin-kpi-note"><?php echo (int) $bucket['count']; ?> invoice(s)</div>
                                <div class="age-bar"><span style="width:<?php echo $bucket_peak > 0 ? round(($bucket['amount'] / $bucket_peak) * 100, 2) : 0; ?>%;"></span></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
                        <h3 class="fin-section-title">Open invoices</h3>
                        <div class="fin-section-copy">Worst-aged first. An invoice spanning several DFs shows every DF it covers and is counted once.</div>
                    </div>
                    <div class="fin-toolbar-right">
                        <div>
                            <label class="fin-label" for="bucketFilter">Show</label>
                            <select id="bucketFilter" class="fin-select">
                                <option value="">Everything</option>
                                <option value="90_plus">Overdue 90+ days</option>
                                <option value="61_90">Overdue 61-90 days</option>
                                <option value="31_60">Overdue 31-60 days</option>
                                <option value="1_30">Overdue 1-30 days</option>
                                <option value="no_due_date">No due date set</option>
                                <option value="not_due">Not yet due</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="receivablesTable" class="table fin-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th style="width:18%;">Invoice</th>
                                <th style="width:20%;">Customer &amp; DF</th>
                                <th style="width:20%;">Money</th>
                                <th style="width:16%;">Ageing</th>
                                <th style="width:24%;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($invoices)): ?>
                            <?php foreach ($invoices as $index => $invoice): ?>
                                <tr class="<?php echo $invoice['is_overdue'] ? 'row-overdue' : ($invoice['bucket_key'] === 'no_due_date' ? 'row-unbilled' : ''); ?>"
                                    data-bucket="<?php echo accounts_master_report_escape($invoice['bucket_key']); ?>">
                                    <td data-order="<?php echo (int) $index; ?>"><span class="fin-idx"><?php echo $index + 1; ?></span></td>
                                    <td>
                                        <div class="cell-title"><?php echo accounts_master_report_escape($invoice['invoice_no']); ?></div>
                                        <div class="cell-copy">Dated <?php echo accounts_master_report_escape($invoice['invoice_date']); ?></div>
                                        <div><span class="tag"><?php echo accounts_master_report_escape($invoice['df_status_label']); ?></span></div>
                                    </td>
                                    <td>
                                        <div class="cell-title"><?php echo accounts_master_report_escape($invoice['company_name']); ?></div>
                                        <div class="cell-copy">DF <?php echo accounts_master_report_escape($invoice['df_label']); ?></div>
                                        <div class="cell-copy">Owner: <?php echo accounts_master_report_escape($invoice['marketing_person']); ?></div>
                                        <div class="cell-copy"><a href="<?php echo accounts_master_report_escape($invoice['df_detail_url']); ?>" target="_blank">DF detail</a></div>
                                    </td>
                                    <td data-order="<?php echo (float) $invoice['balance']; ?>">
                                        <div class="money-grid">
                                            <div class="money-stat"><div class="m-label">Invoiced</div><div class="m-value"><?php echo accounts_master_report_escape($invoice['invoiced_display']); ?></div></div>
                                            <div class="money-stat received"><div class="m-label">Received</div><div class="m-value"><?php echo accounts_master_report_escape($invoice['received_display']); ?></div></div>
                                            <div class="money-stat <?php echo $invoice['is_overdue'] ? 'overdue' : ''; ?>"><div class="m-label">Balance</div><div class="m-value"><?php echo accounts_master_report_escape($invoice['balance_display']); ?></div></div>
                                            <div class="money-stat"><div class="m-label">Collected</div><div class="m-value"><?php echo (int) $invoice['collection_percentage']; ?>%</div></div>
                                        </div>
                                    </td>
                                    <td data-order="<?php echo (int) $invoice['days_overdue']; ?>">
                                        <span class="pill <?php echo $invoice['is_overdue'] ? 'overdue' : ($invoice['bucket_key'] === 'no_due_date' ? 'unbilled' : 'watch'); ?>">
                                            <?php echo accounts_master_report_escape($invoice['bucket_label']); ?>
                                        </span>
                                        <div class="cell-copy" style="margin-top:8px;">
                                            Due <?php echo accounts_master_report_escape($invoice['due_date']); ?>
                                            <?php if ($invoice['days_overdue'] > 0): ?>
                                                <br><strong style="color:var(--fin-red);"><?php echo (int) $invoice['days_overdue']; ?> day(s) late</strong>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($invoice['remarks'] !== ''): ?>
                                            <div class="cell-copy"><?php echo nl2br(accounts_master_report_escape($invoice['remarks'])); ?></div>
                                        <?php endif; ?>
                                        <div class="cell-copy" style="margin-top:5px;">Updated by <?php echo accounts_master_report_escape($invoice['last_updated_by']); ?> on <?php echo accounts_master_report_escape($invoice['last_updated_on']); ?></div>
                                        <?php if (!empty($invoice['issues'])): ?>
                                            <ul class="issue-list">
                                                <?php foreach ($invoice['issues'] as $issue): ?>
                                                    <li><?php echo accounts_master_report_escape($issue); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6"><div class="fin-empty">Nothing is outstanding on the dispatch ledger.<br>If that looks wrong, invoices are recorded on the <a href="<?php echo page_url; ?>Machine/mcsdispatchreport">dispatch ledger</a> or from the Running DF Control screen.</div></td></tr>
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
            var table = $('#receivablesTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excelHtml5', text: 'Excel', title: 'Receivables and overdue' },
                    { extend: 'csvHtml5', text: 'CSV', title: 'Receivables and overdue' },
                    { extend: 'print', text: 'Print', title: 'Receivables and overdue' }
                ]
            });

            $('#bucketFilter').on('change', function () {
                var wanted = $(this).val();
                $.fn.dataTable.ext.search = [];
                if (wanted !== '') {
                    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                        return $(table.row(dataIndex).node()).attr('data-bucket') === wanted;
                    });
                }
                table.draw();
            });
        });
    </script>
</body>
</html>
