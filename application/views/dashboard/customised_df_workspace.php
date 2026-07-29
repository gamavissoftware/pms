<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> | <?php echo htmlspecialchars($page_title ?? 'Customised DF Workspace'); ?></title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        :root {
            --df-ink: #17324a;
            --df-primary: #1e5f8d;
            --df-primary-soft: #d9ebf7;
            --df-bg: #f1f6fb;
            --df-panel: rgba(255, 255, 255, 0.96);
            --df-border: #d7e3ef;
            --df-muted: #698095;
            --df-success: #1d8c5f;
            --df-warning: #d58b1b;
            --df-danger: #cb4e4e;
            --df-info: #3f6dcf;
            --df-shadow: 0 24px 55px rgba(23, 50, 74, 0.11);
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(30, 95, 141, 0.14), transparent 24%),
                radial-gradient(circle at bottom right, rgba(69, 144, 188, 0.14), transparent 28%),
                linear-gradient(180deg, #f7fbff 0%, #edf4fb 100%);
            color: var(--df-ink);
        }

        .workspace-shell {
            max-width: 1420px;
            margin: 0 auto;
        }

        .workspace-hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 22px 24px;
            background: linear-gradient(135deg, #12344d 0%, #1e5f8d 56%, #4f96bb 100%);
            color: #fff;
            box-shadow: var(--df-shadow);
            margin-bottom: 14px;
        }

        .workspace-hero:before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            right: -60px;
            top: -90px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.10);
            filter: blur(8px);
        }

        .workspace-hero:after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            left: -80px;
            bottom: -120px;
            border-radius: 999px;
            background: rgba(203, 230, 247, 0.18);
            filter: blur(14px);
        }

        .workspace-kicker {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: 11px;
            font-weight: 800;
        }

        .workspace-hero h1 {
            position: relative;
            margin: 10px 0 8px;
            font-size: 30px;
            line-height: 1.04;
            font-weight: 900;
            letter-spacing: -.03em;
            color: #fff;
        }

        .workspace-hero p {
            position: relative;
            margin: 0;
            color: rgba(255, 255, 255, 0.84);
            font-size: 13px;
            max-width: 760px;
            line-height: 1.55;
        }

        .hero-title-row {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        .hero-health-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .hero-health-pill.state-stable {
            background: rgba(34, 197, 94, 0.16);
            border-color: rgba(186, 230, 200, 0.35);
        }

        .hero-health-pill.state-attention,
        .hero-health-pill.state-review {
            background: rgba(245, 158, 11, 0.17);
            border-color: rgba(254, 215, 170, 0.36);
        }

        .hero-health-pill.state-critical {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(252, 165, 165, 0.34);
        }

        .hero-health-pill.state-closed,
        .hero-health-pill.state-hold {
            background: rgba(148, 163, 184, 0.18);
            border-color: rgba(226, 232, 240, 0.34);
        }

        .hero-status-line {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .hero-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: rgba(255, 255, 255, 0.92);
            font-size: 11px;
            font-weight: 800;
        }

        .hero-chip-row {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 11px;
            font-weight: 800;
        }

        .hero-actions {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            justify-content: flex-end;
            margin-top: 0;
        }

        .hero-actions .btn,
        .hero-actions .hero-link {
            min-height: 40px;
            border-radius: 12px;
            padding: 9px 14px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .hero-actions-note {
            position: relative;
            margin-top: 10px;
            text-align: right;
            color: rgba(255, 255, 255, 0.82);
            font-size: 12px;
            font-weight: 800;
        }

        .hero-link {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .hero-link:hover,
        .hero-link:focus {
            color: #fff;
            background: rgba(255, 255, 255, 0.18);
            text-decoration: none;
        }

        .hero-primary-btn {
            background: #fff;
            color: var(--df-primary);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 18px 35px rgba(8, 20, 31, 0.16);
        }

        .hero-primary-btn:hover,
        .hero-primary-btn:focus {
            color: var(--df-primary);
            filter: brightness(1.02);
        }

        .workspace-notice {
            display: none;
            margin-bottom: 14px;
            border-radius: 16px;
            padding: 14px 16px;
            font-weight: 800;
        }

        .workspace-notice.success {
            display: block;
            background: #e9f7ef;
            border: 1px solid #c3e7d1;
            color: #167548;
        }

        .workspace-notice.error {
            display: block;
            background: #fff0f0;
            border: 1px solid #f2c5c5;
            color: #ad3f3f;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .metric-card,
        .workspace-panel {
            background: var(--df-panel);
            border: 1px solid var(--df-border);
            border-radius: 24px;
            box-shadow: var(--df-shadow);
        }

        .metric-card {
            padding: 14px 16px;
            min-height: 88px;
        }

        .metric-label {
            display: block;
            color: var(--df-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: 11px;
            font-weight: 800;
        }

        .metric-value {
            display: block;
            margin-top: 6px;
            font-size: 28px;
            line-height: 1;
            font-weight: 900;
            color: var(--df-ink);
        }

        .metric-note {
            display: none;
        }

        .workspace-panel-header {
            padding: 18px 20px 0;
        }

        .workspace-panel-title {
            margin: 0;
            font-size: 22px;
            font-weight: 900;
            color: var(--df-ink);
        }

        .workspace-panel-subtitle {
            margin: 6px 0 0;
            color: var(--df-muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .workspace-panel-body {
            padding: 18px 20px 20px;
        }

        .board-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: end;
            margin-bottom: 14px;
        }

        .board-toolbar .form-group {
            margin-bottom: 0;
            min-width: 220px;
        }

        .board-toolbar label {
            color: var(--df-ink);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 7px;
        }

        .board-toolbar .form-control,
        .planner-table input,
        .planner-table select {
            border-radius: 12px;
            border: 1px solid var(--df-border);
            box-shadow: none;
        }

        .board-toolbar .form-control {
            height: 42px;
        }

        .toolbar-switches {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding-bottom: 8px;
        }

        .toolbar-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 9px 13px;
            background: #f4f8fc;
            border: 1px solid var(--df-border);
            color: var(--df-ink);
            font-size: 12px;
            font-weight: 800;
        }

        .toolbar-toggle input {
            margin: 0;
        }

        .toolbar-meta {
            margin-left: auto;
            padding-bottom: 10px;
            color: var(--df-muted);
            font-size: 12px;
            font-weight: 800;
        }

        .department-stack {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .department-card {
            border: 1px solid var(--df-border);
            border-radius: 20px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            overflow: hidden;
        }

        .department-card.has-dirty {
            border-color: #f0c980;
            box-shadow: inset 0 0 0 1px #f4d699;
        }

        .department-head {
            padding: 16px 18px;
            border-bottom: 1px solid #ebf1f7;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, rgba(30, 95, 141, 0.06) 0%, rgba(30, 95, 141, 0.01) 100%);
        }

        .department-title {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: var(--df-ink);
        }

        .department-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .department-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            background: #eef5fb;
            color: var(--df-primary);
        }

        .department-pill.warning {
            background: #fff7e8;
            color: var(--df-warning);
        }

        .department-pill.danger {
            background: #fff1f1;
            color: var(--df-danger);
        }

        .department-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .dirty-badge {
            display: none;
            border-radius: 999px;
            padding: 7px 11px;
            background: #fff3d9;
            color: #b06b07;
            font-size: 12px;
            font-weight: 900;
        }

        .department-save-btn,
        .save-all-btn {
            border-radius: 12px;
            min-height: 42px;
            padding: 10px 15px;
            font-weight: 800;
        }

        .planner-table-wrap {
            overflow-x: auto;
        }

        .planner-table {
            width: 100%;
            min-width: 970px;
        }

        .planner-table thead th {
            background: #eef5fb;
            color: var(--df-ink);
            border-bottom: none;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 12px 10px;
        }

        .planner-table tbody td {
            vertical-align: top;
            padding: 12px 10px;
            border-top: 1px solid #eef3f8;
        }

        .planner-row.is-dirty td {
            background: #fff9ef;
        }

        .planner-row.is-locked td {
            background: #fafcff;
            color: #8ca0b3;
        }

        .planner-task-name {
            font-size: 15px;
            font-weight: 900;
            color: var(--df-ink);
        }

        .planner-task-meta,
        .planner-task-note,
        .planner-updated {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: var(--df-muted);
            line-height: 1.55;
        }

        .planner-task-note {
            color: #52708a;
        }

        .planner-inline-control {
            width: 100%;
            min-width: 165px;
            min-height: 40px;
            background: #fff;
            padding: 8px 10px;
        }

        .planner-inline-control[disabled] {
            opacity: 0.68;
            cursor: not-allowed;
            background: #f5f8fb;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
        }

        .status-ongoing {
            background: #edf8f2;
            color: var(--df-success);
        }

        .status-delay {
            background: #fff0f0;
            color: var(--df-danger);
        }

        .status-completed {
            background: #eef2f7;
            color: #66788a;
        }

        .status-approval,
        .status-approval-delay {
            background: #f4ecff;
            color: #7a54c1;
        }

        .status-hold {
            background: #eef4ff;
            color: var(--df-info);
        }

        .empty-state {
            padding: 40px 18px;
            text-align: center;
            color: var(--df-muted);
            font-size: 14px;
            font-weight: 800;
        }

        .focus-list,
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .focus-card,
        .activity-card {
            border: 1px solid var(--df-border);
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: 15px 16px;
        }

        .focus-eyebrow,
        .activity-label {
            display: block;
            color: var(--df-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: 11px;
            font-weight: 900;
        }

        .focus-title {
            display: block;
            margin-top: 7px;
            font-size: 15px;
            font-weight: 900;
            color: var(--df-ink);
            line-height: 1.45;
        }

        .focus-copy,
        .activity-copy {
            display: block;
            margin-top: 7px;
            color: var(--df-muted);
            font-size: 12px;
            line-height: 1.55;
        }

        .focus-meta,
        .activity-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .focus-meta span,
        .activity-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 7px 11px;
            background: #edf4fb;
            color: var(--df-primary);
            font-size: 12px;
            font-weight: 800;
        }

        .workspace-loader {
            position: fixed;
            inset: 0;
            background: rgba(16, 30, 43, 0.48);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }

        .workspace-loader.is-active {
            display: flex;
        }

        .workspace-loader-card {
            width: 320px;
            max-width: calc(100vw - 36px);
            border-radius: 22px;
            background: #fff;
            padding: 24px 22px;
            text-align: center;
            box-shadow: var(--df-shadow);
        }

        .loader-ring {
            width: 54px;
            height: 54px;
            border-radius: 999px;
            border: 4px solid #dfeaf4;
            border-top-color: var(--df-primary);
            margin: 0 auto 14px;
            animation: spin 0.8s linear infinite;
        }

        .workspace-loader-title {
            font-size: 18px;
            font-weight: 900;
            color: var(--df-ink);
        }

        .workspace-loader-copy {
            margin-top: 8px;
            color: var(--df-muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .secondary-grid {
            margin-top: 16px;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            min-height: 40px;
            border-radius: 12px;
            border: 1px solid var(--df-border);
            display: flex;
            align-items: center;
            box-shadow: none;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 11px;
            padding-right: 28px;
            color: var(--df-ink);
            font-size: 13px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 8px;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (max-width: 1280px) {
            .metric-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 991px) {
            .workspace-hero {
                padding: 20px 18px;
            }

            .workspace-hero h1 {
                font-size: 26px;
            }

            .hero-actions {
                justify-content: flex-start;
                margin-top: 14px;
            }

            .hero-actions-note {
                text-align: left;
            }

            .board-toolbar .form-group {
                min-width: 100%;
            }

            .toolbar-meta {
                margin-left: 0;
            }
        }

        @media (max-width: 767px) {
            .metric-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .workspace-panel-header,
            .workspace-panel-body {
                padding-left: 16px;
                padding-right: 16px;
            }

            .department-head {
                padding: 16px;
            }
        }

        @media (max-width: 520px) {
            .metric-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid workspace-shell">
            <div class="row" style="margin-top: 14px;">
                <div class="col-lg-12">
                    <div class="workspace-hero">
                        <div class="row">
                            <div class="col-lg-7">
                                <span class="workspace-kicker"><i class="fa fa-sliders"></i> Meeting-first planning workspace</span>
                                <div class="hero-title-row">
                                    <h1 id="workspaceHeroTitle">Customised DF</h1>
                                    <span id="workspaceHealthPill" class="hero-health-pill state-waiting"><i class="fa fa-heartbeat"></i> Waiting</span>
                                </div>
                                <p id="workspaceHeroCopy">Open one DF, align every department in the same discussion, set accountable owners, and update real task dates from a single live board built for marketing-led coordination.</p>
                                <div id="workspaceHeroStatus" class="hero-status-line">
                                    <span class="hero-status-pill"><i class="fa fa-clock-o"></i> Loading latest DF plan</span>
                                </div>
                                <div class="hero-chip-row">
                                    <span class="hero-chip"><i class="fa fa-file-text-o"></i> <span id="workspaceChipDf"><?php echo htmlspecialchars($workspace_context['df_no'] ?? 'DF'); ?></span></span>
                                    <span class="hero-chip"><i class="fa fa-shopping-bag"></i> <span id="workspaceChipPo"><?php echo htmlspecialchars($workspace_context['pono'] ?? 'PO'); ?></span></span>
                                    <span class="hero-chip"><i class="fa fa-building-o"></i> <span id="workspaceChipCompany"><?php echo htmlspecialchars($workspace_context['company_name'] ?? 'Company'); ?></span></span>
                                    <span class="hero-chip"><i class="fa fa-user-circle-o"></i> <span id="workspaceChipMarketing"><?php echo htmlspecialchars($workspace_context['marketing_person'] ?? 'Marketing Owner'); ?></span></span>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="hero-actions">
                                    <button type="button" id="customised-df-refresh" class="btn hero-primary-btn"><i class="fa fa-refresh"></i> Refresh Board</button>
                                    <button type="button" id="customised-df-save-all" class="btn hero-primary-btn save-all-btn" disabled><i class="fa fa-save"></i> Save Changed Tasks</button>
                                    <a href="<?php echo page_url; ?>Task/finalgantchartWithDetails/<?php echo (int) $df_id; ?>" target="_blank" class="hero-link"><i class="fa fa-sitemap"></i> Gantt View</a>
                                    <a href="<?php echo htmlspecialchars($detail_url); ?>" target="_blank" class="hero-link"><i class="fa fa-line-chart"></i> DF Intelligence</a>
                                    <a href="<?php echo htmlspecialchars($back_url); ?>" class="hero-link"><i class="fa fa-arrow-left"></i> Back To DF Review</a>
                                </div>
                                <div id="workspaceUnsavedCaption" class="hero-actions-note">No unsaved task changes.</div>
                            </div>
                        </div>
                    </div>

                    <div id="workspaceNotice" class="workspace-notice"></div>
                </div>
            </div>

            <div class="metric-grid">
                <div class="metric-card">
                    <span class="metric-label">Total DF Tasks</span>
                    <span class="metric-value" id="metricTotal">0</span>
                    <span class="metric-note">Every mapped task currently inside this DF execution plan.</span>
                </div>
                <div class="metric-card">
                    <span class="metric-label">Open Planning Tasks</span>
                    <span class="metric-value" id="metricOpen">0</span>
                    <span class="metric-note">Tasks still active for coordination and date planning.</span>
                </div>
                <div class="metric-card">
                    <span class="metric-label">Delayed Tasks</span>
                    <span class="metric-value" id="metricDelayed">0</span>
                    <span class="metric-note">Rows where the planned due date is already behind today.</span>
                </div>
                <div class="metric-card">
                    <span class="metric-label">Completion %</span>
                    <span class="metric-value" id="metricCompletion">0%</span>
                    <span class="metric-note">Completion across active tasks after excluding hold rows.</span>
                </div>
                <div class="metric-card">
                    <span class="metric-label">Departments</span>
                    <span class="metric-value" id="metricDepartments">0</span>
                    <span class="metric-note">Teams participating in the current DF execution flow.</span>
                </div>
                <div class="metric-card">
                    <span class="metric-label">Open Tickets</span>
                    <span class="metric-value" id="metricTickets">0</span>
                    <span class="metric-note">Support issues still open against the active DF tasks.</span>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="workspace-panel">
                        <div class="workspace-panel-header">
                            <h2 class="workspace-panel-title">Department-by-Department Planner</h2>
                            <p class="workspace-panel-subtitle">Review owners, adjust dates, and save only the rows that changed.</p>
                        </div>
                        <div class="workspace-panel-body">
                            <div class="board-toolbar">
                                <div class="form-group">
                                    <label for="workspaceDepartmentFilter">Department Quick Filter</label>
                                    <select id="workspaceDepartmentFilter" class="form-control">
                                        <option value="">All Departments</option>
                                    </select>
                                </div>
                                <div class="toolbar-switches">
                                    <label class="toolbar-toggle">
                                        <input type="checkbox" id="workspaceShowCompleted">
                                        Show completed and hold rows
                                    </label>
                                    <label class="toolbar-toggle">
                                        <input type="checkbox" id="workspaceDelayedOnly">
                                        Focus only delayed work
                                    </label>
                                </div>
                                <div class="toolbar-meta" id="workspaceSyncMeta">Loading customised DF board...</div>
                            </div>

                            <div id="departmentsBoard" class="department-stack">
                                <div class="empty-state">Loading department planner...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row secondary-grid">
                <div class="col-lg-6">
                    <div class="workspace-panel" style="margin-bottom: 22px;">
                        <div class="workspace-panel-header">
                            <h2 class="workspace-panel-title">Meeting Compass</h2>
                            <p class="workspace-panel-subtitle">Fast focus cards for the live DF meeting.</p>
                        </div>
                        <div class="workspace-panel-body">
                            <div id="workspaceFocusList" class="focus-list">
                                <div class="focus-card">
                                    <span class="focus-eyebrow">Loading</span>
                                    <span class="focus-title">Preparing the latest DF management view...</span>
                                    <span class="focus-copy">Once the board is loaded, this panel will show the current focus, next due task, and biggest delay so marketing can lead the meeting with clarity.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="workspace-panel">
                        <div class="workspace-panel-header">
                            <h2 class="workspace-panel-title">Recent DF Movement</h2>
                            <p class="workspace-panel-subtitle">Latest execution signals before you move dates.</p>
                        </div>
                        <div class="workspace-panel-body">
                            <div id="workspaceActivityList" class="activity-list">
                                <div class="activity-card">
                                    <span class="activity-label">Loading</span>
                                    <span class="activity-copy">Recent activity will appear here once the DF data is loaded.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="workspaceLoader" class="workspace-loader">
        <div class="workspace-loader-card">
            <div class="loader-ring"></div>
            <div class="workspace-loader-title" id="workspaceLoaderTitle">Saving Customised DF</div>
            <div class="workspace-loader-copy" id="workspaceLoaderCopy">Please wait while the latest DF dates and task ownership are being updated.</div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        $(function () {
            var dfId = <?php echo (int) $df_id; ?>;
            var detailUrl = <?php echo json_encode(page_url . 'Dashboard/get_df_details/' . (int) $df_id); ?>;
            var saveUrl = <?php echo json_encode(page_url . 'Dashboard/ajax_customised_df_save'); ?>;
            var pageBaseUrl = <?php echo json_encode(page_url); ?>;
            var workspaceData = null;
            var departmentUsers = {};

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : value).html();
            }

            function showNotice(type, message) {
                var $notice = $('#workspaceNotice');
                $notice.removeClass('success error').addClass(type).html(message).stop(true, true).fadeIn(150);
                if (type === 'success') {
                    setTimeout(function () {
                        $notice.fadeOut(250);
                    }, 2600);
                }
            }

            function setLoader(active, title, copy) {
                var $loader = $('#workspaceLoader');
                $('#workspaceLoaderTitle').text(title || 'Saving Customised DF');
                $('#workspaceLoaderCopy').text(copy || 'Please wait while the latest DF dates and task ownership are being updated.');
                $loader.toggleClass('is-active', !!active);
            }

            function formatDateLabel(value) {
                if (!value) {
                    return 'Not Set';
                }

                var dateObj = new Date(value + 'T00:00:00');
                if (isNaN(dateObj.getTime())) {
                    return escapeHtml(value);
                }

                return dateObj.toLocaleDateString('en-IN', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function formatDateTime(value) {
                if (!value) {
                    return 'No recent movement';
                }

                var normalised = String(value).replace(' ', 'T');
                var dateObj = new Date(normalised);
                if (isNaN(dateObj.getTime())) {
                    return escapeHtml(value);
                }

                return dateObj.toLocaleString('en-IN', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function buildStatusBadge(statusKey, statusLabel) {
                var className = 'status-ongoing';
                if (statusKey === 'delay') {
                    className = 'status-delay';
                } else if (statusKey === 'completed') {
                    className = 'status-completed';
                } else if (statusKey === 'approval' || statusKey === 'approval-delay') {
                    className = 'status-approval';
                } else if (statusKey === 'hold') {
                    className = 'status-hold';
                }

                return '<span class="status-badge ' + className + '">' + escapeHtml(statusLabel || 'Ongoing') + '</span>';
            }

            function initSelect2ForElement($element, options) {
                if (!$element.length) {
                    return;
                }

                if ($element.hasClass('select2-hidden-accessible')) {
                    $element.select2('destroy');
                }

                $element.select2(options || {});
            }

            function initWorkspaceSelect2() {
                initSelect2ForElement($('#workspaceDepartmentFilter'), {
                    width: '100%',
                    allowClear: true,
                    placeholder: 'All Departments'
                });

                $('#departmentsBoard .customised-df-assignee').each(function () {
                    initSelect2ForElement($(this), {
                        width: '100%',
                        dropdownAutoWidth: true
                    });
                });
            }

            function updateMetricCards(metrics, summary) {
                $('#metricTotal').text(parseInt(metrics.total || 0, 10));
                $('#metricOpen').text(parseInt(metrics.open || 0, 10));
                $('#metricDelayed').text(parseInt(metrics.delayed || 0, 10));
                $('#metricCompletion').text(parseFloat(metrics.completion_pct || 0).toFixed(0) + '%');
                $('#metricDepartments').text(parseInt(summary.department_count || 0, 10));
                $('#metricTickets').text(parseInt(summary.open_ticket_count || 0, 10));
            }

            function updateHero(data) {
                var dfInfo = data.df_info || {};
                var poInfo = data.po_info || {};
                var summary = data.summary || {};
                var metrics = data.metrics || {};
                var poNumber = poInfo.pono || <?php echo json_encode($workspace_context['pono'] ?? 'PO'); ?>;
                var companyName = poInfo.party_name || <?php echo json_encode($workspace_context['company_name'] ?? 'Company'); ?>;
                var machineName = poInfo.machine_name || <?php echo json_encode($workspace_context['machine_name'] ?? 'Machine'); ?>;
                var marketingName = poInfo.marketing_person || <?php echo json_encode($workspace_context['marketing_person'] ?? 'Marketing Owner'); ?>;
                var healthKey = summary.health_key || 'waiting';
                var healthLabel = summary.health || 'Waiting';
                var plannedEndLabel = summary.planned_end ? formatDateLabel(summary.planned_end) : '';
                var statusParts = [];

                $('#workspaceHeroTitle').text((dfInfo.df_no || 'DF') + ' | Customised DF');
                $('#workspaceHeroCopy').text(summary.health_note || 'Department-wise execution planning and live date control for marketing-led DF meetings.');
                $('#workspaceHealthPill')
                    .attr('class', 'hero-health-pill state-' + healthKey)
                    .html('<i class="fa fa-heartbeat"></i> ' + escapeHtml(healthLabel));
                $('#workspaceChipDf').text(dfInfo.df_no || 'DF');
                $('#workspaceChipPo').text(poNumber || 'PO');
                $('#workspaceChipCompany').text(companyName || 'Company');
                $('#workspaceChipMarketing').text(marketingName || 'Marketing Owner');

                if (machineName) {
                    statusParts.push('<span class="hero-status-pill"><i class="fa fa-cogs"></i> ' + escapeHtml(machineName) + '</span>');
                }
                if (plannedEndLabel) {
                    statusParts.push('<span class="hero-status-pill"><i class="fa fa-calendar"></i> Planned end ' + plannedEndLabel + '</span>');
                }
                statusParts.push('<span class="hero-status-pill"><i class="fa fa-exclamation-triangle"></i> ' + escapeHtml(String(parseInt(metrics.delayed || 0, 10)) + ' delayed') + '</span>');
                statusParts.push('<span class="hero-status-pill"><i class="fa fa-life-ring"></i> ' + escapeHtml(String(parseInt(summary.open_ticket_count || 0, 10)) + ' open tickets') + '</span>');
                $('#workspaceHeroStatus').html(statusParts.join(''));
            }

            function renderFocusList(data) {
                var summary = data.summary || {};
                var focusCards = [
                    {
                        eyebrow: 'Current Focus',
                        row: summary.current_focus,
                        fallback: 'No open task focus is available yet for this DF.'
                    },
                    {
                        eyebrow: 'Next Due Task',
                        row: summary.next_due_task,
                        fallback: 'No due task could be identified right now.'
                    },
                    {
                        eyebrow: 'Highest Delay',
                        row: summary.highest_delay_task,
                        fallback: 'No delayed task found. The DF is currently stable.'
                    }
                ];

                var html = '';
                focusCards.forEach(function (item) {
                    if (item.row) {
                        html += '' +
                            '<div class="focus-card">' +
                                '<span class="focus-eyebrow">' + escapeHtml(item.eyebrow) + '</span>' +
                                '<span class="focus-title">' + escapeHtml(item.row.task_name || 'Task') + '</span>' +
                                '<span class="focus-copy">' + escapeHtml(item.row.latest_update_text || 'No task note available yet.') + '</span>' +
                                '<div class="focus-meta">' +
                                    '<span><i class="fa fa-building-o"></i> ' + escapeHtml(item.row.department || 'Department') + '</span>' +
                                    '<span><i class="fa fa-user-circle-o"></i> ' + escapeHtml(item.row.responsible_person || 'Unassigned') + '</span>' +
                                    '<span><i class="fa fa-calendar"></i> ' + escapeHtml(formatDateLabel(item.row.end_date || '')) + '</span>' +
                                '</div>' +
                            '</div>';
                    } else {
                        html += '' +
                            '<div class="focus-card">' +
                                '<span class="focus-eyebrow">' + escapeHtml(item.eyebrow) + '</span>' +
                                '<span class="focus-title">' + escapeHtml(item.fallback) + '</span>' +
                            '</div>';
                    }
                });

                $('#workspaceFocusList').html(html);
            }

            function renderRecentActivity(data) {
                var activityRows = (data.recent_activity || []).slice(0, 8);
                var html = '';

                if (!activityRows.length) {
                    html = '' +
                        '<div class="activity-card">' +
                            '<span class="activity-label">No Recent Movement</span>' +
                            '<span class="activity-copy">This DF does not have recent timeline movement to show right now.</span>' +
                        '</div>';
                } else {
                    activityRows.forEach(function (row) {
                        var meta = [];
                        if (row.actor) {
                            meta.push('<span><i class="fa fa-user"></i> ' + escapeHtml(row.actor) + '</span>');
                        }
                        if (row.department) {
                            meta.push('<span><i class="fa fa-building-o"></i> ' + escapeHtml(row.department) + '</span>');
                        }
                        if (row.task_name) {
                            meta.push('<span><i class="fa fa-tasks"></i> ' + escapeHtml(row.task_name) + '</span>');
                        }

                        html += '' +
                            '<div class="activity-card">' +
                                '<span class="activity-label">' + escapeHtml(row.label || 'Update') + ' | ' + escapeHtml(formatDateTime(row.time || '')) + '</span>' +
                                '<span class="activity-copy">' + escapeHtml(row.message || 'Activity logged.') + '</span>' +
                                '<div class="activity-meta">' + meta.join('') + '</div>' +
                            '</div>';
                    });
                }

                $('#workspaceActivityList').html(html);
            }

            function buildDepartmentOptions(task) {
                var departmentList = departmentUsers[String(task.department_id || 0)] || [];
                var currentAssignee = String(task.assigned_user || 0);
                var currentOwnerLabel = task.responsible_person || 'Current Assignment';
                var foundCurrent = false;
                var html = '<option value="0">Unassigned</option>';

                departmentList.forEach(function (row) {
                    var userId = String(row.user_id);
                    if (userId === currentAssignee) {
                        foundCurrent = true;
                    }
                    html += '<option value="' + escapeHtml(userId) + '"' + (userId === currentAssignee ? ' selected' : '') + '>' + escapeHtml(row.label) + '</option>';
                });

                if (!foundCurrent && currentAssignee !== '0') {
                    html = '<option value="' + escapeHtml(currentAssignee) + '" selected>' + escapeHtml(currentOwnerLabel) + '</option>' + html;
                }

                return html;
            }

            function normaliseDepartmentGroups() {
                var groups = {};
                var summaries = {};

                (workspaceData.department_summary || []).forEach(function (row) {
                    summaries[String(row.department || '')] = row;
                });

                (workspaceData.plan_vs_actual || []).forEach(function (task) {
                    var departmentName = task.department || 'Unmapped Department';
                    var groupKey = String(task.department_id || 0) + '::' + departmentName;

                    if (!groups[groupKey]) {
                        groups[groupKey] = {
                            department_id: parseInt(task.department_id || 0, 10),
                            department_name: departmentName,
                            summary: summaries[String(departmentName)] || null,
                            tasks: []
                        };
                    }

                    groups[groupKey].tasks.push(task);
                });

                return Object.keys(groups).map(function (key) {
                    var group = groups[key];
                    group.tasks.sort(function (left, right) {
                        var leftOrder = parseInt(left.sortorder || 0, 10);
                        var rightOrder = parseInt(right.sortorder || 0, 10);
                        if (leftOrder === rightOrder) {
                            var leftDate = String(left.end_date || '');
                            var rightDate = String(right.end_date || '');
                            if (leftDate === rightDate) {
                                return parseInt(left.task_record_id || 0, 10) - parseInt(right.task_record_id || 0, 10);
                            }
                            return leftDate.localeCompare(rightDate);
                        }
                        return leftOrder - rightOrder;
                    });
                    return group;
                }).sort(function (left, right) {
                    var leftSummary = left.summary || {};
                    var rightSummary = right.summary || {};
                    var delayedCompare = parseInt(rightSummary.delayed || 0, 10) - parseInt(leftSummary.delayed || 0, 10);
                    if (delayedCompare !== 0) {
                        return delayedCompare;
                    }
                    var openCompare = parseInt(rightSummary.open || 0, 10) - parseInt(leftSummary.open || 0, 10);
                    if (openCompare !== 0) {
                        return openCompare;
                    }
                    return String(left.department_name).localeCompare(String(right.department_name));
                });
            }

            function taskMatchesFilters(task) {
                var showCompleted = $('#workspaceShowCompleted').is(':checked');
                var delayedOnly = $('#workspaceDelayedOnly').is(':checked');

                if (!showCompleted && (task.status_key === 'completed' || task.status_key === 'hold')) {
                    return false;
                }

                if (delayedOnly && !(parseInt(task.delay_days || 0, 10) > 0 || task.status_key === 'delay' || task.status_key === 'approval-delay')) {
                    return false;
                }

                return true;
            }

            function renderDepartmentFilter(groups) {
                var currentValue = $('#workspaceDepartmentFilter').val();
                var html = '<option value="">All Departments</option>';

                groups.forEach(function (group) {
                    html += '<option value="' + escapeHtml(group.department_id) + '">' + escapeHtml(group.department_name) + '</option>';
                });

                var $filter = $('#workspaceDepartmentFilter');
                $filter.html(html);

                if (currentValue && $filter.find('option[value="' + currentValue + '"]').length) {
                    $filter.val(currentValue);
                } else {
                    $filter.val('');
                }
            }

            function renderDepartmentBoard() {
                var groups = normaliseDepartmentGroups();
                renderDepartmentFilter(groups);

                var selectedDepartmentId = $('#workspaceDepartmentFilter').val();
                var html = '';
                var renderedCount = 0;

                groups.forEach(function (group) {
                    if (selectedDepartmentId && String(group.department_id) !== String(selectedDepartmentId)) {
                        return;
                    }

                    var visibleTasks = group.tasks.filter(taskMatchesFilters);
                    if (!visibleTasks.length) {
                        return;
                    }

                    renderedCount++;

                    var summary = group.summary || {};
                    var departmentDelayed = parseInt(summary.delayed || 0, 10);
                    var earliestStart = '';
                    var latestEnd = '';

                    visibleTasks.forEach(function (task) {
                        var startDate = String(task.start_date || '');
                        var endDate = String(task.end_date || '');
                        if (startDate && (earliestStart === '' || startDate < earliestStart)) {
                            earliestStart = startDate;
                        }
                        if (endDate && (latestEnd === '' || endDate > latestEnd)) {
                            latestEnd = endDate;
                        }
                    });

                    html += '' +
                        '<div class="department-card" data-department-id="' + escapeHtml(group.department_id) + '">' +
                            '<div class="department-head">' +
                                '<div>' +
                                    '<h3 class="department-title">' + escapeHtml(group.department_name) + '</h3>' +
                                    '<div class="department-meta">' +
                                        '<span class="department-pill"><i class="fa fa-tasks"></i> ' + escapeHtml((summary.total || group.tasks.length) + ' Total') + '</span>' +
                                        '<span class="department-pill"><i class="fa fa-play-circle"></i> ' + escapeHtml((summary.open || 0) + ' Open') + '</span>' +
                                        '<span class="department-pill warning"><i class="fa fa-check-circle"></i> ' + escapeHtml((summary.completed || 0) + ' Completed') + '</span>' +
                                        '<span class="department-pill' + (departmentDelayed > 0 ? ' danger' : '') + '"><i class="fa fa-exclamation-triangle"></i> ' + escapeHtml(departmentDelayed + ' Delayed') + '</span>' +
                                        '<span class="department-pill"><i class="fa fa-calendar"></i> ' + escapeHtml(formatDateLabel(earliestStart)) + ' to ' + escapeHtml(formatDateLabel(latestEnd)) + '</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="department-actions">' +
                                    '<span class="dirty-badge">0 Unsaved</span>' +
                                    '<button type="button" class="btn btn-primary department-save-btn" disabled><i class="fa fa-save"></i> Save Department</button>' +
                                '</div>' +
                            '</div>' +
                            '<div class="planner-table-wrap">' +
                                '<table class="table planner-table">' +
                                    '<thead>' +
                                        '<tr>' +
                                            '<th style="width: 34%;">Task</th>' +
                                            '<th style="width: 19%;">Owner</th>' +
                                            '<th style="width: 12%;">Start Date</th>' +
                                            '<th style="width: 12%;">Due Date</th>' +
                                            '<th style="width: 11%;">Status</th>' +
                                            '<th style="width: 12%;">Latest Movement</th>' +
                                        '</tr>' +
                                    '</thead>' +
                                    '<tbody>';

                    visibleTasks.forEach(function (task) {
                        var isEditable = !(task.status_key === 'completed' || task.status_key === 'hold');
                        var taskNote = task.latest_update_text || task.remarks || '';
                        var ticketCount = parseInt(task.open_ticket_count || 0, 10);

                        html += '' +
                            '<tr class="planner-row' + (!isEditable ? ' is-locked' : '') + '"' +
                                ' data-task-id="' + escapeHtml(task.task_record_id) + '"' +
                                ' data-original-assigned="' + escapeHtml(task.assigned_user || 0) + '"' +
                                ' data-original-start="' + escapeHtml(task.start_date || '') + '"' +
                                ' data-original-end="' + escapeHtml(task.end_date || '') + '">' +
                                '<td>' +
                                    '<span class="planner-task-name">' + escapeHtml(task.task_name || 'Task') + '</span>' +
                                    '<span class="planner-task-meta">Record #' + escapeHtml(task.task_record_id) + ' | Sort Order ' + escapeHtml(task.sortorder || 0) + '</span>' +
                                    '<span class="planner-task-note">' + escapeHtml(taskNote || 'No coordination note has been logged yet.') + '</span>' +
                                    '<span class="planner-task-meta">' + (ticketCount > 0 ? '<strong>' + escapeHtml(ticketCount) + '</strong> open help ticket(s)' : 'No open help ticket') + '</span>' +
                                '</td>' +
                                '<td>' +
                                    '<select class="form-control planner-inline-control customised-df-assignee" ' + (isEditable ? '' : 'disabled') + '>' +
                                        buildDepartmentOptions(task) +
                                    '</select>' +
                                '</td>' +
                                '<td>' +
                                    '<input type="date" class="form-control planner-inline-control customised-df-start" value="' + escapeHtml(task.start_date || '') + '" ' + (isEditable ? '' : 'disabled') + '>' +
                                '</td>' +
                                '<td>' +
                                    '<input type="date" class="form-control planner-inline-control customised-df-end" value="' + escapeHtml(task.end_date || '') + '" ' + (isEditable ? '' : 'disabled') + '>' +
                                '</td>' +
                                '<td>' +
                                    buildStatusBadge(task.status_key || 'ongoing', task.status_label || 'Ongoing') +
                                    '<span class="planner-updated">' + (parseInt(task.delay_days || 0, 10) > 0 ? escapeHtml(task.delay_days + ' day(s) delayed') : 'On current track') + '</span>' +
                                '</td>' +
                                '<td>' +
                                    '<span class="planner-updated">' + escapeHtml(formatDateTime(task.latest_update_on || '')) + '</span>' +
                                    '<span class="planner-task-meta">Owner: ' + escapeHtml(task.responsible_person || 'Unassigned') + '</span>' +
                                '</td>' +
                            '</tr>';
                    });

                    html += '' +
                                    '</tbody>' +
                                '</table>' +
                            '</div>' +
                        '</div>';
                });

                if (!renderedCount) {
                    html = '<div class="empty-state">No task rows match the current filters. Try changing the department filter or toggling completed rows back on.</div>';
                }

                $('#departmentsBoard').html(html);
                initWorkspaceSelect2();
                updateDirtyIndicators();
            }

            function rowHasChanges($row) {
                if (!$row.length) {
                    return false;
                }

                var currentAssignee = String($row.find('.customised-df-assignee').val() || '0');
                var currentStart = String($row.find('.customised-df-start').val() || '');
                var currentEnd = String($row.find('.customised-df-end').val() || '');

                return currentAssignee !== String($row.data('original-assigned') || '0') ||
                    currentStart !== String($row.data('original-start') || '') ||
                    currentEnd !== String($row.data('original-end') || '');
            }

            function updateDepartmentCardState($card) {
                var dirtyCount = $card.find('.planner-row.is-dirty').length;
                $card.toggleClass('has-dirty', dirtyCount > 0);
                $card.find('.dirty-badge').toggle(dirtyCount > 0).text(dirtyCount + ' Unsaved');
                $card.find('.department-save-btn').prop('disabled', dirtyCount === 0);
            }

            function updateSaveAllButton() {
                var dirtyCount = $('#departmentsBoard .planner-row.is-dirty').length;
                var $button = $('#customised-df-save-all');
                var $caption = $('#workspaceUnsavedCaption');
                $button.prop('disabled', dirtyCount === 0);
                if (dirtyCount === 0) {
                    $button.html('<i class="fa fa-save"></i> Save Changed Tasks');
                    $caption.text('No unsaved task changes.');
                } else {
                    $button.html('<i class="fa fa-save"></i> Save ' + dirtyCount + ' Changed Task' + (dirtyCount === 1 ? '' : 's'));
                    $caption.text(dirtyCount + ' task change' + (dirtyCount === 1 ? '' : 's') + ' ready to save.');
                }
            }

            function updateDirtyIndicators() {
                $('#departmentsBoard .planner-row').each(function () {
                    var $row = $(this);
                    $row.toggleClass('is-dirty', rowHasChanges($row));
                });

                $('#departmentsBoard .department-card').each(function () {
                    updateDepartmentCardState($(this));
                });

                updateSaveAllButton();
            }

            function collectDirtyTasks($scope) {
                var tasks = [];
                $scope.find('.planner-row.is-dirty').each(function () {
                    var $row = $(this);
                    tasks.push({
                        task_id: parseInt($row.data('task-id') || 0, 10),
                        assigned_user: String($row.find('.customised-df-assignee').val() || '0'),
                        start_date: String($row.find('.customised-df-start').val() || ''),
                        end_date: String($row.find('.customised-df-end').val() || '')
                    });
                });
                return tasks;
            }

            function renderWorkspace(data) {
                workspaceData = data || {};
                departmentUsers = workspaceData.department_users || {};
                updateHero(workspaceData);
                updateMetricCards(workspaceData.metrics || {}, workspaceData.summary || {});
                renderFocusList(workspaceData);
                renderRecentActivity(workspaceData);
                renderDepartmentBoard();

                var summary = workspaceData.summary || {};
                var syncText = summary.health_note || 'Customised DF board is ready for department wise planning.';
                var latestActivity = summary.latest_activity && summary.latest_activity.time ? ' Last movement: ' + formatDateTime(summary.latest_activity.time) + '.' : '';
                $('#workspaceSyncMeta').text(syncText + latestActivity);
            }

            function saveTasks(tasks, $triggerButton) {
                if (!tasks.length) {
                    return;
                }

                var originalHtml = $triggerButton ? $triggerButton.html() : '';
                if ($triggerButton && $triggerButton.length) {
                    $triggerButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving');
                }

                setLoader(true, 'Saving Customised DF', 'Please wait while the latest department schedule is being updated.');

                $.ajax({
                    url: saveUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        df_id: dfId,
                        tasks: tasks
                    },
                    success: function (response) {
                        if (response.success) {
                            showNotice('success', response.message || 'Customised DF dates saved successfully.');
                            loadWorkspaceData(true);
                        } else {
                            showNotice('error', response.message || 'The customised DF plan could not be saved.');
                        }
                    },
                    error: function (xhr) {
                        var message = 'The customised DF plan could not be saved right now.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showNotice('error', message);
                    },
                    complete: function () {
                        setLoader(false);
                        if ($triggerButton && $triggerButton.length) {
                            $triggerButton.html(originalHtml);
                            updateDirtyIndicators();
                        }
                    }
                });
            }

            function loadWorkspaceData(isRefresh) {
                setLoader(true, isRefresh ? 'Refreshing Customised DF' : 'Loading Customised DF', isRefresh ? 'Please wait while the latest DF task board is being refreshed.' : 'Please wait while the latest DF planning board is being prepared.');

                $.ajax({
                    url: detailUrl,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        renderWorkspace(response || {});
                    },
                    error: function () {
                        showNotice('error', 'Unable to load the customised DF workspace right now. Please refresh the page and try again.');
                        $('#departmentsBoard').html('<div class="empty-state">The task planner could not be loaded right now.</div>');
                    },
                    complete: function () {
                        setLoader(false);
                    }
                });
            }

            $('#departmentsBoard').on('change input', '.customised-df-assignee, .customised-df-start, .customised-df-end', function () {
                var $row = $(this).closest('.planner-row');
                $row.toggleClass('is-dirty', rowHasChanges($row));
                updateDepartmentCardState($row.closest('.department-card'));
                updateSaveAllButton();
            });

            $('#departmentsBoard').on('click', '.department-save-btn', function () {
                var $button = $(this);
                var $card = $button.closest('.department-card');
                var tasks = collectDirtyTasks($card);
                if (!tasks.length) {
                    return;
                }
                saveTasks(tasks, $button);
            });

            $('#customised-df-save-all').on('click', function () {
                var $button = $(this);
                var tasks = collectDirtyTasks($('#departmentsBoard'));
                if (!tasks.length) {
                    return;
                }
                saveTasks(tasks, $button);
            });

            $('#customised-df-refresh').on('click', function () {
                loadWorkspaceData(true);
            });

            $('#workspaceDepartmentFilter, #workspaceShowCompleted, #workspaceDelayedOnly').on('change', function () {
                if (workspaceData) {
                    renderDepartmentBoard();
                }
            });

            loadWorkspaceData(false);
        });
    </script>
</body>
</html>
