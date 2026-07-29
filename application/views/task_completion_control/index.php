<?php
if (!function_exists('taskControlSafe')) {
    function taskControlSafe($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('taskControlDate')) {
    function taskControlDate($value, $with_time = false)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = strtotime($value);
        if (empty($timestamp)) {
            return '-';
        }

        return $with_time ? date('d M Y, h:i A', $timestamp) : date('d M Y', $timestamp);
    }
}

if (!function_exists('taskControlNameFromRow')) {
    function taskControlNameFromRow($row, $prefix)
    {
        $parts = array();
        foreach (array('title', 'first_name', 'last_name') as $field) {
            $key = $prefix . '_' . $field;
            if (!empty($row[$key])) {
                $parts[] = trim((string) $row[$key]);
            }
        }

        if (empty($parts)) {
            return '-';
        }

        return ucwords(strtolower(implode(' ', $parts)));
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Completed Task Control</title>

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
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
            background: #eef3f9;
        }

        .panel-card {
            background: #fff;
            border: 1px solid #d8e1ec;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            margin-bottom: 22px;
            overflow: hidden;
        }

        .panel-head {
            padding: 18px 22px;
            border-bottom: 1px solid #e7edf4;
            background: #f7fbff;
        }

        .panel-head h4 {
            margin: 0;
            color: #17365d;
            font-size: 18px;
            font-weight: 700;
        }

        .panel-head p {
            margin: 6px 0 0;
            color: #64748b;
        }

        .panel-body {
            padding: 20px;
        }

        .filter-grid .form-group {
            margin-bottom: 16px;
        }

        .table > thead > tr > th {
            background: #17365d;
            color: #fff;
            border-color: #17365d !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        .df-ref {
            font-weight: 700;
            color: #17365d;
            display: block;
        }

        .df-description {
            color: #64748b;
            font-size: 12px;
            display: block;
            margin-top: 4px;
        }

        .remarks-preview {
            max-width: 320px;
            white-space: pre-wrap;
            color: #334155;
        }

        .helper-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .last-update {
            color: #475569;
            line-height: 1.6;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 34px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 34px;
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
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-center">Completed Task Control</h4>
                    </div>
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            </div>

            <?php if (!$module_ready) { ?>
                <div class="alert alert-danger">
                    <strong>Migration pending:</strong> Please run <code><?php echo taskControlSafe($migration_file); ?></code> before using edit or rollback actions. The listing below stays read-only until then.
                </div>
            <?php } ?>

            <div class="panel-card">
                <div class="panel-head">
                    <h4>Filter completed tasks</h4>
                    <p>Use the filters below to narrow the latest 250 completed tasks. Open any row to review its audit history and apply a controlled correction.</p>
                </div>
                <div class="panel-body">
                    <form method="get" action="<?php echo page_url; ?>Task_completion_control">
                        <div class="row filter-grid">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>DF No.</label>
                                    <select name="df_id" class="form-control task-control-select">
                                        <option value="0">All</option>
                                        <?php foreach ($df_options as $df_option) { ?>
                                            <option value="<?php echo (int) $df_option['id']; ?>" <?php echo !empty($filters['df_id']) && (int) $filters['df_id'] === (int) $df_option['id'] ? 'selected' : ''; ?>>
                                                <?php echo taskControlSafe(strtoupper($df_option['df_no'])); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select name="department_id" class="form-control task-control-select">
                                        <option value="0">All</option>
                                        <?php foreach ($department_options as $department_option) { ?>
                                            <option value="<?php echo (int) $department_option['department_id']; ?>" <?php echo !empty($filters['department_id']) && (int) $filters['department_id'] === (int) $department_option['department_id'] ? 'selected' : ''; ?>>
                                                <?php echo taskControlSafe(strtoupper($department_option['department'])); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Accountable User</label>
                                    <select name="assigned_user" class="form-control task-control-select">
                                        <option value="0">All</option>
                                        <?php foreach ($user_options as $user_option) { ?>
                                            <option value="<?php echo (int) $user_option['user_id']; ?>" <?php echo !empty($filters['assigned_user']) && (int) $filters['assigned_user'] === (int) $user_option['user_id'] ? 'selected' : ''; ?>>
                                                <?php echo taskControlSafe(strtoupper(trim($user_option['first_name'] . ' ' . $user_option['last_name']))); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Completed From</label>
                                    <input type="date" name="from_date" class="form-control" value="<?php echo taskControlSafe(isset($filters['from_date']) ? $filters['from_date'] : ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Completed To</label>
                                    <input type="date" name="to_date" class="form-control" value="<?php echo taskControlSafe(isset($filters['to_date']) ? $filters['to_date'] : ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Keyword</label>
                                    <input type="text" name="keyword" class="form-control" placeholder="DF, task, remarks" value="<?php echo taskControlSafe(isset($filters['keyword']) ? $filters['keyword'] : ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="<?php echo page_url; ?>Task_completion_control" class="btn btn-default">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head">
                    <h4>Completed task review queue</h4>
                    <p>Open <strong>Manage</strong> to edit the completion details or roll the task back with a captured audit entry.</p>
                </div>
                <div class="panel-body">
                    <div class="helper-note">
                        The module loads the latest <strong>250</strong> completed tasks for safety. If you do not see an older task here, narrow the filters and reload the page.
                    </div>
                    <div class="table-responsive">
                        <table id="completed-task-control-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>DF</th>
                                    <th>Department</th>
                                    <th>Accountable Person</th>
                                    <th>Task</th>
                                    <th>Planned Completion</th>
                                    <th>Actual Completion</th>
                                    <th>Completed By</th>
                                    <th>Last Updated</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tasks as $task_row) { ?>
                                    <?php
                                    $owner_name = taskControlNameFromRow($task_row, 'owner');
                                    $completed_by_name = taskControlNameFromRow($task_row, 'completer');
                                    $changed_by_name = taskControlNameFromRow($task_row, 'changer');
                                    $last_changed_on = !empty($task_row['changedOn']) && $task_row['changedOn'] !== '0000-00-00 00:00:00'
                                        ? $task_row['changedOn']
                                        : $task_row['task_completed_on'];
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="df-ref"><?php echo taskControlSafe(strtoupper($task_row['df_no'])); ?></span>
                                            <span class="df-description"><?php echo taskControlSafe($task_row['df_description']); ?></span>
                                        </td>
                                        <td><?php echo taskControlSafe(strtoupper($task_row['department'])); ?></td>
                                        <td><?php echo taskControlSafe(strtoupper($owner_name)); ?></td>
                                        <td><?php echo taskControlSafe(strtoupper($task_row['task_name'])); ?></td>
                                        <td><?php echo taskControlSafe(taskControlDate($task_row['end_date'])); ?></td>
                                        <td><?php echo taskControlSafe(taskControlDate($task_row['task_completed_on'], true)); ?></td>
                                        <td><?php echo taskControlSafe(strtoupper($completed_by_name)); ?></td>
                                        <td class="last-update">
                                            <?php echo taskControlSafe(taskControlDate($last_changed_on, true)); ?>
                                            <br>
                                            <small><?php echo taskControlSafe($changed_by_name !== '-' ? $changed_by_name : $completed_by_name); ?></small>
                                        </td>
                                        <td>
                                            <div class="remarks-preview"><?php echo nl2br(taskControlSafe($task_row['remarks'])); ?></div>
                                        </td>
                                        <td>
                                            <a href="<?php echo page_url; ?>Task_completion_control/view/<?php echo (int) $task_row['id']; ?>" class="btn btn-warning btn-sm">Manage</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <footer class="footer text-right">
                <?php $this->load->view('common/footer'); ?>
            </footer>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('.task-control-select').select2({
                width: '100%'
            });

            $('#completed-task-control-table').DataTable({
                pageLength: 25,
                order: []
            });
        });
    </script>
</body>
</html>
