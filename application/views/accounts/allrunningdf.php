<?php defined('BASEPATH') OR exit('No direct script access allowed');

$report = isset($report) && is_array($report) ? $report : array();
$filters = isset($filters) && is_array($filters) ? $filters : array();
$df_options = isset($df_options) && is_array($df_options) ? $df_options : array();
$rows = isset($report['rows']) && is_array($report['rows']) ? $report['rows'] : array();
$summary = isset($report['summary']) && is_array($report['summary']) ? $report['summary'] : array();
$insights = isset($report['insights']) && is_array($report['insights']) ? $report['insights'] : array();
$top_risk_rows = isset($report['top_risk_rows']) && is_array($report['top_risk_rows']) ? $report['top_risk_rows'] : array();
$gap_rows = isset($report['gap_rows']) && is_array($report['gap_rows']) ? $report['gap_rows'] : array();
$marketing_summary = isset($report['marketing_summary']) && is_array($report['marketing_summary']) ? $report['marketing_summary'] : array();
$filter_summary = isset($report['filter_summary']) && is_array($report['filter_summary']) ? $report['filter_summary'] : array();

$start_date_value = !empty($filters['has_date_filter']) ? (isset($filters['start_date']) ? $filters['start_date'] : '') : '';
$end_date_value = !empty($filters['has_date_filter']) ? (isset($filters['end_date']) ? $filters['end_date'] : '') : '';
$current_df_filter = isset($filters['df_id']) ? (string) $filters['df_id'] : 'ALL';

