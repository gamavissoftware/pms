<?php
$CIA =& get_instance();
$CIA->load->model('Dashboard_model');

if (!function_exists('format_brand_report_currency')) {
    function format_brand_report_currency($amount)
    {
        return 'Rs ' . number_format((float) $amount, 0);
    }
}

$brand_report_filters = isset($brand_report_filters) && is_array($brand_report_filters) ? $brand_report_filters : array();
$brand_rows = isset($order_data_by_brand) && is_array($order_data_by_brand) ? $order_data_by_brand : array();
$brand_report_summary = isset($brand_report_summary) && is_array($brand_report_summary) ? $brand_report_summary : array();

$brand_report_summary_defaults = array(
    'total_orders' => 0,
    'total_order_value' => 0,
    'avg_order_value' => 0,
    'active_brands' => 0,
    'top_brand_name' => 'No data found',
    'top_brand_order_value' => 0,
    'top_brand_order_count' => 0,
    'latest_order_date' => '',
);
$brand_report_summary = array_merge($brand_report_summary_defaults, $brand_report_summary);

$selected_financial_year_label = isset($brand_report_filters['financial_year_label']) ? $brand_report_filters['financial_year_label'] : 'Latest Financial Year';
$selected_month_label = isset($brand_report_filters['month_label']) ? $brand_report_filters['month_label'] : 'All Months';
$selected_period_label = isset($brand_report_filters['filter_mode_label']) ? $brand_report_filters['filter_mode_label'] : $selected_financial_year_label;
$chart_rows = array_slice($brand_rows, 0, 12);
$leaderboard_rows = array_slice($brand_rows, 0, 5);
$top_five_value = 0;

foreach ($leaderboard_rows as $leaderboard_row) {
    $top_five_value += (float) $leaderboard_row['total_order_value'];
}

