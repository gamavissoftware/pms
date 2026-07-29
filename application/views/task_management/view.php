<?php
$page_title = 'Task Details';
$page_script_view = 'task_management/_view_scripts';
$this->load->view('task_management/_head', compact('page_title'));

$status_key = strtolower((string) $task['status']);
$priority_key = strtolower((string) $task['priority']);
$attachment_url = !empty($task['attachment']) ? rtrim(base_url(), '/') . '/image_bank/task_management/' . rawurlencode($task['attachment']) : '';
?>

<div class="tm-banner">
    <div>
        <h1><?php echo htmlspecialchars($task['task_code']); ?></h1>
        <p style="font-size:22px; font-weight:800; color:#183153; margin-top:6px;"><?php echo htmlspecialchars($task['title']); ?></p>
        <div class="tm-banner-meta">
            <span class="tm-chip"><i class="fa fa-user"></i> From <?php echo htmlspecialchars($task['creator_name']); ?></span>
            <span class="tm-chip"><i class="fa fa-user-circle-o"></i> To <?php echo htmlspecialchars($task['assignee_name']); ?></span>
            <?php if (!empty($task['company_name'])) { ?>
                <span class="tm-chip"><i class="fa fa-building-o"></i> <?php echo htmlspecialchars($task['company_name']); ?></span>
            <?php } ?>
        </div>
    </div>
    <div class="tm-banner-actions">
        <span class="tm-status tm-status-<?php echo htmlspecialchars($status_key); ?>">
            <?php echo htmlspecialchars(ucwords(strtolower(str_replace('_', ' ', (string) $task['status'])))); ?>
        </span>
        <span class="tm-priority tm-priority-<?php echo htmlspecialchars($priority_key); ?>">
            <?php echo htmlspecialchars(ucwords(strtolower((string) $task['priority']))); ?>
        </span>
        <a class="tm-btn tm-btn-secondary" href="<?php echo page_url; ?>Task_management">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<?php $this->load->view('task_management/_module_nav', array('module_nav' => $module_nav, 'module_nav_active' => 'dashboard')); ?>

<div class="tm-flash"><?php echo $this->session->flashdata('message'); ?></div>

<div class="tm-mini-grid">
    <div class="tm-mini-card">
        <div class="tm-mini-label">Assigned By</div>
        <div class="tm-mini-value"><?php echo htmlspecialchars($task['creator_name']); ?></div>
        <div class="tm-mini-note"><?php echo !empty($task['created_on']) ? date('d M Y, h:i A', strtotime($task['created_on'])) : ''; ?></div>
    </div>
    <div class="tm-mini-card">
        <div class="tm-mini-label">Assigned To</div>
        <div class="tm-mini-value"><?php echo htmlspecialchars($task['assignee_name']); ?></div>
        <div class="tm-mini-note"><?php echo htmlspecialchars((string) $task['department']); ?></div>
    </div>
    <div class="tm-mini-card">
        <div class="tm-mini-label">Suggested Due Date</div>
        <div class="tm-mini-value"><?php echo !empty($task['requested_due_date']) && $task['requested_due_date'] !== '0000-00-00' ? date('d M Y', strtotime($task['requested_due_date'])) : '-'; ?></div>
    </div>
    <div class="tm-mini-card">
        <div class="tm-mini-label">Final Due Date</div>
        <div class="tm-mini-value"><?php echo !empty($task['committed_due_date']) && $task['committed_due_date'] !== '0000-00-00' ? date('d M Y', strtotime($task['committed_due_date'])) : '-'; ?></div>
        <?php if (!empty($task['is_overdue'])) { ?>
            <div class="tm-mini-note" style="color:#b93232;">Overdue</div>
        <?php } ?>
    </div>
    <div class="tm-mini-card">
        <div class="tm-mini-label">Progress</div>
        <div class="tm-mini-value"><?php echo (int) $task['progress_percent']; ?>%</div>
        <div class="tm-progress-track">
            <span class="tm-progress-bar" style="width: <?php echo (int) $task['progress_percent']; ?>%;"></span>
        </div>
    </div>
</div>

