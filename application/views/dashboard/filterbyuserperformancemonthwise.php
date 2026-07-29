<?php
$CIA =& get_instance();
$CIA->load->model('Dashboard_model');

if (!function_exists('format_report_currency')) {
    function format_report_currency($amount)
    {
        return 'Rs ' . number_format((float) $amount, 0);
    }
}

$selected_financial_year_label = isset($report_filters['financial_year_label']) ? $report_filters['financial_year_label'] : 'Latest Financial Year';
$selected_month_label = isset($report_filters['month_label']) ? $report_filters['month_label'] : 'All Months';
$selected_user_label = !empty($selected_user_label) ? $selected_user_label : 'All Sales Agents';
$performance_summary = isset($performance_summary) && is_array($performance_summary) ? $performance_summary : array();
$performance_rows = isset($performance_rows) ? $performance_rows : array();
$report_users = isset($report_users) ? $report_users : array();
$report_filters = isset($report_filters) && is_array($report_filters) ? $report_filters : array();

$performance_summary_defaults = array(
    'total_orders' => 0,
    'total_order_value' => 0,
    'avg_order_value' => 0,
    'active_agents' => 0,
    'top_agent_name' => 'No data found',
    'top_agent_order_value' => 0,
    'top_agent_order_count' => 0,
    'latest_order_date' => '',
);
$performance_summary = array_merge($performance_summary_defaults, $performance_summary);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | Sales Agent Performance</title>

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
            --brand-ink: #16324f;
            --brand-sky: #0f62fe;
            --brand-green: #2f9e44;
            --brand-amber: #f59f00;
            --page-bg: #f4f7fb;
            --card-border: #e2e9f3;
            --text-main: #1f2d3d;
            --text-soft: #66758a;
            --shadow-soft: 0 14px 35px rgba(15, 23, 42, 0.08);
            --radius-lg: 18px;
            --radius-md: 14px;
        }

        body {
            background: linear-gradient(180deg, #f8fbff 0%, var(--page-bg) 100%);
        }

        .wrapper .container-fluid {
            padding: 10px 18px 24px;
        }

        .page-shell {
            margin-top: 8px;
        }

        .report-hero {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-ink) 54%, var(--brand-green) 100%);
            border-radius: 24px;
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
            height: 180px;
            right: -50px;
            top: -60px;
            width: 180px;
        }

        .report-hero:after {
            bottom: -90px;
            height: 220px;
            left: -70px;
            width: 220px;
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
            max-width: 760px;
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
            min-width: 330px;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            background: linear-gradient(180deg, rgba(15, 98, 254, 0.10) 0%, rgba(15, 98, 254, 0) 100%);
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

        .section-head {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
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
            max-height: 420px;
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
            background: #e9f2ff;
            border-radius: 999px;
            color: var(--brand-sky);
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

        .summary-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
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

        .table-shell .dataTables_wrapper .dt-buttons {
            margin-bottom: 12px;
        }

        .performance-table thead th {
            background: var(--brand-primary);
            color: #fff;
            font-size: 12px;
            text-align: center;
        }

        .performance-table tbody td {
            font-size: 13px;
            text-align: center;
            vertical-align: middle;
        }

        .performance-table .agent-name-cell {
            color: var(--text-main);
            font-weight: 700;
            text-align: left;
        }

        .rank-pill {
            background: #edf4ff;
            border-radius: 999px;
            color: var(--brand-sky);
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
                    <div>
                        <span class="hero-eyebrow">Sales Order Intelligence</span>
                        <h1 class="hero-title">Marketing Team Performance Report</h1>
                        <p class="hero-copy">Track who is driving the most order value, how many orders each sales agent has booked, and drill into the actual PO records without leaving the report.</p>
                        <div class="hero-chip-row">
                            <span class="hero-chip"><strong>Financial Year:</strong> <?php echo htmlspecialchars($selected_financial_year_label, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="hero-chip"><strong>Month:</strong> <?php echo htmlspecialchars($selected_month_label, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="hero-chip"><strong>Sales Agent:</strong> <?php echo htmlspecialchars($selected_user_label, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <div class="hero-filter-box">
                        <div class="hero-filter-title">Refine This Report</div>
                        <form method="get" action="<?php echo page_url; ?>OrderController/userwisemonthlyreport">
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
                                        <label for="user_id">Sales Agent</label>
                                        <select name="user_id" id="user_id" class="form-control">
                                            <option value="">All Sales Agents</option>
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
                                <a href="<?php echo page_url; ?>OrderController/userwisemonthlyreport" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Total Order Value</div>
                    <div class="kpi-value"><?php echo format_report_currency($performance_summary['total_order_value']); ?></div>
                    <div class="kpi-note">Booked within the selected reporting window</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Orders</div>
                    <div class="kpi-value"><?php echo (int) $performance_summary['total_orders']; ?></div>
                    <div class="kpi-note">Orders counted across the visible sales agents</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Active Sales Agents</div>
                    <div class="kpi-value"><?php echo (int) $performance_summary['active_agents']; ?></div>
                    <div class="kpi-note">Agents with at least one booked order</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Average Order Value</div>
                    <div class="kpi-value"><?php echo format_report_currency($performance_summary['avg_order_value']); ?></div>
                    <div class="kpi-note">Average ticket size in the selected period</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Top Performer</div>
                    <div class="kpi-value"><?php echo htmlspecialchars($performance_summary['top_agent_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="kpi-note"><?php echo format_report_currency($performance_summary['top_agent_order_value']); ?> across <?php echo (int) $performance_summary['top_agent_order_count']; ?> orders</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Latest Order Date</div>
                    <div class="kpi-value">
                        <?php echo !empty($performance_summary['latest_order_date']) ? date('d M Y', strtotime($performance_summary['latest_order_date'])) : 'No Data'; ?>
                    </div>
                    <div class="kpi-note">Most recent booked order in this filtered view</div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="report-card">
                        <div class="section-head">
                            <div>
                                <h3 class="section-title">Sales Agent Performance Chart</h3>
                                <p class="section-subtitle">Click any bar to open the exact order details for that sales agent.</p>
                            </div>
                            <span class="summary-pill"><strong>View</strong> <?php echo htmlspecialchars($selected_financial_year_label . ' | ' . $selected_month_label, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <?php if (!empty($performance_rows)) { ?>
                            <div id="performanceChartWrap" class="chart-shell">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        <?php } else { ?>
                            <div class="chart-placeholder">No user performance data found for the selected filters.</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="report-card">
                        <div class="section-head">
                            <div>
                                <h3 class="section-title">Leaderboard</h3>
                                <p class="section-subtitle">Top sales agents by total booked order value.</p>
                            </div>
                        </div>

                        <?php if (!empty($performance_rows)) { ?>
                            <ul class="leaderboard-list">
                                <?php foreach (array_slice($performance_rows, 0, 8) as $index => $performance_row) { ?>
                                    <li class="leaderboard-item">
                                        <div>
                                            <span class="leaderboard-rank">#<?php echo $index + 1; ?></span>
                                            <span class="leaderboard-name"><?php echo htmlspecialchars($performance_row->name, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="leaderboard-meta"><?php echo (int) $performance_row->order_count; ?> orders | Avg <?php echo format_report_currency($performance_row->avg_order_value); ?></span>
                                        </div>
                                        <div>
                                            <span class="leaderboard-value"><?php echo format_report_currency($performance_row->total_order_value); ?></span>
                                            <span class="leaderboard-subvalue">
                                                <?php echo !empty($performance_row->latest_order_date) ? date('d M Y', strtotime($performance_row->latest_order_date)) : 'No latest date'; ?>
                                            </span>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                            <div class="summary-pills">
                                <span class="summary-pill"><strong>Top Agent</strong> <?php echo htmlspecialchars($performance_summary['top_agent_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="summary-pill"><strong>Total Agents</strong> <?php echo (int) $performance_summary['active_agents']; ?></span>
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
                        <h3 class="section-title">Detailed Ranking Table</h3>
                        <p class="section-subtitle">Use this table to compare order count, total booked value, average ticket size, and latest activity agent-wise.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="performanceTable" class="table table-striped table-bordered performance-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Sales Agent</th>
                                <th>Total Orders</th>
                                <th>Total Order Value</th>
                                <th>Average Order Value</th>
                                <th>Latest Order Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($performance_rows as $index => $performance_row) { ?>
                                <tr>
                                    <td><span class="rank-pill">#<?php echo $index + 1; ?></span></td>
                                    <td class="agent-name-cell"><?php echo htmlspecialchars($performance_row->name, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo (int) $performance_row->order_count; ?></td>
                                    <td><?php echo format_report_currency($performance_row->total_order_value); ?></td>
                                    <td><?php echo format_report_currency($performance_row->avg_order_value); ?></td>
                                    <td><?php echo !empty($performance_row->latest_order_date) ? date('d-m-Y', strtotime($performance_row->latest_order_date)) : 'NA'; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs action-btn view-order-details" data-user-id="<?php echo (int) $performance_row->user_id; ?>" data-user-name="<?php echo htmlspecialchars($performance_row->name, ENT_QUOTES, 'UTF-8'); ?>">
                                            View Orders
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
                            <h4 class="modal-title" id="orderDetailsModalLabel">Order Details</h4>
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
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        const performanceData = <?php echo json_encode($performance_rows); ?>;
        const reportQuery = <?php echo json_encode(array(
            'financial_year' => $report_filters['financial_year'],
            'month' => $report_filters['month'],
            'user_id' => $report_filters['user_id'],
        )); ?>;
        const orderDetailsBaseUrl = <?php echo json_encode(page_url . 'OrderController/get_order_detailssss/'); ?>;

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

        function buildOrderDetailsUrl(userId) {
            const params = [];
            if (reportQuery.financial_year) {
                params.push('financial_year=' + encodeURIComponent(reportQuery.financial_year));
            }
            if (reportQuery.month) {
                params.push('month=' + encodeURIComponent(reportQuery.month));
            }
            return orderDetailsBaseUrl + userId + (params.length ? '?' + params.join('&') : '');
        }

        function openOrderDetails(userId, userName) {
            $('#orderDetailsModalLabel').text('Order Details - ' + userName);
            $('#orderDetailsTableBody').html('');
            $('#orderDetailsMessage').hide().text('');
            $('#orderDetailsModal').modal('show');

            $.ajax({
                url: buildOrderDetailsUrl(userId),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (!response || response.length === 0) {
                        $('#orderDetailsMessage').text('No order details found for this sales agent in the selected period.').show();
                        return;
                    }

                    let rowsHtml = '';
                    response.forEach(function(order, index) {
                        rowsHtml += '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + (order.company_name || '') + '</td>' +
                            '<td>' + (order.pono || '') + '</td>' +
                            '<td>' + (order.podate ? formatDate(order.podate) : 'NA') + '</td>' +
                            '<td>' + [order.title || '', order.first_name || '', order.last_name || ''].join(' ').trim() + '</td>' +
                            '<td>' + formatIndianCurrency(order.order_value) + '</td>' +
                            '</tr>';
                    });

                    $('#orderDetailsTableBody').html(rowsHtml);
                },
                error: function() {
                    $('#orderDetailsMessage').text('Unable to load order details right now.').show();
                }
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            if (Number.isNaN(date.getTime())) {
                return dateString;
            }
            const day = ('0' + date.getDate()).slice(-2);
            const month = ('0' + (date.getMonth() + 1)).slice(-2);
            return day + '-' + month + '-' + date.getFullYear();
        }

        $(document).ready(function () {
            $('#performanceTable').DataTable({
                pageLength: 25,
                responsive: true,
                order: [],
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Marketing_Team_Performance_<?php echo addslashes($selected_financial_year_label); ?>'
                    }
                ]
            });

            $('.view-order-details').on('click', function () {
                openOrderDetails($(this).data('user-id'), $(this).data('user-name'));
            });

            if (performanceData.length > 0 && document.getElementById('performanceChart')) {
                const chartWrap = document.getElementById('performanceChartWrap');
                chartWrap.style.height = Math.max(360, performanceData.length * 62) + 'px';

                const chartContext = document.getElementById('performanceChart').getContext('2d');
                const barGradient = chartContext.createLinearGradient(0, 0, 500, 0);
                barGradient.addColorStop(0, 'rgba(15, 98, 254, 0.95)');
                barGradient.addColorStop(1, 'rgba(47, 158, 68, 0.85)');

                const performanceChart = new Chart(chartContext, {
                    type: 'bar',
                    data: {
                        labels: performanceData.map(function(item) { return item.name; }),
                        datasets: [{
                            label: 'Total Order Value',
                            data: performanceData.map(function(item) { return Number(item.total_order_value || 0); }),
                            backgroundColor: barGradient,
                            borderColor: 'rgba(15, 98, 254, 1)',
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
                                        const row = performanceData[context.dataIndex] || {};
                                        return [
                                            'Sales: ' + formatIndianCurrency(context.raw),
                                            'Orders: ' + Number(row.order_count || 0),
                                            'Avg Order: ' + formatIndianCurrency(row.avg_order_value || 0)
                                        ];
                                    }
                                }
                            }
                        },
                        onClick: function(evt) {
                            const activeElements = performanceChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                            if (!activeElements.length) {
                                return;
                            }

                            const clickedIndex = activeElements[0].index;
                            const clickedRow = performanceData[clickedIndex];
                            if (clickedRow && clickedRow.user_id) {
                                openOrderDetails(clickedRow.user_id, clickedRow.name);
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
