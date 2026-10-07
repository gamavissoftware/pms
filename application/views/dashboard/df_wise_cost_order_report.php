<?php
$CIA =& get_instance();
$CIA->load->model('Dashboard_model');

if (!function_exists('format_df_cost_report_currency')) {
    function format_df_cost_report_currency($amount)
    {
        return 'Rs ' . number_format((float) $amount, 0);
    }
}

$report_filters = isset($report_filters) && is_array($report_filters) ? $report_filters : array();
$df_rows = isset($df_rows) ? $df_rows : array();
$df_summary = isset($df_summary) && is_array($df_summary) ? $df_summary : array();
$report_users = isset($report_users) ? $report_users : array();
$selected_financial_year_label = isset($report_filters['financial_year_label']) ? $report_filters['financial_year_label'] : 'Latest Financial Year';
$selected_month_label = isset($report_filters['month_label']) ? $report_filters['month_label'] : 'All Months';
$selected_user_label = !empty($selected_user_label) ? $selected_user_label : 'All Marketing Persons';
$excel_export_query = http_build_query(array(
    'financial_year' => isset($report_filters['financial_year']) ? $report_filters['financial_year'] : '',
    'month' => isset($report_filters['month']) ? $report_filters['month'] : '',
    'user_id' => isset($report_filters['user_id']) ? $report_filters['user_id'] : '',
));
$excel_export_url = page_url . 'OrderController/exportdfwisecostorderexcel' . ($excel_export_query !== '' ? '?' . $excel_export_query : '');

$df_summary_defaults = array(
    'total_dfs' => 0,
    'total_orders' => 0,
    'total_order_value' => 0,
    'avg_df_value' => 0,
    'active_marketing_people' => 0,
    'top_df_no' => 'No data found',
    'top_marketing_person' => 'No data found',
    'top_df_order_value' => 0,
    'top_df_order_count' => 0,
    'latest_order_date' => '',
);
$df_summary = array_merge($df_summary_defaults, $df_summary);
$total_order_value_for_share = (float) $df_summary['total_order_value'];

$marketing_totals = array();
foreach ($df_rows as $df_row) {
    $marketing_key = !empty($df_row->marketing_person) ? $df_row->marketing_person : 'Unassigned';

    if (!isset($marketing_totals[$marketing_key])) {
        $marketing_totals[$marketing_key] = array(
            'name' => $marketing_key,
            'po_count' => 0,
            'df_keys' => array(),
            'total_order_value' => 0,
        );
    }

    $df_key = ((int) $df_row->df_id > 0) ? 'id-' . (int) $df_row->df_id : 'label-' . (string) $df_row->df_no;
    $marketing_totals[$marketing_key]['po_count'] += (int) $df_row->po_count;
    $marketing_totals[$marketing_key]['df_keys'][$df_key] = true;
    $marketing_totals[$marketing_key]['total_order_value'] += (float) $df_row->total_order_value;
}

