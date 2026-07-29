<?php
$page_title = 'Task Management';
$page_script_view = 'task_management/_dashboard_scripts';
$this->load->view('task_management/_head', compact('page_title'));
?>

<div class="tm-banner">
    <div>
        <h1>Task Management</h1>
        <p>Assign cross-functional work, let the assignee lock the due date, and track updates in one clean flow.</p>
        <div class="tm-banner-meta">
            <span class="tm-chip"><i class="fa fa-user-plus"></i> Open for all users</span>
            <span class="tm-chip"><i class="fa fa-calendar-check-o"></i> Assignee confirms due date</span>
            <span class="tm-chip"><i class="fa fa-bell-o"></i> Mail + growl alerts</span>
        </div>
    </div>
    <div class="tm-banner-actions">
        <a class="tm-btn tm-btn-primary" href="<?php echo page_url; ?>Task_management/create">
            <i class="fa fa-plus-circle"></i> Create New Task
        </a>
    </div>
</div>

<?php $this->load->view('task_management/_module_nav', array('module_nav' => $module_nav, 'module_nav_active' => 'dashboard')); ?>

<div class="tm-flash"><?php echo $this->session->flashdata('message'); ?></div>

<?php if (empty($module_ready)) { ?>
    <div class="tm-section">
        <div class="tm-section-body">
            <div class="alert alert-warning" style="margin-bottom:0;">
                <strong>Task Management tables are not ready yet.</strong><br>
                Upload and run <strong><?php echo htmlspecialchars($migration_file); ?></strong>, then refresh this page.
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="tm-grid-5">
        <div class="tm-stat">
            <div class="tm-stat-label">Assigned To Me</div>
            <div class="tm-stat-value"><?php echo (int) $stats['assigned_open']; ?></div>
            <div class="tm-stat-note">Open items requiring my action</div>
        </div>
        <div class="tm-stat">
            <div class="tm-stat-label">Created By Me</div>
            <div class="tm-stat-value"><?php echo (int) $stats['created_open']; ?></div>
            <div class="tm-stat-note">Tasks I am currently driving</div>
        </div>
        <div class="tm-stat">
            <div class="tm-stat-label">Awaiting Due Date</div>
            <div class="tm-stat-value"><?php echo (int) $stats['awaiting_due']; ?></div>
            <div class="tm-stat-note">Assignee still needs to confirm</div>
        </div>
        <div class="tm-stat">
            <div class="tm-stat-label">Overdue</div>
            <div class="tm-stat-value"><?php echo (int) $stats['overdue']; ?></div>
            <div class="tm-stat-note">Final due date already crossed</div>
        </div>
        <div class="tm-stat">
            <div class="tm-stat-label">Completed This Week</div>
            <div class="tm-stat-value"><?php echo (int) $stats['completed_this_week']; ?></div>
            <div class="tm-stat-note">Recently closed by my assignees</div>
        </div>
    </div>

    <?php if (!empty($is_admin_user)) { ?>
        <div id="all-tasks-table" class="tm-section">
            <div class="tm-section-head">
                <div>
                    <h2 class="tm-section-title">All Tasks</h2>
                    <p class="tm-section-subtitle">Administrator view across the full task module with practical filters.</p>
                </div>
                <div class="tm-inline-pills">
                    <span class="tm-soft-pill"><?php echo (int) $all_task_summary['visible']; ?> Visible</span>
                    <span class="tm-soft-pill"><?php echo (int) $all_task_summary['open']; ?> Open</span>
                    <span class="tm-soft-pill"><?php echo (int) $all_task_summary['in_progress']; ?> In Progress</span>
                    <span class="tm-soft-pill"><?php echo (int) $all_task_summary['completed']; ?> Completed</span>
                    <span class="tm-soft-pill"><?php echo (int) $all_task_summary['overdue']; ?> Overdue</span>
                </div>
            </div>
            <div class="tm-section-body">
                <form method="get" action="<?php echo page_url; ?>Task_management#all-tasks-table">
                    <div class="tm-form-grid">
                        <div class="tm-col-3 tm-field">
                            <label>Search</label>
                            <input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars((string) $admin_task_filters['keyword']); ?>" placeholder="Task, code, creator, assignee">
                        </div>
                        <div class="tm-col-3 tm-field">
                            <label>Assigned To</label>
                            <select class="form-control" name="assigned_to_user_id">
                                <option value="">All Assignees</option>
                                <?php foreach ($admin_filter_options['users'] as $user_option) { ?>
                                    <option value="<?php echo (int) $user_option['user_id']; ?>" <?php if ((int) $admin_task_filters['assigned_to_user_id'] === (int) $user_option['user_id']) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($user_option['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="tm-col-3 tm-field">
                            <label>Assigned By</label>
                            <select class="form-control" name="assigned_by_user_id">
                                <option value="">All Creators</option>
                                <?php foreach ($admin_filter_options['users'] as $user_option) { ?>
                                    <option value="<?php echo (int) $user_option['user_id']; ?>" <?php if ((int) $admin_task_filters['assigned_by_user_id'] === (int) $user_option['user_id']) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($user_option['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="tm-col-3 tm-field">
                            <label>Department</label>
                            <select class="form-control" name="department_id">
                                <option value="">All Departments</option>
                                <?php foreach ($admin_filter_options['departments'] as $department_option) { ?>
                                    <option value="<?php echo (int) $department_option['department_id']; ?>" <?php if ((int) $admin_task_filters['department_id'] === (int) $department_option['department_id']) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($department_option['department']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="tm-col-3 tm-field">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="">All Statuses</option>
                                <?php foreach ($admin_filter_options['statuses'] as $status_key => $status_label) { ?>
                                    <option value="<?php echo htmlspecialchars($status_key); ?>" <?php if ((string) $admin_task_filters['status'] === (string) $status_key) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($status_label); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="tm-col-3 tm-field">
                            <label>Priority</label>
                            <select class="form-control" name="priority">
                                <option value="">All Priorities</option>
                                <?php foreach ($admin_filter_options['priorities'] as $priority_key => $priority_label) { ?>
                                    <option value="<?php echo htmlspecialchars($priority_key); ?>" <?php if ((string) $admin_task_filters['priority'] === (string) $priority_key) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($priority_label); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="tm-col-3 tm-field">
                            <label>Due State</label>
                            <select class="form-control" name="due_state">
                                <option value="">All Due States</option>
                                <?php foreach ($admin_filter_options['due_states'] as $due_key => $due_label) { ?>
                                    <option value="<?php echo htmlspecialchars($due_key); ?>" <?php if ((string) $admin_task_filters['due_state'] === (string) $due_key) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($due_label); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="tm-action-row">
                        <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management#all-tasks-table">Reset Filters</a>
                        <button type="submit" class="tm-btn tm-btn-primary">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>
            <div class="tm-table-wrap">
                <table id="allTasksTable" class="tm-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assigned By</th>
                            <th>Assigned To</th>
                            <th>Department</th>
                            <th>Priority</th>
                            <th>Final Due</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Updated</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_tasks)) { ?>
                            <?php foreach ($all_tasks as $task) {
                                $status_key = strtolower((string) $task['status']);
                                $priority_key = strtolower((string) $task['priority']);
                            ?>
                                <tr class="tm-data-row">
                                    <td>
                                        <span class="tm-task-name"><?php echo htmlspecialchars($task['title']); ?></span>
                                        <span class="tm-task-meta"><?php echo htmlspecialchars($task['task_code']); ?><?php if (!empty($task['company_name'])) { ?> | <?php echo htmlspecialchars($task['company_name']); ?><?php } ?></span>
                                    </td>
                                    <td>
                                        <span class="tm-strong"><?php echo htmlspecialchars($task['creator_name']); ?></span><br>
                                        <span class="tm-task-meta"><?php echo !empty($task['created_on']) ? date('d M Y, h:i A', strtotime($task['created_on'])) : '-'; ?></span>
                                    </td>
                                    <td>
                                        <span class="tm-strong"><?php echo htmlspecialchars($task['assignee_name']); ?></span><br>
                                        <span class="tm-task-meta"><?php echo !empty($task['requested_due_date']) && $task['requested_due_date'] !== '0000-00-00' ? 'Suggested ' . date('d M Y', strtotime($task['requested_due_date'])) : 'Suggested due not set'; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars((string) $task['department']); ?></td>
                                    <td>
                                        <span class="tm-priority tm-priority-<?php echo htmlspecialchars($priority_key); ?>">
                                            <?php echo htmlspecialchars(ucwords(strtolower((string) $task['priority']))); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="tm-strong"><?php echo !empty($task['committed_due_date']) && $task['committed_due_date'] !== '0000-00-00' ? date('d M Y', strtotime($task['committed_due_date'])) : '-'; ?></span>
                                        <?php if (!empty($task['is_overdue'])) { ?>
                                            <div class="tm-task-meta" style="margin-top:6px; color:#b93232;">Overdue</div>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="tm-status tm-status-<?php echo htmlspecialchars($status_key); ?>">
                                            <?php echo htmlspecialchars(ucwords(strtolower(str_replace('_', ' ', (string) $task['status'])))); ?>
                                        </span>
                                    </td>
                                    <td style="min-width:170px;">
                                        <span class="tm-strong"><?php echo (int) $task['progress_percent']; ?>%</span>
                                        <div class="tm-progress-track">
                                            <span class="tm-progress-bar" style="width: <?php echo (int) $task['progress_percent']; ?>%;"></span>
                                        </div>
                                    </td>
                                    <td><?php echo !empty($task['updated_on']) ? date('d M Y, h:i A', strtotime($task['updated_on'])) : '-'; ?></td>
                                    <td>
                                        <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management/view/<?php echo (int) $task['id']; ?>">
                                            Open
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="10" class="tm-empty">No tasks matched the current administrator filters.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>

    <div id="assigned-table" class="tm-section">
        <div class="tm-section-head">
            <div>
                <h2 class="tm-section-title">Assigned To Me</h2>
                <p class="tm-section-subtitle">My workload and the latest live status.</p>
            </div>
            <input type="text" id="assignedTaskFilter" class="form-control tm-search" placeholder="Search task, creator, status">
        </div>
        <div class="tm-table-wrap">
            <table id="assignedTasksTable" class="tm-table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned By</th>
                        <th>Suggested Due</th>
                        <th>Final Due</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assigned_tasks)) { ?>
                        <?php foreach ($assigned_tasks as $task) {
                            $status_key = strtolower((string) $task['status']);
                        ?>
                            <tr class="tm-data-row">
                                <td>
                                    <span class="tm-task-name"><?php echo htmlspecialchars($task['title']); ?></span>
                                    <span class="tm-task-meta"><?php echo htmlspecialchars($task['task_code']); ?><?php if (!empty($task['company_name'])) { ?> | <?php echo htmlspecialchars($task['company_name']); ?><?php } ?></span>
                                </td>
                                <td>
                                    <span class="tm-strong"><?php echo htmlspecialchars($task['creator_name']); ?></span><br>
                                    <span class="tm-task-meta"><?php echo htmlspecialchars((string) $task['department']); ?></span>
                                </td>
                                <td><?php echo !empty($task['requested_due_date']) && $task['requested_due_date'] !== '0000-00-00' ? date('d M Y', strtotime($task['requested_due_date'])) : '-'; ?></td>
                                <td><?php echo !empty($task['committed_due_date']) && $task['committed_due_date'] !== '0000-00-00' ? date('d M Y', strtotime($task['committed_due_date'])) : '-'; ?></td>
                                <td>
                                    <span class="tm-status tm-status-<?php echo htmlspecialchars($status_key); ?>">
                                        <?php echo htmlspecialchars(ucwords(strtolower(str_replace('_', ' ', (string) $task['status'])))); ?>
                                    </span>
                                    <?php if (!empty($task['is_overdue'])) { ?>
                                        <div class="tm-task-meta" style="margin-top:6px; color:#b93232;">Overdue</div>
                                    <?php } ?>
                                </td>
                                <td style="min-width:170px;">
                                    <span class="tm-strong"><?php echo (int) $task['progress_percent']; ?>%</span>
                                    <div class="tm-progress-track">
                                        <span class="tm-progress-bar" style="width: <?php echo (int) $task['progress_percent']; ?>%;"></span>
                                    </div>
                                </td>
                                <td>
                                    <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management/view/<?php echo (int) $task['id']; ?>">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="tm-empty">No tasks assigned to you right now.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="created-table" class="tm-section">
        <div class="tm-section-head">
            <div>
                <h2 class="tm-section-title">Created By Me</h2>
                <p class="tm-section-subtitle">Everything I assigned across the organization.</p>
            </div>
            <input type="text" id="createdTaskFilter" class="form-control tm-search" placeholder="Search task, assignee, status">
        </div>
        <div class="tm-table-wrap">
            <table id="createdTasksTable" class="tm-table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned To</th>
                        <th>Priority</th>
                        <th>Final Due</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($created_tasks)) { ?>
                        <?php foreach ($created_tasks as $task) {
                            $status_key = strtolower((string) $task['status']);
                            $priority_key = strtolower((string) $task['priority']);
                        ?>
                            <tr class="tm-data-row">
                                <td>
                                    <span class="tm-task-name"><?php echo htmlspecialchars($task['title']); ?></span>
                                    <span class="tm-task-meta"><?php echo htmlspecialchars($task['task_code']); ?><?php if (!empty($task['company_name'])) { ?> | <?php echo htmlspecialchars($task['company_name']); ?><?php } ?></span>
                                </td>
                                <td>
                                    <span class="tm-strong"><?php echo htmlspecialchars($task['assignee_name']); ?></span><br>
                                    <span class="tm-task-meta"><?php echo htmlspecialchars((string) $task['department']); ?></span>
                                </td>
                                <td>
                                    <span class="tm-priority tm-priority-<?php echo htmlspecialchars($priority_key); ?>">
                                        <?php echo htmlspecialchars(ucwords(strtolower((string) $task['priority']))); ?>
                                    </span>
                                </td>
                                <td><?php echo !empty($task['committed_due_date']) && $task['committed_due_date'] !== '0000-00-00' ? date('d M Y', strtotime($task['committed_due_date'])) : '-'; ?></td>
                                <td>
                                    <span class="tm-status tm-status-<?php echo htmlspecialchars($status_key); ?>">
                                        <?php echo htmlspecialchars(ucwords(strtolower(str_replace('_', ' ', (string) $task['status'])))); ?>
                                    </span>
                                </td>
                                <td style="min-width:170px;">
                                    <span class="tm-strong"><?php echo (int) $task['progress_percent']; ?>%</span>
                                    <div class="tm-progress-track">
                                        <span class="tm-progress-bar" style="width: <?php echo (int) $task['progress_percent']; ?>%;"></span>
                                    </div>
                                </td>
                                <td>
                                    <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management/view/<?php echo (int) $task['id']; ?>">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="tm-empty">You have not created any open tasks yet.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>

<?php $this->load->view('task_management/_foot', compact('page_script_view')); ?>