$top_five_share = $brand_report_summary['total_order_value'] > 0 ? ($top_five_value / $brand_report_summary['total_order_value']) * 100 : 0;
$export_title = 'Brand_Performance_' . preg_replace('/[^A-Za-z0-9]+/', '_', $selected_period_label);
$report_return_url = page_url . 'OrderController';
if (!empty($_SERVER['QUERY_STRING'])) {
    $report_return_url .= '?' . $_SERVER['QUERY_STRING'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | Brand Performance Report</title>

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
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <?php
    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    foreach ($q->result() as $LOGO);
    ?>

    <style>
        :root {
            --brand-primary: <?php echo $LOGO->colorcode; ?>;
            --brand-ink: #12344d;
            --brand-teal: #0f9d8a;
            --brand-amber: #f08c00;
            --page-bg: #f4f7fb;
            --card-border: #e1e9f2;
            --text-main: #1f2d3d;
            --text-soft: #66758a;
            --shadow-soft: 0 16px 38px rgba(15, 23, 42, 0.08);
            --radius-lg: 18px;
            --radius-md: 14px;
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
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-ink) 56%, var(--brand-teal) 100%);
            border-radius: 24px;
            box-shadow: 0 22px 52px rgba(18, 52, 77, 0.18);
            color: #fff;
            margin-bottom: 22px;
            overflow: hidden;
            padding: 28px;
            position: relative;
        }

        .report-hero:before,
        .report-hero:after {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            content: "";
            position: absolute;
        }

        .report-hero:before {
            height: 200px;
            right: -40px;
            top: -70px;
            width: 200px;
        }

        .report-hero:after {
            bottom: -110px;
            height: 240px;
            left: -90px;
            width: 240px;
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

        .hero-copy-wrap {
            flex: 1 1 540px;
            max-width: 760px;
        }

        .hero-eyebrow {
            color: rgba(255, 255, 255, 0.78);
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
            color: rgba(255, 255, 255, 0.88);
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        .hero-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-chip {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
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

        .hero-mini-stats {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            margin-top: 18px;
        }

        .hero-mini-card {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 16px;
            padding: 14px 16px;
        }

        .hero-mini-label {
            color: rgba(255, 255, 255, 0.72);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .hero-mini-value {
            color: #fff;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
        }

        .hero-mini-note {
            color: rgba(255, 255, 255, 0.76);
            font-size: 12px;
            margin-top: 6px;
        }

        .hero-filter-box {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 18px;
            min-width: 340px;
            padding: 18px;
        }

        .hero-filter-title {
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .hero-filter-note {
            color: rgba(255, 255, 255, 0.72);
            font-size: 12px;
            line-height: 1.5;
            margin: 0 0 14px;
        }

        .hero-filter-box .form-group label {
            color: rgba(255, 255, 255, 0.86);
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

        .hero-filter-actions .btn {
            border-radius: 999px;
            font-weight: 700;
            min-width: 120px;
        }

        .flash-message {
            background: #fff7e6;
            border: 1px solid #f3d29b;
            border-radius: 14px;
            color: #8a5b08;
            margin-bottom: 18px;
            padding: 12px 16px;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            margin-bottom: 22px;
        }

        .kpi-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid #dbe6f2;
            border-radius: var(--radius-md);
            min-height: 134px;
            overflow: hidden;
            padding: 18px;
            position: relative;
        }

        .kpi-card:before {
            background: linear-gradient(180deg, rgba(15, 157, 138, 0.10) 0%, rgba(15, 157, 138, 0) 100%);
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
            font-size: 28px;
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 8px;
        }

        .kpi-note {
            color: var(--text-soft);
            font-size: 12px;
            line-height: 1.5;
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

        .summary-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .chart-shell {
            min-height: 380px;
        }

        .chart-placeholder {
            align-items: center;
            background: linear-gradient(180deg, #f9fbfe 0%, #f3f7fb 100%);
            border: 1px dashed #cfdae8;
            border-radius: var(--radius-md);
            color: var(--text-soft);
            display: flex;
            justify-content: center;
            min-height: 380px;
            padding: 24px;
            text-align: center;
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
            justify-content: space-between;
            padding: 14px 0;
        }

        .leaderboard-item:last-child {
            border-bottom: 0;
        }

        .leaderboard-rank {
            background: #e8f7f4;
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

        .leaderboard-meta {
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

        .leaderboard-subvalue {
            color: var(--text-soft);
            display: block;
            font-size: 12px;
            margin-top: 4px;
            text-align: right;
        }

        .table-shell .dataTables_wrapper .dt-buttons {
            margin-bottom: 12px;
        }

        .brand-table thead th {
            background: var(--brand-primary);
            color: #fff;
            font-size: 12px;
            text-align: center;
        }

        .brand-table tbody td {
            font-size: 13px;
            text-align: center;
            vertical-align: middle;
        }

        .brand-table .brand-name-cell {
            color: var(--text-main);
            font-weight: 700;
            text-align: left;
        }

        .rank-pill {
            background: #edf7f5;
            border-radius: 999px;
            color: var(--brand-teal);
            display: inline-flex;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
        }

        .share-pill {
            background: #fff5e8;
            border-radius: 999px;
            color: var(--brand-amber);
            display: inline-flex;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
        }

        .action-btn {
            border-radius: 999px;
            padding-left: 14px;
            padding-right: 14px;
        }

        .select2-container {
            width: 100% !important;
        }

        .assign-brand-note {
            background: #eefbf7;
            border: 1px solid #c6efe3;
            border-radius: var(--radius-md);
            color: #11634f;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 18px;
            padding: 12px 14px;
        }

        .modal-po-context {
            color: var(--text-main);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .modal-content {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.18);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-ink) 100%);
            border-radius: 18px 18px 0 0;
            color: #fff;
        }

        .modal-header .close {
            color: #fff;
            opacity: 1;
        }

        .modal-table thead th {
            background: #eef5ff;
            color: var(--brand-ink);
            font-size: 12px;
            text-align: center;
        }

        .modal-table tbody td {
            font-size: 13px;
            text-align: center;
        }

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

            .hero-filter-actions {
                flex-direction: column;
            }

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
                    <div class="hero-copy-wrap">
                        <span class="hero-eyebrow">Sales Order Intelligence</span>
                        <h1 class="hero-title">Brand Performance Report</h1>
                        <p class="hero-copy">Track which brands are driving the most booked order value, compare their order mix, and open the exact PO records without leaving the report.</p>

                        <div class="hero-chip-row">
                            <span class="hero-chip"><strong>Reporting Window:</strong> <?php echo htmlspecialchars($selected_period_label, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="hero-chip"><strong>Financial Year:</strong> <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="hero-chip"><strong>Month View:</strong> <?php echo htmlspecialchars($selected_month_label, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <div class="hero-mini-stats">
                            <div class="hero-mini-card">
                                <div class="hero-mini-label">Top Brand</div>
                                <div class="hero-mini-value"><?php echo htmlspecialchars($brand_report_summary['top_brand_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="hero-mini-note"><?php echo format_brand_report_currency($brand_report_summary['top_brand_order_value']); ?> from <?php echo (int) $brand_report_summary['top_brand_order_count']; ?> orders</div>
                            </div>
                            <div class="hero-mini-card">
                                <div class="hero-mini-label">Top 5 Contribution</div>
                                <div class="hero-mini-value"><?php echo number_format($top_five_share, 1); ?>%</div>
                                <div class="hero-mini-note">Share of total booked order value from the top five brands</div>
                            </div>
                            <div class="hero-mini-card">
                                <div class="hero-mini-label">Latest Booking</div>
                                <div class="hero-mini-value"><?php echo !empty($brand_report_summary['latest_order_date']) ? date('d M Y', strtotime($brand_report_summary['latest_order_date'])) : 'No Data'; ?></div>
                                <div class="hero-mini-note">Most recent booked order within the selected filters</div>
                            </div>
                        </div>
                    </div>

                    <div class="hero-filter-box">
                        <div class="hero-filter-title">Refine This Report</div>
                        <p class="hero-filter-note">
                            <?php echo !empty($brand_report_filters['custom_range_active']) ? 'Custom date range is active and overrides financial year or month selection.' : 'Pick a financial year, focus on a single month, or use both start and end dates for a custom range.'; ?>
                        </p>
                        <form method="get" action="<?php echo page_url; ?>OrderController">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="financial_year">Financial Year</label>
                                        <select name="financial_year" id="financial_year" class="form-control">
                                            <?php foreach ($brand_report_filters['financial_year_options'] as $financial_year_option) { ?>
                                                <option value="<?php echo htmlspecialchars($financial_year_option['value'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($brand_report_filters['financial_year'] === $financial_year_option['value']) ? 'selected' : ''; ?>>
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
                                            <?php foreach ($brand_report_filters['month_options'] as $month_value => $month_name) { ?>
                                                <option value="<?php echo $month_value; ?>" <?php echo ($brand_report_filters['month'] === $month_value) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($month_name, ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($brand_report_filters['start_date'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($brand_report_filters['end_date'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="hero-filter-actions">
                                <button type="submit" class="btn btn-warning"><i class="fa fa-filter"></i> Apply Filters</button>
                                <a href="<?php echo page_url; ?>OrderController" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('message')) { ?>
                <div class="flash-message"><?php echo $this->session->flashdata('message'); ?></div>
            <?php } ?>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Total Order Value</div>
                    <div class="kpi-value"><?php echo format_brand_report_currency($brand_report_summary['total_order_value']); ?></div>
                    <div class="kpi-note">Total booked order value in the current reporting window</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Orders</div>
                    <div class="kpi-value"><?php echo (int) $brand_report_summary['total_orders']; ?></div>
                    <div class="kpi-note">Orders distributed across the visible brands</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Active Brands</div>
                    <div class="kpi-value"><?php echo (int) $brand_report_summary['active_brands']; ?></div>
                    <div class="kpi-note">Brands with at least one booked order in this view</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Average Order Value</div>
                    <div class="kpi-value"><?php echo format_brand_report_currency($brand_report_summary['avg_order_value']); ?></div>
                    <div class="kpi-note">Average ticket size across all visible brand orders</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Top Brand</div>
                    <div class="kpi-value"><?php echo htmlspecialchars($brand_report_summary['top_brand_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="kpi-note"><?php echo format_brand_report_currency($brand_report_summary['top_brand_order_value']); ?> from <?php echo (int) $brand_report_summary['top_brand_order_count']; ?> orders</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Top 5 Value Share</div>
                    <div class="kpi-value"><?php echo number_format($top_five_share, 1); ?>%</div>
                    <div class="kpi-note">How concentrated the order book is among the leading brands</div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="report-card">
                        <div class="section-head">
                            <div>
                                <h3 class="section-title">Top Brand Performance Chart</h3>
                                <p class="section-subtitle">The chart highlights the top 12 brands by booked order value. Click a bar to open the exact PO records behind it.</p>
                            </div>
                            <span class="summary-pill"><strong>View</strong> <?php echo htmlspecialchars($selected_period_label, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <?php if (!empty($chart_rows)) { ?>
                            <div id="brandPerformanceChartWrap" class="chart-shell">
                                <canvas id="brandPerformanceChart"></canvas>
                            </div>
                        <?php } else { ?>
                            <div class="chart-placeholder">No brand performance data found for the selected filters.</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="report-card">
                        <div class="section-head">
                            <div>
                                <h3 class="section-title">Top 5 Brands</h3>
                                <p class="section-subtitle">Quick leaderboard ranked by total booked order value.</p>
                            </div>
                        </div>

                        <?php if (!empty($leaderboard_rows)) { ?>
                            <ul class="leaderboard-list">
                                <?php foreach ($leaderboard_rows as $index => $leaderboard_row) { ?>
                                    <?php $row_share = $brand_report_summary['total_order_value'] > 0 ? (((float) $leaderboard_row['total_order_value'] / (float) $brand_report_summary['total_order_value']) * 100) : 0; ?>
                                    <li class="leaderboard-item">
                                        <div>
                                            <span class="leaderboard-rank">#<?php echo $index + 1; ?></span>
                                            <span class="leaderboard-name"><?php echo htmlspecialchars($leaderboard_row['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="leaderboard-meta"><?php echo (int) $leaderboard_row['order_count']; ?> orders | Avg <?php echo format_brand_report_currency($leaderboard_row['avg_order_value']); ?></span>
                                        </div>
                                        <div>
                                            <span class="leaderboard-value"><?php echo format_brand_report_currency($leaderboard_row['total_order_value']); ?></span>
                                            <span class="leaderboard-subvalue"><?php echo number_format($row_share, 1); ?>% share</span>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                            <div class="summary-pills">
                                <span class="summary-pill"><strong>Top 5 Value</strong> <?php echo format_brand_report_currency($top_five_value); ?></span>
                                <span class="summary-pill"><strong>Brands Tracked</strong> <?php echo (int) $brand_report_summary['active_brands']; ?></span>
                            </div>
                        <?php } else { ?>
                            <div class="empty-state">No leaderboard data is available for the selected filters.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="report-card table-shell">
                <div class="section-head">
                    <div>
                        <h3 class="section-title">Detailed Brand Ranking Table</h3>
                        <p class="section-subtitle">Compare order count, total booked value, average order value, share of business, and the latest order date for every visible brand.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="brandPerformanceTable" class="table table-striped table-bordered brand-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Brand</th>
                                <th>Total Orders</th>
                                <th>Total Order Value</th>
                                <th>Average Order Value</th>
                                <th>Value Share</th>
                                <th>Latest Order Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($brand_rows as $index => $brand_row) { ?>
                                <?php $row_share = $brand_report_summary['total_order_value'] > 0 ? (((float) $brand_row['total_order_value'] / (float) $brand_report_summary['total_order_value']) * 100) : 0; ?>
                                <tr>
                                    <td><span class="rank-pill">#<?php echo $index + 1; ?></span></td>
                                    <td class="brand-name-cell"><?php echo htmlspecialchars($brand_row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo (int) $brand_row['order_count']; ?></td>
                                    <td><?php echo format_brand_report_currency($brand_row['total_order_value']); ?></td>
                                    <td><?php echo format_brand_report_currency($brand_row['avg_order_value']); ?></td>
                                    <td><span class="share-pill"><?php echo number_format($row_share, 1); ?>%</span></td>
                                    <td><?php echo !empty($brand_row['latest_order_date']) ? date('d-m-Y', strtotime($brand_row['latest_order_date'])) : 'NA'; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs action-btn view-order-details" data-brand-id="<?php echo htmlspecialchars((string) $brand_row['id'], ENT_QUOTES, 'UTF-8'); ?>" data-brand-name="<?php echo htmlspecialchars($brand_row['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo ((int) $brand_row['id'] === 0) ? 'Map Brands' : 'View Orders'; ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="orderDetailsModalLabel">Brand Order Details</h4>
                        </div>
                        <div class="modal-body">
                            <div id="orderDetailsMessage" class="empty-state" style="display:none;"></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered modal-table">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Company Name</th>
                                            <th>PO No.</th>
                                            <th>PO Date</th>
                                            <th>Order Added By</th>
                                            <th>Order Value</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orderDetailsTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="assignBrandModal" tabindex="-1" role="dialog" aria-labelledby="assignBrandModalLabel">
                <div class="modal-dialog" role="document">
                    <form id="assignBrandForm" method="post" action="<?php echo page_url; ?>Task/brandmapping" enctype="multipart/form-data">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="assignBrandModalLabel">Assign Brand</h4>
                            </div>
                            <div class="modal-body">
                                <div class="assign-brand-note">
                                    Select an existing brand, or type a new brand name and press Enter to create it while saving.
                                </div>
                                <div id="assignBrandPoContext" class="modal-po-context"></div>
                                <input type="hidden" id="assignBrandPoId" name="poid" value="">
                                <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($report_return_url, ENT_QUOTES, 'UTF-8'); ?>">

                                <div class="form-group">
                                    <label for="assignBrandSelect">Brand</label>
                                    <select class="form-control brands" name="tagbrand" id="assignBrandSelect" required>
                                        <option value="">Select Brand</option>
                                        <?php
                                        $brand_q = $this->db->select('id, name')->from('company_brand')->order_by('name','asc')->get();
                                        if ($brand_q->num_rows() > 0) {
                                            foreach ($brand_q->result() as $brand) {
                                        ?>
                                            <option value="<?php echo (int) $brand->id; ?>">
                                                <?php echo htmlspecialchars(ucwords(strtolower($brand->name)), ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success action-btn">
                                    <i class="fa fa-check"></i> Save Brand
                                </button>
                            </div>
                        </div>
                    </form>
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
    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        const brandData = <?php echo json_encode($brand_rows); ?>;
        const chartBrandData = <?php echo json_encode($chart_rows); ?>;
        const reportQuery = <?php echo json_encode(array(
            'financial_year' => $brand_report_filters['financial_year'],
            'month' => $brand_report_filters['month'],
            'start_date' => $brand_report_filters['start_date'],
            'end_date' => $brand_report_filters['end_date'],
        )); ?>;
        const orderDetailsBaseUrl = <?php echo json_encode(page_url . 'OrderController/get_order_details/'); ?>;

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

        function formatDisplayDate(dateString) {
            if (!dateString) {
                return 'NA';
            }

            if (dateString.indexOf('-') > -1) {
                const parts = dateString.split('-');
                if (parts.length === 3 && parts[0].length === 4) {
                    return parts[2] + '-' + parts[1] + '-' + parts[0];
                }
            }

            return dateString;
        }

        function escapeHtml(value) {
            return String(value === null || value === undefined ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function openAssignBrandModal(poId, poLabel) {
            $('#assignBrandPoId').val(poId);
            $('#assignBrandPoContext').text(poLabel ? 'PO: ' + poLabel : 'Selected PO');
            $('#assignBrandSelect').val('').trigger('change');
            $('#assignBrandModal').modal('show');
        }

        function buildOrderDetailsUrl(brandId) {
            const params = [];

            if (reportQuery.financial_year) {
                params.push('financial_year=' + encodeURIComponent(reportQuery.financial_year));
            }
            if (reportQuery.month) {
                params.push('month=' + encodeURIComponent(reportQuery.month));
            }
            if (reportQuery.start_date) {
                params.push('start_date=' + encodeURIComponent(reportQuery.start_date));
            }
            if (reportQuery.end_date) {
                params.push('end_date=' + encodeURIComponent(reportQuery.end_date));
            }

            return orderDetailsBaseUrl + encodeURIComponent(brandId) + (params.length ? '?' + params.join('&') : '');
        }

        function openOrderDetails(brandId, brandName) {
            $('#orderDetailsModalLabel').text('Brand Order Details - ' + brandName);
            $('#orderDetailsTableBody').html('');
            $('#orderDetailsMessage').hide().text('');
            $('#orderDetailsModal').modal('show');

            $.ajax({
                url: buildOrderDetailsUrl(brandId),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (!response || response.length === 0) {
                        $('#orderDetailsMessage').text('No order details found for this brand in the selected period.').show();
                        return;
                    }

                    let rowsHtml = '';
                    response.forEach(function(order, index) {
                        const addedBy = [order.title || '', order.first_name || '', order.last_name || ''].join(' ').replace(/\s+/g, ' ').trim();
                        const poLabel = [order.pono || '', order.company_name || ''].filter(Boolean).join(' - ');
                        const actionHtml = Number(brandId) === 0 && order.id
                            ? '<button type="button" class="btn btn-success btn-xs action-btn assign-order-brand" data-po-id="' + escapeHtml(order.id) + '" data-po-label="' + escapeHtml(poLabel) + '"><i class="fa fa-tag"></i> Assign Brand</button>'
                            : '<span class="text-muted">Mapped</span>';

                        rowsHtml += '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + escapeHtml(order.company_name || '') + '</td>' +
                            '<td>' + escapeHtml(order.pono || '') + '</td>' +
                            '<td>' + escapeHtml(order.podate || 'NA') + '</td>' +
                            '<td>' + escapeHtml(addedBy || 'NA') + '</td>' +
                            '<td>' + formatIndianCurrency(order.order_value) + '</td>' +
                            '<td>' + actionHtml + '</td>' +
                            '</tr>';
                    });

                    $('#orderDetailsTableBody').html(rowsHtml);
                },
                error: function() {
                    $('#orderDetailsMessage').text('Unable to load order details right now.').show();
                }
            });
        }

        $(document).ready(function () {
            $('#brandPerformanceTable').DataTable({
                pageLength: 25,
                responsive: true,
                order: [],
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: <?php echo json_encode($export_title); ?>
                    }
                ]
            });

            $('.view-order-details').on('click', function () {
                openOrderDetails($(this).data('brand-id'), $(this).data('brand-name'));
            });

            $('#orderDetailsTableBody').on('click', '.assign-order-brand', function () {
                openAssignBrandModal($(this).data('po-id'), $(this).data('po-label'));
            });

            $('.brands').select2({
                tags: true,
                width: '100%',
                dropdownParent: $('#assignBrandModal')
            });

            if (chartBrandData.length > 0 && document.getElementById('brandPerformanceChart')) {
                const chartWrap = document.getElementById('brandPerformanceChartWrap');
                chartWrap.style.height = Math.max(360, chartBrandData.length * 58) + 'px';

                const chartContext = document.getElementById('brandPerformanceChart').getContext('2d');
                const barGradient = chartContext.createLinearGradient(0, 0, 520, 0);
                barGradient.addColorStop(0, 'rgba(18, 52, 77, 0.95)');
                barGradient.addColorStop(1, 'rgba(15, 157, 138, 0.85)');

                const brandPerformanceChart = new Chart(chartContext, {
                    type: 'bar',
                    data: {
                        labels: chartBrandData.map(function(item) { return item.name; }),
                        datasets: [{
                            label: 'Total Order Value',
                            data: chartBrandData.map(function(item) { return Number(item.total_order_value || 0); }),
                            backgroundColor: barGradient,
                            borderColor: 'rgba(18, 52, 77, 1)',
                            borderWidth: 1,
                            borderRadius: 14,
                            maxBarThickness: 28
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
                                        const row = chartBrandData[context.dataIndex] || {};
                                        return [
                                            'Sales: ' + formatIndianCurrency(context.raw),
                                            'Orders: ' + Number(row.order_count || 0),
                                            'Avg Order: ' + formatIndianCurrency(row.avg_order_value || 0),
                                            'Latest Order: ' + formatDisplayDate(row.latest_order_date || '')
                                        ];
                                    }
                                }
                            }
                        },
                        onClick: function(evt) {
                            const activeElements = brandPerformanceChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                            if (!activeElements.length) {
                                return;
                            }

                            const clickedIndex = activeElements[0].index;
                            const clickedRow = chartBrandData[clickedIndex];
                            if (clickedRow && typeof clickedRow.id !== 'undefined') {
                                openOrderDetails(clickedRow.id, clickedRow.name);
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
