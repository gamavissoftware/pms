<?php
$page_title = !empty($page_title) ? $page_title : 'Task Management';
// date, status, priority and progress formatting shared by every screen here
require_once APPPATH . 'views/task_management/_helpers.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> <?php echo htmlspecialchars($page_title); ?></title>

    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <style>
        body {
            background: #f3f5f7;
            color: #1f2f45;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .tm-shell {
            padding-bottom: 28px;
        }

        .tm-banner {
            background: #ffffff;
            border: 1px solid #dfe6ee;
            border-radius: 18px;
            padding: 22px 24px;
            margin: 18px 0;
            box-shadow: 0 10px 30px rgba(23, 39, 58, 0.05);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
        }

        .tm-banner h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #183153;
            letter-spacing: -0.3px;
        }

        .tm-banner p {
            margin: 8px 0 0;
            font-size: 14px;
            color: #6b7b8f;
        }

        .tm-banner-meta,
        .tm-banner-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tm-banner-meta {
            margin-top: 14px;
        }

        .tm-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef3f8;
            color: #31465f;
            font-size: 12px;
            font-weight: 700;
        }

        .tm-chip i {
            color: #4e7398;
        }

        .tm-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
            text-decoration: none !important;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .tm-btn-primary {
            background: #1f6fd6;
            color: #ffffff !important;
            box-shadow: 0 10px 24px rgba(31, 111, 214, 0.18);
        }

        .tm-btn-primary:hover,
        .tm-btn-primary:focus {
            background: #165cb4;
            color: #ffffff !important;
        }

        .tm-btn-secondary {
            background: #ffffff;
            border-color: #d2dbe5;
            color: #23364d !important;
        }

        .tm-btn-secondary:hover,
        .tm-btn-secondary:focus {
            background: #f7fafc;
            color: #23364d !important;
        }

        .tm-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .tm-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #d8e0e8;
            color: #44586f;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none !important;
        }

        .tm-tab.active {
            background: #183153;
            border-color: #183153;
            color: #ffffff;
        }

        .tm-flash {
            margin-bottom: 16px;
        }

        .tm-grid-5 {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .tm-stat {
            background: #ffffff;
            border: 1px solid #dfe6ee;
            border-radius: 16px;
            padding: 16px 18px;
            box-shadow: 0 10px 24px rgba(23, 39, 58, 0.04);
            min-height: 104px;
        }

        .tm-stat-label {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.45px;
            color: #72839a;
        }

        .tm-stat-value {
            margin-top: 10px;
            font-size: 30px;
            font-weight: 800;
            color: #183153;
            line-height: 1;
        }

        .tm-stat-note {
            margin-top: 10px;
            color: #74869a;
            font-size: 12px;
        }

        .tm-section {
            background: #ffffff;
            border: 1px solid #dfe6ee;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(23, 39, 58, 0.05);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .tm-section-head {
            padding: 18px 20px;
            border-bottom: 1px solid #e8eef4;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .tm-section-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #183153;
            letter-spacing: -0.25px;
        }

        .tm-section-subtitle {
            margin: 6px 0 0;
            font-size: 13px;
            color: #000000;
        }

        .tm-section-body {
            padding: 18px 20px 20px;
        }

        .tm-soft-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            background: #edf3f8;
            color: #37506a;
        }

        .tm-search {
            width: 280px;
            max-width: 100%;
        }

        .tm-table-wrap {
            overflow-x: auto;
            margin: 16px 16px 18px;
            padding: 10px;
            background: #ffffff;
            border: 1px solid #d6dde5;
            border-radius: 16px;
        }

        .tm-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #000000;
            border-radius: 12px;
            background: #ffffff;
        }

        .tm-table th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.45px;
            color: #000000;
            background: #f7f9fb;
            border-bottom: 1px solid #b8c4d0;
            border-right: 1px solid #b8c4d0;
            padding: 14px 14px;
            font-weight: 800;
            white-space: nowrap;
        }

        .tm-table td {
            padding: 16px 14px;
            border-bottom: 1px solid #ccd5de;
            border-right: 1px solid #ccd5de;
            vertical-align: top;
            color: #24364c;
            font-size: 13px;
            background: #ffffff;
            line-height: 1.55;
        }

        .tm-table th:last-child,
        .tm-table td:last-child {
            border-right: none;
        }

        .tm-table tr:last-child td {
            border-bottom: none;
        }

        .tm-task-name {
            display: block;
            font-size: 14px;
            font-weight: 800;
            color: #183153;
            margin-bottom: 4px;
        }

        .tm-task-meta {
            display: block;
            color: #7b8b9d;
            font-size: 12px;
            line-height: 1.5;
        }

        .tm-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            white-space: nowrap;
        }

        .tm-status-awaiting_due_date {
            background: #fff4d6;
            color: #9a6500;
        }

        .tm-status-open {
            background: #e8f2ff;
            color: #1353a8;
        }

        .tm-status-in_progress {
            background: #e7f8ef;
            color: #13814d;
        }

        .tm-status-completed {
            background: #ecf7f0;
            color: #18724d;
        }

        .tm-priority {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            white-space: nowrap;
        }

        .tm-priority-low {
            background: #edf5ff;
            color: #355b87;
        }

        .tm-priority-medium {
            background: #eef4f8;
            color: #3d566f;
        }

        .tm-priority-high {
            background: #fff1dc;
            color: #a36000;
        }

        .tm-priority-critical {
            background: #ffe7e7;
            color: #b93232;
        }

        .tm-progress-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #e8edf3;
            overflow: hidden;
            margin-top: 8px;
        }

        .tm-progress-bar {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #2d7ee6 0%, #2eb872 100%);
        }

        .tm-mini-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .tm-mini-card {
            background: #ffffff;
            border: 1px solid #dfe6ee;
            border-radius: 16px;
            padding: 16px 18px;
        }

        .tm-mini-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.45px;
            color: #7b8b9d;
            margin-bottom: 8px;
        }

        .tm-mini-value {
            font-size: 18px;
            font-weight: 800;
            color: #183153;
            line-height: 1.35;
        }

        .tm-mini-note {
            margin-top: 8px;
            font-size: 12px;
            color: #77889c;
            line-height: 1.5;
        }

        .tm-form-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 16px;
        }

        .tm-col-12 { grid-column: span 12; }
        .tm-col-8 { grid-column: span 8; }
        .tm-col-6 { grid-column: span 6; }
        .tm-col-4 { grid-column: span 4; }
        .tm-col-3 { grid-column: span 3; }

        .tm-field label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: #23364c;
        }

        .tm-field .form-control {
            border-radius: 12px;
            border: 1px solid #d7e0ea;
            box-shadow: none;
            min-height: 44px;
            padding: 10px 14px;
            color: #23364c;
            font-size: 14px;
        }

        .tm-field .form-control:focus {
            border-color: #1f6fd6;
            box-shadow: 0 0 0 3px rgba(31, 111, 214, 0.10);
        }

        .tm-field textarea.form-control {
            min-height: 130px;
            resize: vertical;
        }

        .tm-readonly {
            background: #f7fafc;
        }

        .tm-helper {
            margin-top: 6px;
            font-size: 12px;
            color: #7b8b9d;
        }

        .tm-form-error {
            margin-top: 6px;
            font-size: 12px;
            color: #d84242;
            font-weight: 700;
        }

        .tm-field-error {
            border-color: #d84242 !important;
            box-shadow: 0 0 0 3px rgba(216, 66, 66, 0.12) !important;
            background: #fffafb !important;
        }

        .tm-note-strip {
            padding: 14px 16px;
            border-radius: 14px;
            background: #f7fafc;
            border: 1px solid #e3ebf3;
            font-size: 13px;
            color: #5e7086;
            margin-bottom: 16px;
        }

        .tm-action-row {
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .tm-inline-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .tm-empty {
            padding: 28px 20px;
            text-align: center;
            color: #7c8da2;
            font-size: 14px;
        }

        .tm-inline-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tm-range-value {
            font-size: 12px;
            color: #63768c;
            font-weight: 700;
            margin-left: 8px;
        }

        .tm-strong {
            font-weight: 800;
            color: #183153;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            border-radius: 12px !important;
            border: 1px solid #d7e0ea !important;
            min-height: 44px !important;
            padding-top: 6px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px !important;
            color: #23364c !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
        }

        /* =================================================================
         *  UPDATES INBOX
         *  The unread half of the navigation badge, written out in full. It
         *  sits above everything else because it is the only block on the
         *  page that is asking the user for something.
         * ===============================================================*/
        .tm-inbox {
            background: #ffffff;
            border: 1px solid #dfe6ee;
            border-left: 4px solid #e82646;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(23, 39, 58, 0.05);
            margin-bottom: 18px;
            overflow: hidden;
        }

        .tm-inbox-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            border-bottom: 1px solid #eef3f8;
        }

        .tm-inbox-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: #183153;
            line-height: 1.35;
        }

        .tm-inbox-title i { margin-right: 8px; color: #e82646; }

        .tm-inbox-count {
            display: inline-block;
            margin-left: 8px;
            background: #e82646;
            color: #ffffff;
            border-radius: 999px;
            min-width: 20px;
            height: 20px;
            padding: 0 7px;
            font-size: 11px;
            font-weight: 800;
            line-height: 20px;
            text-align: center;
            vertical-align: middle;
        }

        .tm-inbox-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 13px 18px;
            border-bottom: 1px solid #f2f6fa;
            text-decoration: none !important;
            color: inherit;
        }

        .tm-inbox-item:last-child { border-bottom: none; }
        .tm-inbox-item:hover { background: #f8fbff; }

        .tm-inbox-dot {
            flex: 0 0 8px;
            width: 8px;
            height: 8px;
            margin-top: 6px;
            border-radius: 50%;
            background: #e82646;
        }

        .tm-inbox-text {
            flex: 1;
            min-width: 0;
            font-size: 13.5px;
            color: #243a53;
            line-height: 1.55;
        }

        .tm-inbox-meta {
            display: block;
            margin-top: 3px;
            font-size: 11.5px;
            color: #8496a9;
        }

        .tm-inbox-go {
            flex: 0 0 auto;
            font-size: 12px;
            font-weight: 800;
            color: #1f6fd6;
            white-space: nowrap;
            align-self: center;
        }

        /* =================================================================
         *  STAT CARDS - now links, and colour-coded by what they mean
         * ===============================================================*/
        .tm-stat {
            display: block;
            position: relative;
            text-decoration: none !important;
            color: inherit;
            transition: transform .16s ease, box-shadow .16s ease;
        }

        a.tm-stat:hover,
        a.tm-stat:focus {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(23, 39, 58, 0.10);
            color: inherit;
        }

        .tm-stat-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .tm-stat-icon {
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef3f8;
            color: #4e7398;
            font-size: 15px;
        }

        /* the tone says what the number MEANS: blue is neutral information,
           amber is waiting on somebody, red is late, green is finished */
        .tm-stat.tone-warn { border-color: #f6dfae; }
        .tm-stat.tone-warn .tm-stat-icon { background: #fff4d6; color: #9a6500; }
        .tm-stat.tone-warn .tm-stat-value { color: #9a6500; }
        .tm-stat.tone-danger { border-color: #f3c6c6; }
        .tm-stat.tone-danger .tm-stat-icon { background: #ffe7e7; color: #b93232; }
        .tm-stat.tone-danger .tm-stat-value { color: #b93232; }
        .tm-stat.tone-success { border-color: #c7e7d5; }
        .tm-stat.tone-success .tm-stat-icon { background: #e7f8ef; color: #13814d; }
        .tm-stat.tone-success .tm-stat-value { color: #13814d; }

        /* =================================================================
         *  DUE DATE, IN WORDS
         *  A date alone makes the reader do the arithmetic. This does it for
         *  them, and the wording is what carries the urgency - colour is only
         *  a reinforcement.
         * ===============================================================*/
        .tm-due {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            background: #eef3f8;
            color: #40566f;
        }

        .tm-due.is-late { background: #ffe7e7; color: #b93232; }
        .tm-due.is-today { background: #fff4d6; color: #8a5a00; }
        .tm-due.is-soon { background: #e8f2ff; color: #1353a8; }

        /* =================================================================
         *  TABLES
         * ===============================================================*/
        /* No sticky header: #topnav is position:fixed at z-index 1030, so a
           sticky <th> scrolls underneath the app menu and vanishes rather
           than pinning below it. */
        .tm-table tbody tr:hover td { background: #f7fafd; }

        /* HOW A FILTERED-OUT ROW IS HIDDEN.
           Not jQuery's .toggle() and not the hidden attribute: at phone width
           the rows are display:block cards, and .show() would write an inline
           display:table-row that breaks the card, while [hidden]'s user-agent
           display:none loses to the class rule. An explicit !important rule is
           the only one that holds in both layouts. */
        .tm-table tbody tr.tm-hide-row,
        .tm-table tbody tr[hidden] { display: none !important; }

        /* an overdue row is tinted end to end - findable without reading it */
        .tm-table tbody tr.is-late td { background: #fffafa; }
        .tm-table tbody tr.is-late:hover td { background: #fff5f5; }
        .tm-table tbody tr.is-late td:first-child { box-shadow: inset 3px 0 0 #e14b4b; }

        /* room for the important columns so nothing collapses to one word per
           line when the table is scrolled sideways */
        .tm-table td[data-label="Task"] { min-width: 230px; }
        .tm-table td[data-label="Assigned By"],
        .tm-table td[data-label="Assigned To"],
        .tm-table td[data-label="Updated By"] { min-width: 150px; }
        .tm-table td[data-label="Note"] { min-width: 260px; }

        .tm-progress-wrap {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .tm-progress-wrap .tm-progress-track { margin-top: 0; flex: 1; }
        .tm-progress-wrap .tm-strong { min-width: 38px; font-size: 12px; }

        .tm-empty svg { opacity: .3; margin-bottom: 10px; }
        .tm-empty-title { font-weight: 800; color: #4a5f77; margin-bottom: 4px; }

        /* =================================================================
         *  FILTERS - folded away until they are wanted
         * ===============================================================*/
        .tm-filter-bar {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        .tm-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 11px;
            border-radius: 999px;
            background: #e8f2ff;
            color: #1353a8;
            font-size: 12px;
            font-weight: 700;
        }

        .tm-filter-body[hidden] { display: none; }

        /* =================================================================
         *  TIMELINE - the update history, read top to bottom
         * ===============================================================*/
        .tm-timeline {
            position: relative;
            padding: 4px 0 4px 26px;
            margin: 0;
            list-style: none;
        }

        .tm-timeline::before {
            content: "";
            position: absolute;
            left: 7px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: #e4ebf3;
        }

        .tm-tl-item { position: relative; padding: 0 0 20px; }
        .tm-tl-item:last-child { padding-bottom: 2px; }

        .tm-tl-dot {
            position: absolute;
            left: -26px;
            top: 3px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #b9c7d6;
        }

        .tm-tl-item.is-created .tm-tl-dot { border-color: #1f6fd6; }
        .tm-tl-item.is-due .tm-tl-dot { border-color: #d39b00; }
        .tm-tl-item.is-progress .tm-tl-dot { border-color: #2eb872; }
        .tm-tl-item.is-done .tm-tl-dot { border-color: #13814d; background: #13814d; }
        .tm-tl-item.is-reopened .tm-tl-dot { border-color: #b93232; }

        .tm-tl-head {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tm-tl-event { font-size: 13.5px; font-weight: 800; color: #183153; }
        .tm-tl-when { font-size: 12px; color: #8496a9; }
        .tm-tl-who { font-size: 12px; color: #5d7086; font-weight: 700; }

        .tm-tl-note {
            margin-top: 7px;
            padding: 11px 13px;
            background: #f7fafc;
            border: 1px solid #e7eef5;
            border-radius: 12px;
            font-size: 13px;
            color: #33485f;
            line-height: 1.6;
        }

        /* =================================================================
         *  PROGRESS SLIDER - a range input is not a .form-control
         * ===============================================================*/
        .tm-range {
            width: 100%;
            height: 34px;
            padding: 0;
            border: 0;
            background: transparent;
            box-shadow: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .tm-range:focus { outline: none; box-shadow: none; }

        .tm-range::-webkit-slider-runnable-track {
            height: 8px;
            border-radius: 999px;
            background: #e2e9f1;
        }

        .tm-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            margin-top: -7px;
            border-radius: 50%;
            background: #1f6fd6;
            border: 3px solid #ffffff;
            box-shadow: 0 2px 8px rgba(31, 111, 214, .38);
            cursor: pointer;
        }

        .tm-range::-moz-range-track {
            height: 8px;
            border-radius: 999px;
            background: #e2e9f1;
        }

        .tm-range::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #1f6fd6;
            border: 3px solid #ffffff;
            box-shadow: 0 2px 8px rgba(31, 111, 214, .38);
            cursor: pointer;
        }

        .tm-preset-row {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 8px;
        }

        .tm-preset {
            border: 1px solid #d7e0ea;
            background: #ffffff;
            color: #40566f;
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .tm-preset:hover { background: #eef4fb; border-color: #b9cfe8; }
        .tm-preset.is-on { background: #1f6fd6; border-color: #1f6fd6; color: #ffffff; }

        @media (max-width: 1199px) {
            .tm-grid-5,
            .tm-mini-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .tm-col-8,
            .tm-col-6,
            .tm-col-4,
            .tm-col-3 {
                grid-column: span 12;
            }
        }

        @media (max-width: 767px) {
            .tm-banner,
            .tm-section-head {
                display: block;
            }

            .tm-banner-actions {
                margin-top: 16px;
            }

            /* THE FIRST SCREENFUL SHOULD BE THE WORK, NOT THE MASTHEAD.
               At phone width the banner, its three decorative chips and the
               wrapped tab row filled the entire viewport before a single task
               was visible. */
            .tm-banner { padding: 16px; margin: 12px 0; border-radius: 14px; }
            .tm-banner h1 { font-size: 20px; }
            .tm-banner p { font-size: 13px; margin-top: 6px; }
            .tm-banner-meta { display: none; }
            .tm-banner-actions .tm-btn { width: 100%; }

            /* tabs scroll sideways instead of stacking three rows deep */
            .tm-nav {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 4px;
            }

            .tm-tab { flex: 0 0 auto; padding: 9px 14px; font-size: 12.5px; }

            .tm-section-head { padding: 15px 16px; }
            .tm-section-title { font-size: 19px; }
            .tm-section-body { padding: 15px 16px 16px; }
            .tm-stat { min-height: 0; padding: 14px 16px; }
            .tm-stat-value { font-size: 26px; margin-top: 6px; }

            .tm-inbox-head { flex-wrap: wrap; }
            .tm-inbox-title { font-size: 14px; }
            #tmMarkAllRead { width: 100%; }
            .tm-inbox-go { display: none; }

            .tm-action-row .tm-btn { flex: 1; }

            .tm-grid-5,
            .tm-mini-grid {
                grid-template-columns: 1fr;
            }

            .tm-search {
                width: 100%;
            }

            /* A TABLE IS THE WRONG SHAPE ON A PHONE.
               Ten columns in a 360px window meant every cell wrapped to one
               word per line and the whole grid had to be dragged sideways to
               be read at all. Below this width each row becomes a card and
               every cell carries its own column heading (the data-label the
               markup sets), so a task reads top to bottom like a list entry. */
            .tm-table-wrap {
                margin: 12px;
                padding: 0;
                border: 0;
                background: transparent;
                overflow-x: visible;
            }

            .tm-table {
                border: 0;
                background: transparent;
            }

            .tm-table thead { display: none; }

            .tm-table tbody tr {
                display: block;
                background: #ffffff;
                border: 1px solid #dde5ee;
                border-radius: 14px;
                margin-bottom: 12px;
                padding: 4px 2px;
                box-shadow: 0 6px 16px rgba(23, 39, 58, 0.05);
            }

            .tm-table tbody tr.is-late { border-color: #f0bcbc; }

            /* Label ABOVE the value, both block-level.
               A side-by-side flex cell looked tighter but broke the cells that
               hold two lines - a flex container drops <br>, so "Mr Rakesh
               Sharma" and "Accounts" were laid out side by side and the second
               one fell off the card. */
            .tm-table tbody td {
                display: block;
                min-width: 0 !important;
                padding: 9px 14px;
                border: 0;
                border-bottom: 1px dashed #edf1f6;
                background: transparent !important;
                box-shadow: none !important;
                text-align: left;
            }

            .tm-table tbody tr td:last-child { border-bottom: 0; }

            .tm-table tbody td::before {
                content: attr(data-label);
                display: block;
                margin-bottom: 3px;
                font-size: 10.5px;
                font-weight: 800;
                letter-spacing: .4px;
                text-transform: uppercase;
                color: #8496a9;
            }

            /* the first cell is the card's headline, so it loses the label */
            .tm-table tbody td[data-label="Task"],
            .tm-table tbody td[data-label="Note"] {
                background: #fbfdff !important;
                border-radius: 12px 12px 0 0;
            }

            .tm-table tbody td[data-label="Task"]::before,
            .tm-table tbody td[data-label="Note"]::before,
            .tm-table tbody td[data-label=""]::before { display: none; }

            .tm-table tbody td .tm-btn { width: 100%; justify-content: center; }
            .tm-table tbody td.tm-empty { text-align: center; }
            .tm-table tbody td.tm-empty::before { display: none; }
        }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <?php if (file_exists(APPPATH . 'views/common/info-section.php')) { $this->load->view('common/info-section'); } ?>

    <div class="wrapper">
        <div class="container-fluid tm-shell">
