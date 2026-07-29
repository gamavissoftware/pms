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
        <p>Anyone can assign work to anyone in the organization. The assignee will confirm the final due date after receiving it.</p>
        <div class="tm-banner-meta">
            <span class="tm-chip"><i class="fa fa-paper-plane-o"></i> Mail goes to both users</span>
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
                <p class="tm-section-subtitle">Keep the task brief clear. The final due date will be locked by the assignee.</p>
            </div>
        </div>
        <div class="tm-section-body">
            <div class="tm-note-strip">
                <span class="tm-strong">Flow:</span> creator assigns the work, assignee confirms the final due date, then every progress update is visible live on the task page.
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
                        <select id="assigned_to_user_id" class="form-control" name="assigned_to_user_id" required>
                            <option value="">Select Shubham Pack assignee</option>
                            <?php foreach ($assignable_users as $user) { ?>
                                <option
                                    value="<?php echo (int) $user['user_id']; ?>"
                                    data-department-name="<?php echo htmlspecialchars((string) $user['department']); ?>"
                                    <?php echo set_select('assigned_to_user_id', $user['user_id']); ?>
                                >
                                    <?php echo htmlspecialchars($user['name']); ?><?php if (!empty($user['department'])) { ?> (<?php echo htmlspecialchars($user['department']); ?>)<?php } ?>
                                </option>
                            <?php } ?>
                        </select>
                        <div class="tm-helper">Only users from business location ID 2 are available here.</div>
                        <?php echo form_error('assigned_to_user_id'); ?>
                    </div>

                    <div class="tm-col-4 tm-field">
                        <label>Department</label>
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
                        <input type="date" class="form-control" name="requested_due_date" value="<?php echo set_value('requested_due_date', date('Y-m-d')); ?>" required>
                        <div class="tm-helper">Assignee can revise and confirm the final due date.</div>
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
                        <input type="file" class="form-control" name="attachment">
                        <div class="tm-helper">Optional screenshot, file, or proof.</div>
                    </div>
                </div>

                <div class="tm-action-row">
                    <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management">Cancel</a>
                    <button type="submit" class="tm-btn tm-btn-primary">
                        <i class="fa fa-paper-plane"></i> Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>

<?php $this->load->view('task_management/_foot', compact('page_script_view')); ?>
