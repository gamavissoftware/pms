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
$default_workflow_type = isset($default_workflow_type) ? $default_workflow_type : '';
$raw_schedule_workflow_type = $is_edit_mode && !empty($execution_order->workflow_type) ? $execution_order->workflow_type : $default_workflow_type;
$quotation_flow_display = array(
    'CONSUMABLE' => array('quotation_type' => 'Consumable', 'custom_type' => ''),
    'CRITICAL' => array('quotation_type' => 'Critical', 'custom_type' => ''),
    'CONS_CRITICAL' => array('quotation_type' => 'Consumable + Critical', 'custom_type' => ''),
    'CUSTOM_CHANGEOVER' => array('quotation_type' => 'Custom Engg', 'custom_type' => 'Changeover'),
    'CUSTOM_SPEED_UPGRADATION' => array('quotation_type' => 'Custom Engg', 'custom_type' => 'Speed Upgradation'),
);
$schedule_flow = isset($quotation_flow_display[$raw_schedule_workflow_type]) ? $quotation_flow_display[$raw_schedule_workflow_type] : array('quotation_type' => 'Not mapped from quotation', 'custom_type' => '');
$schedule_workflow_type = isset($quotation_flow_display[$raw_schedule_workflow_type]) || $is_edit_mode ? $raw_schedule_workflow_type : '';
$initial_tasks = array();

