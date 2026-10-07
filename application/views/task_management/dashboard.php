<?php
$page_title = 'Task Management';
$page_script_view = 'task_management/_dashboard_scripts';
$this->load->view('task_management/_head', compact('page_title'));

$inbox = isset($inbox) ? (array) $inbox : array();
$unread_count = isset($unread_count) ? (int) $unread_count : count($inbox);

/* Which filters the administrator actually has on right now. Drawn as chips
   above the table so an empty-looking result is never a mystery - the reason
   is on screen next to it. */
$active_filters = array();
if (!empty($is_admin_user) && !empty($admin_task_filters)) {
    $filter_labels = array(
        'keyword' => 'Search',
        'assigned_to_user_id' => 'Assigned to',
        'assigned_by_user_id' => 'Assigned by',
        'department_id' => 'Department',
        'status' => 'Status',
        'priority' => 'Priority',
        'due_state' => 'Due'
    );
    foreach ($filter_labels as $filter_key => $filter_label) {
        if (empty($admin_task_filters[$filter_key])) {
            continue;
        }

        $value = $admin_task_filters[$filter_key];
        if ($filter_key === 'assigned_to_user_id' || $filter_key === 'assigned_by_user_id') {
            foreach ($admin_filter_options['users'] as $user_option) {
                if ((int) $user_option['user_id'] === (int) $value) { $value = $user_option['name']; break; }
            }
        } elseif ($filter_key === 'department_id') {
            foreach ($admin_filter_options['departments'] as $department_option) {
                if ((int) $department_option['department_id'] === (int) $value) { $value = $department_option['department']; break; }
            }
        } elseif ($filter_key === 'status') {
            $value = isset($admin_filter_options['statuses'][$value]) ? $admin_filter_options['statuses'][$value] : $value;
        } elseif ($filter_key === 'priority') {
            $value = isset($admin_filter_options['priorities'][$value]) ? $admin_filter_options['priorities'][$value] : $value;
        } elseif ($filter_key === 'due_state') {
            $value = isset($admin_filter_options['due_states'][$value]) ? $admin_filter_options['due_states'][$value] : $value;
        }

        $active_filters[] = $filter_label . ': ' . $value;
    }
}
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

    <?php if (!empty($inbox)) { ?>
        <!-- The unread half of the navigation badge, written out. Opening any
             of these marks it read, so this panel empties itself. -->
        <div class="tm-inbox" id="tm-inbox">
            <div class="tm-inbox-head">
                <h2 class="tm-inbox-title">
                    <i class="fa fa-bell"></i> Updates on your tasks
                    <span class="tm-inbox-count"><?php echo $unread_count > 99 ? '99+' : (int) $unread_count; ?></span>
                </h2>
                <button type="button" class="tm-btn tm-btn-secondary" id="tmMarkAllRead" style="padding:7px 14px;font-size:12px;">
                    Mark all as read
                </button>
            </div>
            <?php foreach ($inbox as $note) { ?>
                <a class="tm-inbox-item" href="<?php echo page_url; ?>Task_management/view/<?php echo (int) $note['task_id']; ?>">
                    <span class="tm-inbox-dot"></span>
                    <span class="tm-inbox-text">
                        <?php echo htmlspecialchars((string) $note['message']); ?>
                        <span class="tm-inbox-meta">
                            <?php echo htmlspecialchars((string) $note['task_code']); ?> &middot;
                            <?php echo htmlspecialchars((string) $note['title']); ?> &middot;
                            <span title="<?php echo htmlspecialchars(tm_date($note['created_on'], 'd M Y, h:i A')); ?>"><?php echo htmlspecialchars(tm_relative_time($note['created_on'])); ?></span>
                        </span>
                    </span>
                    <span class="tm-inbox-go">Open <i class="fa fa-angle-right"></i></span>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="tm-grid-5">
        <a class="tm-stat" href="#assigned-table">
            <div class="tm-stat-head">
                <div class="tm-stat-label">Assigned To Me</div>
                <span class="tm-stat-icon"><i class="fa fa-inbox"></i></span>
            </div>
            <div class="tm-stat-value"><?php echo (int) $stats['assigned_open']; ?></div>
            <div class="tm-stat-note">Open items requiring my action</div>
        </a>
        <a class="tm-stat" href="#created-table">
            <div class="tm-stat-head">
                <div class="tm-stat-label">Created By Me</div>
                <span class="tm-stat-icon"><i class="fa fa-paper-plane-o"></i></span>
            </div>
            <div class="tm-stat-value"><?php echo (int) $stats['created_open']; ?></div>
            <div class="tm-stat-note">Tasks I am currently driving</div>
        </a>
        <a class="tm-stat tone-warn" href="#assigned-table" data-tm-quick-filter="Awaiting Due Date">
            <div class="tm-stat-head">
                <div class="tm-stat-label">Awaiting Due Date</div>
                <span class="tm-stat-icon"><i class="fa fa-hourglass-half"></i></span>
            </div>
            <div class="tm-stat-value"><?php echo (int) $stats['awaiting_due']; ?></div>
            <div class="tm-stat-note">I still need to confirm a final date</div>
        </a>
        <a class="tm-stat tone-danger" href="#assigned-table" data-tm-quick-filter="Overdue">
            <div class="tm-stat-head">
                <div class="tm-stat-label">Overdue</div>
                <span class="tm-stat-icon"><i class="fa fa-exclamation-triangle"></i></span>
            </div>
            <div class="tm-stat-value"><?php echo (int) $stats['overdue']; ?></div>
            <div class="tm-stat-note">Final due date already crossed</div>
        </a>
        <div class="tm-stat tone-success">
            <div class="tm-stat-head">
                <div class="tm-stat-label">Completed This Week</div>
                <span class="tm-stat-icon"><i class="fa fa-check-circle-o"></i></span>
            </div>
            <div class="tm-stat-value"><?php echo (int) $stats['completed_this_week']; ?></div>
            <!-- the query counts tasks assigned to ME and closed since Monday,
                 so this is my own output, not my assignees' -->
            <div class="tm-stat-note">Closed by me since Monday</div>
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
                <div class="tm-filter-bar">
                    <button type="button" class="tm-btn tm-btn-secondary" id="tmFilterToggle"
                            aria-expanded="<?php echo !empty($active_filters) ? 'true' : 'false'; ?>" aria-controls="tmFilterBody">
                        <i class="fa fa-sliders"></i> Filters
                    </button>
                    <?php foreach ($active_filters as $chip) { ?>
                        <span class="tm-filter-chip"><?php echo htmlspecialchars($chip); ?></span>
                    <?php } ?>
                    <?php if (!empty($active_filters)) { ?>
                        <a class="tm-btn tm-btn-secondary" style="padding:6px 13px;font-size:12px;"
                           href="<?php echo page_url; ?>Task_management">Clear all</a>
                    <?php } ?>
                </div>

                <!-- Plain action, no #fragment: a GET form drops the fragment
                     when it builds the query string, so the old link never
                     scrolled anywhere. _dashboard_scripts.php puts the reader
                     back at this table after the reload instead. -->
                <div class="tm-filter-body" id="tmFilterBody" <?php if (empty($active_filters)) { echo 'hidden'; } ?>>
                    <form method="get" action="<?php echo page_url; ?>Task_management">
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
                            <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management">Reset Filters</a>
                            <button type="submit" class="tm-btn tm-btn-primary">
                                <i class="fa fa-filter"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
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
                            <?php foreach ($all_tasks as $task) { ?>
                                <tr class="tm-data-row <?php echo !empty($task['is_overdue']) ? 'is-late' : ''; ?>">
                                    <td data-label="Task">
                                        <span class="tm-task-name"><?php echo htmlspecialchars($task['title']); ?></span>
                                        <span class="tm-task-meta"><?php echo htmlspecialchars($task['task_code']); ?><?php if (!empty($task['company_name'])) { ?> | <?php echo htmlspecialchars($task['company_name']); ?><?php } ?></span>
                                    </td>
                                    <td data-label="Assigned By">
                                        <span class="tm-strong"><?php echo htmlspecialchars($task['creator_name']); ?></span><br>
                                        <span class="tm-task-meta"><?php echo tm_date($task['created_on'], 'd M Y, h:i A'); ?></span>
                                    </td>
                                    <td data-label="Assigned To">
                                        <span class="tm-strong"><?php echo htmlspecialchars($task['assignee_name']); ?></span><br>
                                        <span class="tm-task-meta"><?php echo tm_has_date($task['requested_due_date']) ? 'Suggested ' . tm_date($task['requested_due_date']) : 'Suggested due not set'; ?></span>
                                    </td>
                                    <td data-label="Department"><?php echo htmlspecialchars((string) $task['department']); ?></td>
                                    <td data-label="Priority"><?php echo tm_priority_badge($task['priority']); ?></td>
                                    <td data-label="Final Due">
                                        <span class="tm-strong"><?php echo tm_date($task['committed_due_date']); ?></span>
                                        <?php echo tm_due_chip($task['committed_due_date'], $task['status']); ?>
                                    </td>
                                    <td data-label="Status"><?php echo tm_status_badge($task['status']); ?></td>
                                    <td data-label="Progress"><?php echo tm_progress_cell($task['progress_percent']); ?></td>
                                    <!-- "2 hours ago" is what people compare against; the
                                         exact stamp stays available on hover -->
                                    <td data-label="Updated" title="<?php echo htmlspecialchars(tm_date($task['updated_on'], 'd M Y, h:i A')); ?>"><?php echo htmlspecialchars(tm_relative_time($task['updated_on'])); ?></td>
                                    <td data-label="">
                                        <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management/view/<?php echo (int) $task['id']; ?>">Open</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="10" class="tm-empty"><?php echo tm_empty_state('No tasks matched these filters', 'Clear a filter above to widen the search.'); ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="tm-no-match" hidden>
                            <td colspan="10" class="tm-empty"><?php echo tm_empty_state('Nothing matches your search', 'Try a shorter word, or clear the search box.'); ?></td>
                        </tr>
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
                        <?php foreach ($assigned_tasks as $task) { ?>
                            <tr class="tm-data-row <?php echo !empty($task['is_overdue']) ? 'is-late' : ''; ?>">
                                <td data-label="Task">
                                    <span class="tm-task-name"><?php echo htmlspecialchars($task['title']); ?></span>
                                    <span class="tm-task-meta"><?php echo htmlspecialchars($task['task_code']); ?><?php if (!empty($task['company_name'])) { ?> | <?php echo htmlspecialchars($task['company_name']); ?><?php } ?></span>
                                </td>
                                <td data-label="Assigned By">
                                    <span class="tm-strong"><?php echo htmlspecialchars($task['creator_name']); ?></span><br>
                                    <span class="tm-task-meta"><?php echo htmlspecialchars((string) $task['department']); ?></span>
                                </td>
                                <td data-label="Suggested Due"><?php echo tm_date($task['requested_due_date']); ?></td>
                                <td data-label="Final Due">
                                    <span class="tm-strong"><?php echo tm_date($task['committed_due_date']); ?></span>
                                    <?php echo tm_due_chip($task['committed_due_date'], $task['status']); ?>
                                </td>
                                <td data-label="Status"><?php echo tm_status_badge($task['status']); ?></td>
                                <td data-label="Progress"><?php echo tm_progress_cell($task['progress_percent']); ?></td>
                                <td data-label="">
                                    <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management/view/<?php echo (int) $task['id']; ?>">Open</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="tm-empty"><?php echo tm_empty_state('Nothing is assigned to you right now', 'New assignments appear here the moment somebody sends one.'); ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="tm-no-match" hidden>
                        <td colspan="7" class="tm-empty"><?php echo tm_empty_state('Nothing matches your search', 'Try a shorter word, or clear the search box.'); ?></td>
                    </tr>
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
                        <?php foreach ($created_tasks as $task) { ?>
                            <tr class="tm-data-row <?php echo !empty($task['is_overdue']) ? 'is-late' : ''; ?>">
                                <td data-label="Task">
                                    <span class="tm-task-name"><?php echo htmlspecialchars($task['title']); ?></span>
                                    <span class="tm-task-meta"><?php echo htmlspecialchars($task['task_code']); ?><?php if (!empty($task['company_name'])) { ?> | <?php echo htmlspecialchars($task['company_name']); ?><?php } ?></span>
                                </td>
                                <td data-label="Assigned To">
                                    <span class="tm-strong"><?php echo htmlspecialchars($task['assignee_name']); ?></span><br>
                                    <span class="tm-task-meta"><?php echo htmlspecialchars((string) $task['department']); ?></span>
                                </td>
                                <td data-label="Priority"><?php echo tm_priority_badge($task['priority']); ?></td>
                                <td data-label="Final Due">
                                    <span class="tm-strong"><?php echo tm_date($task['committed_due_date']); ?></span>
                                    <?php echo tm_due_chip($task['committed_due_date'], $task['status']); ?>
                                </td>
                                <td data-label="Status"><?php echo tm_status_badge($task['status']); ?></td>
                                <td data-label="Progress"><?php echo tm_progress_cell($task['progress_percent']); ?></td>
                                <td data-label="">
                                    <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management/view/<?php echo (int) $task['id']; ?>">Open</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" class="tm-empty"><?php echo tm_empty_state('You have not created any tasks yet', 'Use Create New Task to assign work to anyone in the organization.'); ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="tm-no-match" hidden>
                        <td colspan="7" class="tm-empty"><?php echo tm_empty_state('Nothing matches your search', 'Try a shorter word, or clear the search box.'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>

<?php $this->load->view('task_management/_foot', compact('page_script_view')); ?>
