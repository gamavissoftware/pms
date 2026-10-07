<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$quotation_task_rows = array();
if (!empty($tasks)) {
    foreach ($tasks as $task) {
        if (isset($quotation_workflows[$task->workflow_type])) {
            $quotation_task_rows[] = $task;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; <?php echo $page_title; ?></title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        body { background: #f5f7fb; }
        .card-box { border-radius: 10px; border: 1px solid #e8edf3; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .workflow-chip { display: inline-block; padding: 3px 10px; border-radius: 999px; background: #eef5ff; color: #1f5aa6; font-size: 11px; font-weight: 600; }
        .quotation-hero { background: #ffffff; border: 1px solid #e1e8f0; border-radius: 12px; padding: 18px; margin-bottom: 18px; box-shadow: 0 6px 18px rgba(31, 90, 166, 0.06); }
        .quotation-hero h4 { margin: 0 0 6px; font-weight: 700; color: #1d2b3a; }
        .quotation-hero p { margin: 0; color: #687386; }
        .quotation-type-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
        .quotation-type-card { border: 1px solid #d9e5f3; background: #f8fbff; border-radius: 8px; padding: 10px 12px; min-width: 180px; }
        .quotation-type-card b { display: block; color: #1f5aa6; }
        .quotation-type-card span { display: block; color: #6b7788; font-size: 11px; margin-top: 3px; }
        .task-master-accordion { padding: 0; overflow: hidden; }
        .task-master-toggle { display: block; padding: 18px 20px; color: #243447; background: #ffffff; border-bottom: 1px solid #edf1f7; cursor: pointer; }
        .task-master-toggle:hover,
        .task-master-toggle:focus { color: #1f5aa6; text-decoration: none; }
        .task-master-toggle .header-title { margin: 0; font-weight: 700; }
        .task-master-toggle .helper-text { display: block; margin-top: 4px; color: #6b7788; font-size: 12px; font-weight: 400; }
        .task-master-toggle .toggle-icon { float: right; margin-top: 10px; color: #6b7788; transition: transform 0.2s ease; }
        .task-master-toggle[aria-expanded="true"] .toggle-icon { transform: rotate(180deg); }
        .task-master-form-wrap { padding: 20px; background: #fbfcff; }
        .task-master-form-wrap .form-control { min-height: 38px; }
        .form-actions { margin-top: 8px; }
        .table-card-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .table-card-header .header-title { margin: 0; font-weight: 700; }
        .section-note { color: #6b7788; font-size: 12px; margin-top: 6px; }
        .task-master-table th { white-space: nowrap; }
        .task-master-table td { vertical-align: middle !important; }
        .task-master-table .task-name { min-width: 220px; }
        .task-master-table .action-cell { min-width: 110px; white-space: nowrap; }
        .readonly-field { background: #f3f6fa !important; color: #667085; cursor: not-allowed; }
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #ccd6e4; border-radius: 6px; padding: 6px 10px; }
        .dataTables_wrapper .dataTables_length select { border: 1px solid #ccd6e4; border-radius: 6px; padding: 4px 8px; }
        .dt-buttons { margin-bottom: 12px; }
        .dt-buttons .dt-button {
            background: #1f5aa6 !important;
            border: 1px solid #1f5aa6 !important;
            color: #fff !important;
            border-radius: 6px !important;
            padding: 6px 12px !important;
            font-weight: 600;
        }
        .dt-buttons .dt-button:hover,
        .dt-buttons .dt-button:focus {
            background: #174583 !important;
            border-color: #174583 !important;
            color: #fff !important;
        }
        @media (max-width: 767px) {
            .table-card-header { display: block; }
            .task-master-toggle .toggle-icon { float: none; display: inline-block; margin-top: 8px; }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">
                            <a href="<?php echo page_url; ?>Dashboard/sparesdashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Spares Dashboard</a>
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-primary waves-effect waves-light"><i class="fa fa-dashboard"></i> Execution Dashboard</a>
                        </div>
                        <h4 class="page-title">Spares Execution Task Master</h4>
                    </div>
                </div>
            </div>

            <div class="quotation-hero">
                <h4>Quotation Type Task And TAT Master</h4>
                <p>Define the execution tasks, sequence, department owner, dependency, and TAT for each quotation type. These templates are used when a new SF execution schedule is loaded.</p>
                <div class="quotation-type-grid">
                    <?php foreach ($quotation_workflows as $workflow_key => $workflow_label): ?>
                        <div class="quotation-type-card">
                            <b><?php echo htmlspecialchars($workflow_label); ?></b>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box task-master-accordion">
                        <a class="task-master-toggle" data-toggle="collapse" href="#taskMasterFormPanel" aria-expanded="<?php echo !empty($edit_task) ? 'true' : 'false'; ?>" aria-controls="taskMasterFormPanel">
                            <i class="fa fa-chevron-down toggle-icon"></i>
                            <h4 class="header-title"><?php echo !empty($edit_task) ? 'Edit Task Master' : 'Add Task Master'; ?></h4>
                            <span class="helper-text"><?php echo !empty($edit_task) ? 'Editing is open because you selected a task from the list.' : 'Click here when you need to add a new task template or map department ownership.'; ?></span>
                        </a>
                        <div id="taskMasterFormPanel" class="collapse <?php echo !empty($edit_task) ? 'in' : ''; ?>">
                            <div class="task-master-form-wrap">
                                <form method="post" action="<?php echo page_url; ?>Spares_execution/save_task_master">
                                    <input type="hidden" name="task_master_id" value="<?php echo !empty($edit_task) ? (int) $edit_task->task_master_id : ''; ?>">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Quotation Type / Flow</label>
                                                <select name="workflow_type" class="form-control" required>
                                                    <option value="">Select Quotation Type</option>
                                                    <?php foreach ($quotation_workflows as $workflow_key => $workflow_label): ?>
                                                        <option value="<?php echo $workflow_key; ?>" <?php echo (!empty($edit_task) && $edit_task->workflow_type === $workflow_key) ? 'selected' : ''; ?>>
                                                            <?php echo $workflow_label; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <select name="department_id" class="form-control">
                                                    <option value="">Select Department</option>
                                                    <?php foreach ($departments as $department): ?>
                                                        <option value="<?php echo (int) $department->department_id; ?>" <?php echo (!empty($edit_task) && (int) $edit_task->department_id === (int) $department->department_id) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($department->department); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Default Owner</label>
                                                <select name="default_owner_id" id="default_owner_id" class="form-control" data-selected-user-id="<?php echo !empty($edit_task) ? (int) $edit_task->default_owner_id : ''; ?>">
                                                    <option value="">Select Department First</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Task Code</label>
                                                <input type="text" name="task_code" class="form-control <?php echo !empty($edit_task) ? 'readonly-field' : ''; ?>" value="<?php echo !empty($edit_task) ? htmlspecialchars($edit_task->task_code) : ''; ?>" <?php echo !empty($edit_task) ? 'readonly' : ''; ?> required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Task Name</label>
                                                <input type="text" name="task_name" class="form-control" value="<?php echo !empty($edit_task) ? htmlspecialchars($edit_task->task_name) : ''; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Sequence</label>
                                                <input type="number" name="sequence_no" class="form-control" min="1" value="<?php echo !empty($edit_task) ? (int) $edit_task->sequence_no : ''; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>TAT Days</label>
                                                <input type="number" name="sla_days" class="form-control" min="1" value="<?php echo !empty($edit_task) ? (int) $edit_task->sla_days : '1'; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Depends On Task Code</label>
                                                <input type="text" name="depends_on_code" class="form-control <?php echo !empty($edit_task) ? 'readonly-field' : ''; ?>" value="<?php echo !empty($edit_task) ? htmlspecialchars($edit_task->depends_on_code) : ''; ?>" placeholder="Example: ORDER_ACK" <?php echo !empty($edit_task) ? 'readonly' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="is_active" class="form-control">
                                                    <option value="1" <?php echo (empty($edit_task) || (!empty($edit_task) && (int) $edit_task->is_active === 1)) ? 'selected' : ''; ?>>Active</option>
                                                    <option value="0" <?php echo (!empty($edit_task) && (int) $edit_task->is_active === 0) ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <div class="checkbox m-t-0">
                                                <label><input type="checkbox" name="can_start_parallel" value="1" <?php echo (!empty($edit_task) && (int) $edit_task->can_start_parallel === 1) ? 'checked' : ''; ?>> Can Start Parallel</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label>&nbsp;</label>
                                            <div class="checkbox m-t-0">
                                                <label><input type="checkbox" name="extension_allowed" value="1" <?php echo (empty($edit_task) || (!empty($edit_task) && (int) $edit_task->extension_allowed === 1)) ? 'checked' : ''; ?>> Allow Extension Requests</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">Save Task Master</button>
                                        <?php if (!empty($edit_task)): ?>
                                            <a href="<?php echo page_url; ?>Spares_execution/task_master" class="btn btn-default">Cancel Edit</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="table-card-header">
                            <div>
                                <h4 class="header-title">Configured Quotation Type Tasks</h4>
                                <div class="section-note">Export or maintain quotation type tasks with department, owner, TAT, dependency, and status details.</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="taskMasterTable" class="table table-bordered table-striped task-master-table">
                                <thead>
                                    <tr>
                                        <th>Quotation Type</th>
                                        <th>Seq</th>
                                        <th>Task</th>
                                        <th>Department</th>
                                        <th>Default Owner</th>
                                        <th>TAT</th>
                                        <th>Dependency</th>
                                        <th>Parallel</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($quotation_task_rows)): ?>
                                        <?php foreach ($quotation_task_rows as $task): ?>
                                            <tr>
                                                <td><span class="workflow-chip"><?php echo htmlspecialchars($quotation_workflows[$task->workflow_type]); ?></span></td>
                                                <td><?php echo (int) $task->sequence_no; ?></td>
                                                <td class="task-name">
                                                    <b><?php echo htmlspecialchars($task->task_name); ?></b><br>
                                                    <small><?php echo htmlspecialchars($task->task_code); ?></small>
                                                </td>
                                                <td><?php echo !empty($task->department) ? htmlspecialchars($task->department) : '<span class="text-muted">Not set</span>'; ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($task->first_name)) {
                                                        echo htmlspecialchars(trim($task->title . ' ' . $task->first_name . ' ' . $task->last_name));
                                                    } else {
                                                        echo '<span class="text-muted">Not set</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo (int) $task->sla_days; ?> day(s)</td>
                                                <td><?php echo !empty($task->depends_on_code) ? htmlspecialchars($task->depends_on_code) : '<span class="text-muted">None</span>'; ?></td>
                                                <td>
                                                    <?php if ((int) $task->can_start_parallel === 1): ?>
                                                        <span class="label label-info">Allowed</span>
                                                    <?php else: ?>
                                                        <span class="label label-default">No</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ((int) $task->is_active === 1): ?>
                                                        <span class="label label-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="label label-default">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="action-cell">
                                                    <a href="<?php echo page_url; ?>Spares_execution/task_master/<?php echo (int) $task->task_master_id; ?>" class="btn btn-xs btn-primary">Edit</a>
                                                    <?php if ((int) $task->is_active === 1): ?>
                                                        <a href="<?php echo page_url; ?>Spares_execution/toggle_task_master/<?php echo (int) $task->task_master_id; ?>/0" class="btn btn-xs btn-warning">Disable</a>
                                                    <?php else: ?>
                                                        <a href="<?php echo page_url; ?>Spares_execution/toggle_task_master/<?php echo (int) $task->task_master_id; ?>/1" class="btn btn-xs btn-success">Enable</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="10" class="text-center text-muted">No quotation type task masters configured yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script>
        (function() {
            var departmentSelect = document.querySelector('select[name="department_id"]');
            var ownerSelect = document.getElementById('default_owner_id');
            var endpoint = '<?php echo page_url; ?>Spares_execution/get_department_users';

            if (!departmentSelect || !ownerSelect) {
                return;
            }

            function setOwnerOptions(users, selectedUserId) {
                ownerSelect.innerHTML = '';

                var defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select User';
                ownerSelect.appendChild(defaultOption);

                users.forEach(function(user) {
                    var option = document.createElement('option');
                    option.value = user.user_id;
                    option.textContent = user.name;
                    ownerSelect.appendChild(option);
                });

                ownerSelect.disabled = false;

                if (selectedUserId) {
                    ownerSelect.value = String(selectedUserId);
                    if (ownerSelect.value !== String(selectedUserId)) {
                        ownerSelect.value = '';
                    }
                } else {
                    ownerSelect.value = '';
                }
            }

            function setOwnerPlaceholder(message) {
                ownerSelect.innerHTML = '';
                var option = document.createElement('option');
                option.value = '';
                option.textContent = message;
                ownerSelect.appendChild(option);
                ownerSelect.disabled = true;
            }

            function loadDepartmentUsers(departmentId, selectedUserId) {
                if (!departmentId) {
                    setOwnerPlaceholder('Select Department First');
                    return;
                }

                setOwnerPlaceholder('Loading users...');

                var xhr = new XMLHttpRequest();
                xhr.open('GET', endpoint + '?department_id=' + encodeURIComponent(departmentId), true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState !== 4) {
                        return;
                    }

                    if (xhr.status >= 200 && xhr.status < 300) {
                        var response = { users: [] };

                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (error) {
                            response = { users: [] };
                        }

                        setOwnerOptions(response.users || [], selectedUserId);
                        return;
                    }

                    setOwnerPlaceholder('Unable to load users');
                };
                xhr.send();
            }

            document.addEventListener('DOMContentLoaded', function() {
                loadDepartmentUsers(departmentSelect.value, ownerSelect.getAttribute('data-selected-user-id'));

                departmentSelect.addEventListener('change', function() {
                    loadDepartmentUsers(this.value, '');
                });
            });
        })();

        $(document).ready(function() {
            $('#taskMasterTable').DataTable({
                order: [[0, 'asc'], [1, 'asc']],
                pageLength: 25,
                dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rt<"row"<"col-sm-6"l><"col-sm-6"p>>',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                        title: 'Spares Execution Quotation Type Task Master',
                        filename: 'spares_execution_task_master_<?php echo date('Ymd_His'); ?>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        }
                    }
                ],
                columnDefs: [
                    { orderable: false, targets: [9] }
                ]
            });
        });
    </script>
</body>
</html>
