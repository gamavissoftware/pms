<?php
$format_currency = function ($currency, $amount, $decimals = 2) {
    $currency = strtoupper(trim((string) $currency));
    $symbol_map = [
        'INR' => '₹ ',
        'USD' => '$ ',
        'EUR' => 'EUR ',
        'GBP' => 'GBP ',
        'AED' => 'AED ',
    ];

    $prefix = isset($symbol_map[$currency]) ? $symbol_map[$currency] : ($currency !== '' ? $currency . ' ' : '');
    return $prefix . number_format((float) $amount, $decimals);
};

$currency_totals = $kpi['currency_totals'] ?? [];
$currency_averages = $kpi['currency_averages'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Commercial Order Report</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f9; }
        .wrapper { padding-top: 80px; }
        .card-box { border-radius: 12px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); background: #fff; margin-bottom: 25px; border: 1px solid #eef2f5; }
        
        /* KPI Styling */
        .kpi-container { display: flex; gap: 20px; margin-bottom: 25px; }
        .kpi-card { flex: 1; padding: 20px; border-radius: 10px; background: #fff; border-left: 5px solid #4a81d4; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .kpi-card.revenue-inr { border-left-color: #28a745; }
        .kpi-card.revenue-usd { border-left-color: #f1b53d; }
        .kpi-card.avg-value { border-left-color: #673ab7; }
        .kpi-label { font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 600; margin-bottom: 5px; }
        .kpi-value { font-size: 22px; font-weight: 700; color: #343a40; }
        .kpi-subvalue { display: block; font-size: 13px; color: #6c757d; font-weight: 500; margin-top: 6px; line-height: 1.6; }
        
        /* Filter Bar */
        .filter-section { background: #fff; padding: 15px 25px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #e1e6ef; }
        .form-inline .form-group { margin-right: 20px; }

        .mini-table td, .mini-table th { padding: 10px 12px !important; vertical-align: middle !important; }
        .revenue-pill { display: inline-block; margin: 2px 6px 2px 0; padding: 4px 8px; border-radius: 20px; background: #eef5ff; color: #2b3d51; font-size: 12px; font-weight: 600; }
        .filter-section .form-control { min-width: 160px; }
        
        /* Table Styling */
        .table thead th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; border-bottom: 2px solid #dee2e6; }
        .po-badge { font-weight: 600; color: #4a81d4; background: #ebf2ff; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .amount-text { font-weight: 700; color: #2b3d51; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Commercial Service Order Reporting</h4>
                        <p class="text-muted">Revenue by order and engineer</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="kpi-card">
                        <div class="kpi-label">Total Orders Won</div>
                        <div class="kpi-value"><i class="fa fa-shopping-cart text-primary"></i> <?php echo $kpi['total_orders']; ?></div>
                    </div>
                </div>
                <?php foreach ($currency_totals as $currency => $total) : ?>
                    <div class="col-md-3">
                        <div class="kpi-card <?php echo ($currency === 'INR') ? 'revenue-inr' : 'revenue-usd'; ?>">
                            <div class="kpi-label">Revenue (<?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>)</div>
                            <div class="kpi-value"><?php echo $format_currency($currency, $total, 2); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="col-md-3">
                    <div class="kpi-card avg-value">
                        <div class="kpi-label">Avg. Order Value</div>
                        <div class="kpi-value">
                            <?php if (!empty($currency_averages)) : ?>
                                <?php $first_average = true; ?>
                                <?php foreach ($currency_averages as $currency => $average) : ?>
                                    <?php if ($first_average) : ?>
                                        <?php echo $format_currency($currency, $average, 0); ?>
                                        <?php $first_average = false; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                0
                            <?php endif; ?>
                        </div>
                        <?php if (count($currency_averages) > 1) : ?>
                            <span class="kpi-subvalue">
                                <?php foreach ($currency_averages as $currency => $average) : ?>
                                    <?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>: <?php echo $format_currency($currency, $average, 0); ?><br>
                                <?php endforeach; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <form class="form-inline" method="get" action="<?php echo page_url; ?>ServiceLeads/order_report">
                    <div class="form-group">
                        <label class="m-r-10">Date From</label>
                        <input type="date" name="from_date" class="form-control input-sm" value="<?php echo htmlspecialchars($filters['from_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="m-r-10">Date To</label>
                        <input type="date" name="to_date" class="form-control input-sm" value="<?php echo htmlspecialchars($filters['to_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="m-r-10">Marketing</label>
                        <select name="marketing_person" class="form-control input-sm">
                            <option value="">All</option>
                            <?php foreach ($marketing_persons as $marketing_row) : ?>
                                <option value="<?php echo (int) $marketing_row->user_id; ?>" <?php echo ((string) $filters['marketing_person'] === (string) $marketing_row->user_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(trim($marketing_row->first_name . ' ' . $marketing_row->last_name), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light"><i class="fa fa-filter"></i> Apply Filters</button>
                    <a href="<?php echo page_url; ?>ServiceLeads/order_report" class="btn btn-default btn-sm">Reset</a>
                </form>
            </div>

            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="m-t-0 m-b-15">Engineer Revenue</h4>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mini-table m-b-0">
                        <thead>
                            <tr>
                                <th>Engineer</th>
                                <th class="text-center">Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($engineer_revenue)) : ?>
                                <?php foreach ($engineer_revenue as $engineer_row) : ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($engineer_row['engineer_name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                        <td class="text-center"><?php echo (int) $engineer_row['order_count']; ?></td>
                                        <td>
                                            <?php if (!empty($engineer_row['revenue_by_currency'])) : ?>
                                                <?php foreach ($engineer_row['revenue_by_currency'] as $currency => $amount) : ?>
                                                    <span class="revenue-pill"><?php echo $format_currency($currency, $amount, 2); ?></span>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <span class="text-muted">₹ 0.00</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No engineer revenue found for this period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-hover table-actions-bar m-b-0" id="commercialTable">
                        <thead>
                            <tr>
                                <th>PO Date</th>
                                <th>Customer Name</th>
                                <th>PO Details</th>
                                <th>Opp. Number</th>
                                <th>Marketing Executive</th>
                                <th class="text-center">Currency</th>
                                <th class="text-right">Commercial Value</th>
                                <th class="text-center">Audit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($orders)): foreach($orders as $row): ?>
                            <tr>
                                <td>
                                    <span class="text-muted"><?php echo date('d M, Y', strtotime($row->po_date)); ?></span>
                                </td>
                                <td>
                                    <h5 class="m-0" style="font-weight: 600;"><?php echo $row->customer_name; ?></h5>
                                    <small class="text-muted"><?php echo ($row->op_type == 1) ? 'Domestic Job' : 'International Job'; ?></small>
                                </td>
                                <td>
                                    <span class="po-badge"><?php echo $row->po_number; ?></span>
                                </td>
                                <td>
                                    <code style="font-size: 11px;"><?php echo $row->op_no; ?></code>
                                </td>
                                <td>
                                    <i class="fa fa-user-circle-o text-muted"></i> <?php echo $row->marketing_person; ?>
                                </td>
                                <td class="text-center">
                                    <span class="label label-default"><?php echo htmlspecialchars(strtoupper(trim($row->currency ?: 'INR')), ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="text-right">
                                    <span class="amount-text">
                                        <?php echo $format_currency($row->currency, $row->po_amount, 2); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <?php if($row->po_attachment): ?>
                                            <a href="<?php echo uploadsurl.'service_pos/'.$row->po_attachment; ?>" target="_blank" class="btn btn-xs btn-success" title="View PO File">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo page_url; ?>ServiceLeads/opportunity_detail/<?php echo $row->opportunity_id; ?>" class="btn btn-xs btn-primary" title="View Detailed Tracking">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td class="text-center p-20 text-muted">No orders found</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables with Default Sorting on PO Date
            $('#commercialTable').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 25,
                "columnDefs": [
                    { "orderable": false, "targets": 7 }
                ],
                "language": {
                    "search": "Quick Search:",
                    "lengthMenu": "Show _MENU_ orders"
                }
            });
        });
    </script>
</body>
</html>
