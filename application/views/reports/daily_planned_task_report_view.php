<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$company_q = $this->db->select('colorcode')->from('company_information')->get();
$brand_row = $company_q->num_rows() > 0 ? $company_q->row() : null;
$theme_color = !empty($brand_row->colorcode) ? $brand_row->colorcode : '#2563eb';

$format_date = function ($value) {
    if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return '--';
    }
    $timestamp = strtotime($value);
    return $timestamp ? date('d M Y', $timestamp) : '--';
};
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo html_escape($page_title); ?></title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css">
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body { background: #f4f7fb; color: #243447; }
        .report-shell { margin-top: 20px; margin-bottom: 30px; }
        .report-head {
            background: linear-gradient(135deg, <?php echo $theme_color; ?>, #17324d);
            color: #fff; border-radius: 16px; padding: 22px 24px; margin-bottom: 16px;
            box-shadow: 0 12px 30px rgba(23, 50, 77, .16);
        }
        .report-head h1 { margin: 0 0 6px; font-size: 25px; font-weight: 700; }
        .report-head p { margin: 0; opacity: .9; font-size: 13px; }
        .panel-card {
            background: #fff; border: 1px solid #e5ebf2; border-radius: 14px;
            box-shadow: 0 7px 20px rgba(36, 52, 71, .06); margin-bottom: 16px;
        }
        .filter-card { padding: 16px; }
        .filter-card label { color: #526579; font-size: 11px; text-transform: uppercase; letter-spacing: .45px; }
        .filter-card .form-control { border-radius: 8px; border-color: #dbe4ee; }
        .filter-actions { padding-top: 23px; white-space: nowrap; }
        .btn-report { background: <?php echo $theme_color; ?>; color: #fff; border-radius: 8px; }
        .btn-report:hover, .btn-report:focus { color: #fff; opacity: .92; }
        .btn-reset { background: #eef3f7; color: #42566c; border-radius: 8px; }
        .kpi-row { margin-left: -6px; margin-right: -6px; }
        .kpi-col { padding-left: 6px; padding-right: 6px; }
        .kpi { padding: 16px; min-height: 100px; }
        .kpi-label { color: #738397; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
        .kpi-value { color: #1c2f45; font-size: 28px; line-height: 34px; font-weight: 750; }
        .kpi-note { color: #8a99a9; font-size: 11px; }
        .table-card { padding: 0 16px 16px; overflow: hidden; }
        .table-head { padding: 17px 0 13px; border-bottom: 1px solid #edf1f5; margin-bottom: 12px; }
        .table-head h3 { margin: 0; font-size: 17px; font-weight: 700; }
        #plannedTaskTable thead th {
            background: #f7f9fc; color: #526579; border-bottom: 1px solid #dfe7ef;
            font-size: 11px; text-transform: uppercase; white-space: nowrap;
        }
        #plannedTaskTable td { vertical-align: middle; font-size: 12px; }
        .df-number { color: #1e5aa8; font-weight: 700; white-space: nowrap; }
        .task-name { min-width: 220px; font-weight: 600; color: #263b50; }
        .subtext { display: block; color: #8a98a8; font-size: 10px; margin-top: 3px; }
        .status-pill { border-radius: 20px; display: inline-block; font-size: 10px; font-weight: 700; padding: 5px 9px; white-space: nowrap; }
        .status-completed { background: #dcfce7; color: #15803d; }
        .status-planned { background: #dbeafe; color: #1d4ed8; }
        .status-overdue { background: #fee2e2; color: #b91c1c; }
        .status-on_hold { background: #fef3c7; color: #92400e; }
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #dbe4ee; border-radius: 8px; padding: 6px 9px; }
        .empty-state { text-align: center; color: #718096; padding: 32px 12px; }
        @media (max-width: 767px) {
            .filter-actions { padding-top: 5px; }
            .report-head h1 { font-size: 21px; }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid report-shell">
            <div class="report-head">
                <h1><?php echo html_escape($page_title); ?></h1>
                <p>Planned work for <?php echo html_escape($report_date_display); ?> across running DFs, with focused ownership and execution visibility.</p>
            </div>

            <div class="panel-card filter-card">
                <form method="get" action="<?php echo page_url; ?>Dashboard/daily_planned_task_report">
                    <div class="row">
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Plan Date</label>
                                <input type="date" name="report_date" class="form-control" value="<?php echo html_escape($report_date); ?>">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="form-group">
                                <label>Department</label>
                                <select name="department_id" class="form-control">
                                    <option value="0">All Departments</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo html_escape($department['department_ids']); ?>" <?php echo $selected_department_ids === (string) $department['department_ids'] ? 'selected' : ''; ?>>
                                            <?php echo html_escape($department['department_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Assigned User</label>
                                <select name="user_id" class="form-control">
                                    <option value="0">All Users</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo (int) $user['user_id']; ?>" data-department-id="<?php echo (int) $user['department_id']; ?>" <?php echo $user_id === (int) $user['user_id'] ? 'selected' : ''; ?>>
                                            <?php echo html_escape($user['user_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>Running DF</label>
                                <select name="df_id" class="form-control">
                                    <option value="0">All Running DFs</option>
                                    <?php foreach ($running_dfs as $df): ?>
                                        <option value="<?php echo (int) $df['id']; ?>" <?php echo $df_id === (int) $df['id'] ? 'selected' : ''; ?>>
                                            <?php echo html_escape($df['df_no'] . (!empty($df['df_description']) ? ' - ' . $df['df_description'] : '')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12 filter-actions">
                            <button type="submit" class="btn btn-report">Apply</button>
                            <a href="<?php echo page_url; ?>Dashboard/daily_planned_task_report" class="btn btn-reset">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row kpi-row">
                <div class="col-md-2 col-sm-4 col-xs-6 kpi-col"><div class="panel-card kpi"><div class="kpi-label">Planned Tasks</div><div class="kpi-value"><?php echo (int) $summary['total_tasks']; ?></div><div class="kpi-note">Visible in selected plan</div></div></div>
                <div class="col-md-2 col-sm-4 col-xs-6 kpi-col"><div class="panel-card kpi"><div class="kpi-label">Running DFs</div><div class="kpi-value"><?php echo (int) $summary['df_count']; ?></div><div class="kpi-note">DFs with planned work</div></div></div>
                <div class="col-md-2 col-sm-4 col-xs-6 kpi-col"><div class="panel-card kpi"><div class="kpi-label">Owners</div><div class="kpi-value"><?php echo (int) $summary['user_count']; ?></div><div class="kpi-note">Assigned team members</div></div></div>
                <div class="col-md-2 col-sm-4 col-xs-6 kpi-col"><div class="panel-card kpi"><div class="kpi-label">Completed</div><div class="kpi-value"><?php echo (int) $summary['completed_tasks']; ?></div><div class="kpi-note"><?php echo (int) $summary['completion_pct']; ?>% completion</div></div></div>
                <div class="col-md-2 col-sm-4 col-xs-6 kpi-col"><div class="panel-card kpi"><div class="kpi-label">Pending</div><div class="kpi-value"><?php echo (int) $summary['pending_tasks']; ?></div><div class="kpi-note">Still requiring action</div></div></div>
                <div class="col-md-2 col-sm-4 col-xs-6 kpi-col"><div class="panel-card kpi"><div class="kpi-label">Overdue</div><div class="kpi-value"><?php echo (int) $summary['overdue_tasks']; ?></div><div class="kpi-note"><?php echo (int) $summary['on_hold_tasks']; ?> on hold</div></div></div>
            </div>

            <div class="panel-card table-card">
                <div class="table-head"><h3>Daily Planned Task Register</h3></div>
                <div class="table-responsive">
                    <table id="plannedTaskTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>DF</th>
                                <th>Task</th>
                                <th>Department</th>
                                <th>Assigned User</th>
                                <th>Plan Window</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td>
                                        <span class="df-number"><?php echo html_escape($row['df_no']); ?></span>
                                        <span class="subtext"><?php echo html_escape($row['company_name']); ?></span>
                                    </td>
                                    <td class="task-name">
                                        <?php echo html_escape($row['task_name']); ?>
                                        <?php if (!empty($row['machine_name'])): ?><span class="subtext"><?php echo html_escape($row['machine_name']); ?></span><?php endif; ?>
                                    </td>
                                    <td><?php echo html_escape(!empty($row['department']) ? $row['department'] : 'Not Assigned'); ?></td>
                                    <td><?php echo html_escape($row['user_name']); ?></td>
                                    <td data-order="<?php echo html_escape($row['end_date']); ?>">
                                        <?php echo html_escape($format_date($row['start_date'])); ?>
                                        <span class="subtext">to <?php echo html_escape($format_date($row['end_date'])); ?></span>
                                    </td>
                                    <td><span class="status-pill status-<?php echo html_escape($row['status_key']); ?>"><?php echo html_escape($row['status_label']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (empty($rows)): ?><div class="empty-state">No planned tasks match the selected date and filters.</div><?php endif; ?>
                </div>
            </div>
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
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script>
        $(function () {
            var users = <?php echo json_encode(array_values($users), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
            var selectedUserId = <?php echo (int) $user_id; ?>;
            var $department = $('select[name="department_id"]');
            var $user = $('select[name="user_id"]');

            function refreshUsers(keepSelection) {
                var selectedDepartmentIds = String($department.val() || '')
                    .split(',')
                    .filter(function (value) { return value !== ''; });
                var allowed = {};
                selectedDepartmentIds.forEach(function (value) {
                    allowed[parseInt(value, 10)] = true;
                });

                var requestedUserId = keepSelection ? parseInt($user.val() || selectedUserId, 10) : 0;
                $user.empty().append($('<option>', { value: 0, text: 'All Users' }));

                users.forEach(function (item) {
                    if (selectedDepartmentIds.length && !allowed[parseInt(item.department_id, 10)]) {
                        return;
                    }
                    $user.append($('<option>', {
                        value: parseInt(item.user_id, 10),
                        text: item.user_name
                    }));
                });

                if (requestedUserId > 0 && $user.find('option[value="' + requestedUserId + '"]').length) {
                    $user.val(String(requestedUserId));
                } else {
                    $user.val('0');
                }
            }

            refreshUsers(true);
            $department.on('change', function () {
                refreshUsers(false);
            });

            if ($('#plannedTaskTable tbody tr').length) {
                $('#plannedTaskTable').DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[4, 'asc'], [0, 'asc']],
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'excelHtml5', className: 'btn btn-success btn-sm', title: 'Daily_Planned_Tasks_<?php echo html_escape($report_date); ?>' },
                        { extend: 'csvHtml5', className: 'btn btn-info btn-sm', title: 'Daily_Planned_Tasks_<?php echo html_escape($report_date); ?>' }
                    ]
                });
            }
        });
    </script>
</body>
</html>