if (!function_exists('accounts_master_report_escape')) {
    function accounts_master_report_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Finance master dashboard for running DFs">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> | Finance Master Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <style>
        :root{
            --finance-ink:#13233b;
            --finance-muted:#5c7089;
            --finance-line:#d9e3ef;
            --finance-surface:#ffffff;
            --finance-bg:#f2f6fb;
            --finance-brand:#0f4fb7;
            --finance-teal:#0f766e;
            --finance-warning:#b45309;
            --finance-danger:#b42318;
            --finance-good:#15803d;
            --finance-violet:#6d28d9;
            --finance-shadow:0 22px 55px rgba(15, 23, 42, .08);
        }

        body{
            font-family:'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15, 79, 183, .10), transparent 26%),
                radial-gradient(circle at top right, rgba(15, 118, 110, .09), transparent 22%),
                linear-gradient(180deg, #edf3f9 0%, #f7fbff 40%, #eef4fa 100%);
            color:var(--finance-ink);
        }

        .finance-page-shell{
            padding:18px 0 34px;
        }

        .finance-card{
            border-radius:26px;
            background:rgba(255,255,255,.96);
            border:1px solid rgba(217, 227, 239, .95);
            box-shadow:var(--finance-shadow);
        }

        .hero-card{
            position:relative;
            overflow:hidden;
            padding:30px;
            background:
                radial-gradient(circle at right bottom, rgba(255,255,255,.14), transparent 30%),
                linear-gradient(135deg, rgba(19, 35, 59, .98) 0%, rgba(15, 79, 183, .96) 55%, rgba(15, 118, 110, .94) 100%);
            color:#fff;
        }

        .hero-card:before,
        .hero-card:after{
            content:"";
            position:absolute;
            border-radius:999px;
            background:rgba(255,255,255,.09);
        }

        .hero-card:before{
            width:240px;
            height:240px;
            right:-70px;
            top:-90px;
        }

        .hero-card:after{
            width:170px;
            height:170px;
            bottom:-55px;
            left:-45px;
        }

        .hero-card > .row{
            position:relative;
            z-index:1;
        }

        .hero-kicker{
            display:inline-block;
            font-size:11px;
            font-weight:800;
            letter-spacing:.18em;
            text-transform:uppercase;
            color:rgba(255,255,255,.72);
        }

        .hero-title{
            margin:12px 0 8px;
            font-size:35px;
            line-height:1.06;
            font-weight:800;
            letter-spacing:-.03em;
            color:#fff;
        }

        .hero-copy{
            max-width:860px;
            font-size:14px;
            line-height:1.8;
            color:rgba(255,255,255,.84);
        }

        .hero-chip-row{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            margin-top:18px;
        }

        .hero-chip{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:9px 14px;
            border-radius:999px;
            background:rgba(255,255,255,.11);
            border:1px solid rgba(255,255,255,.16);
            color:#fff;
            font-size:12px;
            font-weight:800;
        }

        .hero-actions{
            display:flex;
            justify-content:flex-end;
            gap:12px;
            flex-wrap:wrap;
            margin-top:8px;
        }

        .hero-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            min-height:42px;
            padding:0 16px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,.16);
            background:rgba(255,255,255,.12);
            color:#fff;
            font-size:12px;
            font-weight:800;
            text-decoration:none;
        }

        .hero-btn:hover,
        .hero-btn:focus{
            color:#fff;
            text-decoration:none;
            background:rgba(255,255,255,.18);
        }

        .alert-shell{
            margin-top:18px;
            border-radius:18px;
            border:none;
            box-shadow:0 12px 30px rgba(15, 118, 110, .10);
        }

        .filter-card{
            margin-top:18px;
            padding:24px;
        }

        .section-title{
            margin:0;
            font-size:22px;
            font-weight:800;
            color:var(--finance-ink);
        }

        .section-copy{
            margin-top:6px;
            font-size:13px;
            line-height:1.75;
            color:var(--finance-muted);
        }

        .filter-grid{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            gap:14px;
            margin-top:20px;
            align-items:end;
        }

        @media (max-width:1100px){
            .filter-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width:640px){
            .filter-grid{ grid-template-columns:1fr; }
        }

        .filter-label{
            display:block;
            margin-bottom:8px;
            font-size:11px;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--finance-muted);
        }

        .filter-card .form-control{
            min-height:46px;
            border-radius:14px;
            border:1px solid var(--finance-line);
            box-shadow:none;
        }

        .filter-action-row{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }

        .filter-btn{
            min-height:46px;
            padding:0 18px;
            border-radius:14px;
            border:none;
            font-size:12px;
            font-weight:800;
            letter-spacing:.04em;
        }

        .filter-btn.primary{
            background:linear-gradient(135deg, var(--finance-brand) 0%, #0ea5c6 100%);
            color:#fff;
        }

        .filter-btn.soft{
            background:#eef4fb;
            color:var(--finance-ink);
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
        }

        .kpi-grid{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            gap:14px;
            margin-top:18px;
        }

        @media (max-width:1200px){
            .kpi-grid{ grid-template-columns:repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width:640px){
            .kpi-grid{ grid-template-columns:1fr; }
        }

        .kpi-card{
            padding:18px;
            min-height:134px;
            border-radius:22px;
            border:1px solid #e1eaf5;
            background:linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        }

        .kpi-label{
            font-size:11px;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--finance-muted);
        }

        .kpi-value{
            margin-top:10px;
            font-size:29px;
            line-height:1.02;
            font-weight:800;
            color:var(--finance-ink);
        }

        .kpi-note{
            margin-top:9px;
            font-size:12px;
            line-height:1.7;
            color:var(--finance-muted);
        }

        .signal-grid{
            display:grid;
            grid-template-columns:1.15fr 1fr 1fr;
            gap:18px;
            margin-top:18px;
        }

        @media (max-width:1200px){
            .signal-grid{ grid-template-columns:1fr; }
        }

        .signal-card{
            padding:22px;
        }

        .signal-kicker{
            font-size:11px;
            font-weight:800;
            letter-spacing:.16em;
            text-transform:uppercase;
            color:#1d4ed8;
        }

        .signal-title{
            margin-top:12px;
            font-size:24px;
            line-height:1.35;
            font-weight:800;
            color:var(--finance-ink);
        }

        .signal-copy{
            margin-top:12px;
            font-size:13px;
            line-height:1.8;
            color:var(--finance-muted);
        }

        .signal-list{
            margin:16px 0 0;
            padding:0;
            list-style:none;
        }

        .signal-list li{
            position:relative;
            padding-left:18px;
            margin-top:10px;
            font-size:13px;
            line-height:1.7;
            color:var(--finance-ink);
        }

        .signal-list li:before{
            content:"";
            position:absolute;
            left:0;
            top:9px;
            width:8px;
            height:8px;
            border-radius:999px;
            background:linear-gradient(135deg, var(--finance-brand) 0%, #0ea5c6 100%);
        }

        .mini-panel-title{
            font-size:17px;
            font-weight:800;
            color:var(--finance-ink);
        }

        .mini-panel-copy{
            margin-top:5px;
            font-size:12px;
            line-height:1.7;
            color:var(--finance-muted);
        }

        .mini-list{
            display:flex;
            flex-direction:column;
            gap:12px;
            margin-top:16px;
        }

        .mini-item{
            border-radius:18px;
            padding:14px;
            border:1px solid #e5edf7;
            background:#fff;
        }

        .mini-item-title{
            font-size:14px;
            line-height:1.45;
            font-weight:800;
            color:var(--finance-ink);
        }

        .mini-item-copy{
            margin-top:5px;
            font-size:12px;
            line-height:1.7;
            color:var(--finance-muted);
        }

        .mini-meta{
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin-top:10px;
        }

        .mini-chip{
            display:inline-flex;
            align-items:center;
            padding:6px 10px;
            border-radius:999px;
            background:#edf4fb;
            color:#244464;
            font-size:11px;
            font-weight:800;
        }

        .mini-chip.red{ background:#fde7e5; color:var(--finance-danger); }
        .mini-chip.amber{ background:#ffedd5; color:var(--finance-warning); }
        .mini-chip.green{ background:#dcfce7; color:var(--finance-good); }
        .mini-chip.violet{ background:#ede9fe; color:var(--finance-violet); }

        .report-card{
            margin-top:18px;
            padding:22px;
        }

        .report-toolbar{
            display:flex;
            flex-wrap:wrap;
            justify-content:space-between;
            align-items:flex-end;
            gap:14px;
            margin-bottom:18px;
        }

        .report-toolbar-right{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
            align-items:end;
        }

        .toolbar-control label{
            display:block;
            margin-bottom:7px;
            font-size:11px;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--finance-muted);
        }

        .toolbar-select{
            min-width:190px;
            min-height:42px;
            border-radius:14px;
            border:1px solid var(--finance-line);
            padding:0 10px;
            font-size:12px;
            font-weight:700;
            color:var(--finance-ink);
            background:#fff;
        }

        .toolbar-text{
            font-size:12px;
            line-height:1.7;
            color:var(--finance-muted);
        }

        .table-wrap{
            overflow-x:auto;
            border-radius:22px;
            border:1px solid #e3ebf5;
        }

        table.finance-table{
            width:100% !important;
            min-width:1650px;
            margin:0 !important;
            border-collapse:separate;
            border-spacing:0;
        }

        table.finance-table thead th{
            background:#f6f9fd;
            border-bottom:1px solid #e3ebf5 !important;
            color:var(--finance-muted);
            padding:16px 14px !important;
            font-size:11px;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            vertical-align:middle;
        }

        table.finance-table tbody td{
            padding:16px 14px !important;
            border-top:1px solid #edf3f9 !important;
            background:#fff;
            vertical-align:top;
        }

        table.finance-table tbody tr:hover td{
            background:#fbfdff;
        }

        .row-gap td{ background:#fff7f6 !important; }
        .row-critical td{ background:#fff8f1 !important; }
        .row-watch td{ background:#fbfbfe !important; }

        .index-pill{
            width:36px;
            height:36px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:12px;
            background:#edf4fb;
            color:#1f4060;
            font-size:12px;
            font-weight:800;
        }

        .cell-title{
            font-size:14px;
            line-height:1.45;
            font-weight:800;
            color:var(--finance-ink);
        }

        .cell-copy{
            margin-top:5px;
            font-size:12px;
            line-height:1.7;
            color:var(--finance-muted);
        }

        .inline-grid{
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:10px;
            margin-top:12px;
        }

        .inline-stat{
            border-radius:16px;
            padding:11px 12px;
            background:#f7fbff;
            border:1px solid #e3ebf5;
        }

        .inline-label{
            font-size:10px;
            font-weight:800;
            letter-spacing:.10em;
            text-transform:uppercase;
            color:var(--finance-muted);
        }

        .inline-value{
            margin-top:5px;
            font-size:13px;
            line-height:1.5;
            font-weight:800;
            color:var(--finance-ink);
        }

        .status-pill{
            display:inline-flex;
            align-items:center;
            gap:7px;
            padding:6px 11px;
            border-radius:999px;
            font-size:11px;
            font-weight:800;
            letter-spacing:.04em;
            text-transform:uppercase;
            border:1px solid;
        }

        .status-gap{ background:#fde7e5; color:var(--finance-danger); border-color:#f8c7c2; }
        .status-critical{ background:#ffedd5; color:var(--finance-warning); border-color:#fed7aa; }
        .status-watch{ background:#e0f2fe; color:#0369a1; border-color:#bae6fd; }
        .status-active{ background:#dbeafe; color:#1d4ed8; border-color:#bfdbfe; }
        .status-collected{ background:#dcfce7; color:var(--finance-good); border-color:#bbf7d0; }
        .status-completed{ background:#dcfce7; color:var(--finance-good); border-color:#bbf7d0; }
        .status-approval{ background:#ede9fe; color:var(--finance-violet); border-color:#ddd6fe; }
        .status-pending{ background:#eef4fb; color:#2f4f6c; border-color:#d9e3ef; }
        .status-delayed{ background:#fde7e5; color:var(--finance-danger); border-color:#f8c7c2; }
        .status-overdue{ background:#fde7e5; color:var(--finance-danger); border-color:#f8c7c2; }
        .status-collection_pending{ background:#ffedd5; color:var(--finance-warning); border-color:#fed7aa; }
        .status-partial{ background:#e0f2fe; color:#0369a1; border-color:#bae6fd; }
        .status-partial_overdue{ background:#fff1c2; color:#9a6700; border-color:#fde68a; }
        .status-received{ background:#dcfce7; color:var(--finance-good); border-color:#bbf7d0; }
        .status-planned{ background:#eef4fb; color:#2f4f6c; border-color:#d9e3ef; }
        .status-unmapped{ background:#f8e8ea; color:#9f1239; border-color:#fecdd3; }
        .status-due{ background:#fde7e5; color:var(--finance-danger); border-color:#f8c7c2; }
        .status-not_due{ background:#e0f2fe; color:#0369a1; border-color:#bae6fd; }
        .status-hold{ background:#ede9fe; color:var(--finance-violet); border-color:#ddd6fe; }
        .status-not_updated{ background:#eef4fb; color:#2f4f6c; border-color:#d9e3ef; }

        .issue-list{
            margin:12px 0 0;
            padding-left:17px;
            color:var(--finance-muted);
        }

        .issue-list li{
            margin-bottom:7px;
            font-size:12px;
            line-height:1.7;
        }

        .milestone-stack{
            display:flex;
            flex-direction:column;
            gap:12px;
        }

        .milestone-card{
            border-radius:18px;
            border:1px solid #e5edf7;
            background:#fdfefe;
            padding:14px;
        }

        .milestone-head{
            display:flex;
            justify-content:space-between;
            gap:12px;
            align-items:flex-start;
            flex-wrap:wrap;
        }

        .milestone-title{
            font-size:13px;
            line-height:1.55;
            font-weight:800;
            color:var(--finance-ink);
        }

        .milestone-sub{
            margin-top:4px;
            font-size:12px;
            color:var(--finance-muted);
            line-height:1.7;
        }

        .milestone-grid{
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:10px;
            margin-top:12px;
        }

        .milestone-stat{
            border-radius:14px;
            padding:10px 11px;
            background:#f7fbff;
            border:1px solid #e3ebf5;
        }

        .milestone-meta{
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin-top:12px;
        }

        .followup-box{
            margin-top:12px;
            padding:11px 12px;
            border-radius:14px;
            border:1px solid #e7eef8;
            background:#ffffff;
            font-size:12px;
            line-height:1.75;
            color:#304860;
        }

        .dispatch-ledger-box{
            margin-top:12px;
            border-radius:18px;
            border:1px solid #e3ebf5;
            background:#f7fbff;
            padding:14px;
        }

        .dispatch-ledger-head{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:12px;
            flex-wrap:wrap;
        }

        .dispatch-ledger-title{
            font-size:11px;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--finance-muted);
        }

        .dispatch-ledger-grid{
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:10px;
            margin-top:12px;
        }

        .dispatch-ledger-note{
            margin-top:12px;
            font-size:12px;
            line-height:1.75;
            color:var(--finance-muted);
        }

        .dispatch-ledger-note strong{
            color:var(--finance-ink);
        }

        .dispatch-ledger-empty{
            margin-top:12px;
            font-size:12px;
            line-height:1.75;
            color:var(--finance-muted);
        }

        .action-stack{
            display:flex;
            flex-direction:column;
            gap:9px;
        }

        .action-link{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:38px;
            padding:0 12px;
            border-radius:12px;
            font-size:12px;
            font-weight:800;
            text-decoration:none;
            transition:all .15s ease;
        }

        .action-link.primary{
            color:#fff;
            background:linear-gradient(135deg, var(--finance-brand) 0%, #0ea5c6 100%);
        }

        .action-link.soft{
            background:#edf4fb;
            color:#173a60;
        }

        .action-link:hover,
        .action-link:focus{
            text-decoration:none;
            transform:translateY(-1px);
        }

        .update-link{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:34px;
            padding:0 12px;
            border:none;
            border-radius:10px;
            background:#0f4fb7;
            color:#fff;
            font-size:11px;
            font-weight:800;
        }

        .update-link:hover,
        .update-link:focus{
            color:#fff;
            background:#0c4398;
            text-decoration:none;
        }

        .empty-state{
            padding:52px 24px;
            text-align:center;
            font-size:14px;
            line-height:1.9;
            color:var(--finance-muted);
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select{
            border:1px solid var(--finance-line);
            border-radius:12px;
            min-height:38px;
            padding:0 10px;
            color:var(--finance-ink);
            background:#fff;
        }

        .dt-buttons .btn{
            border-radius:12px !important;
            border:1px solid var(--finance-line) !important;
            background:#fff !important;
            color:var(--finance-ink) !important;
            padding:9px 14px !important;
            font-size:12px !important;
            font-weight:800 !important;
        }

        .modal-content{
            border-radius:22px;
            box-shadow:0 25px 60px rgba(15, 23, 42, .16);
            border:none;
        }

        .modal-header{
            border-bottom:1px solid #e7eef8;
            padding:18px 22px;
        }

        .modal-body{
            padding:22px;
        }

        .modal-title{
            font-size:20px;
            font-weight:800;
            color:var(--finance-ink);
        }

        .modal-note{
            margin-top:10px;
            padding:12px 14px;
            border-radius:14px;
            background:#f7fbff;
            border:1px solid #e3ebf5;
            font-size:12px;
            line-height:1.75;
            color:var(--finance-muted);
        }

        #pageloader1{
            display:none;
            text-align:center;
            padding:16px 0 0;
        }

        #pageloader1 img{
            width:70px;
        }

        @media (max-width:991px){
            .hero-actions{ justify-content:flex-start; }
        }

        @media (max-width:767px){
            .hero-card,
            .filter-card,
            .report-card,
            .signal-card{ padding:22px 18px; }
            .hero-title{ font-size:29px; }
            .signal-title{ font-size:19px; }
            .report-toolbar{ align-items:stretch; }
            .report-toolbar-right{ width:100%; }
            .toolbar-control,
            .toolbar-select{ width:100%; }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <?php $this->load->view('common/info-section.php'); ?>

    <div class="wrapper">
        <div class="container-fluid finance-page-shell">
            <div class="hero-card finance-card">
                <div class="row">
                    <div class="col-lg-8">
                        <span class="hero-kicker">Finance Control Room</span>
                        <div class="hero-title">Running DF Finance Master Dashboard</div>
                        <div class="hero-copy">
                            This view tracks each running DF against PO value, configured payment milestones, received amount, overdue exposure, upcoming collections, milestone mapping gaps, and the DF-wise dispatch ledger so the finance team and management can act on one clean source of truth.
                        </div>
                        <div class="hero-chip-row">
                            <span class="hero-chip"><i class="fa fa-calendar"></i> <?php echo accounts_master_report_escape(isset($filter_summary['date_range_label']) ? $filter_summary['date_range_label'] : 'All Running DF Timeline'); ?></span>
                            <span class="hero-chip"><i class="fa fa-sitemap"></i> <?php echo accounts_master_report_escape(isset($filter_summary['df_label']) ? $filter_summary['df_label'] : 'All Running DFs'); ?></span>
                            <span class="hero-chip"><i class="fa fa-clock-o"></i> Generated <?php echo accounts_master_report_escape(isset($filter_summary['generated_on']) ? $filter_summary['generated_on'] : ''); ?></span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="hero-actions">
                            <a href="<?php echo page_url; ?>Accounts/paymentdashboard/<?php echo date('Y-m-d'); ?>/<?php echo date('Y-m-d', strtotime('+7 days')); ?>/ALL" class="hero-btn"><i class="fa fa-line-chart"></i> Upcoming Payments</a>
                            <a href="<?php echo page_url; ?>Accounts/overduepaymentdashboard" class="hero-btn"><i class="fa fa-exclamation-triangle"></i> Overdue Payments</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-info alert-shell">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>

            <div class="filter-card finance-card">
                <div class="row">
                    <div class="col-lg-7">
                        <h3 class="section-title">Filter & Scope</h3>
                        <div class="section-copy"><?php echo accounts_master_report_escape(isset($filter_summary['date_scope_note']) ? $filter_summary['date_scope_note'] : ''); ?></div>
                    </div>
                    <div class="col-lg-5 text-left lg-text-right">
                        <div class="section-copy">Use this filter to review recent running DF releases or drill into one DF without losing payment control visibility.</div>
                    </div>
                </div>

                <form method="post" action="<?php echo page_url; ?>Accounts/filterbydate" class="filter-grid">
                    <div>
                        <label class="filter-label" for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo accounts_master_report_escape($start_date_value); ?>">
                    </div>
                    <div>
                        <label class="filter-label" for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo accounts_master_report_escape($end_date_value); ?>">
                    </div>
                    <div>
                        <label class="filter-label" for="df_no">DF Selection</label>
                        <select id="df_no" name="df_no" class="form-control">
                            <option value="ALL">ALL</option>
                            <?php foreach ($df_options as $df_option): ?>
                                <option value="<?php echo (int) $df_option['id']; ?>" <?php echo $current_df_filter === (string) $df_option['id'] ? 'selected' : ''; ?>>
                                    <?php echo accounts_master_report_escape(trim($df_option['df_no'] . ' ' . $df_option['df_description'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="filter-label">Actions</label>
                        <div class="filter-action-row">
                            <button type="submit" class="filter-btn primary">Apply Filter</button>
                            <a href="<?php echo page_url; ?>Accounts/allrunningdf/ALL/ALL/<?php echo accounts_master_report_escape($current_df_filter); ?>" class="filter-btn soft">All Dates</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="kpi-grid">
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Visible PO Value</div>
                    <div class="kpi-value"><?php echo accounts_master_report_escape(isset($summary['total_order_value_display']) ? $summary['total_order_value_display'] : '₹0.00'); ?></div>
                    <div class="kpi-note"><?php echo (int) (isset($summary['po_count']) ? $summary['po_count'] : 0); ?> PO row(s) across <?php echo (int) (isset($summary['df_count']) ? $summary['df_count'] : 0); ?> running DF(s).</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Received Amount</div>
                    <div class="kpi-value"><?php echo accounts_master_report_escape(isset($summary['total_received_amount_display']) ? $summary['total_received_amount_display'] : '₹0.00'); ?></div>
                    <div class="kpi-note"><?php echo (int) (isset($summary['collection_percentage']) ? $summary['collection_percentage'] : 0); ?>% of visible order value is already captured.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Pending Collection</div>
                    <div class="kpi-value"><?php echo accounts_master_report_escape(isset($summary['total_pending_amount_display']) ? $summary['total_pending_amount_display'] : '₹0.00'); ?></div>
                    <div class="kpi-note">This is the remaining collection exposure across the current filter.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Overdue Exposure</div>
                    <div class="kpi-value" style="color:var(--finance-danger);"><?php echo accounts_master_report_escape(isset($summary['total_overdue_amount_display']) ? $summary['total_overdue_amount_display'] : '₹0.00'); ?></div>
                    <div class="kpi-note"><?php echo (int) (isset($summary['overdue_row_count']) ? $summary['overdue_row_count'] : 0); ?> row(s) already have overdue payment pressure.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Upcoming 7 Days</div>
                    <div class="kpi-value"><?php echo accounts_master_report_escape(isset($summary['total_upcoming_amount_display']) ? $summary['total_upcoming_amount_display'] : '₹0.00'); ?></div>
                    <div class="kpi-note">Expected milestone exposure due within the next seven days.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Configuration Gaps</div>
                    <div class="kpi-value"><?php echo (int) (isset($summary['gap_row_count']) ? $summary['gap_row_count'] : 0); ?></div>
                    <div class="kpi-note">Rows with missing payment-term setup, milestone mapping, or follow-up visibility issues.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Critical Rows</div>
                    <div class="kpi-value"><?php echo (int) (isset($summary['critical_row_count']) ? $summary['critical_row_count'] : 0); ?></div>
                    <div class="kpi-note">Rows currently tagged as overdue or configuration-gap driven risk.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Milestone Pressure</div>
                    <div class="kpi-value"><?php echo (int) (isset($summary['milestone_count']) ? $summary['milestone_count'] : 0); ?></div>
                    <div class="kpi-note"><?php echo (int) (isset($summary['overdue_milestone_count']) ? $summary['overdue_milestone_count'] : 0); ?> milestone(s) carry overdue payment exposure.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Dispatch Ledger Coverage</div>
                    <div class="kpi-value"><?php echo (int) (isset($summary['dispatch_tracker_row_count']) ? $summary['dispatch_tracker_row_count'] : 0); ?></div>
                    <div class="kpi-note"><?php echo (int) (isset($summary['dispatch_tracker_missing_count']) ? $summary['dispatch_tracker_missing_count'] : 0); ?> visible DF(s) do not yet have DF-wise dispatch ledger data.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Dispatch Ledger Received</div>
                    <div class="kpi-value"><?php echo accounts_master_report_escape(isset($summary['dispatch_tracker_received_total_display']) ? $summary['dispatch_tracker_received_total_display'] : '₹0.00'); ?></div>
                    <div class="kpi-note">DF-wise collection captured directly on the dispatch billing ledger.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Dispatch Ledger Balance</div>
                    <div class="kpi-value"><?php echo accounts_master_report_escape(isset($summary['dispatch_tracker_balance_total_display']) ? $summary['dispatch_tracker_balance_total_display'] : '₹0.00'); ?></div>
                    <div class="kpi-note"><?php echo (int) (isset($summary['dispatch_tracker_due_count']) ? $summary['dispatch_tracker_due_count'] : 0); ?> DF(s) are marked Due and <?php echo (int) (isset($summary['dispatch_tracker_hold_count']) ? $summary['dispatch_tracker_hold_count'] : 0); ?> DF(s) are on Hold there.</div>
                </div>
                <div class="kpi-card finance-card">
                    <div class="kpi-label">Dispatch Sync Gaps</div>
                    <div class="kpi-value"><?php echo (int) (isset($summary['dispatch_sync_gap_count']) ? $summary['dispatch_sync_gap_count'] : 0); ?></div>
                    <div class="kpi-note">DF(s) where DF-wise dispatch ledger amounts differ from finance milestone receipts.</div>
                </div>
            </div>

            <div class="signal-grid">
                <div class="signal-card finance-card">
                    <div class="signal-kicker">Management Readout</div>
                    <div class="signal-title">
                        <?php if (!empty($summary['total_overdue_amount']) && $summary['total_overdue_amount'] > 0): ?>
                            Visible overdue finance exposure needs direct follow-up and configuration review.
                        <?php elseif (!empty($summary['gap_row_count']) && $summary['gap_row_count'] > 0): ?>
                            The biggest current finance risk is configuration quality, not collection delay.
                        <?php else: ?>
                            The current finance view is active, mapped, and ready for routine management review.
                        <?php endif; ?>
                    </div>
                    <div class="signal-copy">
                        This report separates commercial exposure, milestone readiness, collection tracking, and DF-wise dispatch billing so finance can explain not only what is due, but also why it is blocked and whether both registers are aligned.
                    </div>
                    <ul class="signal-list">
                        <?php foreach ($insights as $insight): ?>
                            <li><?php echo accounts_master_report_escape($insight); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="signal-card finance-card">
                    <div class="mini-panel-title">Top Finance Risk</div>
                    <div class="mini-panel-copy">Rows with the highest immediate finance pressure based on health priority, overdue amount, and pending amount.</div>
                    <div class="mini-list">
                        <?php if (!empty($top_risk_rows)): ?>
                            <?php foreach ($top_risk_rows as $risk_row): ?>
                                <div class="mini-item">
                                    <div class="mini-item-title"><?php echo accounts_master_report_escape($risk_row['df_no']); ?> | <?php echo accounts_master_report_escape($risk_row['po_no']); ?></div>
                                    <div class="mini-item-copy"><?php echo accounts_master_report_escape($risk_row['company_name']); ?></div>
                                    <div class="mini-meta">
                                        <span class="mini-chip red"><?php echo accounts_master_report_escape($risk_row['overdue_amount_display']); ?> overdue</span>
                                        <span class="mini-chip amber"><?php echo accounts_master_report_escape($risk_row['pending_amount_display']); ?> pending</span>
                                        <span class="mini-chip"><?php echo accounts_master_report_escape($risk_row['health_label']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state" style="padding:28px 12px;">No finance risk visible in the current filter.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="signal-card finance-card">
                    <div class="mini-panel-title">Marketing Pressure</div>
                    <div class="mini-panel-copy">Pending and overdue collection exposure grouped by the marketing owner mapped on each visible PO.</div>
                    <div class="mini-list">
                        <?php if (!empty($marketing_summary)): ?>
                            <?php foreach ($marketing_summary as $marketing_row): ?>
                                <div class="mini-item">
                                    <div class="mini-item-title"><?php echo accounts_master_report_escape($marketing_row['label']); ?></div>
                                    <div class="mini-meta">
                                        <span class="mini-chip"><?php echo (int) $marketing_row['df_count']; ?> row(s)</span>
                                        <span class="mini-chip amber"><?php echo accounts_master_report_escape($marketing_row['pending_amount_display']); ?> pending</span>
                                        <span class="mini-chip red"><?php echo accounts_master_report_escape($marketing_row['overdue_amount_display']); ?> overdue</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state" style="padding:28px 12px;">No marketing ownership signal available.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="report-card finance-card">
                <div class="report-toolbar">
                    <div>
                        <h3 class="section-title">Finance Register by Running DF</h3>
                        <div class="section-copy">This register shows DF-wise commercial value, collection progress, milestone mapping, overdue pressure, the latest finance follow-up trail, and the DF-wise dispatch billing ledger in one table.</div>
                    </div>
                    <div class="report-toolbar-right">
                        <div class="toolbar-control">
                            <label for="healthFilter">Health Filter</label>
                            <select id="healthFilter" class="toolbar-select">
                                <option value="">All health states</option>
                                <option value="gap">Configuration Gap</option>
                                <option value="critical">Overdue</option>
                                <option value="watch">Watch</option>
                                <option value="active">Active</option>
                                <option value="collected">Collected</option>
                            </select>
                        </div>
                        <div class="toolbar-text" id="reportVisibleCounter"></div>
                        <div id="financeReportButtons"></div>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="financeRunningDfTable" class="table finance-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">#</th>
                                <th style="width:230px;">Company & DF</th>
                                <th style="width:220px;">PO & Dates</th>
                                <th style="width:340px;">Commercial & Dispatch Snapshot</th>
                                <th style="width:290px;">Payment Health</th>
                                <th style="width:620px;">Milestone Intelligence</th>
                                <th style="width:180px;">Marketing</th>
                                <th style="width:150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $index => $row): ?>
                                    <?php
                                    $row_class = 'row-watch';
                                    if ($row['health_key'] === 'gap') {
                                        $row_class = 'row-gap';
                                    } elseif ($row['health_key'] === 'critical') {
                                        $row_class = 'row-critical';
                                    } elseif ($row['health_key'] === 'active' || $row['health_key'] === 'collected') {
                                        $row_class = '';
                                    }
                                    ?>
                                    <tr class="<?php echo $row_class; ?>" data-health="<?php echo accounts_master_report_escape($row['health_key']); ?>">
                                        <td data-order="<?php echo (int) $row['health_priority']; ?>">
                                            <span class="index-pill"><?php echo $index + 1; ?></span>
                                        </td>
                                        <td>
                                            <div class="cell-title"><?php echo accounts_master_report_escape($row['company_name']); ?></div>
                                            <div class="cell-copy">
                                                <?php echo accounts_master_report_escape($row['df_no']); ?><br>
                                                <?php echo accounts_master_report_escape($row['df_description'] !== '' ? $row['df_description'] : 'DF description not available'); ?>
                                            </div>
                                            <div class="mini-meta">
                                                <span class="mini-chip"><?php echo accounts_master_report_escape($row['customer_currency'] !== '' ? $row['customer_currency'] : 'Currency N/A'); ?></span>
                                                <span class="mini-chip">Release <?php echo accounts_master_report_escape($row['df_release_date']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="cell-title"><?php echo accounts_master_report_escape($row['po_no'] !== '' ? $row['po_no'] : 'PO not linked'); ?></div>
                                            <div class="cell-copy">PO Date: <?php echo accounts_master_report_escape($row['po_date']); ?></div>
                                            <div class="cell-copy">Payment Term: <?php echo accounts_master_report_escape($row['payment_term_name'] !== '' ? $row['payment_term_name'] : 'Not Mapped'); ?></div>
                                            <?php if ($row['po_download_url'] !== ''): ?>
                                                <div class="mini-meta">
                                                    <a href="<?php echo accounts_master_report_escape($row['po_download_url']); ?>" target="_blank" class="action-link soft" style="min-height:34px;">Download PO</a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-order="<?php echo (float) $row['pending_amount']; ?>">
                                            <div class="inline-grid">
                                                <div class="inline-stat">
                                                    <div class="inline-label">Order Value</div>
                                                    <div class="inline-value"><?php echo accounts_master_report_escape($row['order_value_display']); ?></div>
                                                </div>
                                                <div class="inline-stat">
                                                    <div class="inline-label">Received</div>
                                                    <div class="inline-value"><?php echo accounts_master_report_escape($row['received_amount_display']); ?></div>
                                                </div>
                                                <div class="inline-stat">
                                                    <div class="inline-label">Pending</div>
                                                    <div class="inline-value"><?php echo accounts_master_report_escape($row['pending_amount_display']); ?></div>
                                                </div>
                                                <div class="inline-stat">
                                                    <div class="inline-label">Collection %</div>
                                                    <div class="inline-value"><?php echo (int) $row['collection_percentage']; ?>%</div>
                                                </div>
                                                <div class="inline-stat">
                                                    <div class="inline-label">Overdue</div>
                                                    <div class="inline-value"><?php echo accounts_master_report_escape($row['overdue_amount_display']); ?></div>
                                                </div>
                                                <div class="inline-stat">
                                                    <div class="inline-label">Upcoming 7 Days</div>
                                                    <div class="inline-value"><?php echo accounts_master_report_escape($row['upcoming_amount_display']); ?></div>
                                                </div>
                                            </div>
                                            <div class="dispatch-ledger-box">
                                                <div class="dispatch-ledger-head">
                                                    <div class="dispatch-ledger-title">DF Dispatch Ledger</div>
                                                    <span class="status-pill status-<?php echo accounts_master_report_escape($row['dispatch_due_state_key']); ?>"><?php echo accounts_master_report_escape($row['dispatch_due_state_label']); ?></span>
                                                </div>
                                                <?php if ($row['dispatch_tracker_available']): ?>
                                                    <div class="dispatch-ledger-grid">
                                                        <div class="milestone-stat">
                                                            <div class="inline-label">Invoice</div>
                                                            <div class="inline-value"><?php echo accounts_master_report_escape($row['dispatch_invoice_no'] !== '' ? $row['dispatch_invoice_no'] : 'Not Updated'); ?></div>
                                                        </div>
                                                        <div class="milestone-stat">
                                                            <div class="inline-label">Invoice Date</div>
                                                            <div class="inline-value"><?php echo accounts_master_report_escape($row['dispatch_invoice_date']); ?></div>
                                                        </div>
                                                        <div class="milestone-stat">
                                                            <div class="inline-label">Invoice Amount</div>
                                                            <div class="inline-value"><?php echo accounts_master_report_escape($row['dispatch_invoice_amount_display']); ?></div>
                                                        </div>
                                                        <div class="milestone-stat">
                                                            <div class="inline-label">Taxable Sale</div>
                                                            <div class="inline-value"><?php echo accounts_master_report_escape($row['dispatch_taxable_sale_display']); ?></div>
                                                        </div>
                                                        <div class="milestone-stat">
                                                            <div class="inline-label">Ledger Received</div>
                                                            <div class="inline-value"><?php echo accounts_master_report_escape($row['dispatch_payment_received_display']); ?></div>
                                                        </div>
                                                        <div class="milestone-stat">
                                                            <div class="inline-label">Ledger Balance</div>
                                                            <div class="inline-value"><?php echo accounts_master_report_escape($row['dispatch_balance_amount_display']); ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="dispatch-ledger-note">
                                                        <strong>Due Date:</strong> <?php echo accounts_master_report_escape($row['dispatch_due_date']); ?><br>
                                                        <strong>Machines:</strong> <?php echo accounts_master_report_escape($row['dispatch_nos_of_machines']); ?><br>
                                                        <?php if ($row['dispatch_remarks'] !== ''): ?>
                                                            <strong>Remarks:</strong> <?php echo nl2br(accounts_master_report_escape($row['dispatch_remarks'])); ?><br>
                                                        <?php endif; ?>
                                                        <?php if ($row['dispatch_commissioning_status'] !== ''): ?>
                                                            <strong>Commissioning:</strong> <?php echo accounts_master_report_escape($row['dispatch_commissioning_status']); ?><br>
                                                        <?php endif; ?>
                                                        <strong>Last Updated:</strong> <?php echo accounts_master_report_escape($row['dispatch_last_updated_by']); ?> on <?php echo accounts_master_report_escape($row['dispatch_last_updated_on']); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="dispatch-ledger-empty">
                                                        DF-wise dispatch billing ledger is not updated yet for this row.
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td data-order="<?php echo (float) $row['overdue_amount']; ?>">
                                            <span class="status-pill status-<?php echo accounts_master_report_escape($row['health_key']); ?>"><?php echo accounts_master_report_escape($row['health_label']); ?></span>
                                            <div class="cell-copy" style="margin-top:12px;">
                                                <?php if ($row['latest_followup_text'] !== ''): ?>
                                                    Latest follow-up: <?php echo accounts_master_report_escape($row['latest_followup_text']); ?><br>
                                                    Logged on <?php echo accounts_master_report_escape($row['latest_followup_on_display']); ?>
                                                <?php else: ?>
                                                    No latest finance follow-up note is visible on this row.
                                                <?php endif; ?>
                                            </div>
                                            <div class="dispatch-ledger-note">
                                                <strong>Dispatch Ledger:</strong> <?php echo accounts_master_report_escape($row['dispatch_sync_gap_note'] !== '' ? $row['dispatch_sync_gap_note'] : $row['dispatch_tracker_note']); ?>
                                            </div>
                                            <?php if (!empty($row['issues'])): ?>
                                                <ul class="issue-list">
                                                    <?php foreach ($row['issues'] as $issue): ?>
                                                        <li><?php echo accounts_master_report_escape($issue); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="milestone-stack">
                                                <?php if (!empty($row['milestones'])): ?>
                                                    <?php foreach ($row['milestones'] as $milestone): ?>
                                                        <?php $milestone_context_label = json_encode($row['df_no'] . ' | ' . ($milestone['task_name'] !== '' ? $milestone['task_name'] : 'Milestone')); ?>
                                                        <div class="milestone-card">
                                                            <div class="milestone-head">
                                                                <div>
                                                                    <div class="milestone-title"><?php echo accounts_master_report_escape($milestone['task_name'] !== '' ? $milestone['task_name'] : 'Milestone task not linked'); ?></div>
                                                                    <div class="milestone-sub">Target Date: <?php echo accounts_master_report_escape($milestone['target_date']); ?></div>
                                                                </div>
                                                                <div class="mini-meta" style="margin-top:0;">
                                                                    <span class="mini-chip"><?php echo rtrim(rtrim(number_format((float) $milestone['payment_percentage'], 2, '.', ''), '0'), '.'); ?>%</span>
                                                                    <span class="status-pill status-<?php echo accounts_master_report_escape($milestone['work_status_key']); ?>"><?php echo accounts_master_report_escape($milestone['work_status_label']); ?></span>
                                                                    <span class="status-pill status-<?php echo accounts_master_report_escape($milestone['payment_status_key']); ?>"><?php echo accounts_master_report_escape($milestone['payment_status_label']); ?></span>
                                                                </div>
                                                            </div>

                                                            <div class="milestone-grid">
                                                                <div class="milestone-stat">
                                                                    <div class="inline-label">Milestone Value</div>
                                                                    <div class="inline-value"><?php echo accounts_master_report_escape($milestone['scheduled_amount_display']); ?></div>
                                                                </div>
                                                                <div class="milestone-stat">
                                                                    <div class="inline-label">Received</div>
                                                                    <div class="inline-value"><?php echo accounts_master_report_escape($milestone['amount_received_display']); ?></div>
                                                                </div>
                                                                <div class="milestone-stat">
                                                                    <div class="inline-label">Outstanding</div>
                                                                    <div class="inline-value"><?php echo accounts_master_report_escape($milestone['outstanding_amount_display']); ?></div>
                                                                </div>
                                                                <div class="milestone-stat">
                                                                    <div class="inline-label">Updated By / On</div>
                                                                    <div class="inline-value">
                                                                        <?php echo accounts_master_report_escape($milestone['receiver_name']); ?><br>
                                                                        <span style="font-size:11px; font-weight:700; color:var(--finance-muted);"><?php echo accounts_master_report_escape($milestone['payment_received_on']); ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="followup-box">
                                                                <strong>Follow-up:</strong>
                                                                <?php if ($milestone['followup_text'] !== ''): ?>
                                                                    <?php echo nl2br(accounts_master_report_escape($milestone['followup_text'])); ?><br>
                                                                    <span style="color:var(--finance-muted);">Logged <?php echo accounts_master_report_escape($milestone['followup_logged_on']); ?> | Next follow-up <?php echo accounts_master_report_escape($milestone['next_followup']); ?></span>
                                                                <?php else: ?>
                                                                    No finance follow-up has been logged for this milestone yet.
                                                                <?php endif; ?>
                                                            </div>

                                                            <div class="milestone-meta">
                                                                <?php if ($milestone['can_update_receipt']): ?>
                                                                    <button
                                                                        type="button"
                                                                        class="update-link"
                                                                        onclick="openPaymentReceiptModal(<?php echo (int) $milestone['record_id']; ?>, '<?php echo number_format((float) $milestone['prefill_receipt_amount'], 2, '.', ''); ?>', '<?php echo number_format((float) $milestone['target_receipt_amount'], 2, '.', ''); ?>', '<?php echo number_format((float) $milestone['current_received_amount'], 2, '.', ''); ?>', <?php echo $milestone_context_label; ?>)"
                                                                    >
                                                                        Update Receipt
                                                                    </button>
                                                                <?php endif; ?>
                                                                <?php if ((float) $milestone['outstanding_amount'] > 0): ?>
                                                                    <span class="mini-chip amber"><?php echo accounts_master_report_escape($milestone['outstanding_amount_display']); ?> to collect</span>
                                                                <?php else: ?>
                                                                    <span class="mini-chip green">Fully captured</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="empty-state" style="padding:30px 12px;">No milestone configuration is available for this row.</div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="cell-title"><?php echo accounts_master_report_escape($row['marketing_person']); ?></div>
                                            <div class="cell-copy">Collection owner mapped from the PO creator.</div>
                                        </td>
                                        <td>
                                            <div class="action-stack">
                                                <a href="<?php echo accounts_master_report_escape($row['df_detail_url']); ?>" target="_blank" class="action-link primary">DF Intelligence</a>
                                                <a href="<?php echo accounts_master_report_escape($row['gantt_url']); ?>" target="_blank" class="action-link soft">Open Gantt</a>
                                                <?php if ($row['po_download_url'] !== ''): ?>
                                                    <a href="<?php echo accounts_master_report_escape($row['po_download_url']); ?>" target="_blank" class="action-link soft">PO File</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            No running DF finance rows matched the current filter.<br>
                                            Try widening the DF release date range or switching DF selection back to `ALL`.
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

    <div id="updateprogress" class="modal fade" role="dialog">
        <form id="updateprogressform" method="post" action="<?php echo page_url; ?>Accounts/updatepaymentdetail/<?php echo isset($filters['start_date']) ? accounts_master_report_escape($filters['start_date']) : 'ALL'; ?>/<?php echo isset($filters['end_date']) ? accounts_master_report_escape($filters['end_date']) : 'ALL'; ?>/<?php echo accounts_master_report_escape($current_df_filter); ?>" enctype="multipart/form-data">
            <div id="pageloader1">
                <img src="<?php echo assets_url; ?>images/loading.gif" alt="processing...">
            </div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="modal-title">Update Payment Receipt</div>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="taskkiid" name="taskkiid" value="">
                        <div class="form-group">
                            <label class="filter-label" for="receiptTargetInfo">Milestone Context</label>
                            <input type="text" id="receiptTargetInfo" class="form-control" readonly value="">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="filter-label" for="amountreceived">Received Amount</label>
                                    <input type="number" class="form-control" name="amountreceived" id="amountreceived" step="any" required value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="filter-label" for="paymentreceivedate">Payment Date</label>
                                    <input type="date" class="form-control" name="paymentreceivedate" id="paymentreceivedate" required value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="modal-note" id="receiptHelperNote">
                            Enter the cumulative amount received against this milestone. The report will compare it against the target milestone value to decide whether the milestone is fully collected or still partial.
                        </div>
                        <div class="text-center" style="margin-top:18px;">
                            <button type="submit" class="filter-btn primary" style="min-width:190px;">Save Receipt Update</button>
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
    <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        function openPaymentReceiptModal(recordId, prefillAmount, targetAmount, currentAmount, contextLabel) {
            $('#taskkiid').val(recordId);
            $('#amountreceived').val(prefillAmount);
            $('#receiptTargetInfo').val(contextLabel);
            $('#receiptHelperNote').text(
                'Target milestone value: INR ' + targetAmount +
                ' | Current captured value: INR ' + currentAmount +
                '. Enter the latest cumulative amount received for this milestone.'
            );
            $('#updateprogress').modal('show');
        }

        $(document).ready(function () {
            var hasRows = <?php echo !empty($rows) ? 'true' : 'false'; ?>;
            var healthFilterValue = '';
            var financeTable = null;

            $('#updateprogressform').on('submit', function () {
                $('#pageloader1').fadeIn();
            });

            if (!hasRows) {
                $('#reportVisibleCounter').text('Visible rows: 0');
                $('#healthFilter').prop('disabled', true);
                return;
            }

            financeTable = $('#financeRunningDfTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                scrollX: true,
                responsive: false,
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Running DF Finance Master Dashboard',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'Running DF Finance Master Dashboard',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Running DF Finance Master Dashboard',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    }
                ],
                columnDefs: [
                    { orderable: false, targets: [5, 7] }
                ],
                language: {
                    search: 'Search register',
                    lengthMenu: 'Show _MENU_ rows',
                    info: 'Showing _START_ to _END_ of _TOTAL_ finance row(s)',
                    infoEmpty: 'No finance rows found',
                    zeroRecords: 'No finance rows match the current search'
                }
            });

            financeTable.buttons().container().appendTo('#financeReportButtons');

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'financeRunningDfTable') {
                    return true;
                }

                if (!healthFilterValue) {
                    return true;
                }

                var rowNode = financeTable.row(dataIndex).node();
                return String($(rowNode).attr('data-health') || '') === healthFilterValue;
            });

            function refreshVisibleCounter() {
                var info = financeTable.page.info();
                $('#reportVisibleCounter').text('Visible rows: ' + info.recordsDisplay + ' of ' + info.recordsTotal);
            }

            $('#healthFilter').on('change', function () {
                healthFilterValue = String($(this).val() || '');
                financeTable.draw();
            });

            financeTable.on('draw', function () {
                refreshVisibleCounter();
            });

            refreshVisibleCounter();
        });
    </script>
</body>
</html>
