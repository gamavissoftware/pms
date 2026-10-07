<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="<?php echo copyright; ?>">
<link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
<title><?php echo sitetitle; ?> <?php echo !empty($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'Punch Point Closure'; ?></title>
<link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
<link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
<link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
<style>
:root {
    --closure-ink: #12324f;
    --closure-blue: #1f6070;
    --closure-sky: #dff4ff;
    --closure-surface: #ffffff;
    --closure-surface-soft: #f7fbfe;
    --closure-border: #dce8f1;
    --closure-muted: #607285;
    --closure-accent: #ff8e24;
    --closure-success-bg: #e8f7ec;
    --closure-success-text: #1f6a3a;
    --closure-warning-bg: #fff2d9;
    --closure-warning-text: #93620a;
}
body {
    background: #f3f7fb;
}
.closure-page {
    margin-top: 20px;
}
.flash-stack .alert {
    margin-bottom: 20px;
}
.closure-hero {
    background: linear-gradient(135deg, #12324f 0%, #1f6070 100%);
    border-radius: 20px;
    padding: 22px 24px;
    color: #fff;
    box-shadow: 0 18px 40px rgba(18, 50, 79, 0.18);
    margin-bottom: 18px;
}
.closure-hero-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
    flex-wrap: wrap;
}
.hero-kicker {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.hero-title {
    margin: 10px 0 8px;
    font-size: 30px;
    line-height: 1.15;
    font-weight: 700;
}
.hero-copy {
    max-width: 760px;
    margin: 0;
    color: rgba(255, 255, 255, 0.88);
    font-size: 14px;
    line-height: 1.6;
}
.hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-hero {
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.22);
    background: rgba(255, 255, 255, 0.14);
    color: #fff;
    padding: 10px 18px;
    font-weight: 600;
}
.btn-hero:hover,
.btn-hero:focus {
    color: #fff;
    background: rgba(255, 255, 255, 0.2);
}
.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}
.status-pill-live {
    background: rgba(255, 255, 255, 0.16);
    color: #fff;
}
.status-pill-done {
    background: #e7f8ec;
    color: #1b6a39;
}
.status-pill-pending {
    background: #fff0d1;
    color: #92600c;
}
.hero-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
}
.hero-chip {
    min-width: 150px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.14);
}
.hero-chip-label {
    display: block;
    margin-bottom: 6px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.72);
}
.hero-chip-value {
    font-size: 15px;
    font-weight: 700;
    line-height: 1.5;
    word-break: break-word;
}
.hero-footnote {
    margin-top: 14px;
    color: rgba(255, 255, 255, 0.76);
    font-size: 12px;
}
.section-card {
    background: var(--closure-surface);
    border: 1px solid var(--closure-border);
    border-radius: 20px;
    padding: 20px 22px;
    margin-bottom: 18px;
    box-shadow: 0 16px 34px rgba(17, 52, 82, 0.08);
}
.section-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.section-kicker {
    display: block;
    margin-bottom: 8px;
    color: var(--closure-blue);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.section-title {
    margin: 0;
    color: var(--closure-ink);
    font-size: 26px;
    line-height: 1.25;
    font-weight: 700;
}
.section-copy {
    margin: 10px 0 0;
    color: var(--closure-muted);
    font-size: 14px;
    line-height: 1.7;
    max-width: 760px;
}
.completion-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 22px;
    padding: 16px 18px;
    border-radius: 16px;
    border: 1px solid #cfe7d6;
    background: #eef9f1;
    color: var(--closure-success-text);
    font-weight: 600;
}
.completion-banner small {
    color: #447457;
    font-size: 12px;
    font-weight: 600;
}
.point-row {
    border: 1px solid var(--closure-border);
    border-radius: 18px;
    padding: 18px 18px 2px;
    margin-bottom: 16px;
    background: var(--closure-surface-soft);
}
.point-row-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
}
.point-row-index {
    display: inline-flex;
    align-items: center;
    padding: 7px 12px;
    border-radius: 999px;
    background: var(--closure-sky);
    color: var(--closure-ink);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}
