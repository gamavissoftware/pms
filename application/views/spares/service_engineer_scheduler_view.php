<?php
defined('BASEPATH') or exit('No direct script access allowed');
$schedule_feedback = $this->session->flashdata('schedule_feedback');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Visit Assignment Scheduler</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        :root {
            --scheduler-primary: #0f766e;
            --scheduler-primary-dark: #115e59;
            --scheduler-accent: #f59e0b;
            --scheduler-ink: #17324d;
            --scheduler-muted: #64748b;
            --scheduler-border: #dce6f2;
            --scheduler-soft: #edf6ff;
            --scheduler-card: #ffffff;
            --scheduler-bg: #eef5fb;
            --scheduler-success: #34a853;
            --scheduler-warning: #f59e0b;
            --scheduler-danger: #ef4444;
            --scheduler-info: #2563eb;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.12), transparent 24%),
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.12), transparent 28%),
                var(--scheduler-bg);
            color: var(--scheduler-ink);
        }

        .wrapper {
            padding-top: 82px;
            padding-bottom: 28px;
        }

        .card-box {
            background: var(--scheduler-card);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(220, 230, 242, 0.95);
            margin-bottom: 22px;
        }

        .page-title-box {
            margin-bottom: 18px;
        }

        .scheduler-alert {
            border-radius: 14px;
            border: none;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            margin-bottom: 18px;
        }

        .scheduler-hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0f766e 0%, #124e78 55%, #1d4ed8 100%);
            color: #fff;
            padding: 30px;
        }

        .scheduler-hero::after {
            content: "";
            position: absolute;
            right: -70px;
            top: -50px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .hero-layout {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 600;
            opacity: 0.86;
            margin-bottom: 12px;
        }

        .scheduler-hero h2 {
            margin: 0 0 10px;
            font-size: 30px;
            line-height: 1.2;
            font-weight: 700;
        }

        .scheduler-hero p {
            margin: 0;
            max-width: 740px;
            color: rgba(255, 255, 255, 0.92);
            font-size: 14px;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hero-actions .btn {
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 600;
            box-shadow: none;
        }

        .hero-actions .btn-primary {
            background: #fff;
            border-color: #fff;
            color: var(--scheduler-primary-dark);
        }

        .hero-actions .btn-default {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.22);
            color: #fff;
        }

        .hero-chip-row {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 12px;
            font-weight: 500;
        }

        .scheduler-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .scheduler-kpi-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(220, 230, 242, 0.96);
            border-radius: 18px;
            padding: 18px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.07);
            min-height: 138px;
        }

        .scheduler-kpi-label {
            color: var(--scheduler-muted);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.16em;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .scheduler-kpi-value {
            font-size: 34px;
            font-weight: 700;
            color: var(--scheduler-ink);
            line-height: 1;
            margin-bottom: 10px;
        }

        .scheduler-kpi-meta {
            color: var(--scheduler-muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .header-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .header-title {
            margin: 0;
            color: var(--scheduler-ink);
            font-size: 17px;
            font-weight: 700;
        }

        .section-subtitle {
            color: var(--scheduler-muted);
            font-size: 13px;
            line-height: 1.7;
            margin: 6px 0 0;
        }

        .calendar-card {
            min-height: 720px;
        }

        .calendar-legend {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--scheduler-muted);
            font-size: 12px;
            font-weight: 500;
        }

        .legend-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            display: inline-block;
        }

        .legend-dot.legend-scheduled { background: var(--scheduler-info); }
        .legend-dot.legend-onsite { background: var(--scheduler-warning); }
        .legend-dot.legend-completed { background: var(--scheduler-success); }
        .legend-dot.legend-long { background: #0f766e; }

        .calendar-note {
            font-size: 12px;
            color: var(--scheduler-muted);
            margin-bottom: 12px;
        }

        .insight-card {
            min-height: 340px;
        }

        .insight-list {
            display: grid;
            gap: 14px;
        }

        .workload-item {
            border: 1px solid var(--scheduler-border);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 14px;
            padding: 14px;
        }

        .workload-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 8px;
        }

        .workload-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--scheduler-ink);
        }

        .workload-meta {
            font-size: 11px;
            color: var(--scheduler-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .workload-stats {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .workload-stat {
            font-size: 12px;
            color: var(--scheduler-muted);
            background: #eef5fb;
            border-radius: 999px;
            padding: 6px 10px;
        }

        .workload-bar {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #e4eef8;
            overflow: hidden;
        }

        .workload-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--scheduler-primary), #2d8f88);
        }

        .upcoming-list {
            display: grid;
            gap: 12px;
        }

        .upcoming-item {
            border: 1px solid var(--scheduler-border);
            border-radius: 14px;
            padding: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }

        .upcoming-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 8px;
        }

        .upcoming-order {
            font-size: 14px;
            font-weight: 700;
            color: var(--scheduler-ink);
        }

        .upcoming-date {
            font-size: 12px;
            color: var(--scheduler-primary-dark);
            font-weight: 600;
            background: rgba(15, 118, 110, 0.1);
            border-radius: 999px;
            padding: 6px 10px;
            white-space: nowrap;
        }

        .upcoming-meta {
            color: var(--scheduler-muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .empty-state {
            border: 1px dashed var(--scheduler-border);
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            color: var(--scheduler-muted);
            font-size: 13px;
            background: #fbfdff;
        }

        .history-card {
            margin-top: 4px;
        }

        .history-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .history-counter {
            font-size: 13px;
            color: var(--scheduler-muted);
            background: #eef5fb;
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 600;
        }

        .filter-toolbar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .filter-control label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--scheduler-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 6px;
        }

        .filter-control .form-control {
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--scheduler-border);
            box-shadow: none;
        }

        .filter-actions {
            display: flex;
            align-items: flex-end;
        }

        .filter-actions .btn {
            width: 100%;
            height: 40px;
            border-radius: 12px;
            font-weight: 600;
        }

        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
        }

        #visitTable_wrapper .row {
            margin: 0;
        }

        #visitTable {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        #visitTable thead th {
            border: none;
            background: transparent;
            color: var(--scheduler-muted);
            text-transform: uppercase;
            letter-spacing: 0.11em;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 12px;
        }

        #visitTable tbody td {
            background: #fff;
            border-top: 1px solid var(--scheduler-border);
            border-bottom: 1px solid var(--scheduler-border);
            padding: 14px 12px;
            vertical-align: top;
            font-size: 12px;
        }

        #visitTable tbody td:first-child {
            border-left: 1px solid var(--scheduler-border);
            border-top-left-radius: 14px;
            border-bottom-left-radius: 14px;
        }

        #visitTable tbody td:last-child {
            border-right: 1px solid var(--scheduler-border);
            border-top-right-radius: 14px;
            border-bottom-right-radius: 14px;
        }

        #visitTable tbody tr:hover td {
            background: #f8fbff;
        }

        .order-code {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 700;
            color: var(--scheduler-ink);
            margin-bottom: 6px;
        }

        .order-code i {
            color: var(--scheduler-primary);
        }

        .customer-name {
            color: var(--scheduler-muted);
            line-height: 1.7;
        }

        .engineer-pill,
        .type-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 12px;
            background: #eef5fb;
            color: var(--scheduler-ink);
            font-weight: 600;
        }

        .type-pill {
            background: rgba(245, 158, 11, 0.12);
            color: #a16207;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .window-range {
            font-size: 13px;
            font-weight: 600;
            color: var(--scheduler-ink);
            margin-bottom: 6px;
        }

        .window-meta {
            color: var(--scheduler-muted);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .status-pill.status-scheduled {
            background: rgba(37, 99, 235, 0.12);
            color: var(--scheduler-info);
        }

        .status-pill.status-on-site {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
        }

        .status-pill.status-completed {
            background: rgba(52, 168, 83, 0.14);
            color: #15803d;
        }

        .status-pill.status-cancelled {
            background: rgba(239, 68, 68, 0.14);
            color: #b91c1c;
        }

        .status-pill.status-mixed {
            background: rgba(15, 118, 110, 0.12);
            color: var(--scheduler-primary);
        }

        .note-preview {
            color: var(--scheduler-muted);
            line-height: 1.7;
            max-width: 280px;
        }

        .dataTables_paginate {
            padding-top: 12px;
        }

        .dataTables_paginate .pagination > li > a,
        .dataTables_paginate .pagination > li > span {
            border-radius: 999px !important;
            margin: 0 3px;
            border: none;
            color: var(--scheduler-ink);
        }

        .dataTables_paginate .pagination > .active > a,
        .dataTables_paginate .pagination > .active > span {
            background: var(--scheduler-primary);
            color: #fff;
        }

        .select2-container .select2-selection--single,
        .select2-container .select2-selection--multiple {
            border: 1px solid var(--scheduler-border) !important;
            border-radius: 12px !important;
            min-height: 40px;
            padding-top: 3px;
            box-shadow: none !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            color: var(--scheduler-ink);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--multiple {
            padding: 4px 8px 6px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: rgba(15, 118, 110, 0.12);
            border: none;
            color: var(--scheduler-primary-dark);
            border-radius: 999px;
            padding: 4px 10px;
            margin-top: 4px;
        }

        .engineer-selection-error .select2-selection {
            border-color: var(--scheduler-danger) !important;
            background: #fff6f6 !important;
        }

        .selected-engineer-note {
            margin-top: 8px;
            color: var(--scheduler-muted);
            font-size: 12px;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--scheduler-primary) 0%, #1d4ed8 100%);
            color: #fff;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            overflow: hidden;
        }

        .modal-title {
            color: #fff;
            font-weight: 600;
        }

        .modal-body label.error {
            color: var(--scheduler-danger);
            font-size: 11px;
            margin-top: 5px;
            font-weight: 500;
        }

        .modal-body .form-control.error {
            border: 1px solid var(--scheduler-danger) !important;
            background-color: #fff8f8 !important;
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.4em;
            gap: 12px;
            flex-wrap: wrap;
        }

        .fc .fc-toolbar-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--scheduler-ink);
        }

        .fc .fc-button {
            background: #eff6ff;
            border: none;
            color: var(--scheduler-ink);
            text-transform: capitalize;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.55em 0.95em;
            box-shadow: none !important;
        }

        .fc .fc-button:hover,
        .fc .fc-button.fc-button-active,
        .fc .fc-button:focus {
            background: var(--scheduler-primary);
            color: #fff;
        }

        .fc .fc-daygrid-event,
        .fc .fc-daygrid-dot-event {
            white-space: normal;
            border-radius: 10px;
            padding: 3px 6px;
            font-weight: 600;
        }

        .fc .fc-daygrid-dot-event.event-multi-day {
            background: rgba(15, 118, 110, 0.12) !important;
            border: 1px solid rgba(15, 118, 110, 0.18) !important;
        }

        .fc .fc-event.status-completed {
            background: rgba(52, 168, 83, 0.14) !important;
            border-color: rgba(52, 168, 83, 0.18) !important;
        }

        .fc .fc-event.status-on-site {
            background: rgba(245, 158, 11, 0.16) !important;
            border-color: rgba(245, 158, 11, 0.22) !important;
        }

        .fc .fc-event.status-cancelled {
            background: rgba(239, 68, 68, 0.14) !important;
            border-color: rgba(239, 68, 68, 0.18) !important;
        }

        .fc .fc-event.status-scheduled,
        .fc .fc-event.event-single-day {
            background: rgba(37, 99, 235, 0.14) !important;
            border-color: rgba(37, 99, 235, 0.18) !important;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #e5edf5;
        }

        @media (max-width: 991px) {
            .scheduler-hero h2 {
                font-size: 24px;
            }

            .calendar-card,
            .insight-card {
                min-height: auto;
            }
        }

        @media (max-width: 767px) {
            .wrapper {
                padding-top: 74px;
            }

            .card-box {
                padding: 18px;
                border-radius: 16px;
            }

            .scheduler-hero {
                padding: 22px;
            }

            .hero-actions,
            .history-top,
            .header-block {
                width: 100%;
            }

            .hero-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">
            <?php if (!empty($schedule_feedback)) : ?>
                <?php
                $alert_type = isset($schedule_feedback['type']) ? $schedule_feedback['type'] : 'info';
                $alert_class = 'alert-info';
                if ($alert_type === 'success') {
                    $alert_class = 'alert-success';
                } elseif ($alert_type === 'warning') {
                    $alert_class = 'alert-warning';
                } elseif ($alert_type === 'error') {
                    $alert_class = 'alert-danger';
                }
                ?>
                <div class="alert <?php echo $alert_class; ?> scheduler-alert">
                    <strong><?php echo htmlspecialchars($schedule_feedback['title'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                    <?php echo htmlspecialchars($schedule_feedback['message'], ENT_QUOTES, 'UTF-8'); ?>
                    <?php if (!empty($schedule_feedback['details']) && is_array($schedule_feedback['details'])) : ?>
                        <ul style="margin-top: 10px; margin-bottom: 0;">
                            <?php foreach ($schedule_feedback['details'] as $detail) : ?>
                                <li><?php echo htmlspecialchars($detail, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="card-box scheduler-hero">
                <div class="hero-layout">
                    <div>
                        <div class="hero-eyebrow">
                            <i class="fa fa-map-marker"></i>
                            Scheduler
                        </div>
                        <h2 style="color:#fff;">Visit Assignment Scheduler</h2>
                        <p>Plan, assign, and track deployments across Service and Automation teams.</p>
                    </div>

                    <div class="hero-actions">
                        <button class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#scheduleModal">
                            <i class="fa fa-plus-circle"></i> New Deployment
                        </button>
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list" class="btn btn-default waves-effect waves-light">
                            <i class="fa fa-clipboard"></i> Visit MOM List
                        </a>
                        <a href="<?php echo page_url; ?>Masters/manage_deployment_types" class="btn btn-default waves-effect waves-light">
                            <i class="fa fa-sliders"></i> Deployment Types
                        </a>
                    </div>
                </div>

                <div class="hero-chip-row">
                    <span class="hero-chip"><i class="fa fa-users"></i> <?php echo count($engineers); ?> Team Members</span>
                    <span class="hero-chip"><i class="fa fa-file-text-o"></i> <?php echo (int) $scheduler_kpi['won_orders_ready']; ?> Orders Ready</span>
                </div>
            </div>

          <!--   <div class="scheduler-kpi-grid">
                <div class="scheduler-kpi-card">
                    <div class="scheduler-kpi-label">Active Deployments</div>
                    <div class="scheduler-kpi-value"><?php echo (int) $scheduler_kpi['active_deployments']; ?></div>
                    <div class="scheduler-kpi-meta">Open</div>
                </div>
                <div class="scheduler-kpi-card">
                    <div class="scheduler-kpi-label">Engineers Busy Today</div>
                    <div class="scheduler-kpi-value"><?php echo (int) $scheduler_kpi['engineers_busy_today']; ?></div>
                    <div class="scheduler-kpi-meta">Today</div>
                </div>
                <div class="scheduler-kpi-card">
                    <div class="scheduler-kpi-label">Multi-Engineer Orders</div>
                    <div class="scheduler-kpi-value"><?php echo (int) $scheduler_kpi['multi_engineer_orders']; ?></div>
                    <div class="scheduler-kpi-meta">Shared</div>
                </div>
                <div class="scheduler-kpi-card">
                    <div class="scheduler-kpi-label">Long Deployments</div>
                    <div class="scheduler-kpi-value"><?php echo (int) $scheduler_kpi['long_deployments']; ?></div>
                    <div class="scheduler-kpi-meta">Multi-day</div>
                </div>
                <div class="scheduler-kpi-card">
                    <div class="scheduler-kpi-label">Won Orders Ready</div>
                    <div class="scheduler-kpi-value"><?php echo (int) $scheduler_kpi['won_orders_ready']; ?></div>
                    <div class="scheduler-kpi-meta">Queue</div>
                </div>
            </div> -->

            <div class="row">
                <div class="col-xl-9">
                    <div class="card-box calendar-card">
                        <div class="header-block">
                            <div>
                                <h4 class="header-title"><i class="fa fa-calendar m-r-5 text-primary"></i> Deployment Calendar</h4>
                                <p class="section-subtitle">Long plans show as bullets.</p>
                            </div>
                        </div>

                        <div class="calendar-legend">
                            <span class="legend-item"><span class="legend-dot legend-scheduled"></span> Scheduled</span>
                            <span class="legend-item"><span class="legend-dot legend-onsite"></span> On-Site</span>
                            <span class="legend-item"><span class="legend-dot legend-completed"></span> Completed</span>
                            <span class="legend-item"><span class="legend-dot legend-long"></span> Multi-day bullet</span>
                        </div>

                        <div class="calendar-note">Click any item for details.</div>
                        <div id="calendar"></div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="card-box insight-card">
                        <div class="header-block">
                            <div>
                                <h4 class="header-title"><i class="fa fa-bar-chart m-r-5 text-primary"></i> Engineer Load Board</h4>
                                <p class="section-subtitle">Current load.</p>
                            </div>
                        </div>

                        <?php if (!empty($engineer_workload)) : ?>
                            <div class="insight-list">
                                <?php foreach ($engineer_workload as $load_row) : ?>
                                    <div class="workload-item">
                                        <div class="workload-head">
                                            <div class="workload-name"><?php echo htmlspecialchars($load_row['engineer_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="workload-meta">Load <?php echo (int) $load_row['load_percent']; ?>%</div>
                                        </div>
                                        <div class="workload-stats">
                                            <span class="workload-stat"><?php echo (int) $load_row['active_assignments']; ?> active</span>
                                            <span class="workload-stat"><?php echo (int) $load_row['booked_days']; ?> booked days</span>
                                            <span class="workload-stat"><?php echo (int) $load_row['today_assignments']; ?> today</span>
                                        </div>
                                        <div class="workload-bar">
                                            <div class="workload-fill" style="width: <?php echo (int) $load_row['load_percent']; ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="empty-state">
                                No active load.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <div class="card-box history-card">
                <div class="history-top">
                    <div>
                        <h4 class="header-title"><i class="fa fa-list-alt m-r-5 text-primary"></i> Deployment History</h4>
                        <p class="section-subtitle">Quick filters.</p>
                    </div>
                    <div class="history-counter" id="historyCounter"><?php echo count($all_visits); ?> shown</div>
                </div>

                <div class="filter-toolbar">
                    <div class="filter-control">
                        <label for="historySearch">Keyword Search</label>
                        <input type="text" id="historySearch" class="form-control" placeholder="Search">
                    </div>
                    <div class="filter-control">
                        <label for="historyEngineerFilter">Assignee</label>
                        <select id="historyEngineerFilter" class="form-control history-filter-select">
                            <option value="">All Assignees</option>
                            <?php foreach ($engineers as $engineer) : ?>
                                <?php $engineer_team = (int) $engineer->department_id === 14 ? 'Automation' : 'Service'; ?>
                                <option value="<?php echo (int) $engineer->user_id; ?>">
                                    <?php echo htmlspecialchars(trim($engineer->first_name . ' ' . $engineer->last_name) . ' (' . $engineer_team . ')', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-control">
                        <label for="historyTypeFilter">Deployment Type</label>
                        <select id="historyTypeFilter" class="form-control history-filter-select">
                            <option value="">All Types</option>
                            <?php foreach ($deployment_types as $type) : ?>
                                <option value="<?php echo htmlspecialchars($type->deployment_type_value, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($type->deployment_type_label, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-control">
                        <label for="historyStatusFilter">Status</label>
                        <select id="historyStatusFilter" class="form-control history-filter-select">
                            <option value="">All Statuses</option>
                            <?php foreach ($history_statuses as $history_status) : ?>
                                <option value="<?php echo htmlspecialchars($history_status, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($history_status, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-control">
                        <label for="historyDurationFilter">Duration</label>
                        <select id="historyDurationFilter" class="form-control history-filter-select">
                            <option value="">All Durations</option>
                            <option value="single">Single Day</option>
                            <option value="multi">Multi Day</option>
                        </select>
                    </div>
                    <div class="filter-control">
                        <label for="historyStartFrom">Start From</label>
                        <input type="date" id="historyStartFrom" class="form-control">
                    </div>
                    <div class="filter-control">
                        <label for="historyEndTo">End To</label>
                        <input type="date" id="historyEndTo" class="form-control">
                    </div>
                    <div class="filter-actions">
                        <button type="button" id="resetHistoryFilters" class="btn btn-default waves-effect">Clear Filters</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover m-0" id="visitTable">
                        <thead>
                            <tr>
                                <th>Order &amp; Customer</th>
                                <th>Assigned To</th>
                                <th>Type</th>
                                <th>Window</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($all_visits)) : ?>
                                <?php foreach ($all_visits as $visit) : ?>
                                    <?php
                                    $status_slug = !empty($visit->status_slug) ? $visit->status_slug : strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $visit->visit_status), '-'));
                                    ?>
                                    <tr
                                        data-engineer-id="<?php echo (int) $visit->engineer_id; ?>"
                                        data-type="<?php echo htmlspecialchars($visit->visit_type, ENT_QUOTES, 'UTF-8'); ?>"
                                        data-status="<?php echo htmlspecialchars($visit->visit_status, ENT_QUOTES, 'UTF-8'); ?>"
                                        data-duration-days="<?php echo (int) $visit->duration_days; ?>"
                                        data-start="<?php echo htmlspecialchars($visit->start_date, ENT_QUOTES, 'UTF-8'); ?>"
                                        data-end="<?php echo htmlspecialchars($visit->end_date, ENT_QUOTES, 'UTF-8'); ?>"
                                    >
                                        <td>
                                            <div class="order-code"><i class="fa fa-file-text-o"></i><?php echo htmlspecialchars($visit->op_no, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="customer-name"><?php echo htmlspecialchars($visit->customer_name, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php if (!empty($visit->remarks)) : ?>
                                                <span style="display:none;"><?php echo htmlspecialchars($visit->remarks, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="engineer-pill"><i class="fa fa-user-circle-o"></i><?php echo htmlspecialchars($visit->engineer_full_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </td>
                                        <td>
                                            <span class="type-pill"><?php echo htmlspecialchars($visit->visit_type, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </td>
                                        <td data-order="<?php echo htmlspecialchars($visit->start_date, ENT_QUOTES, 'UTF-8'); ?>">
                                            <div class="window-range"><?php echo date('d M Y', strtotime($visit->start_date)); ?> - <?php echo date('d M Y', strtotime($visit->end_date)); ?></div>
                                            <div class="window-meta"><?php echo htmlspecialchars($visit->duration_label, ENT_QUOTES, 'UTF-8'); ?></div>
                                        </td>
                                        <td>
                                            <span class="status-pill status-<?php echo htmlspecialchars($status_slug, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($visit->visit_status, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="scheduleModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="visitForm" action="<?php echo page_url; ?>ServiceLeads/save_visit_plan" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.9;">&times;</button>
                        <h4 class="modal-title">Schedule Deployment</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Order <span class="text-danger">*</span></label>
                                <select name="opportunity_id" id="opportunity_id" class="form-control modal-select" required>
                                    <option value="">Search Won Orders...</option>
                                    <?php foreach ($pending_orders as $pending_order) : ?>
                                        <option value="<?php echo (int) $pending_order->opportunity_id; ?>">
                                            <?php echo htmlspecialchars($pending_order->op_no . ' | ' . $pending_order->company_name, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select name="visit_type" id="visit_type" class="form-control modal-select" required>
                                    <?php foreach ($deployment_types as $type) : ?>
                                        <option value="<?php echo htmlspecialchars($type->deployment_type_value, ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($type->deployment_type_label, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Assignees <span class="text-danger">*</span></label>
                            <select name="engineer_ids[]" id="engineer_ids" class="form-control" multiple>
                                <?php foreach ($engineers as $engineer) : ?>
                                    <?php $engineer_team = (int) $engineer->department_id === 14 ? 'Automation' : 'Service'; ?>
                                    <option value="<?php echo (int) $engineer->user_id; ?>">
                                        <?php echo htmlspecialchars(trim($engineer->first_name . ' ' . $engineer->last_name) . ' (' . $engineer_team . ')', ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="selected-engineer-note" id="selectedEngineerCount">Select one or more team members.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Start <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>End <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Optional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Schedule Deployment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            var scheduleFeedback = <?php echo json_encode($schedule_feedback); ?>;
            var historyTableId = 'visitTable';

            function escapeHtml(value) {
                return $('<div>').text(value || '').html();
            }

            $('.modal-select').select2({
                width: '100%',
                dropdownParent: $('#scheduleModal')
            });

            $('#engineer_ids').select2({
                width: '100%',
                dropdownParent: $('#scheduleModal'),
                closeOnSelect: false,
                placeholder: 'Select one or more team members'
            });

            $('.history-filter-select').select2({
                width: '100%'
            });

            function updateEngineerCount() {
                var selectedEngineers = $('#engineer_ids').val() || [];
                var message = 'Select one or more team members.';

                if (selectedEngineers.length > 0) {
                    message = selectedEngineers.length + ' selected';
                }

                $('#selectedEngineerCount').text(message);
            }

            function toggleEngineerError(showError) {
                var $container = $('#engineer_ids').next('.select2-container');
                if (showError) {
                    $container.addClass('engineer-selection-error');
                } else {
                    $container.removeClass('engineer-selection-error');
                }
            }

            updateEngineerCount();

            $('#engineer_ids').on('change', function() {
                updateEngineerCount();
                toggleEngineerError(false);
            });

            $('#start_date').on('change', function() {
                var startDate = $(this).val();
                $('#end_date').attr('min', startDate);

                if ($('#end_date').val() && $('#end_date').val() < startDate) {
                    $('#end_date').val(startDate);
                }
            }).trigger('change');

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 690,
                fixedWeekCount: false,
                dayMaxEvents: 3,
                navLinks: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: 'Today',
                    dayGridMonth: 'Month',
                    timeGridWeek: 'Week',
                    listWeek: 'Agenda'
                },
                events: "<?php echo page_url; ?>ServiceLeads/get_scheduled_events",
                eventDidMount: function(info) {
                    var props = info.event.extendedProps || {};
                    var tooltipText = props.engineer_name + ' | ' + props.op_no + ' | ' + props.customer_name;
                    if (props.is_long_deployment) {
                        tooltipText += ' | ' + props.duration_label;
                    }
                    info.el.setAttribute('title', tooltipText);
                },
                eventClick: function(info) {
                    var props = info.event.extendedProps || {};
                    var notes = props.remarks ? props.remarks : 'No internal notes shared.';

                    Swal.fire({
                        title: props.engineer_name,
                        html:
                            '<div style="text-align:left; line-height:1.8;">' +
                                '<strong>Order:</strong> ' + escapeHtml(props.op_no) + '<br>' +
                                '<strong>Customer:</strong> ' + escapeHtml(props.customer_name) + '<br>' +
                                '<strong>Deployment Type:</strong> ' + escapeHtml(props.visit_type) + '<br>' +
                                '<strong>Status:</strong> ' + escapeHtml(props.visit_status) + '<br>' +
                                '<strong>Duration:</strong> ' + escapeHtml(props.duration_label) + '<br>' +
                                '<strong>Window:</strong> ' + escapeHtml(props.start_date) + ' to ' + escapeHtml(props.end_date) + '<br>' +
                                '<strong>Notes:</strong> ' + escapeHtml(notes) +
                            '</div>',
                        icon: 'info',
                        confirmButtonText: 'Close'
                    });
                }
            });
            calendar.render();

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== historyTableId) {
                    return true;
                }

                var rowNode = settings.aoData[dataIndex].nTr;
                var $row = $(rowNode);

                var selectedEngineer = $('#historyEngineerFilter').val();
                var selectedType = $('#historyTypeFilter').val();
                var selectedStatus = $('#historyStatusFilter').val();
                var selectedDuration = $('#historyDurationFilter').val();
                var startFrom = $('#historyStartFrom').val();
                var endTo = $('#historyEndTo').val();

                var rowEngineerId = String($row.data('engineer-id') || '');
                var rowType = String($row.data('type') || '');
                var rowStatus = String($row.data('status') || '');
                var rowDuration = parseInt($row.data('duration-days'), 10) || 1;
                var rowStart = String($row.data('start') || '');
                var rowEnd = String($row.data('end') || '');

                if (selectedEngineer && rowEngineerId !== String(selectedEngineer)) {
                    return false;
                }

                if (selectedType && rowType !== selectedType) {
                    return false;
                }

                if (selectedStatus && rowStatus !== selectedStatus) {
                    return false;
                }

                if (selectedDuration === 'single' && rowDuration !== 1) {
                    return false;
                }

                if (selectedDuration === 'multi' && rowDuration <= 1) {
                    return false;
                }

                if (startFrom && rowEnd < startFrom) {
                    return false;
                }

                if (endTo && rowStart > endTo) {
                    return false;
                }

                return true;
            });

            var visitTable = $('#visitTable').DataTable({
                pageLength: 10,
                dom: 'rtip',
                order: [[3, 'desc']],
                language: {
                    emptyTable: 'No deployments found.',
                    zeroRecords: 'No matching deployments.',
                    paginate: {
                        previous: '<i class="fa fa-angle-left"></i>',
                        next: '<i class="fa fa-angle-right"></i>'
                    }
                }
            });

            function updateHistoryCounter() {
                var filteredCount = visitTable.rows({ search: 'applied' }).count();
                $('#historyCounter').text(filteredCount + ' shown');
            }

            updateHistoryCounter();

            $('#visitTable').on('draw.dt', function() {
                updateHistoryCounter();
            });

            $('#historySearch').on('keyup change', function() {
                visitTable.search(this.value).draw();
            });

            $('#historyEngineerFilter, #historyTypeFilter, #historyStatusFilter, #historyDurationFilter, #historyStartFrom, #historyEndTo').on('change', function() {
                visitTable.draw();
            });

            $('#resetHistoryFilters').on('click', function() {
                $('#historySearch').val('');
                $('#historyEngineerFilter').val('').trigger('change');
                $('#historyTypeFilter').val('').trigger('change');
                $('#historyStatusFilter').val('').trigger('change');
                $('#historyDurationFilter').val('').trigger('change');
                $('#historyStartFrom').val('');
                $('#historyEndTo').val('');
                visitTable.search('').draw();
            });

            $('#visitForm').validate({
                ignore: [],
                rules: {
                    opportunity_id: 'required',
                    visit_type: 'required',
                    start_date: 'required',
                    end_date: 'required'
                },
                highlight: function(element) {
                    $(element).addClass('error');
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').addClass('error');
                    }
                },
                unhighlight: function(element) {
                    $(element).removeClass('error');
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2-container').removeClass('error');
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    var selectedEngineers = $('#engineer_ids').val() || [];
                    var startDate = $('#start_date').val();
                    var endDate = $('#end_date').val();

                    if (selectedEngineers.length === 0) {
                        toggleEngineerError(true);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Select engineers',
                            text: 'Please choose at least one engineer for deployment.'
                        });
                        return false;
                    }

                    if (startDate && endDate && endDate < startDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid date range',
                            text: 'End date cannot be earlier than the start date.'
                        });
                        return false;
                    }

                    Swal.fire({
                        title: 'Scheduling deployment...',
                        text: 'Checking conflicts and saving the plan.',
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });

            if (scheduleFeedback && scheduleFeedback.title) {
                setTimeout(function() {
                    var feedbackHtml = '<div style="text-align:left; line-height:1.8;">' + escapeHtml(scheduleFeedback.message) + '</div>';
                    if (scheduleFeedback.details && scheduleFeedback.details.length) {
                        feedbackHtml += '<ul style="text-align:left; margin-top:12px;">';
                        for (var i = 0; i < scheduleFeedback.details.length; i++) {
                            feedbackHtml += '<li>' + escapeHtml(scheduleFeedback.details[i]) + '</li>';
                        }
                        feedbackHtml += '</ul>';
                    }

                    var alertIcon = 'info';
                    if (scheduleFeedback.type === 'success') {
                        alertIcon = 'success';
                    } else if (scheduleFeedback.type === 'warning') {
                        alertIcon = 'warning';
                    } else if (scheduleFeedback.type === 'error') {
                        alertIcon = 'error';
                    }

                    Swal.fire({
                        icon: alertIcon,
                        title: scheduleFeedback.title,
                        html: feedbackHtml,
                        confirmButtonText: 'Okay'
                    });
                }, 250);
            }
        });
    </script>
</body>
</html>
