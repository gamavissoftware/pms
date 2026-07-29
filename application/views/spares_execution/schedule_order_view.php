<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
ob_start();
?>
<option value="">Select Department</option>
<?php foreach ($departments as $department): ?>
<option value="<?php echo (int) $department->department_id; ?>"><?php echo htmlspecialchars($department->department); ?></option>
<?php endforeach; ?>
<?php
$department_options_html = preg_replace('/\s+/', ' ', trim(ob_get_clean()));

$user_placeholder_options_html = '<option value="">Select Department First</option>';

$is_edit_mode = !empty($execution_order);
$initial_tasks = array();

if ($is_edit_mode && !empty($tasks)) {
    foreach ($tasks as $task) {
        $initial_tasks[] = array(
            'execution_task_id' => (int) $task->execution_task_id,
            'task_master_id' => !empty($task->task_master_id) ? (int) $task->task_master_id : '',
            'task_code' => $task->task_code,
            'task_name' => $task->task_name,
            'department_id' => !empty($task->department_id) ? (int) $task->department_id : '',
            'default_owner_id' => !empty($task->assigned_to) ? (int) $task->assigned_to : '',
            'assigned_to' => !empty($task->assigned_to) ? (int) $task->assigned_to : '',
            'sequence_no' => (int) $task->sequence_no,
            'depends_on_code' => !empty($task->dependency_task_code) ? $task->dependency_task_code : '',
            'can_start_parallel' => !empty($task->can_start_parallel) ? 1 : 0,
            'dependency_label' => !empty($task->dependency_task_name) ? $task->dependency_task_name . (!empty($task->can_start_parallel) ? ' (parallel allowed)' : '') : (!empty($task->dependency_task_code) ? $task->dependency_task_code : ''),
            'planned_start_date' => $task->planned_start_date,
            'planned_end_date' => $task->planned_end_date,
        );
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
    <style>
        body { background: #f5f7fb; }
        .card-box { border-radius: 10px; border: 1px solid #e8edf3; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .summary-box { background: #fbfcff; border: 1px solid #edf1f7; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .summary-key { font-size: 12px; color: #7f8a9a; text-transform: uppercase; }
        .summary-value { font-size: 16px; font-weight: 600; color: #243447; }
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
                            <a href="<?php echo page_url; ?>Spares/order_detail/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back to Order</a>
                            <a href="<?php echo page_url; ?>Spares_execution/dashboard" class="btn btn-default waves-effect waves-light"><i class="fa fa-dashboard"></i> Execution Dashboard</a>
                        </div>
                        <h4 class="page-title"><?php echo $is_edit_mode ? 'Edit Spares Execution Schedule' : 'Schedule Spares Execution Tasks'; ?></h4>
                    </div>
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
                    <div class="card-box">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="summary-box">
                                    <div class="summary-key">Company</div>
                                    <div class="summary-value"><?php echo htmlspecialchars($order_snapshot->company_name); ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-box">
                                    <div class="summary-key">Opportunity</div>
                                    <div class="summary-value"><?php echo htmlspecialchars($order_snapshot->op_no); ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-box">
                                    <div class="summary-key">Customer PO</div>
                                    <div class="summary-value"><?php echo htmlspecialchars($order_snapshot->po_no); ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-box">
                                    <div class="summary-key">Order Value</div>
                                    <div class="summary-value"><?php echo number_format($order_snapshot->order_value, 2); ?></div>
                                </div>
                            </div>
                        </div>

                        <form method="post" action="<?php echo $is_edit_mode ? page_url . 'Spares_execution/update_schedule' : page_url . 'Spares_execution/save_schedule'; ?>" id="scheduleForm">
                            <input type="hidden" name="order_id" value="<?php echo (int) $order_snapshot->order_id; ?>">
                            <?php if ($is_edit_mode): ?>
                                <input type="hidden" name="execution_order_id" value="<?php echo (int) $execution_order->execution_order_id; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Workflow Type</label>
                                        <?php if ($is_edit_mode): ?>
                                            <input type="hidden" name="workflow_type" id="workflow_type" value="<?php echo htmlspecialchars($execution_order->workflow_type); ?>">
                                            <select class="form-control" disabled>
                                                <?php foreach ($workflows as $workflow_key => $workflow_label): ?>
                                                    <option value="<?php echo $workflow_key; ?>" <?php echo $execution_order->workflow_type === $workflow_key ? 'selected' : ''; ?>><?php echo $workflow_label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted">Workflow is locked after schedule creation.</small>
                                        <?php else: ?>
                                            <select name="workflow_type" id="workflow_type" class="form-control" required>
                                                <option value="">Select Workflow</option>
                                                <?php foreach ($workflows as $workflow_key => $workflow_label): ?>
                                                    <option value="<?php echo $workflow_key; ?>"><?php echo $workflow_label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Commit Date</label>
                                        <input type="date" name="commit_date" id="commit_date" class="form-control" value="<?php echo $is_edit_mode ? htmlspecialchars($execution_order->commit_date) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Priority</label>
                                        <select name="priority" class="form-control" required>
                                            <?php $selected_priority = $is_edit_mode ? $execution_order->priority : 'Medium'; ?>
                                            <option value="Medium" <?php echo $selected_priority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                            <option value="Low" <?php echo $selected_priority === 'Low' ? 'selected' : ''; ?>>Low</option>
                                            <option value="High" <?php echo $selected_priority === 'High' ? 'selected' : ''; ?>>High</option>
                                            <option value="Critical" <?php echo $selected_priority === 'Critical' ? 'selected' : ''; ?>>Critical</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label><br>
                                        <?php if (!$is_edit_mode): ?>
                                            <button type="button" id="loadTemplateBtn" class="btn btn-info"><i class="fa fa-magic"></i> Load Task Template</button>
                                        <?php else: ?>
                                            <span class="text-muted">Update the current task dates and owners below.</span>
                                        <?php endif; ?>
                                        <a href="<?php echo page_url; ?>Spares_execution/task_master" class="btn btn-default">Task Master</a>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Scheduling Notes</label>
                                <textarea name="schedule_notes" class="form-control" rows="3" placeholder="Internal notes for marketing, purchase, store, or dispatch."><?php echo $is_edit_mode ? htmlspecialchars($execution_order->schedule_notes) : ''; ?></textarea>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="tasksTable">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">Seq</th>
                                            <th>Task</th>
                                            <th>Department</th>
                                            <th>Owner</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Dependency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="emptyTaskRow">
                                            <td colspan="7" class="text-center text-muted"><?php echo $is_edit_mode ? 'No tasks found in this execution schedule.' : 'Load a workflow template to create the order schedule.'; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $is_edit_mode ? 'Update Execution Schedule' : 'Save Execution Schedule'; ?></button>
                            <?php if ($is_edit_mode): ?>
                                <a href="<?php echo page_url; ?>Spares_execution/order/<?php echo (int) $order_snapshot->order_id; ?>" class="btn btn-default">Back to Tracker</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/footer'); ?>
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script>
        const departmentOptions = <?php echo json_encode($department_options_html); ?>;
        const userPlaceholderOptions = <?php echo json_encode($user_placeholder_options_html); ?>;
        const departmentUserEndpoint = '<?php echo page_url; ?>Spares_execution/get_department_users';
        const departmentUserCache = {};
        const isEditMode = <?php echo $is_edit_mode ? 'true' : 'false'; ?>;
        const initialTasks = <?php echo json_encode($initial_tasks); ?>;

        function escapeHtml(text) {
            return $('<div/>').text(text || '').html();
        }

        function buildUserOptions(users) {
            let options = '<option value="">Select User</option>';

            (users || []).forEach(function(user) {
                options += '<option value="' + escapeHtml(user.user_id) + '">' + escapeHtml(user.name) + '</option>';
            });

            return options;
        }

        function getDepartmentUsers(departmentId) {
            const deferred = $.Deferred();

            if (!departmentId) {
                deferred.resolve([]);
                return deferred.promise();
            }

            if (Object.prototype.hasOwnProperty.call(departmentUserCache, departmentId)) {
                deferred.resolve(departmentUserCache[departmentId]);
                return deferred.promise();
            }

            $.getJSON(departmentUserEndpoint, {
                department_id: departmentId
            }).done(function(response) {
                departmentUserCache[departmentId] = response.users || [];
                deferred.resolve(departmentUserCache[departmentId]);
            }).fail(function() {
                deferred.reject();
            });

            return deferred.promise();
        }

        function loadRowUsers($row, selectedUserId) {
            const departmentId = $row.find('.department-select').val();
            const $userSelect = $row.find('.user-select');

            if (!departmentId) {
                $userSelect.html(userPlaceholderOptions).prop('disabled', true);
                return;
            }

            $userSelect.html('<option value="">Loading users...</option>').prop('disabled', true);

            getDepartmentUsers(departmentId).done(function(users) {
                $userSelect.html(buildUserOptions(users)).prop('disabled', false);

                if (selectedUserId) {
                    $userSelect.val(String(selectedUserId));
                    if ($userSelect.val() !== String(selectedUserId)) {
                        $userSelect.val('');
                    }
                } else {
                    $userSelect.val('');
                }
            }).fail(function() {
                $userSelect.html('<option value="">Unable to load users</option>').prop('disabled', true);
            });
        }

        function renderTaskRows(tasks) {
            const $tbody = $('#tasksTable tbody');
            $tbody.empty();

            if (!tasks || !tasks.length) {
                $tbody.append('<tr><td colspan="7" class="text-center text-muted">No task master found for this workflow.</td></tr>');
                return;
            }

            tasks.forEach(function(task, index) {
                const row = `
                    <tr>
                        <td>
                            ${isEditMode ? `<input type="hidden" name="execution_task_id[]" value="${task.execution_task_id || ''}">` : ''}
                            <input type="hidden" name="task_master_id[]" value="${task.task_master_id}">
                            <input type="hidden" name="task_code[]" value="${escapeHtml(task.task_code)}">
                            <input type="hidden" name="sequence_no[]" value="${task.sequence_no}">
                            <input type="hidden" name="depends_on_code[]" value="${escapeHtml(task.depends_on_code || '')}">
                            <input type="hidden" name="can_start_parallel[]" value="${task.can_start_parallel ? '1' : '0'}">
                            ${task.sequence_no}
                        </td>
                        <td>
                            <input type="text" class="form-control input-sm" name="task_name[]" value="${escapeHtml(task.task_name)}" readonly>
                        </td>
                        <td>
                            <select class="form-control input-sm department-select" name="department_id[]" data-index="${index}">
                                ${departmentOptions}
                            </select>
                        </td>
                        <td>
                            <select class="form-control input-sm user-select" name="assigned_to[]" data-index="${index}" disabled>
                                ${userPlaceholderOptions}
                            </select>
                        </td>
                        <td>
                            <input type="date" class="form-control input-sm" name="planned_start_date[]" value="${task.planned_start_date || ''}" required>
                        </td>
                        <td>
                            <input type="date" class="form-control input-sm" name="planned_end_date[]" value="${task.planned_end_date || ''}" required>
                        </td>
                        <td>${task.dependency_label ? escapeHtml(task.dependency_label) : (task.depends_on_code ? escapeHtml(task.depends_on_code) : '<span class="text-muted">None</span>')}</td>
                    </tr>`;

                $tbody.append(row);
                const $row = $tbody.find('tr:last');
                $row.find('.department-select[data-index="' + index + '"]').val(task.department_id || '');
                loadRowUsers($row, task.assigned_to || task.default_owner_id || '');
            });
        }

        $('#loadTemplateBtn').on('click', function() {
            const workflowType = $('#workflow_type').val();
            const commitDate = $('#commit_date').val();

            if (!workflowType || !commitDate) {
                alert('Please select workflow type and commit date first.');
                return;
            }

            $.get('<?php echo page_url; ?>Spares_execution/get_template_tasks', {
                workflow_type: workflowType,
                commit_date: commitDate
            }, function(response) {
                renderTaskRows(response.tasks || []);
            }, 'json');
        });

        $(document).ready(function() {
            $('#tasksTable').on('change', '.department-select', function() {
                loadRowUsers($(this).closest('tr'), '');
            });

            if (isEditMode && initialTasks.length) {
                renderTaskRows(initialTasks);
            }
        });
    </script>
</body>
</html>