.point-helper {
    color: var(--closure-muted);
    font-size: 12px;
}
.form-group label {
    color: var(--closure-ink);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.form-control {
    height: 44px;
    border-radius: 12px;
    border: 1px solid #cad9e6;
    box-shadow: none;
}
textarea.form-control {
    height: auto;
    min-height: 92px;
    resize: vertical;
}
.form-control:focus {
    border-color: #4d98aa;
    box-shadow: 0 0 0 3px rgba(77, 152, 170, 0.12);
}
.select2-container {
    width: 100% !important;
}
.select2-container .select2-selection--single {
    height: 44px !important;
    border: 1px solid #cad9e6 !important;
    border-radius: 12px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px !important;
    color: #33495e !important;
    padding-left: 14px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
    right: 8px !important;
}
.form-action-bar {
    position: sticky;
    bottom: -24px;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    margin: 22px -24px -24px;
    padding: 18px 24px;
    background: rgba(255, 255, 255, 0.97);
    border-top: 1px solid var(--closure-border);
    border-radius: 0 0 20px 20px;
}
.form-action-copy {
    color: var(--closure-muted);
    font-size: 13px;
    line-height: 1.6;
}
.form-action-buttons {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-outline-soft {
    min-height: 44px;
    padding: 10px 18px;
    border-radius: 999px;
    border: 1px solid #cad9e6;
    background: #fff;
    color: var(--closure-ink);
    font-weight: 700;
}
.btn-outline-soft:hover,
.btn-outline-soft:focus {
    background: #f4f8fb;
    color: var(--closure-ink);
}
.btn-primary-closure {
    min-height: 46px;
    padding: 11px 20px;
    border: none;
    border-radius: 999px;
    background: linear-gradient(135deg, #ff8e24 0%, #ff6b1a 100%);
    color: #fff;
    font-weight: 700;
    box-shadow: 0 14px 28px rgba(255, 107, 26, 0.22);
}
.btn-primary-closure:hover,
.btn-primary-closure:focus {
    color: #fff;
    opacity: 0.94;
}
.table-wrap {
    border: 1px solid #e3edf4;
    border-radius: 18px;
    overflow-x: auto;
}
.table {
    margin-bottom: 0;
}
.table thead th {
    background: #153754;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border-bottom: none;
}
.table tbody td {
    vertical-align: top;
    color: #2f4154;
}
.table-status-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 30px;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}
.table-status-label.label-success {
    background: var(--closure-success-bg);
    color: var(--closure-success-text);
}
.table-status-label.label-warning {
    background: var(--closure-warning-bg);
    color: var(--closure-warning-text);
}
.point-meta {
    display: block;
    margin-top: 6px;
    color: #6f8191;
    font-size: 12px;
}
.small-note {
    font-size: 12px;
    color: #6f8191;
}
.empty-state {
    border: 1px dashed #d6e4ee;
    border-radius: 18px;
    padding: 26px 20px;
    background: #f9fbfd;
    text-align: center;
    color: #6b7b89;
}
.workflow-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 0;
}
.summary-chip-soft {
    min-width: 170px;
    padding: 12px 14px;
    border-radius: 14px;
    border: 1px solid var(--closure-border);
    background: #f8fbfd;
    flex: 1 1 0;
}
.summary-chip-soft-label {
    display: block;
    margin-bottom: 6px;
    color: var(--closure-muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
}
.summary-chip-soft-value {
    color: var(--closure-ink);
    font-size: 16px;
    font-weight: 700;
    line-height: 1.4;
}
.summary-chip-soft-note {
    display: block;
    margin-top: 4px;
    color: #6f8191;
    font-size: 12px;
}
.board-divider {
    height: 1px;
    margin: 18px 0;
    background: #e3edf4;
}
.panel-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.panel-title {
    margin: 0;
    color: var(--closure-ink);
    font-size: 23px;
    line-height: 1.25;
    font-weight: 700;
}
.panel-copy {
    margin: 6px 0 0;
    color: var(--closure-muted);
    font-size: 13px;
    line-height: 1.6;
    max-width: 720px;
}
.action-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    margin-top: 20px;
    padding: 18px 20px;
    border-radius: 18px;
    border: 1px solid #f2d89d;
    background: #fff7e5;
    color: #7b5b18;
}
.action-banner-ready {
    border-color: #cfe7d6;
    background: #eef9f1;
    color: #1f6a3a;
}
.action-banner-title {
    display: block;
    margin-bottom: 4px;
    font-size: 16px;
    font-weight: 700;
}
.action-banner-copy {
    font-size: 13px;
    line-height: 1.6;
}
@media (max-width: 991px) {
    .hero-title {
        font-size: 28px;
    }
    .section-title {
        font-size: 23px;
    }
}
@media (max-width: 767px) {
    .closure-hero,
    .section-card {
        padding: 18px;
        border-radius: 16px;
    }
    .hero-chip {
        min-width: 100%;
    }
    .form-action-bar {
        position: static;
        margin: 18px 0 0;
        padding: 16px 0 0;
        border-top: 1px solid var(--closure-border);
        border-radius: 0;
    }
    .btn-outline-soft,
    .btn-primary-closure,
    .btn-hero {
        width: 100%;
        justify-content: center;
    }
    .form-action-buttons,
    .hero-actions {
        width: 100%;
    }
}
</style>
</head>
<body>
<header id="topnav">
<?php $this->load->view('common/nav-menu'); ?>
</header>

<?php
$department_options_html = '<option value="">--SELECT DEPARTMENT--</option>';
if (!empty($department_options)) {
    foreach ($department_options as $department_row) {
        $department_options_html .= '<option value="' . (int) $department_row->department_id . '">' . htmlspecialchars(strtoupper($department_row->department), ENT_QUOTES, 'UTF-8') . '</option>';
    }
}

$df_label = '';
if (!empty($task_row->df_no)) {
    $df_label = strtoupper($task_row->df_no);
} elseif (!empty($task_row->df_number)) {
    $df_label = 'DF-' . $task_row->df_number;
}

$task_owner_name = strtoupper(trim($task_row->first_name . ' ' . $task_row->last_name));
$task_status = (int) $task_row->task_status;
$is_task_open = ($task_status === 0);
$current_task_id = (int) $task_row->taskid;
$page_mode = !empty($page_mode) ? $page_mode : 'report';
$page_title = !empty($page_title) ? $page_title : 'Punch Point Closure';
$is_list_mode = ($page_mode === 'list');
$is_closure_mode = ($page_mode === 'closure');
$is_report_mode = ($page_mode === 'report');
$total_items = !empty($workflow_state['total_items']) ? (int) $workflow_state['total_items'] : 0;
$completed_items = !empty($workflow_state['completed_items']) ? (int) $workflow_state['completed_items'] : 0;
$can_add_points = !empty($workflow_state['can_add_points']);
$can_complete_task = !empty($workflow_state['can_complete_task']);
$workflow_block_message = !empty($workflow_state['block_message']) ? $workflow_state['block_message'] : '';
$related_task_51_done = !empty($workflow_state['related_task_51_done']);
$list_attachment = '';
foreach ($closure_items as $attachment_item) {
    if (!empty($attachment_item->attachment)) {
        $list_attachment = (string) $attachment_item->attachment;
        break;
    }
}
$hero_kicker = 'Punch Point List';
$hero_copy = 'Add all required punch points in one submission.';
$section_kicker = 'Add Points';
$section_title = 'Assign punch points';
$section_copy = 'Keep each point focused on one action and one owner, then submit once.';
$table_copy = 'Every point created from Punch Point List is shown here with its current progress.';

if ($is_closure_mode) {
    $hero_kicker = 'Punch Point Closure';
    $hero_copy = 'Review delegated points and close this step only after every point is marked done.';
    $section_kicker = 'Closure Review';
    $section_title = 'Review delegated points';
    $section_copy = 'This step stays open until all delegated punch point items are completed.';
    $table_copy = 'Track each delegated point, the owner, and the live completion status before closing Punch Point Closure.';
} else if ($is_report_mode) {
    $hero_kicker = 'Punch Point Report';
    $hero_copy = 'View punch point progress and close this report only after Punch Point Closure is completed.';
    $section_kicker = 'Closure Report';
    $section_title = 'Track punch point progress';
    $section_copy = 'This final report stays open until Punch Point Closure is marked done.';
    $table_copy = 'This report gives you one clean view of every point created in Punch Point List and its latest status.';
}

$status_label = 'Open';
$status_class = 'status-pill-live';
if ($task_status === 1) {
    $status_label = 'Done';
    $status_class = 'status-pill-done';
} elseif ($task_status === 2) {
    $status_label = 'Pending Approval';
    $status_class = 'status-pill-pending';
}
$completed_on_text = '';
if (!empty($task_row->task_completed_on) && $task_row->task_completed_on !== '0000-00-00 00:00:00') {
    $completed_on_text = date('d-M-Y h:i A', strtotime($task_row->task_completed_on));
}
?>

<div class="wrapper">
<div class="container-fluid">
<div class="row closure-page">
    <div class="col-lg-12">
        <div class="flash-stack"><?php echo $this->session->flashdata('message'); ?></div>

        <div class="closure-hero">
            <div class="closure-hero-top">
                <div>
                    <span class="hero-kicker"><?php echo htmlspecialchars($hero_kicker, ENT_QUOTES, 'UTF-8'); ?></span>
                    <h1 class="hero-title" style="color:#fff;"><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="hero-copy"><?php echo htmlspecialchars($hero_copy, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="hero-actions">
                    <span class="status-pill <?php echo $status_class; ?>"><?php echo htmlspecialchars($status_label, ENT_QUOTES, 'UTF-8'); ?></span>
                    <a href="<?php echo page_url; ?>Dashboard" class="btn btn-hero">Back to Dashboard</a>
                </div>
            </div>

            <div class="hero-meta">
                <div class="hero-chip">
                    <span class="hero-chip-label">DF No.</span>
                    <span class="hero-chip-value"><?php echo $df_label !== '' ? htmlspecialchars($df_label, ENT_QUOTES, 'UTF-8') : 'NA'; ?></span>
                </div>
                <div class="hero-chip">
                    <span class="hero-chip-label">Customer</span>
                    <span class="hero-chip-value"><?php echo !empty($task_row->company_name) ? htmlspecialchars(strtoupper($task_row->company_name), ENT_QUOTES, 'UTF-8') : 'NA'; ?></span>
                </div>
                <div class="hero-chip">
                    <span class="hero-chip-label">Task Owner</span>
                    <span class="hero-chip-value"><?php echo $task_owner_name !== '' ? htmlspecialchars($task_owner_name, ENT_QUOTES, 'UTF-8') : 'NA'; ?></span>
                </div>
                <div class="hero-chip">
                    <span class="hero-chip-label">Task Due Date</span>
                    <span class="hero-chip-value"><?php echo !empty($task_row->end_date) && $task_row->end_date !== '0000-00-00' ? date('d-M-Y', strtotime($task_row->end_date)) : 'NA'; ?></span>
                </div>
            </div>

            <div class="hero-footnote">
                <?php echo htmlspecialchars(strtoupper($task_row->task_name), ENT_QUOTES, 'UTF-8'); ?>
                <?php if (!empty($task_row->pono)) { ?>
                    | PO NO. <?php echo htmlspecialchars(strtoupper($task_row->pono), ENT_QUOTES, 'UTF-8'); ?>
                <?php } ?>
                <?php if (!empty($task_row->df_added_on) && $task_row->df_added_on !== '0000-00-00 00:00:00') { ?>
                    | DF RELEASE <?php echo date('d-M-Y', strtotime($task_row->df_added_on)); ?>
                <?php } ?>
            </div>
        </div>

        <div class="section-card" id="task-progress">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Task remarks &amp; tickets</h2>
                    <p class="section-copy">Share the current progress, reason for delay, or help needed for this task.</p>
                </div>
                <a class="btn btn-outline-soft" href="<?php echo page_url . 'Task/viewdfwiseticket/' . (int) $task_row->df_id . '/' . (int) $task_row->id; ?>">View Tickets</a>
            </div>
            <?php if (!empty($task_row->remarks)) { ?>
                <p><strong>Latest remarks</strong><br><?php echo nl2br(htmlspecialchars($task_row->remarks, ENT_QUOTES, 'UTF-8')); ?></p>
            <?php } ?>
            <?php if ($is_task_open) { ?>
                <form method="post" action="<?php echo page_url; ?>Task/save_punch_point_progress">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="task_record_id" value="<?php echo (int) $task_row->id; ?>">
                    <div class="form-group">
                        <label for="progress-remarks">Progress / delay remarks</label>
                        <textarea class="form-control" id="progress-remarks" name="taskremarks" required rows="3" placeholder="What is the current status? Explain any delay or assistance needed."></textarea>
                    </div>
                    <div class="checkbox"><label><input type="checkbox" id="raise-ticket" name="raise_ticket" value="1"> Raise a ticket for this task</label></div>
                    <div id="ticket-fields" class="row" hidden>
                        <div class="col-sm-6 form-group">
                            <label for="ticket-department">Ticket department</label>
                            <select class="form-control" id="ticket-department" name="ticket_department" disabled><?php echo $department_options_html; ?></select>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="ticket-user">Assign ticket to</label>
                            <select class="form-control" id="ticket-user" name="ticket_user" disabled><option value="">Select user</option></select>
                            <p id="ticket-auto" class="small-note" hidden>IT Support tickets are assigned automatically.</p>
                        </div>
                    </div>
                    <button class="btn btn-primary-closure" type="submit" id="save-progress">Save Remarks</button>
                    <p class="small-note">The task stays open. Use the workflow completion action below when ready.</p>
                </form>
            <?php } ?>
        </div>

        <?php if (!$is_task_open) { ?>
            <div class="completion-banner">
                <div><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?> has already been completed and is no longer pending.</div>
                <?php if ($completed_on_text !== '') { ?>
                    <small>Completed on <?php echo htmlspecialchars($completed_on_text, ENT_QUOTES, 'UTF-8'); ?></small>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="section-card">
            <div class="workflow-summary">
                <div class="summary-chip-soft">
                    <span class="summary-chip-soft-label">Points Status</span>
                    <span class="summary-chip-soft-value"><?php echo $completed_items; ?> / <?php echo $total_items; ?> Done</span>
                    <span class="summary-chip-soft-note"><?php echo $total_items > 0 ? 'Live point-level status from delegated tasks.' : 'Available after Punch Point List submission.'; ?></span>
                </div>
                <div class="summary-chip-soft">
                    <span class="summary-chip-soft-label"><?php echo $is_report_mode ? 'Closure Status' : 'Current Rule'; ?></span>
                    <span class="summary-chip-soft-value">
                        <?php
                        if ($is_list_mode) {
                            echo 'Submit Once';
                        } else if ($is_closure_mode) {
                            echo !empty($workflow_state['all_items_done']) ? 'Ready To Close' : 'Waiting For Pending Points';
                        } else {
                            echo $related_task_51_done ? 'Done' : 'Open';
                        }
                        ?>
                    </span>
                    <span class="summary-chip-soft-note">
                        <?php
                        if ($is_list_mode) {
                            echo 'Punch Point List closes right after a successful submission.';
                        } else if ($is_closure_mode) {
                            echo 'Punch Point Closure closes only when every point is done.';
                        } else {
                            echo 'This report closes only after Punch Point Closure is done.';
                        }
                        ?>
                    </span>
                </div>
                <div class="summary-chip-soft">
                    <span class="summary-chip-soft-label">Task Status</span>
                    <span class="summary-chip-soft-value"><?php echo htmlspecialchars($status_label, ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="summary-chip-soft-note"><?php echo htmlspecialchars(strtoupper($task_row->task_name), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>

            <?php if ($is_task_open && $is_list_mode && $can_add_points) { ?>
                <div class="board-divider"></div>
                <div class="section-head">
                    <div>
                        <span class="section-kicker"><?php echo htmlspecialchars($section_kicker, ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3 class="section-title"><?php echo htmlspecialchars($section_title, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="section-copy"><?php echo htmlspecialchars($section_copy, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <span class="status-pill status-pill-done">Punch Point List closes on submit</span>
                </div>

                <form method="post" action="<?php echo page_url; ?>Task/save_punch_point_closure" enctype="multipart/form-data">
                    <input type="hidden" name="task_record_id" value="<?php echo (int) $task_row->id; ?>">
                    <div id="closurePointRows">
                        <div class="point-row" data-row-index="0">
                            <div class="point-row-head">
                                <span class="point-row-index">Point 1</span>
                                <span class="point-helper">One point = one delegated action</span>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Point Title <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="point_title[]" placeholder="Example: Share revised punch layout" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Department <span style="color:red;">*</span></label>
                                        <select class="form-control closure-department" name="department_id[]" data-row-index="0" required>
                                            <?php echo $department_options_html; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Delegate To <span style="color:red;">*</span></label>
                                        <select class="form-control closure-user" name="delegate_to[]" id="closure_user_0" required>
                                            <option value="">--SELECT USER--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Due Date <span style="color:red;">*</span></label>
                                        <input type="date" class="form-control" name="due_date[]" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label>Point Description</label>
                                        <textarea class="form-control" name="point_description[]" rows="3" placeholder="Optional details for the delegated person"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block remove-point-row" style="display:none;">Remove Point</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="point-row" style="margin-top:16px;">
                        <div class="point-row-head">
                            <span class="point-row-index"><i class="fa fa-paperclip"></i> Punch Point List Attachment</span>
                            <span class="point-helper">One attachment for the complete list</span>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Attach File</label>
                                    <input type="file" class="form-control" name="punch_point_attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.zip">
                                    <small class="small-note">Optional · Images, PDF, Word, Excel, CSV or ZIP · Maximum 10 MB</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-action-bar">
                        <div class="form-action-copy">
                            Submit only after all points are added. Punch Point List will be marked done immediately after a successful submission.
                        </div>
                        <div class="form-action-buttons">
                            <button type="button" class="btn btn-outline-soft" id="addMoreClosurePoint">Add Another Point</button>
                            <button type="submit" class="btn btn-primary-closure">Submit and Mark Task Done</button>
                        </div>
                    </div>
                </form>
            <?php } ?>

            <div class="board-divider"></div>

            <div class="panel-head">
                <div>
                    <span class="section-kicker"><?php echo $is_list_mode ? 'Delegated Work' : 'Progress View'; ?></span>
                    <h3 class="panel-title"><?php echo $is_list_mode ? 'Closure points already assigned' : 'Punch point item status'; ?></h3>
                    <p class="panel-copy"><?php echo htmlspecialchars($table_copy, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <span class="status-pill status-pill-live"><?php echo count($closure_items); ?> Point<?php echo count($closure_items) === 1 ? '' : 's'; ?></span>
            </div>

            <?php if ($list_attachment !== '') { ?>
                <div class="action-banner action-banner-ready" style="margin-bottom:18px;">
                    <div>
                        <span class="action-banner-title"><i class="fa fa-paperclip"></i> Punch Point List Attachment</span>
                        <div class="action-banner-copy">One common supporting file attached with this punch point list.</div>
                    </div>
                    <a href="<?php echo base_url('image_bank/punch_point_closure/' . rawurlencode($list_attachment)); ?>" target="_blank" download class="btn btn-primary-closure">
                        <i class="fa fa-download"></i> Download Attachment
                    </a>
                </div>
            <?php } ?>

            <?php if (!empty($closure_items)) { ?>
                <div class="table-wrap">
                    <table class="table table-bordered table-striped" id="closurePointsTable">
                        <thead>
                            <tr>
                                <th>Point</th>
                                <th>Department</th>
                                <th>Delegated To</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Latest Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($closure_items as $closure_item) { ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars(strtoupper($closure_item->point_title), ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <?php if (!empty($closure_item->point_description)) { ?>
                                            <br><small><?php echo nl2br(htmlspecialchars($closure_item->point_description, ENT_QUOTES, 'UTF-8')); ?></small>
                                        <?php } ?>
                                        <?php if (!empty($closure_item->case_no)) { ?>
                                            <span class="point-meta">CASE NO. <?php echo htmlspecialchars(strtoupper($closure_item->case_no), ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php } ?>
                                        <?php if (!empty($closure_item->added_on) && $closure_item->added_on !== '0000-00-00 00:00:00') { ?>
                                            <span class="point-meta">Added on <?php echo date('d-M-Y h:i A', strtotime($closure_item->added_on)); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo !empty($closure_item->department) ? htmlspecialchars(strtoupper($closure_item->department), ENT_QUOTES, 'UTF-8') : 'NA'; ?></td>
                                    <td><?php echo !empty($closure_item->delegate_name) ? htmlspecialchars($closure_item->delegate_name, ENT_QUOTES, 'UTF-8') : 'NA'; ?></td>
                                    <td><?php echo !empty($closure_item->due_date) && $closure_item->due_date !== '0000-00-00' ? date('d-M-Y', strtotime($closure_item->due_date)) : 'NA'; ?></td>
                                    <td>
                                        <span class="table-status-label <?php echo $closure_item->response_status_class; ?>">
                                            <?php echo htmlspecialchars($closure_item->response_status_label, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($closure_item->response_remarks)) {
                                            echo nl2br(htmlspecialchars($closure_item->response_remarks, ENT_QUOTES, 'UTF-8'));
                                            if (!empty($closure_item->response_updated_on) && $closure_item->response_updated_on !== '0000-00-00 00:00:00') {
                                                echo '<span class="point-meta">Updated on ' . date('d-M-Y h:i A', strtotime($closure_item->response_updated_on)) . '</span>';
                                            }
                                        } else {
                                            echo '<span class="small-note">No response yet</span>';
                                        }
                                        if (!empty($closure_item->response_attachment)) {
                                            echo '<span class="point-meta"><a href="' . delegationfile . rawurlencode($closure_item->response_attachment) . '" target="_blank" download><i class="fa fa-download"></i> Download completion proof</a></span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="empty-state">
                    <?php echo $is_list_mode ? 'No closure points have been delegated yet.' : 'Punch point items from Punch Point List are not available yet.'; ?>
                </div>
            <?php } ?>

            <?php if ($is_task_open && !$is_list_mode) { ?>
                <div class="action-banner <?php echo $can_complete_task ? 'action-banner-ready' : ''; ?>">
                    <div>
                        <span class="action-banner-title">
                            <?php
                            if ($can_complete_task) {
                                echo $is_closure_mode ? 'All delegated points are complete.' : 'Punch Point Closure is complete.';
                            } else {
                                echo 'Action blocked';
                            }
                            ?>
                        </span>
                        <div class="action-banner-copy">
                            <?php
                            if ($can_complete_task) {
                                echo $is_closure_mode
                                    ? 'You can now mark Punch Point Closure done.'
                                    : 'You can now mark Punch Point Closure Report done.';
                            } else {
                                echo htmlspecialchars($workflow_block_message, ENT_QUOTES, 'UTF-8');
                            }
                            ?>
                        </div>
                    </div>

                    <?php if ($can_complete_task) { ?>
                        <form method="post" action="<?php echo page_url; ?>Task/complete_punch_point_workflow_task" style="margin:0;">
                            <input type="hidden" name="task_record_id" value="<?php echo (int) $task_row->id; ?>">
                            <button type="submit" class="btn btn-primary-closure"><?php echo $is_closure_mode ? 'Mark Closure Done' : 'Mark Report Done'; ?></button>
                        </form>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php $this->load->view('common/footer'); ?>
</div>
</div>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/detect.js"></script>
<script src="<?php echo assets_url; ?>js/fastclick.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url; ?>js/waves.js"></script>
<script src="<?php echo assets_url; ?>js/wow.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

<script>
var ticketUsers = <?php echo json_encode($ticket_users, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
$('#raise-ticket').on('change', function() {
    var enabled = this.checked;
    $('#ticket-fields').prop('hidden', !enabled);
    $('#ticket-department').prop('disabled', !enabled).prop('required', enabled);
    $('#save-progress').text(enabled ? 'Save Remarks & Raise Ticket' : 'Save Remarks');
    $('#ticket-department').trigger('change');
});
$('#ticket-department').on('change', function() {
    var department = this.value;
    var enabled = $('#raise-ticket').prop('checked');
    var automatic = department === '18';
    var select = $('#ticket-user').empty().append($('<option>').val('').text('Select user'));
    ticketUsers.forEach(function(user) {
        if (String(user.department_id) === department) {
            select.append($('<option>').val(user.user_id).text(user.first_name + ' ' + user.last_name));
        }
    });
    select.prop('disabled', !enabled || automatic).prop('required', enabled && !automatic);
    $('#ticket-auto').prop('hidden', !automatic);
});

var closurePointRowIndex = 1;
var departmentOptionsHtml = <?php echo json_encode($department_options_html); ?>;

function initializeSelect2(elements) {
    if ($.fn.select2) {
        var $elements = elements ? $(elements) : $('.closure-department, .closure-user');
        $elements.each(function() {
            var $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({
                width: '100%'
            });
        });
    }
}

function loadUsersForDepartment(rowIndex, selectedUserId) {
    var departmentId = $('.closure-department[data-row-index="' + rowIndex + '"]').val();
    var $userSelect = $('#closure_user_' + rowIndex);

    if (!departmentId) {
        $userSelect.html('<option value="">--SELECT USER--</option>').trigger('change');
        return;
    }

    $.ajax({
        type: 'POST',
        url: '<?php echo page_url; ?>Task/punch_point_department_users',
        data: 'department=' + departmentId,
        success: function(data) {
            $userSelect.html(data);
            if (selectedUserId) {
                $userSelect.val(selectedUserId);
            }
            $userSelect.trigger('change');
        }
    });
}

function refreshPointRowHeaders() {
    $('#closurePointRows .point-row').each(function(index) {
        $(this).find('.point-row-index').text('Point ' + (index + 1));
        $(this).find('.remove-point-row').toggle(index > 0);
    });
}

function addClosurePointRow() {
    var rowHtml = ''
        + '<div class="point-row" data-row-index="' + closurePointRowIndex + '">'
        + '    <div class="point-row-head"><span class="point-row-index">Point ' + (closurePointRowIndex + 1) + '</span><span class="point-helper">One point = one delegated action</span></div>'
        + '    <div class="row">'
        + '        <div class="col-md-4"><div class="form-group"><label>Point Title <span style="color:red;">*</span></label><input type="text" class="form-control" name="point_title[]" placeholder="Example: Share revised punch layout" required></div></div>'
        + '        <div class="col-md-3"><div class="form-group"><label>Department <span style="color:red;">*</span></label><select class="form-control closure-department" name="department_id[]" data-row-index="' + closurePointRowIndex + '" required>' + departmentOptionsHtml + '</select></div></div>'
        + '        <div class="col-md-3"><div class="form-group"><label>Delegate To <span style="color:red;">*</span></label><select class="form-control closure-user" name="delegate_to[]" id="closure_user_' + closurePointRowIndex + '" required><option value="">--SELECT USER--</option></select></div></div>'
        + '        <div class="col-md-2"><div class="form-group"><label>Due Date <span style="color:red;">*</span></label><input type="date" class="form-control" name="due_date[]" required></div></div>'
        + '    </div>'
        + '    <div class="row">'
        + '        <div class="col-md-10"><div class="form-group"><label>Point Description</label><textarea class="form-control" name="point_description[]" rows="3" placeholder="Optional details for the delegated person"></textarea></div></div>'
        + '        <div class="col-md-2"><div class="form-group"><label>&nbsp;</label><button type="button" class="btn btn-danger btn-block remove-point-row">Remove Point</button></div></div>'
        + '    </div>'
        + '</div>';

    var $row = $(rowHtml);
    $('#closurePointRows').append($row);
    initializeSelect2($row.find('.closure-department, .closure-user'));
    refreshPointRowHeaders();
    closurePointRowIndex++;
}

$(document).ready(function() {
    initializeSelect2();
    refreshPointRowHeaders();

    $('#addMoreClosurePoint').on('click', function() {
        addClosurePointRow();
    });

    $(document).on('change', '.closure-department', function() {
        var rowIndex = $(this).data('row-index');
        loadUsersForDepartment(rowIndex, '');
    });

    $(document).on('click', '.remove-point-row', function() {
        $(this).closest('.point-row').remove();
        refreshPointRowHeaders();
    });
});
</script>
</body>
</html>