<div class="tm-section">
    <div class="tm-section-head">
        <div>
            <h2 class="tm-section-title">Task Brief</h2>
            <p class="tm-section-subtitle">The working note and reference links stay here.</p>
        </div>
        <div class="tm-inline-links">
            <?php if (!empty($task['reference_url'])) { ?>
                <a class="tm-btn tm-btn-secondary" href="<?php echo htmlspecialchars($task['reference_url']); ?>" target="_blank">
                    <i class="fa fa-external-link"></i> Open Reference
                </a>
            <?php } ?>
            <?php if (!empty($attachment_url)) { ?>
                <a class="tm-btn tm-btn-secondary" href="<?php echo htmlspecialchars($attachment_url); ?>" target="_blank">
                    <i class="fa fa-paperclip"></i> Open Attachment
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="tm-section-body">
        <div class="tm-note-strip" style="margin-bottom:0;">
            <?php echo nl2br(htmlspecialchars((string) $task['task_details'])); ?>
        </div>
    </div>
</div>

<?php if (!empty($can_set_due_date)) { ?>
    <div class="tm-section">
        <div class="tm-section-head">
            <div>
                <h2 class="tm-section-title">Confirm Final Due Date</h2>
                <p class="tm-section-subtitle">This step activates the task for live execution.</p>
            </div>
            <span class="tm-soft-pill">Assignee action</span>
        </div>
        <div class="tm-section-body">
            <form method="post" action="<?php echo page_url; ?>Task_management/confirm_due_date/<?php echo (int) $task['id']; ?>">
                <div class="tm-form-grid">
                    <div class="tm-col-4 tm-field">
                        <label>Final Due Date <span style="color:#d33;">*</span></label>
                        <input type="date" class="form-control" name="committed_due_date" value="<?php echo !empty($task['requested_due_date']) && $task['requested_due_date'] !== '0000-00-00' ? date('Y-m-d', strtotime($task['requested_due_date'])) : ''; ?>" required>
                    </div>
                    <div class="tm-col-8 tm-field">
                        <label>Schedule Note <span style="color:#d33;">*</span></label>
                        <input type="text" class="form-control" name="schedule_note" placeholder="Example: I will close this after the vendor confirmation call." required>
                    </div>
                </div>
                <div class="tm-action-row">
                    <button type="submit" class="tm-btn tm-btn-primary">
                        <i class="fa fa-check-circle"></i> Confirm Due Date
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>

<?php if (!empty($can_update_progress)) { ?>
    <div class="tm-section">
        <div class="tm-section-head">
            <div>
                <h2 class="tm-section-title">Live Progress Update</h2>
                <p class="tm-section-subtitle">Each update notifies the creator and keeps leaders in loop by email.</p>
            </div>
            <span class="tm-soft-pill">Assignee action</span>
        </div>
        <div class="tm-section-body">
            <form method="post" action="<?php echo page_url; ?>Task_management/save_progress/<?php echo (int) $task['id']; ?>">
                <div class="tm-form-grid">
                    <div class="tm-col-3 tm-field">
                        <label>Status <span style="color:#d33;">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="OPEN">Open</option>
                            <option value="IN_PROGRESS" selected>In Progress</option>
                            <option value="COMPLETED">Completed</option>
                        </select>
                    </div>
                    <div class="tm-col-3 tm-field">
                        <label>Progress <span style="color:#d33;">*</span> <span id="progressPercentValue" class="tm-range-value"><?php echo (int) $task['progress_percent']; ?>%</span></label>
                        <input type="range" class="form-control" id="progress_percent" name="progress_percent" min="0" max="100" step="5" value="<?php echo (int) $task['progress_percent']; ?>">
                    </div>
                    <div class="tm-col-6 tm-field">
                        <label>Progress Note <span style="color:#d33;">*</span></label>
                        <input type="text" class="form-control" name="progress_note" placeholder="Example: First draft shared with finance team." required>
                    </div>
                </div>
                <div class="tm-action-row">
                    <button type="submit" class="tm-btn tm-btn-primary">
                        <i class="fa fa-refresh"></i> Save Update
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php } ?>

<div class="tm-section">
    <div class="tm-section-head">
        <div>
            <h2 class="tm-section-title">Update History</h2>
            <p class="tm-section-subtitle">All milestones, progress notes, and closure remarks.</p>
        </div>
    </div>
    <div class="tm-table-wrap">
        <table class="tm-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Updated By</th>
                    <th>Event</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($updates)) { ?>
                    <?php foreach ($updates as $update) {
                        $update_status_key = strtolower((string) $update['status']);
                    ?>
                        <tr>
                            <td><?php echo !empty($update['created_on']) ? date('d M Y, h:i A', strtotime($update['created_on'])) : '-'; ?></td>
                            <td><?php echo htmlspecialchars(!empty($update['actor_name']) ? $update['actor_name'] : 'System'); ?></td>
                            <td><?php echo htmlspecialchars(ucwords(strtolower(str_replace('_', ' ', (string) $update['update_type'])))); ?></td>
                            <td>
                                <span class="tm-status tm-status-<?php echo htmlspecialchars($update_status_key); ?>">
                                    <?php echo htmlspecialchars(ucwords(strtolower(str_replace('_', ' ', (string) $update['status'])))); ?>
                                </span>
                            </td>
                            <td><?php echo (int) $update['progress_percent']; ?>%</td>
                            <td><?php echo nl2br(htmlspecialchars((string) $update['update_note'])); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="tm-empty">No updates recorded yet.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('task_management/_foot', compact('page_script_view')); ?>
