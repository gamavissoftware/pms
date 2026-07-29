<?php
$page_title = 'DF Weekly Meeting Points';
$page_script_view = 'master/_dfweeklymeetingpoints_scripts';
$this->load->view('task_management/_head', compact('page_title', 'page_script_view'));
?>

<style>
    .weekly-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .weekly-pill-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .weekly-mode-note {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #eef4fb;
        color: #27486a;
        font-size: 12px;
        font-weight: 700;
    }

    .weekly-filter-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .weekly-df-name {
        display: block;
        font-size: 14px;
        font-weight: 800;
        color: #183153;
        margin-bottom: 4px;
    }

    .weekly-df-meta {
        display: block;
        color: #6f8196;
        font-size: 12px;
        line-height: 1.5;
    }

    .weekly-point-text {
        color: #1f2f45;
        font-size: 13px;
        line-height: 1.6;
        min-width: 260px;
    }

    .weekly-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 96px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .weekly-status-open {
        background: #e8f2ff;
        color: #1456ad;
    }

    .weekly-status-progress {
        background: #fff3dc;
        color: #a46200;
    }

    .weekly-status-done {
        background: #e9f7ee;
        color: #19734f;
    }

    .weekly-row-overdue td {
        background: #fff8f8;
    }

    .weekly-overdue-note {
        display: block;
        margin-top: 6px;
        font-size: 11px;
        color: #b23a3a;
        font-weight: 700;
    }

    .weekly-action-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tm-btn.tm-btn-sm {
        padding: 8px 12px;
        font-size: 12px;
        min-width: 88px;
    }

    .weekly-remarks {
        white-space: normal;
        line-height: 1.6;
    }

    .weekly-modal-label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #6d7f93;
    }

    .weekly-modal-point {
        padding: 12px 14px;
        background: #f6f9fc;
        border: 1px solid #dbe5ef;
        border-radius: 12px;
        color: #183153;
        font-size: 13px;
        line-height: 1.65;
    }

    .weekly-empty {
        text-align: center;
        color: #6f8196;
        padding: 36px 24px;
        font-size: 14px;
    }

    @media (max-width: 1400px) {
        .weekly-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .weekly-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .tm-banner {
            display: block;
        }
    }

    @media (max-width: 640px) {
        .weekly-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<div class="tm-banner">
    <div>
        <h1>DF Weekly Meeting Points</h1>
        <p>Track weekly MOM points by DF, meeting date, owner, due date, and live work status in one clean table.</p>
        <div class="weekly-pill-row">
            <span class="tm-chip"><i class="fa fa-calendar"></i> Date-wise weekly MOM tracking</span>
            <span class="tm-chip"><i class="fa fa-user"></i> Responsible person updates live status</span>
            <span class="tm-chip"><i class="fa fa-check-circle-o"></i> Clean tabular review for follow-up</span>
        </div>
    </div>
</div>

<div class="tm-flash"><?php echo $this->session->flashdata('message'); ?></div>

<div class="weekly-grid">
    <div class="tm-stat">
        <div class="tm-stat-label">Visible Points</div>
        <div class="tm-stat-value"><?php echo (int) $summary['total']; ?></div>
        <div class="tm-stat-note">Points in the current worklist</div>
    </div>
    <div class="tm-stat">
        <div class="tm-stat-label">Open</div>
        <div class="tm-stat-value"><?php echo (int) $summary['open']; ?></div>
        <div class="tm-stat-note">Still waiting for work completion</div>
    </div>
    <div class="tm-stat">
        <div class="tm-stat-label">In Progress</div>
        <div class="tm-stat-value"><?php echo (int) $summary['in_progress']; ?></div>
        <div class="tm-stat-note">Work has started and needs follow-up</div>
    </div>
    <div class="tm-stat">
        <div class="tm-stat-label">Done</div>
        <div class="tm-stat-value"><?php echo (int) $summary['done']; ?></div>
        <div class="tm-stat-note">Completed meeting points</div>
    </div>
    <div class="tm-stat">
        <div class="tm-stat-label">Overdue</div>
        <div class="tm-stat-value"><?php echo (int) $summary['overdue']; ?></div>
        <div class="tm-stat-note">Due date crossed and still not done</div>
    </div>
    <div class="tm-stat">
        <div class="tm-stat-label">DF Covered</div>
        <div class="tm-stat-value"><?php echo (int) $summary['unique_df']; ?></div>
        <div class="tm-stat-note">Unique running DFs represented here</div>
    </div>
</div>

<div class="tm-section">
    <div class="tm-section-head">
        <div>
            <h2 class="tm-section-title">Points Workboard</h2>
            <p class="tm-section-subtitle">Compact DF-wise and date-wise point tracking with clear accountability.</p>
        </div>
        <div class="weekly-filter-wrap">
            <span class="weekly-mode-note">
                <i class="fa <?php echo !empty($show_all_points) ? 'fa-eye' : 'fa-user-circle-o'; ?>"></i>
                <?php echo !empty($show_all_points) ? 'Administrator view: all assigned weekly meeting points' : 'My view: only points assigned to me'; ?>
            </span>
            <input type="text" id="weeklyMeetingPointFilter" class="form-control tm-search" placeholder="Search DF, point, owner, status">
        </div>
    </div>

    <div class="tm-table-wrap">
        <table id="weeklyMeetingPointsTable" class="tm-table">
            <thead>
                <tr>
                    <th>Meeting Date</th>
                    <th>DF</th>
                    <th>Point / Task</th>
                    <th>Responsible Person</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Latest Remarks</th>
                    <th>Updated</th>
                    <th style="min-width: 170px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($points)) { ?>
                    <?php
                    $today = date('Y-m-d');
                    foreach ($points as $point) {
                        $status_value = (int) $point->workstatus;
                        $status_label = 'Open';
                        $status_class = 'weekly-status-open';

                        if ($status_value === 1) {
                            $status_label = 'Done';
                            $status_class = 'weekly-status-done';
                        } else if ($status_value === 2) {
                            $status_label = 'In Progress';
                            $status_class = 'weekly-status-progress';
                        }

                        $is_overdue = !empty($point->due_date) && $status_value !== 1 && $point->due_date < $today;
                        $responsible_name = trim((string) $point->responsible_title . ' ' . $point->responsible_first_name . ' ' . $point->responsible_last_name);
                        $creator_name = trim((string) $point->creator_title . ' ' . $point->creator_first_name . ' ' . $point->creator_last_name);
                        $updater_name = trim((string) $point->updater_title . ' ' . $point->updater_first_name . ' ' . $point->updater_last_name);
                        $can_update = !empty($show_all_points) || (int) $point->responsible_person === (int) $current_user_id;
                        $has_valid_update_on = !empty($point->update_on)
                            && $point->update_on !== '0000-00-00 00:00:00'
                            && $point->update_on !== '0000-00-00';
                    ?>
                        <tr class="tm-data-row<?php echo $is_overdue ? ' weekly-row-overdue' : ''; ?>">
                            <td>
                                <span class="tm-strong"><?php echo date('d M Y', strtotime($point->added_on)); ?></span><br>
                                <span class="tm-task-meta"><?php echo date('h:i A', strtotime($point->added_on)); ?></span>
                            </td>
                            <td>
                                <span class="weekly-df-name"><?php echo htmlspecialchars((string) $point->df_no); ?></span>
                                <span class="weekly-df-meta"><?php echo htmlspecialchars((string) $point->df_description); ?></span>
                                <span class="weekly-df-meta">Created by <?php echo htmlspecialchars($creator_name !== '' ? $creator_name : 'System'); ?></span>
                            </td>
                            <td>
                                <div class="weekly-point-text"><?php echo nl2br(htmlspecialchars((string) $point->mom_point)); ?></div>
                            </td>
                            <td>
                                <span class="tm-strong"><?php echo htmlspecialchars($responsible_name !== '' ? $responsible_name : 'Not Assigned'); ?></span>
                            </td>
                            <td>
                                <span class="tm-strong"><?php echo !empty($point->due_date) ? date('d M Y', strtotime($point->due_date)) : '-'; ?></span>
                                <?php if ($is_overdue) { ?>
                                    <span class="weekly-overdue-note">Overdue</span>
                                <?php } ?>
                            </td>
                            <td>
                                <span class="weekly-status <?php echo $status_class; ?>"><?php echo htmlspecialchars($status_label); ?></span>
                            </td>
                            <td class="weekly-remarks">
                                <?php echo !empty($point->work_remarks) ? nl2br(htmlspecialchars((string) $point->work_remarks)) : '<span class="tm-task-meta">No remarks yet</span>'; ?>
                            </td>
                            <td>
                                <?php if ($has_valid_update_on) { ?>
                                    <span class="tm-strong"><?php echo date('d M Y', strtotime($point->update_on)); ?></span><br>
                                    <span class="tm-task-meta"><?php echo date('h:i A', strtotime($point->update_on)); ?><?php if ($updater_name !== '') { ?> by <?php echo htmlspecialchars($updater_name); ?><?php } ?></span>
                                <?php } else { ?>
                                    <span class="tm-task-meta">No update yet</span>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="weekly-action-stack">
                                    <?php if ($can_update) { ?>
                                        <button
                                            type="button"
                                            class="tm-btn tm-btn-primary tm-btn-sm js-point-update"
                                            data-point-id="<?php echo (int) $point->id; ?>"
                                            data-point-text="<?php echo htmlspecialchars((string) $point->mom_point, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-status="<?php echo (int) $point->workstatus; ?>"
                                            data-remarks="<?php echo htmlspecialchars((string) $point->work_remarks, ENT_QUOTES, 'UTF-8'); ?>"
                                        >
                                            Update
                                        </button>
                                    <?php } ?>
                                    <a class="tm-btn tm-btn-secondary tm-btn-sm" href="<?php echo page_url; ?>Task/viewdfmeetingmom/<?php echo (int) $point->df_id; ?>">
                                        View MOM
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="9" class="weekly-empty">
                            No weekly meeting points are available right now.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div id="weeklyMeetingPointModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="post" id="weeklyMeetingPointUpdateForm" action="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Update Weekly Meeting Point</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <span class="weekly-modal-label">Point / Task</span>
                        <div id="weeklyMeetingPointText" class="weekly-modal-point"></div>
                    </div>
                    <div class="form-group">
                        <label for="weeklyMeetingPointStatus" class="weekly-modal-label">Work Status</label>
                        <select name="workstatus" id="weeklyMeetingPointStatus" class="form-control" required>
                            <option value="0">Open</option>
                            <option value="2">In Progress</option>
                            <option value="1">Done</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="weeklyMeetingPointRemarks" class="weekly-modal-label">Latest Remarks</label>
                        <textarea name="work_remarks" id="weeklyMeetingPointRemarks" class="form-control" rows="4" placeholder="Add a short update for the team"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="tm-btn tm-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="tm-btn tm-btn-primary">Save Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('task_management/_foot', compact('page_script_view')); ?>
