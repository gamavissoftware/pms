<?php
$page_title = 'Task Details';
$page_script_view = 'task_management/_view_scripts';
$this->load->view('task_management/_head', compact('page_title'));

$status_key = strtolower((string) $task['status']);
$priority_key = strtolower((string) $task['priority']);
$attachment_url = !empty($task['attachment']) ? rtrim(base_url(), '/') . '/image_bank/task_management/' . rawurlencode($task['attachment']) : '';

/* Updates come back newest first, so the first row is the latest event. A
   reopened task is waiting for a due date exactly like a new one, and the
   confirm form below says which of the two situations the reader is in. */
$latest_update = !empty($updates) ? reset($updates) : array();
$was_reopened = !empty($latest_update['update_type'])
    && strtoupper((string) $latest_update['update_type']) === 'REOPENED';

/* Default for the due-date picker: the creator's suggestion while it is still
   in the future, otherwise today. After a reopen the original suggestion has
   usually long passed, and pre-filling a date in the past invites confirming
   a deadline that is already overdue. */
$suggested_due = tm_has_date($task['requested_due_date'])
    ? date('Y-m-d', strtotime((string) $task['requested_due_date'])) : '';
$due_default = ($suggested_due !== '' && $suggested_due >= date('Y-m-d'))
    ? $suggested_due : date('Y-m-d');
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
        <?php echo tm_status_badge($task['status']); ?>
        <?php echo tm_priority_badge($task['priority']); ?>
        <?php echo tm_due_chip($task['committed_due_date'], $task['status']); ?>
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
        <div class="tm-mini-note"><?php echo tm_date($task['created_on'], 'd M Y, h:i A'); ?></div>
    </div>
    <div class="tm-mini-card">
        <div class="tm-mini-label">Assigned To</div>
        <div class="tm-mini-value"><?php echo htmlspecialchars($task['assignee_name']); ?></div>
        <div class="tm-mini-note"><?php echo htmlspecialchars((string) $task['department']); ?></div>
    </div>
    <div class="tm-mini-card">
        <div class="tm-mini-label">Suggested Due Date</div>
        <div class="tm-mini-value"><?php echo tm_date($task['requested_due_date']); ?></div>
        <div class="tm-mini-note">Proposed by the creator</div>
    </div>
    <div class="tm-mini-card">
        <div class="tm-mini-label">Final Due Date</div>
        <div class="tm-mini-value"><?php echo tm_date($task['committed_due_date']); ?></div>
        <?php
        $due_chip = tm_due_chip($task['committed_due_date'], $task['status']);
        if ($due_chip !== '') {
            echo $due_chip;
        } elseif (strtoupper((string) $task['status']) === 'COMPLETED') { ?>
            <div class="tm-mini-note">Closed <?php echo htmlspecialchars(tm_relative_time($task['completed_on'])); ?></div>
        <?php } else { ?>
            <div class="tm-mini-note">Not confirmed yet</div>
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
                <p class="tm-section-subtitle">
                    <?php echo $was_reopened
                        ? 'This task was reopened. Commit to a new date for the work that is left, and it goes live again.'
                        : 'This step activates the task for live execution.'; ?>
                </p>
            </div>
            <span class="tm-soft-pill">Assignee action</span>
        </div>
        <div class="tm-section-body">
            <form method="post" action="<?php echo page_url; ?>Task_management/confirm_due_date/<?php echo (int) $task['id']; ?>">
                <div class="tm-form-grid">
                    <div class="tm-col-4 tm-field">
                        <label>Final Due Date <span style="color:#d33;">*</span></label>
                        <input type="date" class="form-control" name="committed_due_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($due_default); ?>" required>
                        <div class="tm-helper">
                            <?php echo $suggested_due !== ''
                                ? 'Creator suggested ' . htmlspecialchars(tm_date($task['requested_due_date'])) . '.'
                                : 'No date was suggested by the creator.'; ?>
                        </div>
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
                        <?php $current_status = strtoupper((string) $task['status']); ?>
                        <select class="form-control" id="progress_status" name="status" required>
                            <option value="OPEN" <?php echo $current_status === 'OPEN' ? 'selected' : ''; ?>>Open</option>
                            <option value="IN_PROGRESS" <?php echo $current_status !== 'OPEN' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="COMPLETED">Completed</option>
                        </select>
                        <div class="tm-helper">Set to Completed, or drag to 100%, to close the task.</div>
                    </div>
                    <div class="tm-col-3 tm-field">
                        <label>Progress <span style="color:#d33;">*</span> <span id="progressPercentValue" class="tm-range-value"><?php echo (int) $task['progress_percent']; ?>%</span></label>
                        <!-- a range input is not a .form-control: Bootstrap's
                             box styling gave it a 44px bordered frame with the
                             track floating inside it -->
                        <input type="range" class="tm-range" id="progress_percent" name="progress_percent" min="0" max="100" step="5" value="<?php echo (int) $task['progress_percent']; ?>">
                        <div class="tm-preset-row">
                            <?php foreach (array(25, 50, 75, 100) as $preset) { ?>
                                <button type="button" class="tm-preset" data-tm-preset="<?php echo $preset; ?>"><?php echo $preset; ?>%</button>
                            <?php } ?>
                        </div>
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

<?php if (!empty($can_reopen_task)) { ?>
    <div class="tm-section">
        <div class="tm-section-head">
            <div>
                <h2 class="tm-section-title">Reopen Task</h2>
                <p class="tm-section-subtitle">Return this task to the same assignee when the work is incomplete or needs correction.</p>
            </div>
            <span class="tm-soft-pill">Creator action</span>
        </div>
        <div class="tm-section-body">
            <form method="post" action="<?php echo page_url; ?>Task_management/reopen/<?php echo (int) $task['id']; ?>" class="js-reopen-task-form">
                <div class="tm-form-grid">
                    <div class="tm-col-12 tm-field">
                        <label>Reopen Remark <span style="color:#d33;">*</span></label>
                        <textarea class="form-control" name="reopen_remark" rows="3" maxlength="2000" placeholder="Explain what is incomplete or what correction is required." required></textarea>
                    </div>
                </div>
                <div class="tm-note-strip">
                    The task returns to <strong><?php echo htmlspecialchars($task['assignee_name']); ?></strong> at 0% progress and waits on a <strong>new final due date</strong> from them, the same way a fresh assignment does. The previous completion and the date they had committed to both remain in Update History.
                </div>
                <div class="tm-action-row">
                    <button type="submit" class="tm-btn tm-btn-primary">
                        <i class="fa fa-undo"></i> Reopen and Reassign
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
            <p class="tm-section-subtitle">All milestones, progress notes, and closure remarks - newest first.</p>
        </div>
        <span class="tm-soft-pill"><?php echo count((array) $updates); ?> update<?php echo count((array) $updates) === 1 ? '' : 's'; ?></span>
    </div>
    <div class="tm-section-body">
        <?php if (!empty($updates)) { ?>
            <!-- A TIMELINE, NOT A TABLE.
                 Six columns of one-line cells made the note - the only part
                 anybody actually reads - the narrowest thing on the row, and
                 on a phone each note wrapped to one word per line. -->
            <ul class="tm-timeline">
                <?php foreach ($updates as $update) {
                    $event = strtoupper((string) $update['update_type']);
                    $dot_class = 'is-progress';
                    if ($event === 'CREATED')                  $dot_class = 'is-created';
                    elseif ($event === 'DUE_DATE_CONFIRMED')   $dot_class = 'is-due';
                    elseif ($event === 'COMPLETED')            $dot_class = 'is-done';
                    elseif ($event === 'REOPENED')             $dot_class = 'is-reopened';

                    $event_label = ucwords(strtolower(str_replace('_', ' ', $event)));
                ?>
                    <li class="tm-tl-item <?php echo $dot_class; ?>">
                        <span class="tm-tl-dot"></span>
                        <div class="tm-tl-head">
                            <span class="tm-tl-event"><?php echo htmlspecialchars($event_label); ?></span>
                            <?php echo tm_status_badge($update['status']); ?>
                            <span class="tm-tl-who"><?php echo htmlspecialchars(!empty($update['actor_name']) ? $update['actor_name'] : 'System'); ?></span>
                            <span class="tm-tl-when">
                                <?php echo tm_date($update['created_on'], 'd M Y, h:i A'); ?>
                                &middot; <?php echo htmlspecialchars(tm_relative_time($update['created_on'])); ?>
                            </span>
                            <span class="tm-tl-when"><?php echo (int) $update['progress_percent']; ?>%</span>
                            <?php if (tm_has_date($update['due_date'])) { ?>
                                <!-- the commitment as it stood at this event - after a
                                     reopen clears the date, this row is where the previous
                                     one survives -->
                                <span class="tm-tl-when">Due <?php echo tm_date($update['due_date']); ?></span>
                            <?php } ?>
                        </div>
                        <?php if (trim((string) $update['update_note']) !== '') { ?>
                            <div class="tm-tl-note"><?php echo nl2br(htmlspecialchars((string) $update['update_note'])); ?></div>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <?php echo tm_empty_state('No updates recorded yet', 'Progress notes and closure remarks will appear here.'); ?>
        <?php } ?>
    </div>
</div>

<?php $this->load->view('task_management/_foot', compact('page_script_view')); ?>
