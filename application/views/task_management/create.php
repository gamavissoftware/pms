<?php
$page_title = 'Create Task';
$page_script_view = 'task_management/_create_scripts';
$this->load->view('task_management/_head', compact('page_title'));
$current_user_name = !empty($current_user['name']) ? $current_user['name'] : 'Current User';
$task_company_name = !empty($task_company_name) ? $task_company_name : 'Shubham Pack';
?>

<div class="tm-banner">
    <div>
        <h1>Create Task</h1>
        <p>Anyone can assign work to anyone in the organization. Tasks assigned to yourself use the selected due date automatically; other assignees confirm their final due date.</p>
        <div class="tm-banner-meta">
            <span class="tm-chip"><i class="fa fa-paper-plane-o"></i> Mail goes to every assignee</span>
            <span class="tm-chip"><i class="fa fa-users"></i> Team leaders stay in CC</span>
            <span class="tm-chip"><i class="fa fa-bell-o"></i> Real-time toast notification</span>
        </div>
    </div>
    <div class="tm-banner-actions">
        <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management">
            <i class="fa fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php $this->load->view('task_management/_module_nav', array('module_nav' => $module_nav, 'module_nav_active' => 'create')); ?>

<div class="tm-flash"><?php echo $this->session->flashdata('message'); ?></div>

<?php if (empty($module_ready)) { ?>
    <div class="tm-section">
        <div class="tm-section-body">
            <div class="alert alert-warning" style="margin-bottom:0;">
                <strong>Task Management tables are not ready yet.</strong><br>
                Upload and run <strong><?php echo htmlspecialchars($migration_file); ?></strong>, then open this form again.
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="tm-section">
        <div class="tm-section-head">
            <div>
                <h2 class="tm-section-title">New Task</h2>
                <p class="tm-section-subtitle">Keep the task brief clear. For self-assigned tasks, the selected due date becomes the final due date.</p>
            </div>
        </div>
        <div class="tm-section-body">
            <div class="tm-note-strip">
                <span class="tm-strong">Flow:</span> assign the work, confirm the final due date (automatic for yourself), then post progress updates on the task page.
            </div>

            <form id="taskManagementCreateForm" method="post" action="<?php echo page_url; ?>Task_management/save" enctype="multipart/form-data" novalidate>
                <div class="tm-form-grid">
                    <div class="tm-col-4 tm-field">
                        <label>Task Creator</label>
                        <input type="text" class="form-control tm-readonly" value="<?php echo htmlspecialchars($current_user_name); ?>" readonly>
                        <div class="tm-helper">Internal task flow for <?php echo htmlspecialchars($task_company_name); ?> users only.</div>
                    </div>

                    <div class="tm-col-8 tm-field">
                        <label>Assign To <span style="color:#d33;">*</span></label>
                        <?php
                        /* Read straight from the POST, not through set_value().
                           set_value() always returned the default here - the
                           rule is registered under the name WITH brackets - and
                           even when asked by the right name it hands back one
                           element at a time (CI array_shift()s array postdata),
                           so a validation error on any other field silently
                           cleared every assignee the user had picked. */
                        $selected_assignee_ids = (array) $this->input->post('assigned_to_user_id');
                        ?>
                        <select id="assigned_to_user_id" class="form-control" name="assigned_to_user_id[]" multiple required>
                            <?php foreach ($assignable_users as $user) { ?>
                                <option
                                    value="<?php echo (int) $user['user_id']; ?>"
                                    data-department-name="<?php echo htmlspecialchars((string) $user['department']); ?>"
                                    <?php echo in_array((string) $user['user_id'], array_map('strval', $selected_assignee_ids), true) ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($user['name']); ?><?php if (!empty($user['department'])) { ?> (<?php echo htmlspecialchars($user['department']); ?>)<?php } ?>
                                </option>
                            <?php } ?>
                        </select>
                        <div class="tm-helper">Select one or more users. Each person receives an individual task. Your own task skips due-date confirmation.</div>
                        <?php echo form_error('assigned_to_user_id[]'); ?>
                    </div>

                    <div class="tm-col-4 tm-field">
                        <label>Selected Department(s)</label>
                        <input type="text" id="selectedDepartmentName" class="form-control tm-readonly" value="-" readonly>
                    </div>

                    <div class="tm-col-4 tm-field">
                        <label>Priority <span style="color:#d33;">*</span></label>
                        <select class="form-control" name="priority" required>
                            <option value="LOW" <?php echo set_select('priority', 'LOW'); ?>>Low</option>
                            <option value="MEDIUM" <?php echo set_select('priority', 'MEDIUM', TRUE); ?>>Medium</option>
                            <option value="HIGH" <?php echo set_select('priority', 'HIGH'); ?>>High</option>
                            <option value="CRITICAL" <?php echo set_select('priority', 'CRITICAL'); ?>>Critical</option>
                        </select>
                        <?php echo form_error('priority'); ?>
                    </div>

                    <div class="tm-col-4 tm-field">
                        <label>Suggested Due Date <span style="color:#d33;">*</span></label>
                        <!-- min= is rendered from the SERVER's today, the same day the
                             callback_not_past_date rule measures against -->
                        <input type="date" class="form-control" name="requested_due_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo set_value('requested_due_date', date('Y-m-d')); ?>" required>
                        <div class="tm-helper">For yourself, this becomes the final due date immediately. Other assignees can revise and confirm it.</div>
                        <?php echo form_error('requested_due_date'); ?>
                    </div>

                    <div class="tm-col-12 tm-field">
                        <label>Task Title <span style="color:#d33;">*</span></label>
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars(set_value('title')); ?>" placeholder="Example: Share updated vendor payment tracker" required>
                        <?php echo form_error('title'); ?>
                    </div>

                    <div class="tm-col-12 tm-field">
                        <label>Task Details <span style="color:#d33;">*</span></label>
                        <textarea class="form-control" name="task_details" placeholder="Write the required output, dependency, context, and any approval condition." required><?php echo htmlspecialchars(set_value('task_details')); ?></textarea>
                        <?php echo form_error('task_details'); ?>
                    </div>

                    <div class="tm-col-8 tm-field">
                        <label>Reference URL</label>
                        <input type="text" class="form-control" name="reference_url" value="<?php echo htmlspecialchars(set_value('reference_url')); ?>" placeholder="Email thread, document link, or supporting URL">
                    </div>

                    <div class="tm-col-4 tm-field">
                        <label>Attachment</label>
                        <!-- accept= is a convenience in the file picker; the
                             server keeps its own allow list, which is the one
                             that actually decides -->
                        <input type="file" class="form-control" name="attachment"
                               accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.rtf,.zip,.rar,.7z">
                        <div class="tm-helper">Optional screenshot, file, or proof. Images, PDF, Office files, text or archives.</div>
                    </div>
                </div>

                <div class="tm-action-row">
                    <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management">Cancel</a>
                    <button id="createTaskButton" type="submit" class="tm-btn tm-btn-primary">
                        <i class="fa fa-paper-plane"></i> <span id="createTaskButtonLabel">Create Task</span>
                    </button>
                </div>
            </form>
            <div id="taskCreateLoading" hidden role="status" aria-live="polite" aria-atomic="true" style="position:fixed;inset:0;z-index:10000;background:rgba(255,255,255,.88);">
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;color:#243746;">
                    <i class="fa fa-spinner fa-spin fa-3x" aria-hidden="true"></i>
                    <p style="margin-top:16px;font-size:18px;font-weight:600;">Creating task…</p>
                    <p>Please wait while your task is saved.</p>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php $this->load->view('task_management/_foot', compact('page_script_view')); ?>
