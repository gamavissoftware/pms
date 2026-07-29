<?php
defined('BASEPATH') or exit('No direct script access allowed');

$status_map = isset($status_map) && is_array($status_map) ? $status_map : [];
$engineers = isset($engineers) && is_array($engineers) ? $engineers : [];
$active_status_slug = !empty($active_status_slug) ? $active_status_slug : '';
$active_engineer_id = !empty($active_engineer_id) ? (int) $active_engineer_id : 0;
$events_url = !empty($events_url) ? $events_url : page_url . 'ServiceLeads/get_assignment_overview_events';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Visit Assignments</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        :root {
            --assignment-primary: #1f5f8b;
            --assignment-primary-dark: #184b6d;
            --assignment-ink: #1f2d3d;
            --assignment-muted: #64748b;
            --assignment-border: #d9e2ec;
            --assignment-card: #ffffff;
            --assignment-bg: #f4f7fb;
            --assignment-info: #2563eb;
            --assignment-success: #34a853;
            --assignment-warning: #f59e0b;
            --assignment-danger: #ef4444;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--assignment-bg);
            color: var(--assignment-ink);
        }

        .wrapper {
            padding-top: 78px;
            padding-bottom: 24px;
        }

        .card-box {
            background: var(--assignment-card);
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
            border: 1px solid var(--assignment-border);
            margin-bottom: 18px;
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .page-kicker {
            display: inline-block;
            margin-bottom: 6px;
            color: var(--assignment-primary);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .page-title {
            margin: 0 0 6px;
            color: var(--assignment-ink);
            font-size: 26px;
            font-weight: 700;
            line-height: 1.18;
        }

        .page-copy {
            color: var(--assignment-muted);
            font-size: 13px;
            line-height: 1.6;
            max-width: 620px;
            margin: 0;
        }

        .page-tools {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .page-mode-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--assignment-border);
            background: #f8fbff;
            color: var(--assignment-primary-dark);
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 12px;
            font-weight: 600;
        }

        .filter-shell {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            align-items: end;
            margin-bottom: 18px;
            padding: 16px;
            border: 1px solid var(--assignment-border);
            border-radius: 14px;
            background: #fbfdff;
        }

        .filter-group {
            min-width: 0;
        }

        .filter-group label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: var(--assignment-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }

        .filter-group .form-control {
            height: 42px;
            border-radius: 10px;
            border: 1px solid var(--assignment-border);
            box-shadow: none;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .filter-actions .btn {
            border-radius: 10px;
            height: 42px;
            padding: 10px 16px;
            font-weight: 600;
        }

        .calendar-legend {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #506176;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--assignment-border);
            border-radius: 999px;
            padding: 7px 12px;
            background: #fff;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .legend-scheduled { background: var(--assignment-info); }
        .legend-onsite { background: var(--assignment-warning); }
        .legend-completed { background: var(--assignment-success); }
        .legend-cancelled { background: var(--assignment-danger); }

        .calendar-note {
            color: var(--assignment-muted);
            font-size: 12px;
            margin-bottom: 14px;
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.1em;
            gap: 12px;
            flex-wrap: wrap;
        }

        .fc .fc-toolbar-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: var(--assignment-ink);
        }

        .fc .fc-button {
            background: #ffffff;
            border: 1px solid var(--assignment-border);
            color: var(--assignment-ink);
            text-transform: capitalize;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.52em 0.9em;
            box-shadow: none !important;
        }

        .fc .fc-button:hover,
        .fc .fc-button.fc-button-active,
        .fc .fc-button:focus {
            background: var(--assignment-primary);
            color: #fff;
        }

        .fc .fc-daygrid-event,
        .fc .fc-daygrid-dot-event {
            white-space: normal;
            border-radius: 8px;
            padding: 3px 6px;
            font-weight: 600;
        }

        .fc .fc-daygrid-dot-event.event-multi-day {
            background: rgba(21, 94, 117, 0.12) !important;
            border: 1px solid rgba(21, 94, 117, 0.18) !important;
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

        .modal-header {
            background: #f8fbff;
            color: var(--assignment-ink);
            border-bottom: 1px solid var(--assignment-border);
            padding: 16px 18px;
        }

        .modal-content {
            border-radius: 14px;
            border: 1px solid var(--assignment-border);
            overflow: hidden;
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
        }

        .modal-title {
            color: var(--assignment-ink);
            font-weight: 700;
        }

        .visit-info-modal .modal-dialog {
            width: 860px;
            max-width: calc(100% - 30px);
        }

        .visit-modal-topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .visit-modal-code {
            margin: 0 0 4px;
            color: var(--assignment-ink);
            font-size: 19px;
            font-weight: 700;
        }

        .visit-modal-subcopy {
            margin: 0;
            color: var(--assignment-muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .visit-modal-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .visit-summary-strip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 11px;
            border-radius: 10px;
            border: 1px solid var(--assignment-border);
            background: #fbfdff;
            color: #495a70;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-pill.status-scheduled {
            background: rgba(37, 99, 235, 0.12);
            color: var(--assignment-info);
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

        .visit-detail-table-wrap {
            border: 1px solid var(--assignment-border);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .visit-detail-table {
            margin-bottom: 0;
        }

        .visit-detail-table thead th {
            background: #f6f9fc;
            color: #506176;
            font-size: 11px;
            font-weight: 700;
            border-bottom: 1px solid var(--assignment-border);
            padding: 10px 12px;
        }

        .visit-detail-table tbody th,
        .visit-detail-table tbody td {
            padding: 11px 12px;
            border-top: 1px solid #edf2f7;
            vertical-align: top;
            font-size: 13px;
        }

        .visit-detail-table tbody th {
            width: 18%;
            min-width: 120px;
            color: #5e7087;
            font-weight: 600;
            background: #fbfdff;
        }

        .visit-detail-table tbody td {
            color: var(--assignment-ink);
            line-height: 1.7;
            word-break: break-word;
        }

        .visit-detail-table .full-row {
            white-space: pre-line;
        }

        .select2-container .select2-selection--single {
            border: 1px solid var(--assignment-border) !important;
            border-radius: 10px !important;
            min-height: 42px;
            padding-top: 4px;
            box-shadow: none !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            color: var(--assignment-ink);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        @media (max-width: 767px) {
            .wrapper {
                padding-top: 74px;
            }

            .card-box {
                padding: 18px;
            }

            .page-title {
                font-size: 22px;
            }

            .filter-shell {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                width: 100%;
                justify-content: stretch;
            }

            .filter-actions .btn {
                flex: 1 1 100%;
            }

            .visit-detail-table tbody th,
            .visit-detail-table tbody td {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="card-box">
                <div class="page-head">
                    <div>
                        <span class="page-kicker">Engineer Assignment Overview</span>
                        <h2 class="page-title">Visit Assignments</h2>
                        <p class="page-copy">Track Service and Automation engineer deployments in one clean calendar. Click any visit to open a compact detail view.</p>
                    </div>
                    <div class="page-tools">
                        <span class="page-mode-tag"><i class="fa fa-calendar-check-o"></i> Read-only view</span>
                    </div>
                </div>

                <form method="get" action="<?php echo page_url; ?>ServiceLeads/engineer_assignment_overview" class="filter-shell">
                    <div class="filter-group">
                        <label for="engineer_id">Assignee</label>
                        <select name="engineer_id" id="engineer_id" class="form-control overview-select">
                            <option value="">All Assignees</option>
                            <?php foreach ($engineers as $engineer) : ?>
                                <?php $engineer_team = (int) $engineer->department_id === 14 ? 'Automation' : 'Service'; ?>
                                <option value="<?php echo (int) $engineer->user_id; ?>" <?php echo (int) $engineer->user_id === $active_engineer_id ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(trim($engineer->first_name . ' ' . $engineer->last_name) . ' (' . $engineer_team . ')', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control overview-select">
                            <option value="">All Statuses</option>
                            <?php foreach ($status_map as $slug => $label) : ?>
                                <option value="<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $slug === $active_status_slug ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="fa fa-filter"></i> Apply
                        </button>
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_assignment_overview" class="btn btn-default waves-effect">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                    </div>
                </form>

                <div class="calendar-legend">
                    <span class="legend-item"><span class="legend-dot legend-scheduled"></span> Scheduled</span>
                    <span class="legend-item"><span class="legend-dot legend-onsite"></span> On-Site</span>
                    <span class="legend-item"><span class="legend-dot legend-completed"></span> Completed</span>
                    <span class="legend-item"><span class="legend-dot legend-cancelled"></span> Cancelled</span>
                </div>

                <div class="calendar-note">Click any assignment on the calendar to open visit details.</div>
                <div id="assignmentCalendar"></div>
            </div>
        </div>
    </div>

    <div id="assignmentVisitInfoModal" class="modal fade visit-info-modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" style="color: #5e7087; opacity: 0.9;">&times;</button>
                    <h4 class="modal-title" id="assignmentVisitInfoTitle">Visit Information</h4>
                </div>
                <div class="modal-body">
                    <div class="visit-modal-topbar">
                        <div>
                            <div class="visit-modal-code" id="assignmentVisitInfoOrderNo">Not available</div>
                            <p class="visit-modal-subcopy">Compact assignment details for quick review.</p>
                        </div>
                        <div class="visit-modal-actions">
                            <span class="status-pill" id="assignmentVisitInfoStatusPill">Scheduled</span>
                            <a href="#" id="assignmentVisitInfoPdfBtn" class="btn btn-danger waves-effect waves-light" target="_blank">
                                <i class="fa fa-file-pdf-o"></i> View PDF
                            </a>
                        </div>
                    </div>

                    <div class="calendar-legend" style="margin-bottom: 14px;">
                        <span class="visit-summary-strip"><i class="fa fa-user-circle-o"></i> <span id="assignmentVisitInfoEngineer">Not available</span></span>
                        <span class="visit-summary-strip"><i class="fa fa-wrench"></i> <span id="assignmentVisitInfoType">Not available</span></span>
                        <span class="visit-summary-strip"><i class="fa fa-clock-o"></i> <span id="assignmentVisitInfoDuration">Not available</span></span>
                    </div>

                    <div class="visit-detail-table-wrap">
                        <div class="table-responsive">
                            <table class="table visit-detail-table">
                                <thead>
                                    <tr>
                                        <th colspan="4">Visit details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Opportunity date</th>
                                        <td id="assignmentVisitInfoOpDate">Not available</td>
                                        <th>Visit reference</th>
                                        <td id="assignmentVisitInfoVisitRef">Not available</td>
                                    </tr>
                                    <tr>
                                        <th>Customer name</th>
                                        <td id="assignmentVisitInfoCustomerName">Not available</td>
                                        <th>Customer contact</th>
                                        <td id="assignmentVisitInfoCustomerContact">Not available</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td id="assignmentVisitInfoCustomerPhone">Not available</td>
                                        <th>Email</th>
                                        <td id="assignmentVisitInfoCustomerEmail">Not available</td>
                                    </tr>
                                    <tr>
                                        <th>Start date</th>
                                        <td id="assignmentVisitInfoStartDate">Not available</td>
                                        <th>End date</th>
                                        <td id="assignmentVisitInfoEndDate">Not available</td>
                                    </tr>
                                    <tr>
                                        <th>Completed on</th>
                                        <td id="assignmentVisitInfoCompletedOn">Not completed yet</td>
                                        <th>Daily MOM</th>
                                        <td id="assignmentVisitInfoMomMeta">No MOM uploaded yet</td>
                                    </tr>
                                    <tr>
                                        <th>Signed documents</th>
                                        <td id="assignmentVisitInfoDocMeta">Pending signed documents</td>
                                        <th>Visit window</th>
                                        <td id="assignmentVisitInfoWindowMeta">Not available</td>
                                    </tr>
                                    <tr>
                                        <th>Customer address</th>
                                        <td colspan="3" id="assignmentVisitInfoCustomerAddress" class="full-row">Not available</td>
                                    </tr>
                                    <tr>
                                        <th>Planner notes</th>
                                        <td colspan="3" id="assignmentVisitInfoRemarks" class="full-row">No internal notes shared.</td>
                                    </tr>
                                    <tr>
                                        <th>Completion notes</th>
                                        <td colspan="3" id="assignmentVisitInfoCompletionNotes" class="full-row">No completion notes yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            function escapeHtml(value) {
                return $('<div>').text(value || '').html();
            }

            function textOrFallback(value, fallback) {
                var trimmed = $.trim(value || '');
                return trimmed !== '' ? trimmed : fallback;
            }

            function formatMultiline(value, fallback) {
                return escapeHtml(textOrFallback(value, fallback)).replace(/\n/g, '<br>');
            }

            function formatDisplayDate(value, includeTime) {
                var trimmed = $.trim(value || '');
                if (trimmed === '' || trimmed === '0000-00-00' || trimmed === '0000-00-00 00:00:00') {
                    return 'Not available';
                }

                var normalized = trimmed.indexOf(' ') > -1 ? trimmed.replace(' ', 'T') : trimmed + 'T00:00:00';
                var date = new Date(normalized);
                if (isNaN(date.getTime())) {
                    return trimmed;
                }

                var options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };

                if (includeTime) {
                    options.hour = '2-digit';
                    options.minute = '2-digit';
                    options.hour12 = true;
                }

                return date.toLocaleString('en-IN', options);
            }

            function setVisitStatusPill(statusSlug, statusLabel) {
                var $pill = $('#assignmentVisitInfoStatusPill');
                $pill
                    .removeClass('status-scheduled status-on-site status-completed status-cancelled')
                    .addClass('status-' + (statusSlug || 'scheduled'))
                    .text(textOrFallback(statusLabel, 'Scheduled'));
            }

            function openVisitInfoModal(props) {
                var documentCount = parseInt(props.document_count, 10) || 0;
                var momCount = parseInt(props.mom_count, 10) || 0;
                var lastMomText = momCount > 0
                    ? momCount + ' update' + (momCount > 1 ? 's' : '') + (props.latest_work_date ? ' | Last entry: ' + formatDisplayDate(props.latest_work_date, false) : '')
                    : 'No MOM uploaded yet';
                var documentText = documentCount > 0
                    ? documentCount + ' document' + (documentCount > 1 ? 's' : '') + ' uploaded'
                    : 'Pending signed documents';
                var windowText = formatDisplayDate(props.start_date, false) + ' to ' + formatDisplayDate(props.end_date, false);

                $('#assignmentVisitInfoTitle').text(textOrFallback(props.op_no, 'Visit Information'));
                setVisitStatusPill(props.status_slug, props.visit_status);

                $('#assignmentVisitInfoEngineer').text(textOrFallback(props.engineer_name, 'Not available'));
                $('#assignmentVisitInfoType').text(textOrFallback(props.visit_type, 'Not available'));
                $('#assignmentVisitInfoDuration').text(textOrFallback(props.duration_label, 'Not available'));

                $('#assignmentVisitInfoMomMeta').text(lastMomText);
                $('#assignmentVisitInfoDocMeta').text(documentText);
                $('#assignmentVisitInfoWindowMeta').text(windowText);

                $('#assignmentVisitInfoOrderNo').text(textOrFallback(props.op_no, 'Not available'));
                $('#assignmentVisitInfoOpDate').text(formatDisplayDate(props.op_date, false));
                $('#assignmentVisitInfoVisitRef').text(props.visit_id ? '#' + props.visit_id : 'Not available');
                $('#assignmentVisitInfoCustomerName').text(textOrFallback(props.customer_name, 'Not available'));
                $('#assignmentVisitInfoCustomerContact').text(textOrFallback(props.customer_contact_name, 'Not available'));
                $('#assignmentVisitInfoCustomerPhone').text(textOrFallback(props.customer_contact_no, 'Not available'));
                $('#assignmentVisitInfoCustomerEmail').text(textOrFallback(props.customer_email, 'Not available'));
                $('#assignmentVisitInfoStartDate').text(formatDisplayDate(props.start_date, false));
                $('#assignmentVisitInfoEndDate').text(formatDisplayDate(props.end_date, false));
                $('#assignmentVisitInfoCompletedOn').text(props.completed_on ? formatDisplayDate(props.completed_on, true) : 'Not completed yet');
                $('#assignmentVisitInfoCustomerAddress').html(formatMultiline(props.customer_address, 'Not available'));
                $('#assignmentVisitInfoRemarks').html(formatMultiline(props.remarks, 'No internal notes shared.'));
                $('#assignmentVisitInfoCompletionNotes').html(formatMultiline(props.completion_notes, 'No completion notes yet.'));
                $('#assignmentVisitInfoPdfBtn').attr('href', props.pdf_url || '#');

                $('#assignmentVisitInfoModal').modal('show');
            }

            $('.overview-select').select2({
                width: '100%'
            });

            var calendarEl = document.getElementById('assignmentCalendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 720,
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
                events: <?php echo json_encode($events_url); ?>,
                eventDidMount: function(info) {
                    var props = info.event.extendedProps || {};
                    var tooltipText = props.engineer_name + ' | ' + props.op_no + ' | ' + props.customer_name;
                    if (props.is_long_deployment) {
                        tooltipText += ' | ' + props.duration_label;
                    }
                    info.el.setAttribute('title', tooltipText);
                },
                eventClick: function(info) {
                    openVisitInfoModal(info.event.extendedProps || {});
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>
