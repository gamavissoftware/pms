<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> | DF Full Intelligence Report</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root{
            --brand:#2563eb;
            --brand-2:#0f172a;
            --brand-soft:#dbeafe;
            --surface:#ffffff;
            --surface-2:#f8fafc;
            --surface-3:#eff6ff;
            --border:#dbe3ef;
            --text:#0f172a;
            --muted:#64748b;
            --success:#16a34a;
            --warning:#d97706;
            --danger:#dc2626;
            --info:#0284c7;
            --shadow:0 20px 50px rgba(15, 23, 42, .08);
        }

        body{
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .12), transparent 28%),
                radial-gradient(circle at top right, rgba(14, 165, 233, .10), transparent 24%),
                linear-gradient(180deg, #f7faff 0%, #eef4fb 100%);
            color: var(--text);
        }

        .page-shell{ max-width: 1440px; margin: 0 auto; }
        .card-shell{
            border-radius: 28px;
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(219, 227, 239, .8);
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }

        .select2-container{ width:100% !important; }
        .select2-container .select2-selection--single{
            height: 52px;
            border-radius: 16px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 14px;
            background: #fff;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            color: var(--text);
            line-height: 50px;
            padding-left: 2px;
            font-size: 13px;
            font-weight: 700;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            height: 50px;
            right: 12px;
        }
        .select2-dropdown{
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .hero-head{
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 28px;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, .95) 0%, rgba(37, 99, 235, .92) 55%, rgba(14, 165, 233, .85) 100%);
            color: #fff;
        }
        .hero-head:before{
            content: "";
            position: absolute;
            inset: auto -80px -80px auto;
            width: 260px;
            height: 260px;
            border-radius: 999px;
            background: rgba(255,255,255,.10);
            filter: blur(10px);
        }
        .hero-head:after{
            content: "";
            position: absolute;
            inset: -90px auto auto -50px;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: rgba(148, 197, 255, .22);
            filter: blur(15px);
        }
        .hero-title{
            position: relative;
            font-size: 32px;
            line-height: 1.05;
            font-weight: 900;
            letter-spacing: -.03em;
        }
        .hero-subtitle{
            position: relative;
            color: rgba(255,255,255,.82);
            font-size: 14px;
            margin-top: 10px;
            max-width: 760px;
        }
        .hero-chip-row{
            position: relative;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }
        .hero-chip{
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 7px 13px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.20);
        }
        .hero-chip.muted{
            color: rgba(255,255,255,.78);
        }

        .search-panel{
            margin-top: 18px;
            border-radius: 22px;
            padding: 18px;
            border: 1px solid var(--border);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }
        .search-grid{
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 14px;
            align-items: end;
        }
        @media (max-width: 767px){
            .search-grid{ grid-template-columns: 1fr; }
        }

        .primary-btn{
            border: 0;
            border-radius: 16px;
            height: 52px;
            padding: 0 20px;
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .05em;
            text-transform: uppercase;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            box-shadow: 0 14px 34px rgba(37, 99, 235, .28);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
        }
        .primary-btn:hover{ transform: translateY(-1px); filter: brightness(1.03); }
        .primary-btn[disabled]{ opacity: .72; cursor: not-allowed; transform: none; box-shadow: none; }
        .ghost-btn{
            border-radius: 14px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            font-weight: 800;
            font-size: 12px;
            padding: 10px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .ghost-btn.disabled{
            opacity: .48;
            pointer-events: none;
        }

        .inline-alert{
            display: none;
            margin-bottom: 12px;
            border-radius: 14px;
            padding: 11px 13px;
            font-size: 12px;
            font-weight: 800;
            color: #991b1b;
            border: 1px solid #fecaca;
            background: #fff1f2;
        }

        .kpi-strip{
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }
        @media (max-width: 1100px){
            .kpi-strip{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 767px){
            .kpi-strip{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 520px){
            .kpi-strip{ grid-template-columns: 1fr; }
        }
        .kpi-card{
            border-radius: 18px;
            border: 1px solid #e6edf7;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: 15px 16px;
            min-height: 118px;
        }
        .kpi-label{
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .kpi-value{
            margin-top: 8px;
            font-size: 26px;
            line-height: 1;
            font-weight: 900;
            color: var(--text);
        }
        .kpi-sub{
            margin-top: 7px;
            font-size: 12px;
            color: var(--muted);
        }

        #dfNavigationSection{
            width: 290px;
            transition: width .25s ease, padding .25s ease;
        }
        #dfNavigationSection.collapsed{
            width: 90px;
            padding-left: 14px !important;
            padding-right: 14px !important;
        }
        #dfNavigationSection.collapsed .nav-text,
        #dfNavigationSection.collapsed .nav-hint,
        #dfNavigationSection.collapsed h3{ display: none; }
        #mainContentArea{
            width: calc(100% - 290px);
            transition: width .25s ease;
        }
        #mainContentArea.expanded{ width: calc(100% - 90px); }
        @media (max-width: 1023px){
            #dfNavigationSection, #mainContentArea{ width: 100%; }
            #toggleSidebar{ display: none; }
        }

        .sidebar-panel{
            position: sticky;
            top: 14px;
            border-radius: 22px;
            padding: 18px;
            border: 1px solid var(--border);
            background: linear-gradient(180deg, #f9fbfe 0%, #ffffff 100%);
        }
        .nav-hint{
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
        }
        .tab-button{
            width: 100%;
            text-align: left;
            border: 0;
            border-left: 4px solid transparent;
            border-radius: 16px;
            padding: 13px 14px;
            color: #334155;
            background: transparent;
            display: flex;
            align-items: center;
            font-weight: 800;
            gap: 12px;
            margin-bottom: 10px;
            transition: all .15s ease;
        }
        .tab-button:hover{ background: rgba(37,99,235,.08); }
        .tab-button.active{
            background: rgba(37,99,235,.12);
            border-left-color: var(--brand);
            color: var(--brand);
        }
        .tab-button svg{ flex-shrink: 0; }

        .content-panel{
            border-radius: 24px;
            border: 1px solid var(--border);
            background: #fff;
            overflow: hidden;
        }
        .content-section{ display: none; opacity: 0; transform: translateY(8px); transition: all .2s ease; }
        .content-section.active{ display: block; opacity: 1; transform: translateY(0); }

        .section-head{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid #eef3f9;
        }
        .section-title{
            font-size: 24px;
            line-height: 1.05;
            font-weight: 900;
            color: var(--text);
            letter-spacing: -.03em;
        }
        .section-subtitle{
            margin-top: 6px;
            font-size: 12px;
            color: var(--muted);
        }

        .summary-hero{
            border-radius: 24px;
            padding: 24px;
            background:
                radial-gradient(circle at top right, rgba(14,165,233,.18), transparent 30%),
                linear-gradient(135deg, #0f172a 0%, #1d4ed8 54%, #0284c7 100%);
            color: #fff;
        }
        .summary-hero-title{
            font-size: 30px;
            line-height: 1.04;
            font-weight: 900;
            letter-spacing: -.03em;
        }
        .summary-hero-copy{
            margin-top: 10px;
            color: rgba(255,255,255,.84);
            font-size: 13px;
        }
        .summary-grid{
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
            margin-top: 20px;
        }
        @media (max-width: 1100px){
            .summary-grid{ grid-template-columns: 1fr; }
        }

        .mini-meta-grid{
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }
        @media (max-width: 900px){
            .mini-meta-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 520px){
            .mini-meta-grid{ grid-template-columns: 1fr; }
        }
        .mini-meta-card{
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(255,255,255,.10);
            padding: 14px;
        }
        .mini-meta-label{
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: rgba(255,255,255,.74);
        }
        .mini-meta-value{
            margin-top: 7px;
            font-size: 18px;
            line-height: 1.15;
            font-weight: 900;
            color: #fff;
        }

        .insight-panel{
            border-radius: 22px;
            border: 1px solid #e7eef9;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: 18px;
        }
        .insight-title{
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .insight-value{
            margin-top: 8px;
            font-size: 19px;
            line-height: 1.2;
            font-weight: 900;
            color: var(--text);
        }
        .insight-copy{
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.55;
            color: var(--muted);
        }

        .status-pill{
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 5px 11px;
            font-size: 11px;
            font-weight: 900;
            border: 1px solid;
            white-space: nowrap;
        }
        .pill-active{ background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
        .pill-stable{ background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .pill-review{ background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .pill-delay{ background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .pill-hold{ background: #e2e8f0; color: #334155; border-color: #cbd5e1; }
        .pill-info{ background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .pill-closed{ background: #ede9fe; color: #6d28d9; border-color: #ddd6fe; }

        .overview-grid{
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 18px;
        }
        @media (max-width: 1100px){
            .overview-grid{ grid-template-columns: 1fr; }
        }
        .chart-card{
            border-radius: 22px;
            border: 1px solid var(--border);
            background: #fff;
            padding: 18px;
            min-height: 360px;
        }
        .chart-wrap{
            position: relative;
            height: 270px;
            margin-top: 16px;
        }

        .two-col-grid{
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 18px;
        }
        @media (max-width: 1100px){
            .two-col-grid{ grid-template-columns: 1fr; }
        }

        .list-card{
            border-radius: 22px;
            border: 1px solid var(--border);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: 18px;
        }
        .list-stack{
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .list-item{
            border-radius: 18px;
            border: 1px solid #e8eef8;
            padding: 14px;
            background: #fff;
        }
        .list-item-title{
            font-size: 15px;
            line-height: 1.25;
            font-weight: 900;
            color: var(--text);
        }
        .list-item-sub{
            margin-top: 4px;
            font-size: 12px;
            color: var(--muted);
        }
        .list-item-meta{
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .mini-pill{
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 900;
            background: #f1f5f9;
            color: #334155;
        }
        .mini-pill.blue{ background: #dbeafe; color: #1d4ed8; }
        .mini-pill.red{ background: #fee2e2; color: #b91c1c; }
        .mini-pill.green{ background: #dcfce7; color: #166534; }
        .mini-pill.amber{ background: #fef3c7; color: #92400e; }
        .mini-pill.slate{ background: #e2e8f0; color: #334155; }

        .department-grid{
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }
        @media (max-width: 1200px){
            .department-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px){
            .department-grid{ grid-template-columns: 1fr; }
        }
        .department-card{
            border-radius: 20px;
            border: 1px solid #e8eef8;
            padding: 16px;
            background: #fff;
        }
        .department-name{
            font-size: 16px;
            line-height: 1.2;
            font-weight: 900;
            color: var(--text);
        }
        .department-bar{
            margin-top: 12px;
            height: 10px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }
        .department-bar span{
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
        }

        .timeline-card{
            border-radius: 22px;
            border: 1px solid var(--border);
            background: #fff;
            padding: 18px;
            margin-top: 18px;
        }
        .timeline-list{
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .timeline-item{
            position: relative;
            padding: 14px 14px 14px 18px;
            border-radius: 18px;
            border: 1px solid #e8eef8;
            background: #f8fbff;
        }
        .timeline-item:before{
            content: "";
            position: absolute;
            left: 0;
            top: 14px;
            bottom: 14px;
            width: 4px;
            border-radius: 999px;
            background: linear-gradient(180deg, #2563eb 0%, #0ea5e9 100%);
        }
        .timeline-top{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .timeline-label{
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--brand);
        }
        .timeline-time{
            font-size: 12px;
            color: var(--muted);
            font-weight: 700;
        }
        .timeline-message{
            margin-top: 7px;
            font-size: 14px;
            line-height: 1.55;
            color: var(--text);
            font-weight: 700;
        }
        .timeline-meta{
            margin-top: 7px;
            font-size: 12px;
            color: var(--muted);
        }

        .timeline-wrap{
            background: #e2e8f0;
            border: 1px solid #dbe3ef;
            border-radius: 999px;
            height: 12px;
            overflow: hidden;
            position: relative;
        }
        .timeline-green, .timeline-red{
            float: left;
            height: 100%;
        }
        .timeline-green{ background: #22c55e; width: 0%; }
        .timeline-red{ background: #ef4444; width: 0%; }
        .timeline-meta-row{
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 7px;
            font-size: 11px;
            color: var(--muted);
            font-weight: 800;
        }

        .table-shell{
            margin-top: 18px;
            border-radius: 22px;
            border: 1px solid #e7eef8;
            overflow: hidden;
        }
        .table-scroll{ overflow-x: auto; }
        .report-table{
            width: 100%;
            min-width: 1080px;
        }
        .report-table thead th{
            background: #f8fbff;
            border-bottom: 1px solid #e6edf7;
            padding: 16px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #64748b;
            vertical-align: middle;
        }
        .report-table tbody td{
            padding: 15px 16px;
            border-top: 1px solid #eff4fb;
            vertical-align: top;
        }
        .report-table tbody tr:hover{ background: #f8fbff; }
        .row-delay{ background: #fff1f2 !important; }
        .row-delay td{ border-top-color: #ffe4e6; }
        .row-hold{ background: #f8fafc !important; }
        .row-hold td{ border-top-color: #e2e8f0; }
        .row-approval{ background: #fffbeb !important; }
        .row-approval td{ border-top-color: #fde68a; }

        .variance-filter{
            width: 190px;
            max-width: 100%;
            height: 38px;
            border-radius: 12px;
            border: 1px solid #dbe3ef;
            background: #fff;
            color: var(--text);
            font-size: 12px;
            font-weight: 800;
            padding: 0 10px;
        }

        .doc-grid{
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 18px;
            margin-top: 18px;
        }
        @media (max-width: 1000px){
            .doc-grid{ grid-template-columns: 1fr; }
        }
        .doc-card{
            border-radius: 22px;
            border: 1px solid var(--border);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: 20px;
        }
        .doc-meta-grid{
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 18px;
        }
        @media (max-width: 640px){
            .doc-meta-grid{ grid-template-columns: 1fr; }
        }
        .doc-label{
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .doc-value{
            margin-top: 6px;
            font-size: 15px;
            line-height: 1.45;
            color: var(--text);
            font-weight: 800;
        }

        .ticket-summary{
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }
        @media (max-width: 767px){
            .ticket-summary{ grid-template-columns: 1fr; }
        }

        /* The overtime tab shows a fourth card when the viewer may see cost. */
        .ticket-summary.ot-kpis{ grid-template-columns: repeat(4, minmax(0, 1fr)); }
        @media (max-width: 1199px){
            .ticket-summary.ot-kpis{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 767px){
            .ticket-summary.ot-kpis{ grid-template-columns: 1fr; }
        }

        .ticket-card{
            border-radius: 22px;
            border: 1px solid #e8eef8;
            background: #fff;
            padding: 18px;
        }

        .empty-state{
            border-radius: 20px;
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            color: var(--muted);
            padding: 28px 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 800;
        }

        .skeleton{
            border-radius: 12px;
            background: linear-gradient(90deg, #edf2f7 25%, #f8fbff 37%, #edf2f7 63%);
            background-size: 400% 100%;
            animation: shimmer 1.1s ease infinite;
        }
        @keyframes shimmer{
            0%{ background-position: 100% 0; }
            100%{ background-position: 0 0; }
        }
    </style>
</head>
<body>
<header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
</header>

<div class="wrapper min-h-screen py-6 px-4 sm:px-6 lg:px-8">
    <div class="page-shell">
        <div class="card-shell p-4 sm:p-6 lg:p-7">

            <div class="hero-head">
                <div class="hero-title">DF Full Intelligence Report</div>
                <div class="hero-subtitle">
                    Management-grade single-screen visibility for execution status, blockers, ownership load, document traceability, and support movement for any DF.
                </div>
                <div class="hero-chip-row">
                    <span class="hero-chip">Execution Intelligence</span>
                    <span class="hero-chip muted">Plan vs Actual</span>
                    <span class="hero-chip muted">Ownership Pressure</span>
                    <span class="hero-chip muted">Ticket Traceability</span>
                </div>
            </div>

            <div class="search-panel">
                <div class="inline-alert" id="inlineAlert"></div>

                <div class="search-grid">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-widest mb-2">Select DF</label>
                        <select id="dfSearchInput" aria-label="Select DF">
                            <option value="">Select DF Number...</option>
                            <?php
                            $q = $this->db->select('id, df_no, df_description')
                                ->from('df_release')
                                ->where('df_no !=', 0)
                                ->order_by('id', 'DESC')
                                ->get();
                            foreach ($q->result() as $row) {
                                $label = trim($row->df_no . ' | ' . $row->df_description);
                                echo '<option value="' . (int) $row->id . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
                            }
                            ?>
                        </select>
                        <div class="mt-2 text-[12px] text-slate-500 font-semibold">
                            Search by DF number or description to open the full execution story.
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button id="searchButton" class="primary-btn w-full sm:w-auto" type="button">
                            <span id="searchBtnText">Generate Intelligence</span>
                            <span id="searchBtnSpinner" class="hidden" aria-hidden="true">
                                <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <div class="kpi-strip">
                    <div class="kpi-card">
                        <div class="kpi-label">Completion</div>
                        <div class="kpi-value text-blue-600" id="dfCompletion">—</div>
                        <div class="kpi-sub">Active task completion</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Health</div>
                        <div class="kpi-value" id="dfStatus">—</div>
                        <div class="kpi-sub">Live project health</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Ongoing Tasks</div>
                        <div class="kpi-value" id="dfOpenTasks">—</div>
                        <div class="kpi-sub">Open execution items</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Delayed Tasks</div>
                        <div class="kpi-value text-rose-600" id="dfDelayedTasks">—</div>
                        <div class="kpi-sub">Open or late-completed tasks</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Open Tickets</div>
                        <div class="kpi-value text-sky-600" id="dfOpenTickets">—</div>
                        <div class="kpi-sub">Support issues still active</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Active Owners</div>
                        <div class="kpi-value" id="dfActiveOwners">—</div>
                        <div class="kpi-sub">People currently holding work</div>
                    </div>
                </div>
            </div>

            <div id="mainSearchResultsArea" class="hidden mt-6">
                <div class="flex flex-col lg:flex-row gap-6">
                    <aside id="dfNavigationSection" class="sidebar-panel flex-shrink-0 relative">
                        <button id="toggleSidebar" class="absolute -right-3 top-4 h-8 w-8 rounded-full border border-slate-200 bg-white shadow-sm flex items-center justify-center z-10" type="button" aria-label="Toggle navigation">
                            <svg id="toggleIcon" class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <div class="px-2">
                            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Management View</h3>
                            <div class="nav-hint">Switch between summary, execution, documents, and ticket visibility.</div>
                        </div>

                        <nav class="mt-4">
                            <button class="tab-button active" data-tab="managementOverview" type="button">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M11 3a1 1 0 011 1v7h7a1 1 0 110 2h-7v7a1 1 0 11-2 0v-7H3a1 1 0 110-2h7V4a1 1 0 011-1z"></path>
                                </svg>
                                <span class="nav-text">Management Summary</span>
                            </button>

                            <button class="tab-button" data-tab="planVsActual" type="button">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="nav-text">Plan vs Actual</span>
                            </button>

                            <button class="tab-button" data-tab="dfDownload" type="button">
                                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="nav-text">DF Document</span>
                            </button>

                            <button class="tab-button" data-tab="poDownload" type="button">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="nav-text">PO Status</span>
                            </button>

                            <button class="tab-button" data-tab="helpTickets" type="button">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <span class="nav-text">Help Tickets</span>
                            </button>

                            <button class="tab-button" data-tab="dfOvertime" type="button">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="nav-text">Approved Overtime</span>
                            </button>
                        </nav>
                    </aside>

                    <section id="mainContentArea" class="content-panel p-4 sm:p-6 lg:p-7">
                        <div id="managementOverview" class="content-section active">
                            <div id="managementSummaryContent"></div>
                        </div>

                        <div id="planVsActual" class="content-section">
                            <div class="section-head">
                                <div>
                                    <div class="section-title">Execution Roadmap</div>
                                    <div class="section-subtitle">Task-wise execution, planned dates, latest movement, support exposure, and variance from schedule.</div>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
                                    <div class="text-right">
                                        <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Completion</div>
                                        <div id="dfCompletionTop" class="text-xl font-black text-blue-600">—</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Health</div>
                                        <div id="dfStatusTop" class="text-xl font-black">—</div>
                                    </div>
                                </div>
                            </div>

                            <div class="insight-panel mt-5">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <div>
                                        <div class="insight-title">Overall Execution Timeline</div>
                                        <div class="insight-value" style="font-size:18px;" id="overallTimelineLabel">—</div>
                                        <div class="insight-copy" id="overallTimelineMeta">—</div>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] font-extrabold text-slate-600">
                                        <span class="inline-flex items-center gap-2">
                                            <span style="width:10px;height:10px;border-radius:999px;background:#22c55e;display:inline-block;"></span>
                                            Planned window
                                        </span>
                                        <span class="inline-flex items-center gap-2">
                                            <span style="width:10px;height:10px;border-radius:999px;background:#ef4444;display:inline-block;"></span>
                                            Extended delay
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="timeline-wrap" id="overallTimelineBar">
                                        <div class="timeline-green" id="overallTimelineGreen"></div>
                                        <div class="timeline-red" id="overallTimelineRed"></div>
                                    </div>
                                    <div class="timeline-meta-row">
                                        <span id="overallTimelineStart">—</span>
                                        <span id="overallTimelineEnd">—</span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-shell">
                                <div class="table-scroll">
                                    <table class="report-table">
                                        <thead>
                                        <tr>
                                            <th>Task & Department</th>
                                            <th>Owner</th>
                                            <th>Status</th>
                                            <th class="text-center">Planned End</th>
                                            <th>Latest Update</th>
                                            <th class="text-center">Tickets</th>
                                            <th class="text-center">Actual Finish</th>
                                            <th class="text-right">
                                                <div>Execution Filter</div>
                                                <select id="varianceFilter" class="variance-filter">
                                                    <option value="all">All</option>
                                                    <option value="ongoing">Ongoing</option>
                                                    <option value="delay">Delayed</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="approval">Waiting Approval</option>
                                                    <option value="hold">On Hold</option>
                                                </select>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody id="planVsActualTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="dfDownload" class="content-section">
                            <div class="section-head">
                                <div>
                                    <div class="section-title">DF Document Intelligence</div>
                                    <div class="section-subtitle">Release metadata, traceability, document access, and quick links for deeper review.</div>
                                </div>
                            </div>
                            <div id="dfDownloadContent"></div>
                        </div>

                        <div id="poDownload" class="content-section">
                            <div class="section-head">
                                <div>
                                    <div class="section-title">PO & Commercial Snapshot</div>
                                    <div class="section-subtitle">Customer, PO reference, commercial value, machine context, and supporting document visibility.</div>
                                </div>
                            </div>
                            <div id="poDownloadContent"></div>
                        </div>

                        <div id="helpTickets" class="content-section">
                            <div class="section-head">
                                <div>
                                    <div class="section-title">Support Ticket Movement</div>
                                    <div class="section-subtitle">Open issues, closure trail, ownership, and remarks linked with this DF.</div>
                                </div>
                            </div>
                            <div id="helpTicketsContent"></div>
                        </div>

                        <div id="dfOvertime" class="content-section">
                            <div class="section-head">
                                <div>
                                    <div class="section-title">Approved Overtime</div>
                                    <div class="section-subtitle">Overtime approved against this DF, one row per person, with the day-wise rollup. Pending and rejected requests are not shown.</div>
                                </div>
                            </div>
                            <div id="dfOvertimeContent"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js"></script>
<script>
    $(document).ready(function () {
        var pageBaseUrl = <?php echo json_encode(page_url); ?>;
        var taskMixChart = null;
        var deptProgressChart = null;

        $('#dfSearchInput').select2({
            placeholder: 'Select DF Number...',
            allowClear: true
        });

        $('#toggleSidebar').on('click', function () {
            $('#dfNavigationSection').toggleClass('collapsed');
            $('#mainContentArea').toggleClass('expanded');
            var isCollapsed = $('#dfNavigationSection').hasClass('collapsed');
            $('#toggleIcon').css('transform', isCollapsed ? 'rotate(180deg)' : 'rotate(0deg)');
        });

        $('.tab-button').on('click', function () {
            $('.tab-button').removeClass('active');
            $(this).addClass('active');
            $('.content-section').removeClass('active');
            $('#' + $(this).data('tab')).addClass('active');
        });

        function escapeHtml(value) {
            if (value === null || value === undefined) {
                return '';
            }
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function textOrDefault(value, fallback) {
            var clean = $.trim(String(value || ''));
            return clean !== '' ? escapeHtml(clean) : escapeHtml(fallback || 'N/A');
        }

        function formatDate(value) {
            if (!value || String(value).indexOf('0000-00-00') !== -1) {
                return 'N/A';
            }
            var parts = String(value).split(' ')[0].split('-');
            if (parts.length !== 3) {
                return escapeHtml(String(value));
            }
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }

        function formatDateTime(value) {
            if (!value || String(value).indexOf('0000-00-00') !== -1) {
                return 'N/A';
            }
            var parts = String(value).split(' ');
            var datePart = formatDate(parts[0]);
            var timePart = parts[1] ? parts[1].slice(0, 5) : '';
            return timePart ? (datePart + ' ' + timePart) : datePart;
        }

        function formatCurrency(value, currency) {
            var number = parseFloat(value || 0);
            if (isNaN(number)) {
                number = 0;
            }
            return escapeHtml((currency || 'INR') + ' ' + number.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }

        function truncateText(value, maxLength) {
            var plain = $.trim(String(value || ''));
            if (plain.length <= maxLength) {
                return escapeHtml(plain);
            }
            return escapeHtml(plain.slice(0, maxLength - 3) + '...');
        }

        function getHealthClass(key) {
            switch (String(key || '').toLowerCase()) {
                case 'critical':
                    return 'text-rose-600';
                case 'attention':
                    return 'text-rose-600';
                case 'review':
                    return 'text-amber-600';
                case 'closed':
                    return 'text-violet-600';
                case 'hold':
                    return 'text-slate-600';
                default:
                    return 'text-emerald-600';
            }
        }

        function buildStatusBadge(statusKey, label) {
            var key = String(statusKey || '').toLowerCase();
            var cls = 'pill-info';
            if (key === 'active' || key === 'ongoing') {
                cls = 'pill-active';
            } else if (key === 'stable' || key === 'completed') {
                cls = 'pill-stable';
            } else if (key === 'review' || key === 'approval') {
                cls = 'pill-review';
            } else if (key === 'approval-delay' || key === 'delay' || key === 'critical' || key === 'attention') {
                cls = 'pill-delay';
            } else if (key === 'hold') {
                cls = 'pill-hold';
            } else if (key === 'closed') {
                cls = 'pill-closed';
            }
            return '<span class="status-pill ' + cls + '">' + escapeHtml(label || 'N/A') + '</span>';
        }

        function buildMiniPill(text, cls) {
            return '<span class="mini-pill ' + escapeHtml(cls || 'slate') + '">' + escapeHtml(text || '') + '</span>';
        }

        function showInlineAlert(message) {
            $('#inlineAlert').text(message).fadeIn(120);
        }

        function hideInlineAlert() {
            $('#inlineAlert').hide().text('');
        }

        function setLoading(isLoading) {
            if (isLoading) {
                $('#searchButton').attr('disabled', true);
                $('#searchBtnText').text('Generating...');
                $('#searchBtnSpinner').removeClass('hidden');
            } else {
                $('#searchButton').attr('disabled', false);
                $('#searchBtnText').text('Generate Intelligence');
                $('#searchBtnSpinner').addClass('hidden');
            }
        }

        function destroyCharts() {
            if (taskMixChart) {
                taskMixChart.destroy();
                taskMixChart = null;
            }
            if (deptProgressChart) {
                deptProgressChart.destroy();
                deptProgressChart = null;
            }
        }

        function renderSkeleton() {
            destroyCharts();

            $('#managementSummaryContent').html(
                '<div class="summary-hero">' +
                    '<div class="skeleton h-8 w-72 mb-3"></div>' +
                    '<div class="skeleton h-4 w-full max-w-2xl mb-2"></div>' +
                    '<div class="skeleton h-4 w-3/4"></div>' +
                    '<div class="mini-meta-grid">' +
                        '<div class="skeleton h-24 w-full"></div>' +
                        '<div class="skeleton h-24 w-full"></div>' +
                        '<div class="skeleton h-24 w-full"></div>' +
                        '<div class="skeleton h-24 w-full"></div>' +
                    '</div>' +
                '</div>' +
                '<div class="overview-grid">' +
                    '<div class="skeleton h-80 w-full"></div>' +
                    '<div class="skeleton h-80 w-full"></div>' +
                '</div>' +
                '<div class="two-col-grid">' +
                    '<div class="skeleton h-72 w-full"></div>' +
                    '<div class="skeleton h-72 w-full"></div>' +
                '</div>'
            );

            var planSkeleton = '';
            for (var i = 0; i < 6; i++) {
                planSkeleton += '<tr>' +
                    '<td><div class="skeleton h-4 w-48 mb-2"></div><div class="skeleton h-3 w-24"></div></td>' +
                    '<td><div class="skeleton h-4 w-32"></div></td>' +
                    '<td><div class="skeleton h-6 w-24"></div></td>' +
                    '<td><div class="skeleton h-4 w-20 mx-auto"></div></td>' +
                    '<td><div class="skeleton h-4 w-40 mb-2"></div><div class="skeleton h-3 w-20"></div></td>' +
                    '<td><div class="skeleton h-6 w-16 mx-auto"></div></td>' +
                    '<td><div class="skeleton h-4 w-20 mx-auto"></div></td>' +
                    '<td><div class="skeleton h-6 w-24 ml-auto"></div></td>' +
                '</tr>';
            }
            $('#planVsActualTableBody').html(planSkeleton);

            $('#overallTimelineLabel').text('Loading timeline...');
            $('#overallTimelineMeta').text('—');
            $('#overallTimelineStart').text('—');
            $('#overallTimelineEnd').text('—');
            $('#overallTimelineGreen').css('width', '0%');
            $('#overallTimelineRed').css('width', '0%');

            $('#dfDownloadContent').html('<div class="skeleton h-72 w-full mt-5"></div>');
            $('#poDownloadContent').html('<div class="skeleton h-72 w-full mt-5"></div>');
            $('#helpTicketsContent').html('<div class="skeleton h-72 w-full mt-5"></div>');
            $('#dfOvertimeContent').html('<div class="skeleton h-72 w-full mt-5"></div>');
        }

        function applyVarianceFilter() {
            var filterValue = String($('#varianceFilter').val() || 'all').toLowerCase();
            $('#planVsActualTableBody tr[data-variance]').each(function () {
                var rowType = String($(this).attr('data-variance') || '').toLowerCase();
                if (filterValue === 'all') {
                    $(this).show();
                } else {
                    $(this).toggle(rowType === filterValue);
                }
            });
        }

        function renderOverallTimeline(summary, planRows, dfInfo) {
            if (!summary || !summary.planned_start || !summary.planned_end) {
                $('#overallTimelineLabel').text('Timeline not available');
                $('#overallTimelineMeta').text('—');
                $('#overallTimelineStart').text('—');
                $('#overallTimelineEnd').text('—');
                $('#overallTimelineGreen').css('width', '0%');
                $('#overallTimelineRed').css('width', '0%');
                return;
            }

            function parseYmd(value) {
                if (!value || String(value).indexOf('0000-00-00') !== -1) {
                    return null;
                }
                var raw = String(value).split(' ')[0].split('-');
                if (raw.length !== 3) {
                    return null;
                }
                return new Date(parseInt(raw[0], 10), parseInt(raw[1], 10) - 1, parseInt(raw[2], 10));
            }

            function dayDiff(start, end) {
                var a = new Date(start.getFullYear(), start.getMonth(), start.getDate());
                var b = new Date(end.getFullYear(), end.getMonth(), end.getDate());
                return Math.floor((b - a) / (24 * 60 * 60 * 1000));
            }

            var start = parseYmd(summary.planned_start);
            var end = parseYmd(summary.planned_end);
            if (!start || !end) {
                $('#overallTimelineLabel').text('Timeline not available');
                $('#overallTimelineMeta').text('—');
                $('#overallTimelineStart').text('—');
                $('#overallTimelineEnd').text('—');
                $('#overallTimelineGreen').css('width', '0%');
                $('#overallTimelineRed').css('width', '0%');
                return;
            }

            var referenceDate = new Date();
            if (dfInfo && parseInt(dfInfo.df_status || 0, 10) === 1 && summary.actual_end) {
                var actualEnd = parseYmd(summary.actual_end);
                if (actualEnd) {
                    referenceDate = actualEnd;
                }
            }

            var plannedDays = Math.max(1, dayDiff(start, end) + 1);
            var exceededDays = referenceDate > end ? dayDiff(end, referenceDate) : 0;
            var redPct = Math.max(0, Math.min((exceededDays / plannedDays) * 100, 65));

            $('#overallTimelineLabel').text('From ' + formatDate(summary.planned_start) + ' to ' + formatDate(summary.planned_end));
            $('#overallTimelineMeta').text(plannedDays + ' planned day(s) • ' + (exceededDays > 0 ? ('Extended by ' + exceededDays + ' day(s)') : 'Within planned window'));
            $('#overallTimelineStart').text(formatDate(summary.planned_start));
            $('#overallTimelineEnd').text(formatDate(summary.planned_end));
            $('#overallTimelineGreen').css('width', '100%');
            $('#overallTimelineRed').css('width', redPct + '%');
        }

        function renderTaskMixChart(data) {
            var canvas = document.getElementById('taskMixChart');
            if (!canvas) {
                return;
            }
            var metrics = data.metrics || {};
            taskMixChart = new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Ongoing', 'Waiting Approval', 'On Hold'],
                    datasets: [{
                        data: [
                            parseInt(metrics.completed || 0, 10),
                            parseInt(metrics.open || 0, 10) - parseInt(metrics.approval_pending || 0, 10),
                            parseInt(metrics.approval_pending || 0, 10),
                            parseInt(metrics.on_hold || 0, 10)
                        ],
                        backgroundColor: ['#16a34a', '#2563eb', '#d97706', '#94a3b8'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                usePointStyle: true,
                                font: { weight: '700' }
                            }
                        }
                    },
                    cutout: '68%'
                }
            });
        }

        function renderDepartmentChart(data) {
            var canvas = document.getElementById('deptProgressChart');
            if (!canvas) {
                return;
            }
            var rows = (data.department_summary || []).slice(0, 6);
            var labels = [];
            var openValues = [];
            var delayValues = [];

            rows.forEach(function (row) {
                labels.push(row.department || 'Unmapped');
                openValues.push(parseInt(row.open || 0, 10));
                delayValues.push(parseInt(row.delayed || 0, 10));
            });

            deptProgressChart = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Open',
                        data: openValues,
                        backgroundColor: '#2563eb',
                        borderRadius: 10
                    }, {
                        label: 'Delayed',
                        data: delayValues,
                        backgroundColor: '#dc2626',
                        borderRadius: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                usePointStyle: true,
                                font: { weight: '700' }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: { weight: '700' }
                            },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { weight: '700' }
                            },
                            grid: { color: '#edf2f7' }
                        }
                    }
                }
            });
        }

        function renderManagementOverview(data) {
            var dfInfo = data.df_info || {};
            var poInfo = data.po_info || {};
            var summary = data.summary || {};
            var metrics = data.metrics || {};
            var currentFocus = summary.current_focus || null;
            var nextDueTask = summary.next_due_task || null;
            var highestDelayTask = summary.highest_delay_task || null;
            var recentActivity = data.recent_activity || [];
            var owners = data.owner_summary || [];
            var departments = data.department_summary || [];
            var priorityTasks = data.priority_tasks || [];

            var heroBadges = '';
            heroBadges += buildStatusBadge(String(dfInfo.on_hold || '0') === '1' ? 'hold' : (parseInt(dfInfo.df_status || 0, 10) === 1 ? 'closed' : 'active'), dfInfo.status_text || 'Active');
            heroBadges += buildStatusBadge(summary.health_key, summary.health || 'Stable');
            heroBadges += buildStatusBadge('info', 'DF ' + (dfInfo.df_no || 'N/A'));
            if (poInfo.party_name) {
                heroBadges += buildStatusBadge('info', poInfo.party_name);
            }

            var actionButtons = '';
            actionButtons += '<a href="' + pageBaseUrl + 'gantt/' + parseInt(dfInfo.id || 0, 10) + '" target="_blank" class="ghost-btn">Open Gantt</a>';
            actionButtons += dfInfo.df_download_url
                ? '<a href="' + escapeHtml(dfInfo.df_download_url) + '" target="_blank" class="ghost-btn">DF File</a>'
                : '<span class="ghost-btn disabled">DF File Missing</span>';
            actionButtons += (poInfo && poInfo.po_download_url)
                ? '<a href="' + escapeHtml(poInfo.po_download_url) + '" target="_blank" class="ghost-btn">PO File</a>'
                : '<span class="ghost-btn disabled">PO File Missing</span>';

            var priorityHtml = '';
            if (priorityTasks.length) {
                priorityTasks.forEach(function (row) {
                    priorityHtml +=
                        '<div class="list-item">' +
                            '<div class="list-item-title">' + textOrDefault(row.task_name, 'Task') + '</div>' +
                            '<div class="list-item-sub">' + textOrDefault(row.department, 'Department') + ' | ' + textOrDefault(row.responsible_person, 'Unassigned') + '</div>' +
                            '<div class="list-item-meta">' +
                                buildMiniPill(row.status_label || 'Ongoing', (String(row.status_key || '').indexOf('delay') !== -1 ? 'red' : (String(row.status_key || '') === 'approval' ? 'amber' : 'blue'))) +
                                buildMiniPill('Due ' + formatDate(row.end_date), 'slate') +
                                buildMiniPill('Tickets ' + parseInt(row.open_ticket_count || 0, 10), 'blue') +
                                ((parseInt(row.delay_days || 0, 10) > 0) ? buildMiniPill('Delay ' + parseInt(row.delay_days || 0, 10) + ' day(s)', 'red') : '') +
                            '</div>' +
                            '<div class="list-item-sub mt-2">' + (row.latest_update_text ? truncateText(row.latest_update_text, 110) : 'No fresh remark captured yet.') + '</div>' +
                        '</div>';
                });
            } else {
                priorityHtml = '<div class="empty-state">No active task pressure found for this DF.</div>';
            }

            var ownerHtml = '';
            if (owners.length) {
                owners.slice(0, 8).forEach(function (row) {
                    ownerHtml +=
                        '<div class="list-item">' +
                            '<div class="list-item-title">' + textOrDefault(row.owner, 'Owner') + '</div>' +
                            '<div class="list-item-sub">' + textOrDefault(row.departments || 'No mapped department', 'No mapped department') + '</div>' +
                            '<div class="list-item-meta">' +
                                buildMiniPill('Open ' + parseInt(row.open_tasks || 0, 10), 'blue') +
                                ((parseInt(row.delayed_tasks || 0, 10) > 0) ? buildMiniPill('Delayed ' + parseInt(row.delayed_tasks || 0, 10), 'red') : '') +
                                ((parseInt(row.approval_tasks || 0, 10) > 0) ? buildMiniPill('Approval ' + parseInt(row.approval_tasks || 0, 10), 'amber') : '') +
                                ((row.next_due_date) ? buildMiniPill('Next Due ' + formatDate(row.next_due_date), 'slate') : '') +
                            '</div>' +
                        '</div>';
                });
            } else {
                ownerHtml = '<div class="empty-state">No active owner load found. This DF may be closed, on hold, or fully unassigned.</div>';
            }

            var departmentHtml = '';
            if (departments.length) {
                departments.forEach(function (row) {
                    departmentHtml +=
                        '<div class="department-card">' +
                            '<div class="department-name">' + textOrDefault(row.department, 'Unmapped') + '</div>' +
                            '<div class="list-item-meta mt-3">' +
                                buildMiniPill('Total ' + parseInt(row.total || 0, 10), 'slate') +
                                buildMiniPill('Completed ' + parseInt(row.completed || 0, 10), 'green') +
                                buildMiniPill('Open ' + parseInt(row.open || 0, 10), 'blue') +
                                ((parseInt(row.delayed || 0, 10) > 0) ? buildMiniPill('Delayed ' + parseInt(row.delayed || 0, 10), 'red') : '') +
                            '</div>' +
                            '<div class="department-bar"><span style="width:' + Math.min(parseFloat(row.completion_pct || 0), 100) + '%;"></span></div>' +
                            '<div class="list-item-sub mt-2">' + parseFloat(row.completion_pct || 0).toFixed(0) + '% completion against active task base</div>' +
                        '</div>';
                });
            } else {
                departmentHtml = '<div class="empty-state col-span-full">No department level execution data found for this DF.</div>';
            }

            var timelineHtml = '';
            if (recentActivity.length) {
                recentActivity.forEach(function (eventRow) {
                    var metaParts = [];
                    if (eventRow.actor) {
                        metaParts.push(eventRow.actor);
                    }
                    if (eventRow.task_name) {
                        metaParts.push(eventRow.task_name);
                    }
                    if (eventRow.department) {
                        metaParts.push(eventRow.department);
                    }
                    timelineHtml +=
                        '<div class="timeline-item">' +
                            '<div class="timeline-top">' +
                                '<div class="timeline-label">' + textOrDefault(eventRow.label, 'Update') + '</div>' +
                                '<div class="timeline-time">' + formatDateTime(eventRow.time) + '</div>' +
                            '</div>' +
                            '<div class="timeline-message">' + textOrDefault(eventRow.message, 'Update available') + '</div>' +
                            '<div class="timeline-meta">' + escapeHtml(metaParts.join(' | ')) + '</div>' +
                        '</div>';
                });
            } else {
                timelineHtml = '<div class="empty-state">No recent movement trail found for this DF yet.</div>';
            }

            var focusPanel = function (title, row, emptyText) {
                if (!row) {
                    return '<div class="insight-panel">' +
                        '<div class="insight-title">' + escapeHtml(title) + '</div>' +
                        '<div class="insight-copy mt-3">' + escapeHtml(emptyText) + '</div>' +
                    '</div>';
                }
                return '<div class="insight-panel">' +
                    '<div class="insight-title">' + escapeHtml(title) + '</div>' +
                    '<div class="insight-value">' + textOrDefault(row.task_name, 'Task') + '</div>' +
                    '<div class="insight-copy">' + textOrDefault(row.department, 'Department') + ' | ' + textOrDefault(row.responsible_person, 'Unassigned') + '</div>' +
                    '<div class="list-item-meta mt-3">' +
                        buildMiniPill(row.status_label || 'Ongoing', (String(row.status_key || '').indexOf('delay') !== -1 ? 'red' : (String(row.status_key || '') === 'approval' ? 'amber' : 'blue'))) +
                        ((row.end_date) ? buildMiniPill('Due ' + formatDate(row.end_date), 'slate') : '') +
                        ((parseInt(row.delay_days || 0, 10) > 0) ? buildMiniPill('Delay ' + parseInt(row.delay_days || 0, 10) + ' day(s)', 'red') : '') +
                        buildMiniPill('Tickets ' + parseInt(row.open_ticket_count || 0, 10), 'blue') +
                    '</div>' +
                    '<div class="insight-copy">' + (row.latest_update_text ? truncateText(row.latest_update_text, 130) : 'No latest task remark captured for this item.') + '</div>' +
                '</div>';
            };

            var html = '';
            html += '<div class="summary-hero">';
            html += '  <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">';
            html += '      <div class="flex-1">';
            html += '          <div class="summary-hero-title">' + textOrDefault(dfInfo.df_no, 'DF') + '</div>';
            html += '          <div class="summary-hero-copy">' + textOrDefault(dfInfo.df_description || 'Description not available', 'Description not available') + '</div>';
            html += '          <div class="hero-chip-row">' + heroBadges + '</div>';
            html += '      </div>';
            html += '      <div class="xl:max-w-[360px] w-full">';
            html += '          <div class="insight-panel !bg-white/12 !border-white/15">';
            html += '              <div class="insight-title !text-white/70">Management Note</div>';
            html += '              <div class="insight-value" style="color:#fff;font-size:22px;">' + textOrDefault(summary.health, 'Stable') + '</div>';
            html += '              <div class="insight-copy !text-white/80">' + textOrDefault(summary.health_note, 'No current health note available.') + '</div>';
            html += '              <div class="flex flex-wrap gap-2 mt-4">' + actionButtons + '</div>';
            html += '          </div>';
            html += '      </div>';
            html += '  </div>';
            html += '  <div class="mini-meta-grid">';
            html += '      <div class="mini-meta-card"><div class="mini-meta-label">Planned Start</div><div class="mini-meta-value">' + formatDate(summary.planned_start) + '</div></div>';
            html += '      <div class="mini-meta-card"><div class="mini-meta-label">Planned End</div><div class="mini-meta-value">' + formatDate(summary.planned_end) + '</div></div>';
            html += '      <div class="mini-meta-card"><div class="mini-meta-label">Latest Activity</div><div class="mini-meta-value">' + (summary.latest_activity ? formatDateTime(summary.latest_activity.time) : 'N/A') + '</div></div>';
            html += '      <div class="mini-meta-card"><div class="mini-meta-label">Customer / Machine</div><div class="mini-meta-value">' + textOrDefault((poInfo.party_name || 'N/A') + (poInfo.machine_name ? (' | ' + poInfo.machine_name) : ''), 'N/A') + '</div></div>';
            html += '  </div>';
            html += '</div>';

            html += '<div class="overview-grid">';
            html += '  <div class="chart-card">';
            html += '      <div class="section-title" style="font-size:20px;">Task Execution Mix</div>';
            html += '      <div class="section-subtitle">Completed, ongoing, approval-pending, and on-hold distribution for this DF.</div>';
            html += '      <div class="chart-wrap"><canvas id="taskMixChart"></canvas></div>';
            html += '  </div>';
            html += '  <div class="chart-card">';
            html += '      <div class="section-title" style="font-size:20px;">Department Pressure</div>';
            html += '      <div class="section-subtitle">Top departments by open workload and delay exposure.</div>';
            html += '      <div class="chart-wrap"><canvas id="deptProgressChart"></canvas></div>';
            html += '  </div>';
            html += '</div>';

            html += '<div class="two-col-grid">';
            html += '  <div>';
            html +=        focusPanel('Current Focus', currentFocus, 'No current focus task identified.');
            html += '      <div class="mt-4"></div>';
            html +=        focusPanel('Next Due Task', nextDueTask, 'No upcoming due task found.');
            html += '      <div class="mt-4"></div>';
            html +=        focusPanel('Highest Delay', highestDelayTask, 'No delayed task found right now.');
            html += '  </div>';
            html += '  <div class="list-card">';
            html += '      <div class="section-title" style="font-size:20px;">Task Pressure Queue</div>';
            html += '      <div class="section-subtitle">Items management should monitor first based on delay, tickets, and pending approvals.</div>';
            html += '      <div class="list-stack">' + priorityHtml + '</div>';
            html += '  </div>';
            html += '</div>';

            html += '<div class="two-col-grid">';
            html += '  <div class="list-card">';
            html += '      <div class="section-title" style="font-size:20px;">Ownership Pressure</div>';
            html += '      <div class="section-subtitle">Who is actively holding open work on this DF and where follow-up may be needed.</div>';
            html += '      <div class="list-stack">' + ownerHtml + '</div>';
            html += '  </div>';
            html += '  <div class="timeline-card !mt-0">';
            html += '      <div class="section-title" style="font-size:20px;">Latest Movement Trail</div>';
            html += '      <div class="section-subtitle">Recent remarks, task completions, and help ticket activity captured on this DF.</div>';
            html += '      <div class="timeline-list">' + timelineHtml + '</div>';
            html += '  </div>';
            html += '</div>';

            html += '<div class="timeline-card">';
            html += '  <div class="section-title" style="font-size:20px;">Department Execution Sheet</div>';
            html += '  <div class="section-subtitle">Department-wise completion, active load, and delay exposure for the selected DF.</div>';
            html += '  <div class="department-grid">' + departmentHtml + '</div>';
            html += '</div>';

            $('#managementSummaryContent').html(html);
            renderTaskMixChart(data);
            renderDepartmentChart(data);
        }

        function renderPlanVsActual(data) {
            var rows = data.plan_vs_actual || [];
            var html = '';

            if (!rows.length) {
                html = '<tr><td colspan="8"><div class="empty-state">No scheduling records found for this DF.</div></td></tr>';
                $('#planVsActualTableBody').html(html);
                return;
            }

            rows.forEach(function (row) {
                var rowClass = '';
                var varianceType = 'ongoing';
                var delayDays = parseInt(row.delay_days || 0, 10);
                var statusKey = String(row.status_key || '').toLowerCase();
                var statusLabel = row.status_label || 'Ongoing';
                var actualFinish = (row.task_completed_on && String(row.task_completed_on).indexOf('0000-00-00') === -1)
                    ? '<div class="font-extrabold text-slate-900">' + formatDate(row.task_completed_on) + '</div><div class="text-[11px] text-slate-500 mt-1">' + textOrDefault(row.completed_by_name || 'Completed', 'Completed') + '</div>'
                    : '<span class="text-slate-300 font-extrabold">—</span>';

                if (statusKey === 'hold') {
                    rowClass = 'row-hold';
                    varianceType = 'hold';
                } else if (statusKey === 'approval' || statusKey === 'approval-delay') {
                    rowClass = (delayDays > 0) ? 'row-delay' : 'row-approval';
                    varianceType = (delayDays > 0) ? 'delay' : 'approval';
                } else if (delayDays > 0) {
                    rowClass = 'row-delay';
                    varianceType = 'delay';
                } else if (statusKey === 'completed') {
                    varianceType = 'completed';
                } else {
                    varianceType = 'ongoing';
                }

                var latestUpdate = row.latest_update_text
                    ? '<div class="font-semibold text-slate-700">' + truncateText(row.latest_update_text, 90) + '</div><div class="text-[11px] text-slate-500 mt-1">' + (row.latest_update_on ? formatDateTime(row.latest_update_on) : 'Latest remark time not available') + '</div>'
                    : '<div class="text-slate-400 font-semibold">No remark captured yet</div>';

                var varianceBadge = '';
                if (statusKey === 'hold') {
                    varianceBadge = buildStatusBadge('hold', 'On Hold');
                } else if (delayDays > 0) {
                    varianceBadge = buildStatusBadge('delay', '+' + delayDays + ' day(s)');
                } else if (statusKey === 'completed') {
                    varianceBadge = buildStatusBadge('stable', 'On Time');
                } else if (statusKey === 'approval' || statusKey === 'approval-delay') {
                    varianceBadge = buildStatusBadge('review', 'Approval Pending');
                } else {
                    varianceBadge = buildStatusBadge('active', 'In Progress');
                }

                var ticketText = parseInt(row.open_ticket_count || 0, 10) > 0
                    ? buildMiniPill('Open ' + parseInt(row.open_ticket_count || 0, 10), 'blue') + '<div class="text-[11px] text-slate-500 mt-2">Total ' + parseInt(row.total_ticket_count || 0, 10) + '</div>'
                    : '<span class="text-slate-300 font-extrabold">—</span>';

                html += '<tr class="' + rowClass + '" data-variance="' + escapeHtml(varianceType) + '">' +
                    '<td>' +
                        '<div class="font-extrabold text-slate-900">' + textOrDefault(row.task_name, 'Task') + '</div>' +
                        '<div class="text-[11px] text-blue-600 font-extrabold uppercase tracking-widest mt-1">' + textOrDefault(row.department, 'Department') + '</div>' +
                    '</td>' +
                    '<td><div class="font-semibold text-slate-700">' + textOrDefault(row.responsible_person, 'Unassigned') + '</div></td>' +
                    '<td>' + buildStatusBadge(statusKey, statusLabel) + '</td>' +
                    '<td class="text-center"><div class="font-extrabold text-slate-900">' + formatDate(row.end_date) + '</div></td>' +
                    '<td>' + latestUpdate + '</td>' +
                    '<td class="text-center">' + ticketText + '</td>' +
                    '<td class="text-center">' + actualFinish + '</td>' +
                    '<td class="text-right">' + varianceBadge + '</td>' +
                '</tr>';
            });

            $('#planVsActualTableBody').html(html);
            applyVarianceFilter();
        }

        function renderDfDocument(data) {
            var dfInfo = data.df_info || {};
            var summary = data.summary || {};
            var html = '';

            if (!dfInfo.id) {
                $('#dfDownloadContent').html('<div class="empty-state mt-5">No DF information found for this selection.</div>');
                return;
            }

            html += '<div class="doc-grid">';
            html += '  <div class="doc-card">';
            html += '      <div class="section-title" style="font-size:20px;">Design Form Record</div>';
            html += '      <div class="section-subtitle">Primary release information, execution status, and management traceability.</div>';
            html += '      <div class="doc-meta-grid">';
            html += '          <div><div class="doc-label">DF Number</div><div class="doc-value">' + textOrDefault(dfInfo.df_no, 'N/A') + '</div></div>';
            html += '          <div><div class="doc-label">Status</div><div class="doc-value">' + buildStatusBadge(String(dfInfo.on_hold || '0') === '1' ? 'hold' : (parseInt(dfInfo.df_status || 0, 10) === 1 ? 'closed' : 'active'), dfInfo.status_text || 'Active') + '</div></div>';
            html += '          <div><div class="doc-label">Release Date</div><div class="doc-value">' + formatDate(dfInfo.added_on) + '</div></div>';
            html += '          <div><div class="doc-label">Released By</div><div class="doc-value">' + textOrDefault(dfInfo.released_by, 'N/A') + '</div></div>';
            html += '          <div><div class="doc-label">Planned Window</div><div class="doc-value">' + formatDate(summary.planned_start) + ' to ' + formatDate(summary.planned_end) + '</div></div>';
            html += '          <div><div class="doc-label">Actual Closure</div><div class="doc-value">' + formatDate(summary.actual_end || dfInfo.completed_on) + '</div></div>';
            html += '      </div>';
            html += '      <div class="mt-5"><div class="doc-label">DF Description</div><div class="doc-value">' + textOrDefault(dfInfo.df_description, 'Description not available') + '</div></div>';
            html += '  </div>';
            html += '  <div class="doc-card">';
            html += '      <div class="section-title" style="font-size:20px;">Quick Actions</div>';
            html += '      <div class="section-subtitle">Open related views or supporting files from one place.</div>';
            html += '      <div class="flex flex-col gap-3 mt-5">';
            html += '          <a href="' + pageBaseUrl + 'gantt/' + parseInt(dfInfo.id || 0, 10) + '" target="_blank" class="primary-btn" style="height:48px;justify-content:flex-start;">Open Gantt View</a>';
            html += dfInfo.df_download_url
                ? '          <a href="' + escapeHtml(dfInfo.df_download_url) + '" target="_blank" class="ghost-btn" style="justify-content:flex-start;">Download DF File</a>'
                : '          <span class="ghost-btn disabled" style="justify-content:flex-start;">DF Attachment Not Available</span>';
            html += '          <a href="' + pageBaseUrl + 'Dashboard/daily_df_progress_report" target="_blank" class="ghost-btn" style="justify-content:flex-start;">Open Daily DF Intelligence</a>';
            html += '      </div>';
            html += '      <div class="mt-5"><div class="doc-label">Management Note</div><div class="doc-value">' + textOrDefault(summary.health_note, 'No note available') + '</div></div>';
            html += '  </div>';
            html += '</div>';

            $('#dfDownloadContent').html(html);
        }

        function renderPoStatus(data) {
            var poInfo = data.po_info || null;
            if (!poInfo) {
                $('#poDownloadContent').html('<div class="empty-state mt-5">No PO record found against this DF.</div>');
                return;
            }

            var html = '';
            html += '<div class="doc-grid">';
            html += '  <div class="doc-card">';
            html += '      <div class="section-title" style="font-size:20px;">Commercial Snapshot</div>';
            html += '      <div class="section-subtitle">Latest PO record mapped with this DF for management reference.</div>';
            html += '      <div class="doc-meta-grid">';
            html += '          <div><div class="doc-label">Customer</div><div class="doc-value">' + textOrDefault(poInfo.party_name, 'N/A') + '</div></div>';
            html += '          <div><div class="doc-label">PO Number</div><div class="doc-value">' + textOrDefault(poInfo.pono, 'N/A') + '</div></div>';
            html += '          <div><div class="doc-label">PO Date</div><div class="doc-value">' + formatDate(poInfo.podate) + '</div></div>';
            html += '          <div><div class="doc-label">Value</div><div class="doc-value">' + formatCurrency(poInfo.povalue, poInfo.currency) + '</div></div>';
            html += '          <div><div class="doc-label">Machine</div><div class="doc-value">' + textOrDefault(poInfo.machine_name, 'N/A') + '</div></div>';
            html += '          <div><div class="doc-label">Marketing Person</div><div class="doc-value">' + textOrDefault(poInfo.marketing_person, 'N/A') + '</div></div>';
            html += '          <div><div class="doc-label">PO Version Count</div><div class="doc-value">' + escapeHtml(String(parseInt(poInfo.po_count || 0, 10))) + '</div></div>';
            html += '          <div><div class="doc-label">Currency</div><div class="doc-value">' + textOrDefault(poInfo.currency || 'INR', 'INR') + '</div></div>';
            html += '      </div>';
            html += '  </div>';
            html += '  <div class="doc-card">';
            html += '      <div class="section-title" style="font-size:20px;">PO Attachment</div>';
            html += '      <div class="section-subtitle">Supporting PO file access for management review.</div>';
            html += '      <div class="flex flex-col gap-3 mt-5">';
            html += poInfo.po_download_url
                ? '          <a href="' + escapeHtml(poInfo.po_download_url) + '" target="_blank" class="primary-btn" style="height:48px;justify-content:flex-start;background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);">Download PO File</a>'
                : '          <span class="ghost-btn disabled" style="justify-content:flex-start;">PO Attachment Not Available</span>';
            html += '      </div>';
            html += '      <div class="mt-5"><div class="doc-label">Commercial Comment</div><div class="doc-value">Latest PO reference is shown here so management can connect execution status with the active commercial record.</div></div>';
            html += '  </div>';
            html += '</div>';

            $('#poDownloadContent').html(html);
        }

        function renderHelpTickets(data) {
            var tickets = data.help_tickets || [];
            var summary = data.summary || {};
            var html = '';

            html += '<div class="ticket-summary">';
            html += '  <div class="kpi-card"><div class="kpi-label">Open Tickets</div><div class="kpi-value text-sky-600">' + escapeHtml(String(parseInt(summary.open_ticket_count || 0, 10))) + '</div><div class="kpi-sub">Still active on this DF</div></div>';
            html += '  <div class="kpi-card"><div class="kpi-label">Closed Tickets</div><div class="kpi-value text-emerald-600">' + escapeHtml(String(parseInt(summary.closed_ticket_count || 0, 10))) + '</div><div class="kpi-sub">Resolved communication loops</div></div>';
            html += '  <div class="kpi-card"><div class="kpi-label">Total Tickets</div><div class="kpi-value">' + escapeHtml(String(tickets.length)) + '</div><div class="kpi-sub">Mapped support trail</div></div>';
            html += '</div>';

            if (!tickets.length) {
                html += '<div class="empty-state mt-5">No communication ticket is mapped with this DF.</div>';
                $('#helpTicketsContent').html(html);
                return;
            }

            html += '<div class="list-stack mt-5">';
            tickets.forEach(function (ticket) {
                var statusBadge = buildStatusBadge(ticket.status_key, ticket.status_text || 'Open');
                var noteBlock = ticket.remarks
                    ? '<div class="insight-copy !mt-3 !text-slate-700"><strong>Logged Remark:</strong> ' + textOrDefault(ticket.remarks, '') + '</div>'
                    : '';
                if (ticket.updated_remarks) {
                    noteBlock += '<div class="insight-copy !mt-2 !text-slate-700"><strong>Updated Remark:</strong> ' + textOrDefault(ticket.updated_remarks, '') + '</div>';
                }
                if (ticket.ticket_closing_remarks) {
                    noteBlock += '<div class="insight-copy !mt-2 !text-slate-700"><strong>Closing Remark:</strong> ' + textOrDefault(ticket.ticket_closing_remarks, '') + '</div>';
                }

                html += '<div class="ticket-card">';
                html += '  <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">';
                html += '      <div>';
                html += '          <div class="flex flex-wrap items-center gap-2">' + statusBadge + buildMiniPill('Ticket #' + (ticket.help_ticket_no || 'N/A'), 'blue') + (ticket.task_name ? buildMiniPill(ticket.task_name, 'slate') : '') + '</div>';
                html += '          <div class="section-title mt-3" style="font-size:18px;">Department: ' + textOrDefault(ticket.dept_name, 'N/A') + '</div>';
                html += '          <div class="section-subtitle mt-1">Raised by ' + textOrDefault(ticket.raised_by, 'N/A') + (ticket.assigned_to ? (' | Action owner ' + textOrDefault(ticket.assigned_to, 'N/A')) : '') + '</div>';
                html +=            noteBlock;
                html += '      </div>';
                html += '      <div class="text-left lg:text-right min-w-[190px]">';
                html += '          <div class="doc-label">Logged On</div>';
                html += '          <div class="doc-value">' + formatDateTime(ticket.added_on) + '</div>';
                html += '          <div class="doc-label mt-3">Updated On</div>';
                html += '          <div class="doc-value">' + formatDateTime(ticket.updated_on) + '</div>';
                html += '          <div class="doc-label mt-3">Closed On</div>';
                html += '          <div class="doc-value">' + formatDateTime(ticket.ticket_closed_on) + '</div>';
                html += (ticket.closed_by ? ('<div class="doc-label mt-3">Closed By</div><div class="doc-value">' + textOrDefault(ticket.closed_by, 'N/A') + '</div>') : '');
                html += '      </div>';
                html += '  </div>';
                html += '</div>';
            });
            html += '</div>';

            $('#helpTicketsContent').html(html);
        }

        function overtimeHours(minutes) {
            return (parseInt(minutes || 0, 10) / 60).toFixed(2);
        }

        // Indian digit grouping, matching the overtime module's own totals.
        function overtimeMoney(amount) {
            var value = Math.round((parseFloat(amount) || 0) * 100) / 100;
            var negative = value < 0;
            var parts = Math.abs(value).toFixed(2).split('.');
            var whole = parts[0];
            if (whole.length > 3) {
                var last = whole.slice(-3);
                whole = whole.slice(0, -3).replace(/\B(?=(\d{2})+$)/g, ',') + ',' + last;
            }
            return (negative ? '-' : '') + whole + '.' + parts[1];
        }

        function renderOvertime(data) {
            var overtime = data.overtime || {};
            var rows = overtime.rows || [];
            var byDay = overtime.by_day || [];
            var summary = overtime.summary || {};
            var showCost = !!overtime.show_cost;
            var html = '';

            if (!overtime.available) {
                $('#dfOvertimeContent').html('<div class="empty-state mt-5">The overtime module is not installed on this server yet.</div>');
                return;
            }

            html += '<div class="ticket-summary' + (showCost ? ' ot-kpis' : '') + '">';
            html += '  <div class="kpi-card"><div class="kpi-label">Overtime Occasions</div><div class="kpi-value text-sky-600">' + escapeHtml(String(parseInt(summary.occasions || 0, 10))) + '</div><div class="kpi-sub">Separate approved requests</div></div>';
            html += '  <div class="kpi-card"><div class="kpi-label">People Engaged</div><div class="kpi-value text-teal-600">' + escapeHtml(String(parseInt(summary.people_entries || 0, 10))) + '</div><div class="kpi-sub">One entry per person per request</div></div>';
            html += '  <div class="kpi-card"><div class="kpi-label">Approved Person-Hours</div><div class="kpi-value text-emerald-600">' + escapeHtml(overtimeHours(summary.person_minutes)) + '</div><div class="kpi-sub">4 people x 2 hours = 8 hours</div></div>';
            if (showCost) {
                html += '  <div class="kpi-card"><div class="kpi-label">Overhead Cost</div><div class="kpi-value text-amber-600">&#8377; ' + escapeHtml(overtimeMoney(summary.total_cost)) + '</div><div class="kpi-sub">At the rate stored on each row</div></div>';
            }
            html += '</div>';

            if (!rows.length) {
                html += '<div class="empty-state mt-5">No approved overtime is recorded against this DF.</div>';
                $('#dfOvertimeContent').html(html);
                return;
            }

            html += '<div class="section-head mt-6"><div><div class="section-title" style="font-size:18px;">Day-wise Overtime</div><div class="section-subtitle">How often overtime was run on this DF, and what it added up to.</div></div></div>';
            html += '<div class="table-shell"><div class="table-scroll"><table class="report-table"><thead><tr>';
            html += '<th>Date</th><th>Occasions</th><th>People</th><th>Person-Hours</th>' + (showCost ? '<th>Cost</th>' : '') + '</tr></thead><tbody>';
            byDay.forEach(function (day) {
                html += '<tr>';
                html += '<td>' + formatDate(day.day) + '</td>';
                html += '<td>' + escapeHtml(String(parseInt(day.occasions || 0, 10))) + '</td>';
                html += '<td>' + escapeHtml(String(parseInt(day.people_entries || 0, 10))) + '</td>';
                html += '<td>' + escapeHtml(overtimeHours(day.person_minutes)) + '</td>';
                if (showCost) html += '<td>&#8377; ' + escapeHtml(overtimeMoney(day.total_cost)) + '</td>';
                html += '</tr>';
            });
            html += '</tbody></table></div></div>';

            html += '<div class="section-head mt-6"><div><div class="section-title" style="font-size:18px;">Person-wise Detail</div><div class="section-subtitle">Every person approved for overtime on this DF' + (summary.last_approved_at ? ', last approved ' + escapeHtml(formatDateTime(summary.last_approved_at)) : '') + '.</div></div></div>';
            html += '<div class="table-shell"><div class="table-scroll"><table class="report-table"><thead><tr>';
            html += '<th>Request</th><th>Person</th><th>Department</th><th>Overtime Period</th><th>Hours</th>' + (showCost ? '<th>Cost</th>' : '') + '<th>Requested By</th><th>Approved By</th></tr></thead><tbody>';
            rows.forEach(function (row) {
                html += '<tr>';
                html += '<td>' + escapeHtml(row.request_code || '') + (row.work_reference ? '<br><span class="text-[11px] text-slate-400">' + escapeHtml(row.work_reference) + '</span>' : '') + '</td>';
                html += '<td>' + escapeHtml(row.person_name || '') + '<br><span class="text-[11px] text-slate-400">' + escapeHtml(row.person_type || '') + '</span></td>';
                html += '<td>' + textOrDefault(row.department, 'N/A') + '</td>';
                html += '<td>' + formatDateTime(row.start_at) + '<br><span class="text-[11px] text-slate-400">to ' + formatDateTime(row.end_at) + '</span></td>';
                html += '<td>' + escapeHtml(overtimeHours(row.minutes)) + '</td>';
                if (showCost) {
                    html += '<td>' + (parseFloat(row.hourly_rate || 0) > 0
                        ? '&#8377; ' + escapeHtml(overtimeMoney(row.cost_amount)) + '<br><span class="text-[11px] text-slate-400">@ &#8377;' + escapeHtml(overtimeMoney(row.hourly_rate)) + '/hr</span>'
                        : '<span class="text-slate-400">No rate</span>') + '</td>';
                }
                html += '<td>' + textOrDefault(row.requested_by, 'N/A') + '</td>';
                html += '<td>' + textOrDefault(row.approved_by, 'N/A') + '<br><span class="text-[11px] text-slate-400">' + formatDateTime(row.approved_at) + '</span></td>';
                html += '</tr>';
            });
            html += '</tbody></table></div></div>';

            if (!showCost) {
                html += '<div class="section-subtitle mt-4">Overtime cost is hidden here because your account does not hold the Overtime Reports or Overtime Cost Rates permission.</div>';
            }

            $('#dfOvertimeContent').html(html);
        }

        function updateTopKpis(data, selectedLabel) {
            var metrics = data.metrics || {};
            var summary = data.summary || {};
            var completion = parseFloat(metrics.completion_pct || 0);
            var ongoingTasks = Math.max(0, parseInt(metrics.open || 0, 10) - parseInt(metrics.approval_pending || 0, 10));
            $('#dfCompletion').text(completion.toFixed(0) + '%');
            $('#dfCompletionTop').text(completion.toFixed(0) + '%');
            $('#dfStatus').text(summary.health || 'Stable').removeClass().addClass('kpi-value ' + getHealthClass(summary.health_key));
            $('#dfStatusTop').text(summary.health || 'Stable').removeClass().addClass('text-xl font-black ' + getHealthClass(summary.health_key));
            $('#dfOpenTasks').text(ongoingTasks);
            $('#dfDelayedTasks').text(parseInt(metrics.delayed || 0, 10));
            $('#dfOpenTickets').text(parseInt(summary.open_ticket_count || 0, 10));
            $('#dfActiveOwners').text(parseInt(summary.owner_count || 0, 10));
            document.title = '<?php echo addslashes(sitetitle); ?> | ' + (selectedLabel || 'DF Full Intelligence Report');
        }

        $('#searchButton').on('click', function () {
            hideInlineAlert();

            var dfId = $('#dfSearchInput').val();
            var selectedText = $('#dfSearchInput option:selected').text();

            if (!dfId) {
                showInlineAlert('Please select a DF to generate the management intelligence report.');
                return;
            }

            $('#mainSearchResultsArea').removeClass('hidden');
            $('#varianceFilter').val('all');
            $('.tab-button').removeClass('active');
            $('.tab-button[data-tab="managementOverview"]').addClass('active');
            $('.content-section').removeClass('active');
            $('#managementOverview').addClass('active');

            setLoading(true);
            renderSkeleton();

            $.ajax({
                url: pageBaseUrl + 'Dashboard/get_df_details/' + dfId,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    updateTopKpis(data, selectedText);
                    renderManagementOverview(data);
                    renderOverallTimeline(data.summary || {}, data.plan_vs_actual || [], data.df_info || {});
                    renderPlanVsActual(data);
                    renderDfDocument(data);
                    renderPoStatus(data);
                    renderHelpTickets(data);
                    renderOvertime(data);
                },
                error: function () {
                    destroyCharts();
                    showInlineAlert('Unable to fetch DF details right now. Please try again or check the server response.');
                    $('#managementSummaryContent').html('<div class="empty-state">Failed to load management intelligence for this DF.</div>');
                    $('#planVsActualTableBody').html('<tr><td colspan="8"><div class="empty-state">Failed to load execution roadmap.</div></td></tr>');
                    $('#dfDownloadContent').html('<div class="empty-state mt-5">Document panel could not be loaded.</div>');
                    $('#poDownloadContent').html('<div class="empty-state mt-5">PO panel could not be loaded.</div>');
                    $('#helpTicketsContent').html('<div class="empty-state mt-5">Ticket panel could not be loaded.</div>');
                    $('#dfOvertimeContent').html('<div class="empty-state mt-5">Overtime panel could not be loaded.</div>');
                    $('#overallTimelineLabel').text('Timeline not available');
                    $('#overallTimelineMeta').text('—');
                    $('#overallTimelineStart').text('—');
                    $('#overallTimelineEnd').text('—');
                    $('#overallTimelineGreen').css('width', '0%');
                    $('#overallTimelineRed').css('width', '0%');
                },
                complete: function () {
                    setLoading(false);
                }
            });
        });

        $(document).on('change', '#varianceFilter', function () {
            applyVarianceFilter();
        });
    });
</script>

<?php $this->load->view('common/footer'); ?>
</body>
</html>