$marketing_leaderboard = array_values($marketing_totals);
usort($marketing_leaderboard, function ($left, $right) {
    if ($left['total_order_value'] == $right['total_order_value']) {
        return strcmp($left['name'], $right['name']);
    }

    return ($left['total_order_value'] < $right['total_order_value']) ? 1 : -1;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | DF Wise Cost Order Value</title>

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <?php
    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    foreach ($q->result() as $LOGO);
    ?>

    <style>
        :root {
            --brand-primary: <?php echo $LOGO->colorcode; ?>;
            --brand-ink: #17324d;
            --brand-blue: #175cd3;
            --brand-teal: #248a84;
            --brand-green: #2f9e44;
            --page-bg: #f4f7fb;
            --card-border: #e1e9f2;
            --text-main: #1f2d3d;
            --text-soft: #68768a;
            --shadow-soft: 0 14px 35px rgba(15, 23, 42, 0.08);
            --radius-lg: 18px;
            --radius-md: 12px;
        }

        body {
            background: linear-gradient(180deg, #f9fbff 0%, var(--page-bg) 100%);
        }

        .wrapper .container-fluid {
            padding: 10px 18px 24px;
        }

        .page-shell {
            margin-top: 8px;
        }

        .report-hero {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-ink) 52%, var(--brand-teal) 100%);
            border-radius: 22px;
            box-shadow: 0 20px 50px rgba(18, 52, 86, 0.18);
            color: #fff;
            margin-bottom: 22px;
            overflow: hidden;
            padding: 28px;
            position: relative;
        }

        .report-hero:before,
        .report-hero:after {
            background: rgba(255,255,255,0.08);
            border-radius: 999px;
            content: "";
            position: absolute;
        }

        .report-hero:before {
            height: 170px;
            right: -48px;
            top: -58px;
            width: 170px;
        }

        .report-hero:after {
            bottom: -86px;
            height: 210px;
            left: -70px;
            width: 210px;
        }

        .hero-grid {
            align-items: flex-start;
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow {
            color: rgba(255,255,255,0.76);
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .hero-title {
            color: #fff;
            font-size: 31px;
            font-weight: 800;
            margin: 0 0 10px;
        }

        .hero-copy {
            color: rgba(255,255,255,0.88);
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            max-width: 770px;
        }

        .hero-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-chip {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-size: 12px;
            gap: 8px;
            padding: 8px 12px;
        }

        .hero-chip strong {
            font-weight: 700;
        }

        .hero-filter-box {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 18px;
            min-width: 350px;
            padding: 16px;
        }

        .hero-filter-title {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .hero-filter-box .form-group label {
            color: rgba(255,255,255,0.86);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .hero-filter-box .form-control {
            border: 0;
            border-radius: 12px;
            box-shadow: none;
            min-height: 42px;
        }

        .hero-filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }

        .report-card {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            margin-bottom: 22px;
            padding: 20px;
        }

        .kpi-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            margin-bottom: 22px;
        }

        .kpi-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid #dce7f4;
            border-radius: var(--radius-md);
            min-height: 132px;
            overflow: hidden;
            padding: 18px;
            position: relative;
        }

        .kpi-card:before {
            background: linear-gradient(180deg, rgba(23, 92, 211, 0.10) 0%, rgba(23, 92, 211, 0) 100%);
            content: "";
            inset: 0;
            position: absolute;
        }

        .kpi-card > * {
            position: relative;
            z-index: 1;
        }

        .kpi-label {
            color: var(--text-soft);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .kpi-value {
            color: var(--brand-ink);
            font-size: 27px;
            font-weight: 800;
            line-height: 1.08;
            margin-bottom: 8px;
        }

        .kpi-note {
            color: var(--text-soft);
            font-size: 12px;
        }

        .section-head {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .section-title {
            color: var(--text-main);
            font-size: 19px;
            font-weight: 700;
            margin: 0 0 6px;
        }

        .section-subtitle {
            color: var(--text-soft);
            font-size: 13px;
            margin: 0;
        }

        .chart-shell {
            min-height: 390px;
        }

        .chart-placeholder,
        .empty-state {
            align-items: center;
            background: linear-gradient(180deg, #fffdf5 0%, #fff8e8 100%);
            border: 1px solid #f3de9b;
            border-radius: var(--radius-md);
            color: #8a6d1d;
            display: flex;
            justify-content: center;
            min-height: 120px;
            padding: 18px;
            text-align: center;
        }

        .chart-placeholder {
            min-height: 390px;
        }

        .leaderboard-list {
            list-style: none;
            margin: 0;
            max-height: 430px;
            overflow-y: auto;
            padding: 0;
        }

        .leaderboard-item {
            align-items: center;
            border-bottom: 1px solid #edf2f8;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            padding: 14px 0;
        }

        .leaderboard-item:last-child {
            border-bottom: 0;
        }

        .leaderboard-rank {
            background: #e8f6f4;
            border-radius: 999px;
            color: var(--brand-teal);
            display: inline-flex;
            font-size: 11px;
            font-weight: 700;
            justify-content: center;
            margin-right: 10px;
            min-width: 28px;
            padding: 5px 7px;
        }

        .leaderboard-name {
            color: var(--text-main);
            font-size: 14px;
            font-weight: 700;
        }

        .leaderboard-meta,
        .leaderboard-subvalue {
            color: var(--text-soft);
            display: block;
            font-size: 12px;
            margin-top: 4px;
        }

        .leaderboard-value {
            color: var(--brand-ink);
            font-size: 15px;
            font-weight: 800;
            text-align: right;
        }

        .summary-pill {
            background: #f6f9fc;
            border: 1px solid #dce6f2;
            border-radius: 999px;
            color: #425466;
            display: inline-flex;
            font-size: 12px;
            gap: 8px;
            padding: 8px 12px;
        }

        .summary-pill strong {
            color: var(--brand-ink);
        }

        .df-table thead th {
            background: var(--brand-primary);
            color: #fff;
            font-size: 12px;
            text-align: center;
            vertical-align: middle;
        }

        .df-table tbody td {
            font-size: 13px;
            text-align: center;
            vertical-align: middle;
        }

        .df-table .text-left-cell {
            color: var(--text-main);
            font-weight: 700;
            text-align: left;
        }

        .rank-pill {
            background: #edf4ff;
            border-radius: 999px;
            color: var(--brand-blue);
            display: inline-flex;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
        }

        .table-shell .dataTables_wrapper .dt-buttons {
            margin-bottom: 12px;
        }

        .table-shell .btn {
            border-radius: 999px;
        }

        @media (max-width: 991px) {
            .hero-filter-box {
                min-width: 100%;
            }
        }

        @media (max-width: 767px) {
            .wrapper .container-fluid {
                padding: 8px 12px 20px;
            }

            .report-hero {
                padding: 22px 18px;
            }

            .hero-title {
                font-size: 24px;
            }

            .hero-filter-actions,
            .section-head {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid page-shell">
            <div class="report-hero">
                <div class="hero-grid">
                    <div>
                        <span class="hero-eyebrow">Sales Order Intelligence</span>
                        <h1 class="hero-title">DF Wise Cost Order Value Report</h1>
                        <p class="hero-copy">Review DF-wise booked order value with the marketing person responsible, switch between the full team and individual owners, and export a clean Excel sheet for sharing.</p>
                        <div class="hero-chip-row">
                            <span class="hero-chip"><strong>Financial Year:</strong> <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="hero-chip"><strong>Month:</strong> <?php echo htmlspecialchars($selected_month_label, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="hero-chip"><strong>Marketing:</strong> <?php echo htmlspecialchars($selected_user_label, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <div class="hero-filter-box">
                        <div class="hero-filter-title">Refine This Report</div>
                        <form method="get" action="<?php echo page_url; ?>OrderController/dfwisecostorderreport">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="financial_year">Financial Year</label>
                                        <select name="financial_year" id="financial_year" class="form-control">
                                            <?php foreach ($report_filters['financial_year_options'] as $financial_year_option) { ?>
                                                <option value="<?php echo htmlspecialchars($financial_year_option['value'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($report_filters['financial_year'] === $financial_year_option['value']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($financial_year_option['label'], ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="month">Month</label>
                                        <select name="month" id="month" class="form-control">
                                            <option value="">All Months</option>
                                            <?php foreach ($report_filters['month_options'] as $month_value => $month_name) { ?>
                                                <option value="<?php echo $month_value; ?>" <?php echo ($report_filters['month'] === $month_value) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($month_name, ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="user_id">Marketing Person</label>
                                        <select name="user_id" id="user_id" class="form-control">
                                            <option value="">All Marketing Persons</option>
                                            <?php foreach ($report_users as $report_user) { ?>
                                                <option value="<?php echo $report_user->user_id; ?>" <?php echo ((string) $report_filters['user_id'] === (string) $report_user->user_id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($report_user->full_name, ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-filter-actions">
                                <button type="submit" class="btn btn-warning"><i class="fa fa-filter"></i> Apply Filters</button>
                                <a href="<?php echo page_url; ?>OrderController/dfwisecostorderreport" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Total Cost Order Value</div>
                    <div class="kpi-value"><?php echo format_df_cost_report_currency($df_summary['total_order_value']); ?></div>
                    <div class="kpi-note">Total INR order value in the selected reporting window</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">DFs Tracked</div>
                    <div class="kpi-value"><?php echo (int) $df_summary['total_dfs']; ?></div>
                    <div class="kpi-note">Unique DF numbers represented in this view</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">PO Count</div>
                    <div class="kpi-value"><?php echo (int) $df_summary['total_orders']; ?></div>
                    <div class="kpi-note">PO records contributing to the value</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Marketing Persons</div>
                    <div class="kpi-value"><?php echo (int) $df_summary['active_marketing_people']; ?></div>
                    <div class="kpi-note">Owners with at least one visible DF/order row</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Average DF Value</div>
                    <div class="kpi-value"><?php echo format_df_cost_report_currency($df_summary['avg_df_value']); ?></div>
                    <div class="kpi-note">Total value divided by visible unique DFs</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Top DF</div>
                    <div class="kpi-value"><?php echo htmlspecialchars($df_summary['top_df_no'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="kpi-note"><?php echo format_df_cost_report_currency($df_summary['top_df_order_value']); ?> | <?php echo htmlspecialchars($df_summary['top_marketing_person'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="report-card">
                        <div class="section-head">
                            <div>
                                <h3 class="section-title">Top DF Cost Order Value</h3>
                                <p class="section-subtitle">Top DF and marketing-person combinations ranked by booked INR value.</p>
                            </div>
                            <span class="summary-pill"><strong>View</strong> <?php echo htmlspecialchars($selected_financial_year_label . ' | ' . $selected_month_label, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <?php if (!empty($df_rows)) { ?>
                            <div id="dfCostChartWrap" class="chart-shell">
                                <canvas id="dfCostChart"></canvas>
                            </div>
                        <?php } else { ?>
                            <div class="chart-placeholder">No DF cost order value found for the selected filters.</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="report-card">
                        <div class="section-head">
                            <div>
                                <h3 class="section-title">Marketing Summary</h3>
                                <p class="section-subtitle">Overall owner-wise totals from the same DF rows.</p>
                            </div>
                        </div>

                        <?php if (!empty($marketing_leaderboard)) { ?>
                            <ul class="leaderboard-list">
                                <?php foreach (array_slice($marketing_leaderboard, 0, 10) as $index => $marketing_row) { ?>
                                    <li class="leaderboard-item">
                                        <div>
                                            <span class="leaderboard-rank">#<?php echo $index + 1; ?></span>
                                            <span class="leaderboard-name"><?php echo htmlspecialchars($marketing_row['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="leaderboard-meta"><?php echo count($marketing_row['df_keys']); ?> DFs | <?php echo (int) $marketing_row['po_count']; ?> POs</span>
                                        </div>
                                        <div>
                                            <span class="leaderboard-value"><?php echo format_df_cost_report_currency($marketing_row['total_order_value']); ?></span>
                                            <span class="leaderboard-subvalue">Total value</span>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <div class="empty-state">No marketing summary is available for the selected filters.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="report-card table-shell">
                <div class="section-head">
                    <div>
                        <h3 class="section-title">Detailed DF Wise Table</h3>
                        <p class="section-subtitle">Export this table to Excel for a formatted DF, marketing person, PO, customer, date, and value view.</p>
                    </div>
                    <a href="<?php echo htmlspecialchars($excel_export_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Management Excel
                    </a>
                </div>

                <div class="table-responsive">
                    <table id="dfCostTable" class="table table-striped table-bordered df-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>DF No</th>
                                <th>Marketing Person</th>
                                <th>Financial Year</th>
                                <th>Customers</th>
                                <th>PO Count</th>
                                <th>PO Numbers</th>
                                <th>First PO Date</th>
                                <th>Latest PO Date</th>
                                <th>Total Cost Order Value</th>
                                <th>Average PO Value</th>
                                <th>Value Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($df_rows as $index => $df_row) { ?>
                                <?php $value_share = $total_order_value_for_share > 0 ? (((float) $df_row->total_order_value / $total_order_value_for_share) * 100) : 0; ?>
                                <tr>
                                    <td><span class="rank-pill">#<?php echo $index + 1; ?></span></td>
                                    <td class="text-left-cell"><?php echo htmlspecialchars($df_row->df_no, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-left-cell"><?php echo htmlspecialchars($df_row->marketing_person, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-left-cell"><?php echo htmlspecialchars(!empty($df_row->customer_names) ? $df_row->customer_names : 'NA', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo (int) $df_row->po_count; ?></td>
                                    <td class="text-left-cell"><?php echo htmlspecialchars(!empty($df_row->po_numbers) ? $df_row->po_numbers : 'NA', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo !empty($df_row->first_order_date) ? date('d-m-Y', strtotime($df_row->first_order_date)) : 'NA'; ?></td>
                                    <td><?php echo !empty($df_row->latest_order_date) ? date('d-m-Y', strtotime($df_row->latest_order_date)) : 'NA'; ?></td>
                                    <td><?php echo format_df_cost_report_currency($df_row->total_order_value); ?></td>
                                    <td><?php echo format_df_cost_report_currency($df_row->avg_order_value); ?></td>
                                    <td><?php echo number_format($value_share, 1); ?>%</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">Grand Total</th>
                                <th><?php echo (int) $df_summary['total_orders']; ?></th>
                                <th colspan="3"></th>
                                <th><?php echo format_df_cost_report_currency($df_summary['total_order_value']); ?></th>
                                <th><?php echo !empty($df_summary['total_orders']) ? format_df_cost_report_currency((float) $df_summary['total_order_value'] / (int) $df_summary['total_orders']) : format_df_cost_report_currency(0); ?></th>
                                <th>100.0%</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <?php $this->load->view('common/footer'); ?>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        const dfCostData = <?php echo json_encode(array_slice($df_rows, 0, 15)); ?>;

        function formatIndianCurrency(value) {
            const amount = Number(value || 0);
            return 'Rs ' + new Intl.NumberFormat('en-IN', {
                maximumFractionDigits: 0
            }).format(amount);
        }

        function formatCompactCurrency(value) {
            const amount = Number(value || 0);
            const absoluteAmount = Math.abs(amount);

            if (absoluteAmount >= 10000000) {
                return 'Rs ' + (amount / 10000000).toFixed(1).replace(/\.0$/, '') + ' Cr';
            }
            if (absoluteAmount >= 100000) {
                return 'Rs ' + (amount / 100000).toFixed(1).replace(/\.0$/, '') + ' L';
            }
            if (absoluteAmount >= 1000) {
                return 'Rs ' + (amount / 1000).toFixed(1).replace(/\.0$/, '') + ' K';
            }
            return formatIndianCurrency(amount);
        }

        $(document).ready(function () {
            $('#dfCostTable').DataTable({
                pageLength: 25,
                responsive: true,
                order: [],
                dom: 'lfrtip'
            });

            if (dfCostData.length > 0 && document.getElementById('dfCostChart')) {
                const chartWrap = document.getElementById('dfCostChartWrap');
                chartWrap.style.height = Math.max(390, dfCostData.length * 58) + 'px';

                const chartContext = document.getElementById('dfCostChart').getContext('2d');
                const barGradient = chartContext.createLinearGradient(0, 0, 520, 0);
                barGradient.addColorStop(0, 'rgba(23, 92, 211, 0.92)');
                barGradient.addColorStop(1, 'rgba(36, 138, 132, 0.88)');

                new Chart(chartContext, {
                    type: 'bar',
                    data: {
                        labels: dfCostData.map(function(item) {
                            return (item.df_no || 'DF Not Mapped') + ' | ' + (item.marketing_person || 'Unassigned');
                        }),
                        datasets: [{
                            label: 'Total Cost Order Value',
                            data: dfCostData.map(function(item) { return Number(item.total_order_value || 0); }),
                            backgroundColor: barGradient,
                            borderColor: 'rgba(23, 92, 211, 1)',
                            borderWidth: 1,
                            borderRadius: 12,
                            maxBarThickness: 26
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: { color: '#e3ebf5' },
                                ticks: {
                                    callback: function(value) {
                                        return formatCompactCurrency(value);
                                    }
                                }
                            },
                            y: {
                                grid: { display: false }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const row = dfCostData[context.dataIndex] || {};
                                        return [
                                            'Value: ' + formatIndianCurrency(context.raw),
                                            'PO Count: ' + Number(row.po_count || 0),
                                            'Avg PO: ' + formatIndianCurrency(row.avg_order_value || 0)
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
