<?php
$page_title = !empty($page_title) ? $page_title : 'Task Management';
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

            .tm-grid-5,
            .tm-mini-grid {
                grid-template-columns: 1fr;
            }

            .tm-search {
                width: 100%;
            }
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
