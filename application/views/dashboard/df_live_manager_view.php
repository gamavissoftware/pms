<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> - <?php echo htmlspecialchars($page_title ?? 'Live DF Date & Assignment Manager'); ?></title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        :root {
            --manager-ink: #20344a;
            --manager-primary: #2d5d8a;
            --manager-accent: #e0ecf7;
            --manager-bg: #f4f7fb;
            --manager-panel: #ffffff;
            --manager-border: #dce6f1;
            --manager-success: #1f9d63;
            --manager-warning: #f2a93b;
            --manager-danger: #d9534f;
            --manager-muted: #718197;
        }

        body {
            background: var(--manager-bg);
        }

        .manager-hero {
            background: linear-gradient(135deg, #244364 0%, #356fa7 100%);
            border-radius: 22px;
            padding: 28px;
            color: #fff;
            margin-bottom: 22px;
            box-shadow: 0 20px 50px rgba(25, 50, 78, 0.18);
        }

        .manager-hero h3 {
            margin: 0 0 10px;
            font-size: 30px;
            font-weight: 800;
            color: #fff;
        }

        .manager-hero p {
            margin: 0;
            color: rgba(255,255,255,0.86);
            font-size: 15px;
            max-width: 780px;
        }

        .manager-hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .manager-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }

        .manager-panel {
            background: var(--manager-panel);
            border: 1px solid var(--manager-border);
            border-radius: 20px;
            box-shadow: 0 12px 34px rgba(27, 45, 68, 0.08);
            margin-bottom: 22px;
        }

        .manager-panel-header {
            padding: 20px 22px 0;
        }

        .manager-panel-title {
            margin: 0;
            color: var(--manager-ink);
            font-size: 22px;
            font-weight: 800;
        }

        .manager-panel-subtitle {
            margin: 7px 0 0;
            color: var(--manager-muted);
            font-size: 14px;
        }

        .manager-panel-body {
            padding: 20px 22px 22px;
        }

        .manager-summary-card {
            background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
            border: 1px solid var(--manager-border);
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 18px;
            min-height: 126px;
        }

        .manager-summary-value {
            display: block;
            font-size: 34px;
            line-height: 1;
            font-weight: 900;
            color: var(--manager-ink);
        }

        .manager-summary-label {
            display: block;
            margin-top: 10px;
            color: var(--manager-muted);
            text-transform: uppercase;
            letter-spacing: .6px;
            font-size: 12px;
            font-weight: 800;
        }

        .manager-summary-note {
            display: block;
            margin-top: 8px;
            color: #7f8fa3;
            font-size: 12px;
        }

        .manager-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: end;
        }

        .manager-toolbar .form-group {
            margin-bottom: 0;
            min-width: 210px;
        }

        .manager-toolbar label {
            color: var(--manager-ink);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 7px;
        }

        .manager-toolbar .form-control {
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--manager-border);
            box-shadow: none;
        }

        .manager-panel .select2-container {
            width: 100% !important;
        }

        .manager-panel .select2-container .select2-selection--single {
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--manager-border);
            display: flex;
            align-items: center;
            box-shadow: none;
        }

        .manager-panel .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            color: var(--manager-ink);
            padding-left: 12px;
            padding-right: 32px;
        }

        .manager-panel .select2-container .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }

        .manager-panel .select2-container--default.select2-container--focus .select2-selection--single,
        .manager-panel .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #7aa5d2;
            box-shadow: 0 0 0 3px rgba(53, 111, 167, 0.12);
        }

        .select2-dropdown {
            border: 1px solid var(--manager-border);
            border-radius: 12px;
            overflow: hidden;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--manager-border);
            border-radius: 10px;
            height: 36px;
            padding: 7px 10px;
        }

        .select2-results__option {
            padding: 8px 12px;
        }

        .manager-toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-left: auto;
        }

        .manager-action-btn {
            height: 42px;
            border-radius: 12px;
            padding: 0 16px;
            font-weight: 700;
        }

        .manager-action-btn.btn-primary {
            background: var(--manager-primary);
            border-color: var(--manager-primary);
        }

        .manager-auto-btn.active {
            background: #eaf5ee;
            color: var(--manager-success);
            border-color: #b9e4ca;
        }

        .manager-notice {
            display: none;
            margin-bottom: 16px;
            border-radius: 14px;
            padding: 13px 16px;
            font-weight: 700;
        }

        .manager-notice.success {
            display: block;
            background: #e9f8ef;
            color: #167548;
            border: 1px solid #bde4cb;
        }

        .manager-notice.error {
            display: block;
            background: #fff0f0;
            color: #b53939;
            border: 1px solid #f2c2c2;
        }

        .manager-sync-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            color: var(--manager-muted);
            font-size: 12px;
            font-weight: 700;
        }

        .manager-sync-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eff6fc;
            color: var(--manager-primary);
            border: 1px solid #d7e7f6;
            border-radius: 999px;
            padding: 7px 12px;
        }

        .table-manager-wrap {
            overflow-x: auto;
        }

        #df-live-manager-table {
            width: 100% !important;
        }

        #df-live-manager-table thead th {
            background: #eef4fb;
            color: var(--manager-ink);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: none;
            padding: 14px 12px;
        }

        #df-live-manager-table tbody td {
            vertical-align: top;
            padding: 14px 12px;
            border-top: 1px solid #edf2f7;
        }

        .manager-df-line {
            font-size: 15px;
            font-weight: 800;
            color: var(--manager-ink);
        }

        .manager-df-desc,
        .manager-df-meta,
        .manager-task-meta {
            display: block;
            margin-top: 5px;
            color: var(--manager-muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .manager-task-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--manager-ink);
        }

        .manager-inline-control {
            width: 100%;
            min-width: 170px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--manager-border);
            box-shadow: none;
            padding: 7px 10px;
            background: #fff;
        }

        .manager-inline-control:focus {
            border-color: #7aa5d2;
            box-shadow: 0 0 0 3px rgba(53, 111, 167, 0.12);
        }

        .manager-date-control {
            min-width: 145px;
        }

        .manager-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
        }

        .status-on-track {
            background: #edf8f2;
            color: var(--manager-success);
        }

        .status-soon {
            background: #fff7ea;
            color: var(--manager-warning);
        }

        .status-today {
            background: #eef4ff;
            color: #486bb3;
        }

        .status-overdue {
            background: #fff0f0;
            color: var(--manager-danger);
        }

        .status-completed {
            background: #edf1f6;
            color: #617287;
        }

        .status-awaiting {
            background: #f6f0ff;
            color: #7b58b5;
        }

        .manager-row-dirty td {
            background: #fff9ee !important;
        }

        .manager-overdue-row td {
            background: #fffafb;
        }

        .manager-save-row {
            min-width: 108px;
            border-radius: 10px;
            font-weight: 800;
        }

        .manager-save-row[disabled] {
            opacity: 0.55;
        }

        .manager-last-touch {
            display: block;
            margin-top: 8px;
            color: var(--manager-muted);
            font-size: 11px;
            line-height: 1.5;
        }

        .manager-empty {
            text-align: center;
            padding: 34px 12px !important;
            color: var(--manager-muted);
            font-weight: 700;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 14px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--manager-border);
            border-radius: 12px;
            height: 38px;
            padding: 8px 12px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--manager-border);
            border-radius: 10px;
            height: 36px;
        }

        .dataTables_length .select2-container {
            min-width: 80px;
        }

        .dataTables_length .select2-container .select2-selection--single {
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--manager-border);
        }

        .dataTables_length .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
            padding-left: 10px;
        }

        .dataTables_length .select2-container .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }

        @media (max-width: 991px) {
            .manager-hero {
                padding: 22px;
            }

            .manager-hero h3 {
                font-size: 24px;
            }

            .manager-toolbar-actions {
                margin-left: 0;
            }

            .manager-toolbar .form-group {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="manager-hero">
                        <h3><i class="fa fa-exchange"></i> Live DF Date & Assignment Manager</h3>
                        <p>Select a DF and manage only its pending tasks in one live table. Dates and assignees stay editable inline so the team can update work instantly without page-by-page follow-up.</p>
                        <div class="manager-hero-meta">
                            <span class="manager-chip"><i class="fa fa-shield"></i> <?php echo htmlspecialchars($scope['scope_label']); ?></span>
                            <span class="manager-chip"><i class="fa fa-flash"></i> Real-Time Refresh</span>
                            <span class="manager-chip"><i class="fa fa-tasks"></i> Pending Task Focus</span>
                            <span class="manager-chip"><i class="fa fa-users"></i> Select2 User Search</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="manager-summary-card">
                        <span class="manager-summary-value" id="summary-visible-rows">0</span>
                        <span class="manager-summary-label">Visible Pending Tasks</span>
                        <span class="manager-summary-note">All pending task rows currently loaded in this table.</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="manager-summary-card">
                        <span class="manager-summary-value" id="summary-overdue-rows">0</span>
                        <span class="manager-summary-label">Overdue Pending</span>
                        <span class="manager-summary-note">Pending tasks whose due date has already crossed.</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="manager-summary-card">
                        <span class="manager-summary-value" id="summary-today-rows">0</span>
                        <span class="manager-summary-label">Due Today</span>
                        <span class="manager-summary-note">Pending tasks that need action today.</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="manager-summary-card">
                        <span class="manager-summary-value" id="summary-unassigned-rows">0</span>
                        <span class="manager-summary-label">Unassigned Pending</span>
                        <span class="manager-summary-note">Pending rows that still need user assignment.</span>
                    </div>
                </div>
            </div>

            <div class="manager-panel">
                <div class="manager-panel-header">
                    <h4 class="manager-panel-title">Live Control View</h4>
                    <p class="manager-panel-subtitle">Start with a DF filter, review only pending tasks for that DF, and update assignee plus dates inline with searchable Select2 dropdowns.</p>
                </div>
                <div class="manager-panel-body">
                    <div id="manager-notice" class="manager-notice"></div>

                    <div class="manager-toolbar">
                        <div class="form-group">
                            <label for="manager-df-filter">DF Filter</label>
                            <select id="manager-df-filter" class="form-control">
                                <option value="">All Pending DFs</option>
                                <?php foreach (($dfs ?? array()) as $df): ?>
                                    <option value="<?php echo (int) $df['df_id']; ?>">
                                        <?php echo htmlspecialchars($df['df_no'] . ' - ' . $df['df_description']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="manager-department-filter">Department</label>
                            <select id="manager-department-filter" class="form-control">
                                <option value="">All Visible Departments</option>
                                <?php foreach (($departments ?? array()) as $department): ?>
                                    <option value="<?php echo (int) $department['department_id']; ?>">
                                        <?php echo htmlspecialchars(ucwords(strtolower(trim($department['department'])))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="manager-user-filter">Assigned User</label>
                            <select id="manager-user-filter" class="form-control">
                                <option value="">All Users</option>
                                <option value="0">Unassigned Tasks</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="manager-due-filter">Due View</label>
                            <select id="manager-due-filter" class="form-control">
                                <option value="">All Matching Rows</option>
                                <option value="overdue">Overdue Only</option>
                                <option value="today">Due Today</option>
                                <option value="week">Next 7 Days</option>
                            </select>
                        </div>

                        <div class="manager-toolbar-actions">
                            <button type="button" id="manager-refresh-btn" class="btn btn-primary manager-action-btn">
                                <i class="fa fa-refresh"></i> Refresh Now
                            </button>
                            <button type="button" id="manager-auto-refresh-btn" class="btn btn-default manager-action-btn manager-auto-btn active">
                                <i class="fa fa-bolt"></i> Auto Refresh ON
                            </button>
                        </div>
                    </div>

                    <div class="manager-sync-row">
                        <div id="manager-sync-text">Live data is loading...</div>
                        <div class="manager-sync-badge">
                            <i class="fa fa-clock-o"></i>
                            <span id="manager-last-sync">Waiting for first sync</span>
                        </div>
                    </div>

                    <div class="table-manager-wrap">
                        <table id="df-live-manager-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>DF / Company</th>
                                    <th>Department</th>
                                    <th>Task</th>
                                    <th>Assigned User</th>
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Pending Status</th>
                                    <th>Save</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        $(function () {
            var managerTable;
            var managerUsersByDepartment = {};
            var managerAutoRefreshEnabled = true;
            var managerLoading = false;
            var managerAutoRefreshTimer = null;
            var managerDataUrl = '<?php echo page_url; ?>Dashboard/ajax_df_live_manager_data';
            var managerUpdateUrl = '<?php echo page_url; ?>Dashboard/ajax_df_live_manager_update';

            function escapeHtml(value) {
                return $('<div>').text(value == null ? '' : value).html();
            }

            function showManagerNotice(type, message) {
                var $notice = $('#manager-notice');
                $notice.removeClass('success error').addClass(type).html(message).stop(true, true).fadeIn(150);

                if (type === 'success') {
                    setTimeout(function () {
                        $notice.fadeOut(250);
                    }, 2600);
                }
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

            function initManagerFilterSelect2() {
                initSelect2ForElement($('#manager-df-filter'), {
                    width: '100%',
                    placeholder: 'Select DF',
                    allowClear: true
                });

                initSelect2ForElement($('#manager-department-filter'), {
                    width: '100%',
                    placeholder: 'Select Department',
                    allowClear: true
                });

                initSelect2ForElement($('#manager-user-filter'), {
                    width: '100%',
                    placeholder: 'Select User',
                    allowClear: true
                });

                initSelect2ForElement($('#manager-due-filter'), {
                    width: '100%',
                    placeholder: 'Select Due View',
                    allowClear: true
                });
            }

            function initManagerLengthSelect2() {
                initSelect2ForElement($('.dataTables_length select'), {
                    width: 'style',
                    minimumResultsForSearch: Infinity
                });
            }

            function initInlineAssigneeSelect2() {
                $('#df-live-manager-table tbody .manager-assignee').each(function () {
                    initSelect2ForElement($(this), {
                        width: '100%',
                        dropdownAutoWidth: true
                    });
                });
            }

            function buildAssigneeOptions(row) {
                var departmentUsers = managerUsersByDepartment[String(row.department_id)] || [];
                var currentAssigneeId = String(row.assigned_user_id || 0);
                var foundCurrentAssignee = false;
                var html = '<option value="0">Unassigned</option>';

                departmentUsers.forEach(function (user) {
                    var userId = String(user.user_id);
                    if (userId === currentAssigneeId) {
                        foundCurrentAssignee = true;
                    }
                    html += '<option value="' + escapeHtml(userId) + '"' + (userId === currentAssigneeId ? ' selected' : '') + '>' + escapeHtml(user.label) + '</option>';
                });

                if (!foundCurrentAssignee && currentAssigneeId !== '0' && row.assigned_user_name) {
                    html = '<option value="' + escapeHtml(currentAssigneeId) + '" selected>' + escapeHtml(row.assigned_user_name + ' (Current Assignment)') + '</option>' + html;
                }

                return html;
            }

            function renderDfColumn(row) {
                var company = row.company_name ? escapeHtml(row.company_name) : 'N/A';
                return '' +
                    '<div class="manager-df-line">' + escapeHtml(row.df_no || 'DF Not Found') + '</div>' +
                    '<span class="manager-df-desc">' + escapeHtml(row.df_description || '') + '</span>' +
                    '<span class="manager-df-meta"><strong>Company:</strong> ' + company + '</span>' +
                    '<span class="manager-df-meta"><strong>Marketing:</strong> ' + escapeHtml(row.marketing_person || 'N/A') + '</span>';
            }

            function renderTaskColumn(row) {
                return '' +
                    '<div class="manager-task-name">' + escapeHtml(row.task_name || '') + '</div>' +
                    '<span class="manager-task-meta">Task Record ID: ' + escapeHtml(row.task_record_id) + '</span>';
            }

            function renderAssigneeColumn(row) {
                return '' +
                    '<select class="manager-inline-control manager-assignee" data-field="assigned_user">' +
                        buildAssigneeOptions(row) +
                    '</select>';
            }

            function renderDateColumn(row, fieldName) {
                var value = fieldName === 'start_date' ? row.start_date : row.end_date;
                return '<input type="date" class="manager-inline-control manager-date-control manager-' + fieldName + '" data-field="' + fieldName + '" value="' + escapeHtml(value || '') + '">';
            }

            function renderStatusColumn(row) {
                return '' +
                    '<span class="manager-status-badge ' + escapeHtml(row.due_class || 'status-on-track') + '">' + escapeHtml(row.due_state || 'On Track') + '</span>' +
                    '<span class="manager-last-touch"><strong>Type:</strong> ' + escapeHtml(row.task_status_label || 'Pending') + '</span>';
            }

            function renderSaveColumn(row) {
                return '' +
                    '<button type="button" class="btn btn-primary manager-save-row" disabled data-task-id="' + escapeHtml(row.task_record_id) + '">' +
                        '<i class="fa fa-save"></i> Save' +
                    '</button>' +
                    '<span class="manager-last-touch"><strong>Last update:</strong> ' + escapeHtml(row.last_updated_label || 'Not updated yet') + '</span>';
            }

            function updateSummary(summary) {
                $('#summary-visible-rows').text(summary.visible_rows || 0);
                $('#summary-overdue-rows').text(summary.overdue_rows || 0);
                $('#summary-today-rows').text(summary.today_rows || 0);
                $('#summary-unassigned-rows').text(summary.unassigned_rows || 0);
            }

            function populateUserFilter(selectedValue) {
                var departmentId = $('#manager-department-filter').val();
                var users = [];
                var seen = {};
                var html = '<option value="">All Users</option><option value="0">Unassigned Tasks</option>';

                if (departmentId) {
                    users = managerUsersByDepartment[String(departmentId)] || [];
                } else {
                    Object.keys(managerUsersByDepartment).forEach(function (key) {
                        (managerUsersByDepartment[key] || []).forEach(function (user) {
                            if (!seen[user.user_id]) {
                                seen[user.user_id] = true;
                                users.push(user);
                            }
                        });
                    });
                }

                users.forEach(function (user) {
                    html += '<option value="' + escapeHtml(user.user_id) + '">' + escapeHtml(user.label) + '</option>';
                });

                var $filter = $('#manager-user-filter');
                $filter.html(html);

                if (selectedValue && $filter.find('option[value="' + selectedValue + '"]').length > 0) {
                    $filter.val(selectedValue);
                } else if (selectedValue === '0') {
                    $filter.val('0');
                } else {
                    $filter.val('');
                }

                initSelect2ForElement($filter, {
                    width: '100%',
                    placeholder: 'Select User',
                    allowClear: true
                });
            }

            function rowHasChanges($row) {
                var rowData = managerTable.row($row).data();
                if (!rowData) {
                    return false;
                }

                var currentAssignee = String($row.find('.manager-assignee').val() || '0');
                var currentStart = $row.find('.manager-start_date').val() || '';
                var currentEnd = $row.find('.manager-end_date').val() || '';

                return currentAssignee !== String(rowData.assigned_user_id || 0) ||
                    currentStart !== String(rowData.start_date || '') ||
                    currentEnd !== String(rowData.end_date || '');
            }

            function updateRowDirtyState($row) {
                var changed = rowHasChanges($row);
                $row.toggleClass('manager-row-dirty', changed);
                $row.find('.manager-save-row').prop('disabled', !changed);
            }

            function getRequestParams() {
                return {
                    df_id: $('#manager-df-filter').val(),
                    department_id: $('#manager-department-filter').val(),
                    assigned_user: $('#manager-user-filter').val(),
                    due_view: $('#manager-due-filter').val()
                };
            }

            function hasDirtyRows() {
                return $('#df-live-manager-table tbody tr.manager-row-dirty').length > 0;
            }

            function syncHeader(meta) {
                $('#manager-last-sync').text(meta.refreshed_at || 'Just now');
                if (meta.selected_df_id) {
                    $('#manager-sync-text').text('Showing only pending tasks for the selected DF with live inline editing.');
                } else {
                    $('#manager-sync-text').text('Showing ' + (meta.scope_label || 'visible scope') + ' with only pending tasks in real-time view.');
                }
            }

            function loadManagerData(preservePage, skipIfDirty) {
                if (managerLoading) {
                    return;
                }

                if (skipIfDirty && hasDirtyRows()) {
                    $('#manager-sync-text').text('Auto refresh paused because you have unsaved row changes.');
                    return;
                }

                managerLoading = true;
                $('#manager-refresh-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Refreshing');

                var selectedUserValue = $('#manager-user-filter').val();

                $.ajax({
                    url: managerDataUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: getRequestParams(),
                    success: function (response) {
                        if (!response.success) {
                            showManagerNotice('error', response.message || 'Could not load live manager data.');
                            return;
                        }

                        managerUsersByDepartment = response.department_users || {};
                        populateUserFilter(selectedUserValue);
                        updateSummary(response.summary || {});
                        syncHeader(response.meta || {});

                        if (!preservePage) {
                            managerTable.page('first');
                        }

                        managerTable.clear();
                        managerTable.rows.add(response.rows || []);
                        managerTable.draw(false);
                        initInlineAssigneeSelect2();
                        initManagerLengthSelect2();
                    },
                    error: function () {
                        showManagerNotice('error', 'An error occurred while loading the live manager view.');
                    },
                    complete: function () {
                        managerLoading = false;
                        $('#manager-refresh-btn').prop('disabled', false).html('<i class="fa fa-refresh"></i> Refresh Now');
                    }
                });
            }

            function saveManagerRow($row, $button) {
                var rowData = managerTable.row($row).data();
                if (!rowData) {
                    return;
                }

                var payload = {
                    task_id: rowData.task_record_id,
                    assigned_user: $row.find('.manager-assignee').val(),
                    start_date: $row.find('.manager-start_date').val(),
                    end_date: $row.find('.manager-end_date').val()
                };

                $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving');

                $.ajax({
                    url: managerUpdateUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: payload,
                    success: function (response) {
                        if (response.success) {
                            showManagerNotice('success', response.message || 'Task updated successfully.');
                            loadManagerData(true);
                        } else {
                            showManagerNotice('error', response.message || 'Could not update the selected task.');
                            updateRowDirtyState($row);
                        }
                    },
                    error: function (xhr) {
                        var message = 'An error occurred while saving the task.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showManagerNotice('error', message);
                        updateRowDirtyState($row);
                    },
                    complete: function () {
                        $button.html('<i class="fa fa-save"></i> Save');
                    }
                });
            }

            function startAutoRefresh() {
                if (managerAutoRefreshTimer) {
                    clearInterval(managerAutoRefreshTimer);
                }

                managerAutoRefreshTimer = setInterval(function () {
                    if (managerAutoRefreshEnabled) {
                        loadManagerData(true, true);
                    }
                }, 30000);
            }

            managerTable = $('#df-live-manager-table').DataTable({
                data: [],
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                order: [],
                columns: [
                    { data: null, render: function (data, type, row) { return renderDfColumn(row); } },
                    { data: 'department_name', render: function (data) { return '<strong>' + escapeHtml(data || '') + '</strong>'; } },
                    { data: null, render: function (data, type, row) { return renderTaskColumn(row); } },
                    { data: null, orderable: false, searchable: false, render: function (data, type, row) { return renderAssigneeColumn(row); } },
                    { data: null, orderable: false, searchable: false, render: function (data, type, row) { return renderDateColumn(row, 'start_date'); } },
                    { data: null, orderable: false, searchable: false, render: function (data, type, row) { return renderDateColumn(row, 'end_date'); } },
                    { data: null, render: function (data, type, row) { return renderStatusColumn(row); } },
                    { data: null, orderable: false, searchable: false, render: function (data, type, row) { return renderSaveColumn(row); } }
                ],
                language: {
                    emptyTable: 'No pending DF task rows found for the selected filters.'
                },
                createdRow: function (row, data) {
                    if (data.due_class === 'status-overdue') {
                        $(row).addClass('manager-overdue-row');
                    }
                },
                drawCallback: function () {
                    initInlineAssigneeSelect2();
                    initManagerLengthSelect2();
                }
            });

            $('#df-live-manager-table tbody').on('change input', '.manager-inline-control', function () {
                updateRowDirtyState($(this).closest('tr'));
            });

            $('#df-live-manager-table tbody').on('click', '.manager-save-row', function () {
                var $button = $(this);
                var $row = $button.closest('tr');
                saveManagerRow($row, $button);
            });

            $('#manager-refresh-btn').on('click', function () {
                loadManagerData(true, false);
            });

            $('#manager-df-filter').on('change', function () {
                loadManagerData(false, false);
            });

            $('#manager-department-filter').on('change', function () {
                populateUserFilter('');
                loadManagerData(false, false);
            });

            $('#manager-user-filter, #manager-due-filter').on('change', function () {
                loadManagerData(false, false);
            });

            $('#manager-auto-refresh-btn').on('click', function () {
                managerAutoRefreshEnabled = !managerAutoRefreshEnabled;
                $(this)
                    .toggleClass('active', managerAutoRefreshEnabled)
                    .html(managerAutoRefreshEnabled
                        ? '<i class="fa fa-bolt"></i> Auto Refresh ON'
                        : '<i class="fa fa-pause"></i> Auto Refresh OFF');
            });

            initManagerFilterSelect2();
            startAutoRefresh();
            loadManagerData(false, false);
        });
    </script>
</body>
</html>