if ($is_edit_mode && !empty($tasks)) {
    foreach ($tasks as $task) {
        $initial_tasks[] = array(
            'execution_task_id' => (int) $task->execution_task_id,
            'task_master_id' => !empty($task->task_master_id) ? (int) $task->task_master_id : '',
            'task_code' => $task->task_code,
            'task_name' => $task->task_name,
            'department_id' => !empty($task->department_id) ? (int) $task->department_id : '',
            'department_name' => !empty($task->department) ? $task->department : '',
            'default_owner_id' => !empty($task->assigned_to) ? (int) $task->assigned_to : '',
            'assigned_to' => !empty($task->assigned_to) ? (int) $task->assigned_to : '',
            'owner_name' => !empty($task->first_name) ? trim($task->title . ' ' . $task->first_name . ' ' . $task->last_name) : '',
            'sequence_no' => (int) $task->sequence_no,
            'sla_days' => !empty($task->sla_days) ? (int) $task->sla_days : '',
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
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
    <style>
        body { background: #f5f7fb; }
        .card-box { border-radius: 10px; border: 1px solid #e8edf3; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .order-strip { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
        .summary-box { background: #fbfcff; border: 1px solid #edf1f7; border-radius: 8px; padding: 12px 14px; min-height: 76px; }
        .summary-key { font-size: 11px; color: #7f8a9a; text-transform: uppercase; font-weight: 600; }
        .summary-value { font-size: 15px; line-height: 1.35; font-weight: 600; color: #243447; word-break: break-word; }
        .flow-display { background: #fbfcff; border: 1px solid #dfe7f1; border-radius: 6px; min-height: 48px; padding: 8px 11px; }
        .flow-main { font-size: 15px; line-height: 1.25; font-weight: 600; color: #243447; }
        .flow-sub { margin-top: 3px; font-size: 12px; color: #667085; }
        .schedule-actions { padding-top: 24px; }
        .schedule-actions .btn { width: 100%; }
        #tasksTable .select2-container { min-width: 240px; }
        .select2-container { width: 100% !important; }
        .select2-container .select2-selection--single { height: 34px; border-color: #ddd; }
        .select2-container .select2-selection--single .select2-selection__rendered { line-height: 32px; padding-left: 12px; color: #555; }
        .select2-container .select2-selection--single .select2-selection__arrow { height: 32px; }
        #tasksTable th, #tasksTable td { vertical-align: middle; }
        #tasksTable .task-cell { min-width: 280px; white-space: normal; }
        #tasksTable .mapped-cell { min-width: 180px; color: #243447; }
        #tasksTable .mapped-muted { color: #98a2b3; font-size: 12px; }
        .task-title { font-weight: 600; color: #243447; white-space: normal; line-height: 1.4; }
        .task-code { font-size: 11px; color: #7f8a9a; margin-top: 4px; }
        @media (max-width: 1199px) { .order-strip { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 767px) { .order-strip { grid-template-columns: 1fr; } .schedule-actions { padding-top: 0; } }
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
                        <div class="order-strip">
                            <div class="summary-box">
                                <div class="summary-key">Company</div>
                                <div class="summary-value" title="<?php echo htmlspecialchars($order_snapshot->company_name); ?>"><?php echo htmlspecialchars($order_snapshot->company_name); ?></div>
                            </div>
                            <div class="summary-box">
                                <div class="summary-key">Opportunity</div>
                                <div class="summary-value" title="<?php echo htmlspecialchars($order_snapshot->op_no); ?>"><?php echo htmlspecialchars($order_snapshot->op_no); ?></div>
                            </div>
                            <div class="summary-box">
                                <div class="summary-key">Customer PO</div>
                                <div class="summary-value" title="<?php echo htmlspecialchars($order_snapshot->po_no); ?>"><?php echo htmlspecialchars($order_snapshot->po_no); ?></div>
                            </div>
                            <div class="summary-box">
                                <div class="summary-key">Order Value</div>
                                <div class="summary-value"><?php echo number_format($order_snapshot->order_value, 2); ?></div>
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
                                        <label>Quotation Type / Flow</label>
                                        <input type="hidden" name="workflow_type" id="workflow_type" value="<?php echo htmlspecialchars($schedule_workflow_type); ?>">
                                        <div class="flow-display">
                                            <div class="flow-main"><?php echo htmlspecialchars($schedule_flow['quotation_type']); ?></div>
                                            <?php if (!empty($schedule_flow['custom_type'])): ?>
                                                <div class="flow-sub">Custom Engg Type: <?php echo htmlspecialchars($schedule_flow['custom_type']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($schedule_workflow_type) && !$is_edit_mode): ?>
                                            <small class="text-muted">Picked from the latest quotation.</small>
                                        <?php elseif (empty($schedule_workflow_type)): ?>
                                            <small class="text-danger">Update the quotation type before loading TAT.</small>
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
                                        <select name="priority" class="form-control schedule-select2" data-placeholder="Select Priority" required>
                                            <?php $selected_priority = $is_edit_mode ? $execution_order->priority : 'Medium'; ?>
                                            <option value="Medium" <?php echo $selected_priority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                            <option value="Low" <?php echo $selected_priority === 'Low' ? 'selected' : ''; ?>>Low</option>
                                            <option value="High" <?php echo $selected_priority === 'High' ? 'selected' : ''; ?>>High</option>
                                            <option value="Critical" <?php echo $selected_priority === 'Critical' ? 'selected' : ''; ?>>Critical</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group schedule-actions">
                                        <?php if (!$is_edit_mode): ?>
                                            <button type="button" id="loadTemplateBtn" class="btn btn-info"><i class="fa fa-magic"></i> Load TAT Template</button>
                                        <?php else: ?>
                                            <span class="text-muted">Update the current task dates and owners below.</span>
                                        <?php endif; ?>
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
                                            <th style="width:90px;">TAT</th>
                                            <th>Department</th>
                                            <th>Owner</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
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
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
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

        function destroySelect2($select) {
            if ($.fn.select2 && $select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
        }

        function initializeSelect2($scope) {
            if (!$.fn.select2) {
                return;
            }

            const $root = $scope && $scope.length ? $scope : $(document);
            $root.find('select.schedule-select2, select.department-select, select.user-select').each(function() {
                const $select = $(this);
                const placeholder = $select.data('placeholder') || $select.find('option:first').text() || 'Select';

                destroySelect2($select);
                $select.select2({
                    width: '100%',
                    placeholder: placeholder,
                    allowClear: !$select.prop('required')
                });
            });
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
                destroySelect2($userSelect);
                $userSelect.html(userPlaceholderOptions).prop('disabled', true);
                initializeSelect2($row);
                return;
            }

            destroySelect2($userSelect);
            $userSelect.html('<option value="">Loading users...</option>').prop('disabled', true);
            initializeSelect2($row);

            getDepartmentUsers(departmentId).done(function(users) {
                destroySelect2($userSelect);
                $userSelect.html(buildUserOptions(users)).prop('disabled', false);

                if (selectedUserId) {
                    $userSelect.val(String(selectedUserId));
                    if ($userSelect.val() !== String(selectedUserId)) {
                        $userSelect.val('');
                    }
                } else {
                    $userSelect.val('');
                }
                initializeSelect2($row);
            }).fail(function() {
                destroySelect2($userSelect);
                $userSelect.html('<option value="">Unable to load users</option>').prop('disabled', true);
                initializeSelect2($row);
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
                const departmentName = task.department_name || 'Not mapped in Task Master';
                const ownerName = task.owner_name || 'Not mapped in Task Master';
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
                        <td class="task-cell">
                            <input type="hidden" name="task_name[]" value="${escapeHtml(task.task_name)}">
                            <div class="task-title">${escapeHtml(task.task_name)}</div>
                            <div class="task-code">${escapeHtml(task.task_code || '')}</div>
                        </td>
                        <td>${task.sla_days ? escapeHtml(task.sla_days) + ' day(s)' : '-'}</td>
                        <td class="mapped-cell">
                            <input type="hidden" name="department_id[]" value="${task.department_id || ''}">
                            <div>${escapeHtml(departmentName)}</div>
                            ${task.department_name ? '' : '<div class="mapped-muted">Update Task Master</div>'}
                        </td>
                        <td class="mapped-cell">
                            <input type="hidden" name="assigned_to[]" value="${task.assigned_to || task.default_owner_id || ''}">
                            <div>${escapeHtml(ownerName)}</div>
                            ${task.owner_name ? '' : '<div class="mapped-muted">Update Task Master</div>'}
                        </td>
                        <td>
                            <input type="date" class="form-control input-sm" name="planned_start_date[]" value="${task.planned_start_date || ''}" required>
                        </td>
                        <td>
                            <input type="date" class="form-control input-sm" name="planned_end_date[]" value="${task.planned_end_date || ''}" required>
                        </td>
                    </tr>`;

                $tbody.append(row);
            });
        }

        $('#loadTemplateBtn').on('click', function() {
            const workflowType = $('#workflow_type').val();
            const commitDate = $('#commit_date').val();

            if (!workflowType) {
                alert('Quotation Type / Flow is not mapped from quotation. Please update the quotation first.');
                return;
            }

            if (!commitDate) {
                alert('Please select Commit Date first.');
                $('#commit_date').focus();
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
            initializeSelect2($(document));

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
