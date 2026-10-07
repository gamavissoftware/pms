<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Visit MOM Detail</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7fb; }
        .wrapper { padding-top: 80px; padding-bottom: 28px; }
        .card-box { border-radius: 16px; box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06); padding: 24px; margin-bottom: 20px; background: #fff; border: 1px solid #edf2f7; }
        .page-title { font-weight: 700; margin-bottom: 8px; }
        .page-subtitle { color: #64748b; margin-bottom: 0; }
        .status-pill { display: inline-block; border-radius: 999px; padding: 7px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
        .status-scheduled { background: #dbeafe; color: #1d4ed8; }
        .status-on-site { background: #fef3c7; color: #b45309; }
        .status-completed { background: #dcfce7; color: #15803d; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }
        .visit-hero { background: linear-gradient(135deg, #0f766e 0%, #124e78 55%, #1d4ed8 100%); color: #fff; overflow: hidden; position: relative; padding: 16px 20px; }
        .visit-hero:after { content: ""; position: absolute; right: -40px; top: -55px; width: 160px; height: 160px; border-radius: 50%; background: rgba(255,255,255,0.08); }
        .visit-hero-inner { position: relative; z-index: 1; display: flex; justify-content: space-between; gap: 14px; flex-wrap: wrap; align-items: center; min-height: 68px; }
        .visit-title-block { min-width: 0; flex: 1 1 520px; }
        .visit-hero h3 { color: #fff; margin-top: 0; margin-bottom: 4px; font-size: 22px; line-height: 1.15; font-weight: 700; }
        .visit-hero p { margin-bottom: 0; color: rgba(255,255,255,0.9); font-size: 13px; line-height: 1.5; }
        .hero-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .hero-actions .btn { border-radius: 999px; font-weight: 600; padding: 8px 14px; font-size: 12px; }
        .hero-actions .btn-primary { background: #fff; color: #124e78; border-color: #fff; }
        .hero-actions .btn-default { background: rgba(255,255,255,0.14); color: #fff; border-color: rgba(255,255,255,0.24); }
        .hero-meta { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
        .hero-meta-item { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border-radius: 999px; background: rgba(255,255,255,0.14); color: #fff; font-size: 12px; font-weight: 500; line-height: 1.2; }
        .hero-meta-item i { opacity: 0.86; }
        .visit-hero .status-pill { padding: 5px 10px; font-size: 10px; }
        .section-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 6px; }
        .section-text { color: #64748b; font-size: 13px; line-height: 1.7; margin-bottom: 18px; }
        .form-control { border-radius: 10px; min-height: 42px; border-color: #d7e2ee; box-shadow: none; }
        textarea.form-control { min-height: 120px; resize: vertical; }
        .btn-submit { border-radius: 999px; padding: 10px 18px; font-weight: 600; }
        .update-card { border: 1px solid #edf2f7; border-radius: 14px; padding: 18px; margin-bottom: 14px; background: #fbfdff; }
        .update-header { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
        .update-date { font-size: 17px; font-weight: 700; color: #0f172a; }
        .update-meta { color: #64748b; font-size: 12px; }
        .update-block-title { font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; font-weight: 700; margin-bottom: 6px; }
        .update-body { color: #0f172a; line-height: 1.75; white-space: pre-line; }
        .doc-list { list-style: none; padding: 0; margin: 0; }
        .doc-list li { border: 1px solid #edf2f7; border-radius: 12px; padding: 14px; margin-bottom: 12px; display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; align-items: center; }
        .doc-title { font-weight: 600; color: #0f172a; }
        .doc-meta { color: #64748b; font-size: 12px; }
        .info-note { padding: 12px 14px; border-radius: 12px; background: #f8fafc; color: #475569; font-size: 13px; line-height: 1.7; }
        .form-note { color: #64748b; font-size: 12px; line-height: 1.7; margin-top: 8px; }
        .mom-translate { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 8px; }
        .mom-translate .btn { border-radius: 999px; font-weight: 600; }
        .mom-translate-status { font-size: 12px; color: #64748b; }
        .mom-translate-status.is-error { color: #b91c1c; }
        .mom-translate-status.is-done { color: #15803d; }
        .visit-version-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .visit-version-table thead th { background: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; padding: 12px 10px; }
        .visit-version-table tbody td { border-bottom: 1px solid #edf2f7; padding: 12px 10px; vertical-align: top; font-size: 12px; color: #0f172a; }
        .visit-version-table tbody tr:last-child td { border-bottom: none; }
        .table-current-badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 3px 8px; background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-left: 8px; }
        .version-meta { color: #64748b; font-size: 11px; line-height: 1.6; margin-top: 4px; }
        .mom-share-item { border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 10px; }
        .mom-share-item:last-child { margin-bottom: 0; }
        .mom-share-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap; }
        .mom-share-to { font-weight: 700; font-size: 13px; color: #0f172a; }
        .mom-share-meta { color: #64748b; font-size: 11px; line-height: 1.7; }
        .mom-share-pill { display: inline-block; border-radius: 999px; padding: 4px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
        .mom-share-pill.is-sent { background: #dcfce7; color: #15803d; }
        .mom-share-pill.is-failed { background: #fee2e2; color: #b91c1c; }
        .share-hod-box { padding: 12px 14px; border-radius: 12px; background: #f8fafc; color: #475569; font-size: 13px; line-height: 1.7; margin-bottom: 14px; }
        .extension-payment { border: 1px dashed #cbd5e1; border-radius: 12px; padding: 14px; margin-bottom: 16px; background: #f8fafc; }
        .extension-payment-toggle { display: flex; align-items: flex-start; gap: 9px; font-weight: 600; color: #0f172a; margin-bottom: 4px; cursor: pointer; }
        .extension-payment-toggle input { margin-top: 3px; }
        .extension-payment-fields { margin-top: 12px; }
        .extension-payment-fields .form-group:last-child { margin-bottom: 0; }
        .payment-request-item { border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; margin-bottom: 10px; }
        .payment-request-item:last-child { margin-bottom: 0; }
        .payment-request-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; flex-wrap: wrap; }
        .payment-request-code { font-weight: 700; font-size: 13px; color: #0f172a; }
        .payment-request-meta { color: #64748b; font-size: 11px; line-height: 1.7; }
        .payment-request-amount { font-weight: 700; font-size: 14px; color: #0f172a; }
        .payment-request-revised { color: #b45309; font-size: 11px; font-weight: 600; }
        .payment-status-pill { display: inline-block; border-radius: 999px; padding: 4px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
        .payment-status-pending-hod-approval { background: #fef3c7; color: #b45309; }
        .payment-status-approved { background: #dcfce7; color: #15803d; }
        .payment-status-rejected { background: #fee2e2; color: #b91c1c; }
        @media (max-width: 991px) {
            .visit-hero { padding: 16px; }
            .visit-hero-inner { align-items: flex-start; min-height: auto; }
        }
    </style>
</head>
<body>
    <?php
    $payment_permissions = isset($payment_permissions) && is_array($payment_permissions) ? $payment_permissions : array();
    $can_create_service_payment = !empty($payment_permissions['can_create']);
    $version_history = isset($version_history) && is_array($version_history) ? $version_history : array();
    $visit_payment_requests = isset($visit_payment_requests) && is_array($visit_payment_requests) ? $visit_payment_requests : array();
    $ai_translate_enabled = !empty($ai_translate_enabled);
    // A payment request hangs off a won order, so a DF-only visit has nothing to
    // attach one to. Same rule as the Request Payment button in the hero.
    $can_raise_extension_payment = $can_create_service_payment && (int) $visit->opportunity_id > 0;

    $mom_shares = isset($mom_shares) && is_array($mom_shares) ? $mom_shares : array();
    $service_hod = isset($service_hod) ? $service_hod : null;
    $service_hod_name = !empty($service_hod->full_name) ? trim((string) $service_hod->full_name) : '';
    $service_hod_email = !empty($service_hod->email) ? trim((string) $service_hod->email) : '';
    $mom_entry_count = is_array($updates) ? count($updates) : 0;
    // Nothing to review means nothing to send, so the button only appears once
    // there is at least one day-wise entry in the consolidated PDF.
    $can_share_mom_with_hod = $mom_entry_count > 0;
    ?>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:8px;">
                    <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:8px;">
                    <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card-box visit-hero">
                <div class="visit-hero-inner">
                    <div class="visit-title-block">
                        <h3><?php echo htmlspecialchars((string) $visit->op_no, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars((string) $visit->customer_name, ENT_QUOTES, 'UTF-8'); ?> | Engineer: <?php echo htmlspecialchars((string) $visit->engineer_full_name, ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="hero-meta">
                            <span class="hero-meta-item">
                                <i class="fa fa-flag"></i>
                                <span class="status-pill status-<?php echo $visit->status_slug; ?>"><?php echo $visit->visit_status; ?></span>
                            </span>
                            <span class="hero-meta-item">
                                <i class="fa fa-calendar"></i>
                                <?php echo date('d M, Y', strtotime($visit->start_date)); ?> to <?php echo date('d M, Y', strtotime($visit->end_date)); ?>
                            </span>
                            <span class="hero-meta-item">
                                <i class="fa fa-clock-o"></i>
                                <?php echo $visit->duration_label; ?>
                            </span>
                            <span class="hero-meta-item">
                                <i class="fa fa-code-fork"></i>
                                Version V<?php echo (int) $visit->schedule_version; ?>
                            </span>
                            <span class="hero-meta-item">
                                <i class="fa fa-wrench"></i>
                                <?php echo $visit->visit_type; ?>
                            </span>
                        </div>
                    </div>

                    <div class="hero-actions">
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_visit_list" class="btn btn-default">
                            <i class="fa fa-list"></i> Visit List
                        </a>
                        <?php if ($can_create_service_payment && (int) $visit->opportunity_id > 0): ?>
                            <a href="<?php echo page_url; ?>ServiceLeads/service_payment_request_form?basis=VISIT&opportunity_id=<?php echo (int) $visit->opportunity_id; ?>&visit_id=<?php echo (int) $visit->visit_id; ?>" class="btn btn-primary">
                                <i class="fa fa-credit-card"></i> Request Payment
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo page_url; ?>ServiceLeads/view_visit_mom_pdf/<?php echo (int) $visit->visit_id; ?>" target="_blank" class="btn btn-primary">
                            <i class="fa fa-file-pdf-o"></i> View PDF
                        </a>
                        <?php if ($can_share_mom_with_hod): ?>
                            <button type="button" class="btn btn-primary" id="shareMomBtn" data-toggle="modal" data-target="#shareMomModal">
                                <i class="fa fa-paper-plane"></i> Send MOM to HOD
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo page_url; ?>ServiceLeads/engineer_scheduler" class="btn btn-default">
                            <i class="fa fa-calendar"></i> Scheduler
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <?php if (strtolower(trim((string) $visit->visit_status)) !== 'completed'): ?>
                        <div class="card-box">
                            <h4 class="section-title">Daily MOM Update</h4>
                            <p class="section-text">Engineers can save one MOM entry per day for this visit. Saving again on the same date will update that day&rsquo;s record.</p>

                            <form method="post" action="<?php echo page_url; ?>ServiceLeads/save_visit_daily_update/<?php echo (int) $visit->visit_id; ?>">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label>Work Date</label>
                                        <input type="date" name="work_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <label>Next Plan</label>
                                        <input type="text" id="momNextPlan" name="next_plan" class="form-control" placeholder="Tomorrow plan / pending action">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>MOM / Work Done</label>
                                    <textarea id="momPoints" name="mom_points" class="form-control" required placeholder="Enter the work completed, discussions held, observations, and site actions for the day."></textarea>
                                    <?php if ($ai_translate_enabled): ?>
                                        <div class="mom-translate">
                                            <button type="button" class="btn btn-default btn-sm" id="momTranslateBtn">
                                                <i class="fa fa-language"></i> Convert into English
                                            </button>
                                            <button type="button" class="btn btn-link btn-sm" id="momTranslateUndo" style="display:none;">
                                                <i class="fa fa-undo"></i> Undo
                                            </button>
                                            <span class="mom-translate-status" id="momTranslateStatus"></span>
                                        </div>
                                        <p class="form-note">
                                            Dictated this in Hindi, Marathi or Hinglish with your phone&rsquo;s voice typing? Press
                                            <strong>Convert into English</strong> &mdash; the English version comes back into the boxes
                                            above (MOM and Next Plan) for you to read and correct. Nothing is saved until you press
                                            <strong>Save Daily Update</strong>, and <strong>Undo</strong> brings your own words back.
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="fa fa-save"></i> Save Daily Update
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="card-box">
                        <h4 class="section-title">Daily MOM History</h4>
                        <p class="section-text">Every saved day-wise work update will appear here and will also be included in the PDF.</p>

                        <?php if (empty($updates)): ?>
                            <div class="info-note">No MOM updates have been saved for this visit yet.</div>
                        <?php else: ?>
                            <?php foreach ($updates as $update): ?>
                                <div class="update-card">
                                    <div class="update-header">
                                        <div>
                                            <div class="update-date"><?php echo date('d M, Y', strtotime($update->work_date)); ?></div>
                                            <div class="update-meta">
                                                <?php
                                                    $updated_by_name = trim((string) $update->updated_by_name);
                                                    $added_by_name = trim((string) $update->added_by_name);
                                                    $editor_name = $updated_by_name !== '' ? $updated_by_name : $added_by_name;
                                                    echo $editor_name !== '' ? 'Updated by ' . $editor_name : 'System update';
                                                ?>
                                            </div>
                                        </div>
                                        <div class="update-meta">
                                            <?php echo !empty($update->updated_on) ? date('d M, Y h:i A', strtotime($update->updated_on)) : ''; ?>
                                        </div>
                                    </div>

                                    <div class="update-block-title">MOM / Work Done</div>
                                    <div class="update-body"><?php echo nl2br(htmlspecialchars($update->mom_points)); ?></div>

                                    <?php if (!empty($update->next_plan)): ?>
                                        <div class="update-block-title m-t-15">Next Plan</div>
                                        <div class="update-body"><?php echo nl2br(htmlspecialchars($update->next_plan)); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($can_share_mom_with_hod || !empty($mom_shares)): ?>
                        <div class="card-box">
                            <h4 class="section-title">MOM Sharing History</h4>
                            <p class="section-text">Every time the consolidated MOM is sent to the HOD, the exact PDF that went out is archived here with the delivery outcome.</p>

                            <?php if (empty($mom_shares)): ?>
                                <div class="info-note">
                                    The MOM has not been sent for HOD review yet.
                                    <?php if ($can_share_mom_with_hod): ?>
                                        Use <strong>Send MOM to HOD</strong> at the top of this page &mdash; all
                                        <?php echo (int) $mom_entry_count; ?> day-wise
                                        <?php echo $mom_entry_count === 1 ? 'entry goes' : 'entries go'; ?>
                                        out as one PDF on the company letterhead, with a signature block for the HOD.
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php foreach ($mom_shares as $share): ?>
                                    <?php $share_failed = strtoupper(trim((string) $share->status)) !== 'SENT'; ?>
                                    <div class="mom-share-item">
                                        <div class="mom-share-head">
                                            <div>
                                                <div class="mom-share-to">
                                                    <?php echo htmlspecialchars(trim((string) $share->shared_to_name) !== '' ? (string) $share->shared_to_name : 'Service HOD', ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                                <div class="mom-share-meta">
                                                    <?php echo htmlspecialchars((string) $share->shared_to_email, ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php if (!empty($share->created_at)): ?>
                                                        &middot; <?php echo date('d M, Y h:i A', strtotime($share->created_at)); ?>
                                                    <?php endif; ?>
                                                    <?php if (trim((string) $share->shared_by_name) !== ''): ?>
                                                        &middot; sent by <?php echo htmlspecialchars((string) $share->shared_by_name, ENT_QUOTES, 'UTF-8'); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <span class="mom-share-pill <?php echo $share_failed ? 'is-failed' : 'is-sent'; ?>">
                                                <?php echo $share_failed ? 'Email failed' : 'Emailed'; ?>
                                            </span>
                                        </div>

                                        <div class="mom-share-meta" style="margin-top:8px;">
                                            <?php echo (int) $share->mom_count; ?> MOM
                                            <?php echo (int) $share->mom_count === 1 ? 'entry' : 'entries'; ?>
                                            <?php if (!empty($share->period_from) && !empty($share->period_to)): ?>
                                                covering <?php echo date('d M Y', strtotime($share->period_from)); ?>
                                                to <?php echo date('d M Y', strtotime($share->period_to)); ?>
                                            <?php endif; ?>
                                            <?php if (trim((string) $share->cc_emails) !== ''): ?>
                                                <br>Copy to: <?php echo htmlspecialchars((string) $share->cc_emails, ENT_QUOTES, 'UTF-8'); ?>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (trim((string) $share->note) !== ''): ?>
                                            <div class="mom-share-meta" style="margin-top:8px;">
                                                <strong>Note:</strong> <?php echo nl2br(htmlspecialchars((string) $share->note, ENT_QUOTES, 'UTF-8')); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($share_failed && trim((string) $share->failure_reason) !== ''): ?>
                                            <div class="mom-share-meta" style="margin-top:8px; color:#b91c1c;">
                                                <strong>Delivery problem:</strong> <?php echo htmlspecialchars((string) $share->failure_reason, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (trim((string) $share->file_name) !== ''): ?>
                                            <div style="margin-top:10px;">
                                                <a href="<?php echo base_url('uploads/service_visit_mom_shares/' . rawurlencode((string) $share->file_name)); ?>" target="_blank" class="btn btn-default btn-sm">
                                                    <i class="fa fa-file-pdf-o"></i> Open the copy that was sent
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-5">
                    <div class="card-box">
                        <h4 class="section-title">Visit Snapshot</h4>
                        <p class="section-text">A clean summary for customer, assigned engineer, and closure status.</p>

                        <div class="info-note">
                            <strong>Reference:</strong> <?php echo htmlspecialchars((string) $visit->reference_type . ' - ' . (string) $visit->op_no, ENT_QUOTES, 'UTF-8'); ?><br>
                            <strong>Customer:</strong> <?php echo htmlspecialchars((string) $visit->customer_name, ENT_QUOTES, 'UTF-8'); ?><br>
                            <strong>Contact:</strong> <?php echo $visit->customer_contact_name !== '' ? $visit->customer_contact_name : 'Not available'; ?><br>
                            <strong>Phone:</strong> <?php echo $visit->customer_contact_no !== '' ? $visit->customer_contact_no : 'Not available'; ?><br>
                            <strong>Email:</strong> <?php echo $visit->customer_email !== '' ? $visit->customer_email : 'Not available'; ?><br>
                            <strong>Visit Window:</strong> <?php echo date('d M, Y', strtotime($visit->start_date)); ?> to <?php echo date('d M, Y', strtotime($visit->end_date)); ?><br>
                            <strong>Current Version:</strong> V<?php echo (int) $visit->schedule_version; ?><br>
                            <strong>Address:</strong> <?php echo $visit->customer_address !== '' ? nl2br(htmlspecialchars($visit->customer_address)) : 'Not available'; ?><br>
                            <strong>Planner Remarks:</strong> <?php echo trim((string) $visit->remarks) !== '' ? nl2br(htmlspecialchars($visit->remarks)) : 'No planning remarks'; ?>
                        </div>

                        <?php if (!empty($visit->completion_notes)): ?>
                            <div class="info-note m-t-15">
                                <strong>Completion Notes:</strong><br>
                                <?php echo nl2br(htmlspecialchars($visit->completion_notes)); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!in_array(strtolower(trim((string) $visit->visit_status)), ['completed', 'cancelled'], true)): ?>
                        <div class="card-box">
                            <h4 class="section-title">Extend Visit Window</h4>
                            <p class="section-text">If more on-site days are required, extend the visit here. Each extension creates a new version in the schedule history.</p>

                            <form method="post" action="<?php echo page_url; ?>ServiceLeads/extend_visit_schedule/<?php echo (int) $visit->visit_id; ?>">
                                <div class="form-group">
                                    <label>Current End Date</label>
                                    <input type="text" class="form-control" value="<?php echo date('d M, Y', strtotime($visit->end_date)); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Revised End Date</label>
                                    <input type="date" name="new_end_date" class="form-control" min="<?php echo htmlspecialchars((string) $visit->end_date, ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Reason for Extension</label>
                                    <textarea name="extension_reason" class="form-control" rows="4" required placeholder="Mention why the engineer visit is being extended and what work is still pending."></textarea>
                                </div>

                                <?php if ($can_raise_extension_payment): ?>
                                    <div class="extension-payment">
                                        <label class="extension-payment-toggle" for="raisePaymentRequest">
                                            <input type="checkbox" name="raise_payment_request" id="raisePaymentRequest" value="1">
                                            <span>Also raise a payment request for the extra days</span>
                                        </label>
                                        <div class="form-note" style="margin-top:0;">
                                            Optional. Extra days do not always cost anything &mdash; tick this only if money is needed.
                                            The request goes to the service HOD, who can approve the amount as it is or approve a
                                            revised amount.
                                        </div>

                                        <div class="extension-payment-fields" id="extensionPaymentFields" style="display:none;">
                                            <div class="form-group">
                                                <label>Amount Required (Rs.)</label>
                                                <input type="number" name="extension_payment_amount" id="extensionPaymentAmount" class="form-control" min="0.01" step="0.01" placeholder="e.g. 5000">
                                            </div>
                                            <div class="form-group">
                                                <label>What Is This Payment For? <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                                                <textarea name="extension_payment_purpose" class="form-control" rows="3" placeholder="Travel, stay, food or any other spend for the extended days. Left blank, the extension reason above is used."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <button type="submit" class="btn btn-primary btn-submit" onclick="return confirmVisitExtension();">
                                    <i class="fa fa-plus-circle"></i> Extend Visit
                                </button>
                                <div class="form-note">The extension will be blocked automatically if the engineer already has another overlapping assignment in the revised window.</div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ($can_create_service_payment || !empty($visit_payment_requests)): ?>
                        <div class="card-box">
                            <h4 class="section-title">Payment Requests for This Visit</h4>
                            <p class="section-text">Anything raised from the extension box above, or from the Request Payment button, is tracked here with the HOD decision.</p>

                            <?php if (empty($visit_payment_requests)): ?>
                                <div class="info-note">No payment request has been raised against this visit yet.</div>
                            <?php else: ?>
                                <?php foreach ($visit_payment_requests as $payment_request): ?>
                                    <div class="payment-request-item">
                                        <div class="payment-request-head">
                                            <div>
                                                <div class="payment-request-code"><?php echo htmlspecialchars((string) $payment_request->request_code, ENT_QUOTES, 'UTF-8'); ?></div>
                                                <div class="payment-request-meta">
                                                    <?php echo htmlspecialchars((string) $payment_request->request_type, ENT_QUOTES, 'UTF-8'); ?>
                                                    &middot; Raised <?php echo date('d M, Y', strtotime($payment_request->request_date)); ?>
                                                    by <?php echo htmlspecialchars((string) $payment_request->created_by_name, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </div>
                                            <span class="payment-status-pill payment-status-<?php echo htmlspecialchars((string) $payment_request->status_slug, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars((string) $payment_request->status, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </div>

                                        <div class="payment-request-amount" style="margin-top:10px;">
                                            Rs. <?php echo number_format((float) $payment_request->payable_amount, 2); ?>
                                        </div>
                                        <?php if (!empty($payment_request->amount_revised)): ?>
                                            <div class="payment-request-revised">
                                                HOD revised this from Rs. <?php echo number_format((float) $payment_request->amount, 2); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (trim((string) $payment_request->purpose) !== ''): ?>
                                            <div class="payment-request-meta" style="margin-top:8px;">
                                                <?php echo nl2br(htmlspecialchars((string) $payment_request->purpose, ENT_QUOTES, 'UTF-8')); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (trim((string) $payment_request->hod_remarks) !== ''): ?>
                                            <div class="payment-request-meta" style="margin-top:8px;">
                                                <strong>HOD remarks:</strong>
                                                <?php echo nl2br(htmlspecialchars((string) $payment_request->hod_remarks, ENT_QUOTES, 'UTF-8')); ?>
                                            </div>
                                        <?php endif; ?>

                                        <div style="margin-top:10px;">
                                            <a href="<?php echo page_url; ?>ServiceLeads/print_service_payment_request_pdf/<?php echo (int) $payment_request->request_id; ?>" target="_blank" class="btn btn-default btn-sm">
                                                <i class="fa fa-print"></i> Print
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-box">
                        <h4 class="section-title">Signed Documents</h4>
                        <p class="section-text">Signed visit closure files uploaded by the engineer will stay linked with this visit.</p>

                        <?php if (empty($documents)): ?>
                            <div class="info-note">No signed documents uploaded yet.</div>
                        <?php else: ?>
                            <ul class="doc-list">
                                <?php foreach ($documents as $document): ?>
                                    <li>
                                        <div>
                                            <div class="doc-title"><?php echo $document->original_name; ?></div>
                                            <div class="doc-meta">
                                                Uploaded <?php echo !empty($document->uploaded_at) ? date('d M, Y h:i A', strtotime($document->uploaded_at)) : ''; ?>
                                                <?php if (!empty($document->uploaded_by_name)): ?>
                                                    by <?php echo $document->uploaded_by_name; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="<?php echo base_url($document->file_path); ?>" target="_blank" class="btn btn-sm btn-default">
                                                <i class="fa fa-download"></i> Open
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <?php if (strtolower(trim((string) $visit->visit_status)) !== 'completed'): ?>
                        <div class="card-box">
                            <h4 class="section-title">Mark Visit Completed</h4>
                            <p class="section-text">Before closing the visit, upload the signed documents. At least one MOM entry and one signed file are required.</p>

                            <form method="post" action="<?php echo page_url; ?>ServiceLeads/mark_visit_completed/<?php echo (int) $visit->visit_id; ?>" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Completion Notes</label>
                                    <textarea name="completion_notes" class="form-control" placeholder="Final summary, closure remarks, pending customer points, or sign-off notes."></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Signed Documents</label>
                                    <input type="file" name="signed_documents[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple required>
                                </div>
                                <button type="submit" class="btn btn-success btn-submit" onclick="return confirm('Mark this visit as completed and upload the signed documents?');">
                                    <i class="fa fa-check-circle"></i> Complete Visit
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card-box">
                            <h4 class="section-title">Visit Closed</h4>
                            <p class="section-text">This visit is already completed and its MOM history is ready for PDF export.</p>
                            <div class="info-note">
                                <strong>Completed On:</strong>
                                <?php echo !empty($visit->completed_on) ? date('d M, Y h:i A', strtotime($visit->completed_on)) : 'Not available'; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-box">
                <h4 class="section-title">Schedule Version History</h4>
                <p class="section-text">Every schedule change is captured here so the team can see the active version, the previous window, and the reason for extension.</p>

                <div class="table-responsive">
                    <table class="visit-version-table">
                        <thead>
                            <tr>
                                <th style="width: 12%;">Version</th>
                                <th style="width: 22%;">Schedule Window</th>
                                <th style="width: 18%;">Change</th>
                                <th>Reason</th>
                                <th style="width: 14%;">Updated By</th>
                                <th style="width: 14%;">Updated On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($version_history as $history_row): ?>
                                <tr>
                                    <td>
                                        <strong>V<?php echo (int) $history_row->version_no; ?></strong>
                                        <?php if ((int) $history_row->version_no === (int) $visit->schedule_version): ?>
                                            <span class="table-current-badge">Current</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo date('d M, Y', strtotime($history_row->start_date)); ?> to <?php echo date('d M, Y', strtotime($history_row->end_date)); ?></strong>
                                        <div class="version-meta"><?php echo htmlspecialchars((string) $history_row->duration_label, ENT_QUOTES, 'UTF-8'); ?></div>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars((string) $history_row->change_summary, ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <?php if (!empty($history_row->previous_end_date)): ?>
                                            <div class="version-meta">Previous end date: <?php echo date('d M, Y', strtotime($history_row->previous_end_date)); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo trim((string) $history_row->change_reason) !== '' ? nl2br(htmlspecialchars((string) $history_row->change_reason)) : '-'; ?></td>
                                    <td><?php echo trim((string) $history_row->created_by_name) !== '' ? htmlspecialchars((string) $history_row->created_by_name, ENT_QUOTES, 'UTF-8') : 'System'; ?></td>
                                    <td><?php echo !empty($history_row->created_at) ? date('d M, Y h:i A', strtotime($history_row->created_at)) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-box">
                <h4 class="section-title">Visit Modification History</h4>
                <p class="section-text">Every controlled visit edit records the old and new values, the mandatory modification remarks, and who made the change.</p>

                <?php if (empty($change_history)): ?>
                    <div class="info-note">No controlled modifications have been made to this visit.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="visit-version-table">
                            <thead>
                                <tr>
                                    <th style="width: 34%;">Changed Values</th>
                                    <th>Modification Remarks</th>
                                    <th style="width: 16%;">Updated By</th>
                                    <th style="width: 16%;">Updated On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($change_history as $history_row): ?>
                                    <?php
                                    $field_labels = [
                                        'engineer_id' => 'Assigned Engineer',
                                        'start_date' => 'Start Date',
                                        'end_date' => 'End Date',
                                        'visit_type' => 'Visit Type',
                                        'remarks' => 'Planner Notes',
                                    ];
                                    $old_values = is_array($history_row->old_values) ? $history_row->old_values : [];
                                    $new_values = is_array($history_row->new_values) ? $history_row->new_values : [];
                                    ?>
                                    <tr>
                                        <td>
                                            <?php foreach ($new_values as $field => $new_value): ?>
                                                <div class="version-meta" style="margin-bottom:7px;">
                                                    <strong><?php echo htmlspecialchars(isset($field_labels[$field]) ? $field_labels[$field] : ucwords(str_replace('_', ' ', $field)), ENT_QUOTES, 'UTF-8'); ?>:</strong><br>
                                                    <span><?php echo nl2br(htmlspecialchars(isset($old_values[$field]) ? (string) $old_values[$field] : '', ENT_QUOTES, 'UTF-8')); ?></span>
                                                    <i class="fa fa-long-arrow-right text-muted" style="margin:0 5px;"></i>
                                                    <span><?php echo nl2br(htmlspecialchars((string) $new_value, ENT_QUOTES, 'UTF-8')); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </td>
                                        <td><?php echo nl2br(htmlspecialchars((string) $history_row->change_remarks, ENT_QUOTES, 'UTF-8')); ?></td>
                                        <td><?php echo !empty($history_row->changed_by_name) ? htmlspecialchars((string) $history_row->changed_by_name, ENT_QUOTES, 'UTF-8') : 'System'; ?></td>
                                        <td><?php echo !empty($history_row->changed_at) ? date('d M, Y h:i A', strtotime($history_row->changed_at)) : '--'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($can_share_mom_with_hod): ?>
        <?php
        // Period covered by the consolidated PDF, shown so the sender can see
        // exactly what the HOD is being asked to sign off.
        $mom_share_dates = array();
        foreach ($updates as $mom_row) {
            if (!empty($mom_row->work_date)) {
                $mom_share_dates[] = date('Y-m-d', strtotime($mom_row->work_date));
            }
        }
        sort($mom_share_dates);
        $mom_share_period = !empty($mom_share_dates)
            ? date('d M, Y', strtotime($mom_share_dates[0])) . ' to ' . date('d M, Y', strtotime($mom_share_dates[count($mom_share_dates) - 1]))
            : '';
        ?>
        <div class="modal fade" id="shareMomModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="<?php echo page_url; ?>ServiceLeads/share_visit_mom_with_hod/<?php echo (int) $visit->visit_id; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Send MOM to HOD for Review &amp; Sign</h4>
                        </div>
                        <div class="modal-body">
                            <div class="share-hod-box">
                                <strong>Goes to:</strong>
                                <?php echo $service_hod_name !== '' ? htmlspecialchars($service_hod_name, ENT_QUOTES, 'UTF-8') : 'Service HOD'; ?>
                                <?php if ($service_hod_email !== ''): ?>
                                    (<?php echo htmlspecialchars($service_hod_email, ENT_QUOTES, 'UTF-8'); ?>)
                                <?php endif; ?><br>
                                <strong>Attached:</strong> one PDF on the company letterhead with all
                                <?php echo (int) $mom_entry_count; ?> day-wise MOM
                                <?php echo $mom_entry_count === 1 ? 'entry' : 'entries'; ?>
                                <?php if ($mom_share_period !== ''): ?>
                                    (<?php echo htmlspecialchars($mom_share_period, ENT_QUOTES, 'UTF-8'); ?>)
                                <?php endif; ?>
                                and a signature block for the HOD.<br>
                                <strong>Copy to:</strong> the assigned engineer and you.
                            </div>

                            <?php if ($service_hod_email === ''): ?>
                                <div class="alert alert-warning" style="border-radius:10px;">
                                    No email address is saved on the HOD&rsquo;s PMS account, so the mail cannot be delivered.
                                    The PDF will still be archived and the HOD will be notified inside PMS.
                                </div>
                            <?php endif; ?>

                            <div class="form-group" style="margin-bottom:0;">
                                <label>Note for the HOD <span style="color:#94a3b8; font-weight:400;">(optional)</span></label>
                                <textarea name="share_note" class="form-control" rows="3" placeholder="Anything the HOD should look at first &mdash; pending customer points, approvals needed, observations."></textarea>
                                <div class="form-note">The note appears on the PDF itself and in the covering email.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane"></i> Send for Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>

    <script>
    /*
     * The payment request that can ride along with an extension. The amount box
     * stays hidden and unrequired until the engineer ticks the box, so an
     * extension with no money attached is still one field and one button.
     */

    // Plain DOM and defined unconditionally: the Extend button calls this even
    // when the payment box is not on the page at all.
    function confirmVisitExtension() {
        var toggle = document.getElementById('raisePaymentRequest');

        return confirm(toggle && toggle.checked
            ? 'Extend this engineer visit and send a payment request to the service HOD for approval?'
            : 'Extend this engineer visit and capture a new version?');
    }

    (function ($) {
        var $toggle = $('#raisePaymentRequest');
        var $fields = $('#extensionPaymentFields');
        var $amount = $('#extensionPaymentAmount');

        if (!$toggle.length) { return; }

        function sync() {
            var on = $toggle.is(':checked');
            $fields.toggle(on);
            // Required only while the section is visible - a hidden required
            // field blocks the form with a validation message nobody can see.
            $amount.prop('required', on);
            if (!on) { $amount.val(''); }
        }

        $toggle.on('change', sync);
        sync();
    })(jQuery);

    /*
     * Sending the MOM builds a PDF and talks to the mail server, so the button
     * sits there for a few seconds. Without this guard an impatient second click
     * sends the HOD two copies and writes two share records.
     */
    (function ($) {
        var $form = $('#shareMomModal form');

        if (!$form.length) { return; }

        $form.on('submit', function () {
            $form.find('button[type="submit"]')
                .prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Sending\u2026');
        });
    })(jQuery);
    </script>

<?php if ($ai_translate_enabled): ?>
    <script>
    /*
     * "Convert into English" for the Daily MOM box.
     *
     * Engineers dictate the MOM on site with their phone keyboard's voice
     * typing, which types whatever language the keyboard is set to. This sends
     * what is in the two boxes to the server, which translates it and sends
     * plain English back. Nothing is saved here - the engineer reads the
     * English, fixes anything wrong with it, and presses Save as usual.
     *
     * Undo restores exactly what they dictated. A conversion with no way back
     * is worse than none: the original is the engineer's own record of the day.
     */
    (function ($) {
        var $btn    = $('#momTranslateBtn');
        var $undo   = $('#momTranslateUndo');
        var $status = $('#momTranslateStatus');
        var $mom    = $('#momPoints');
        var $plan   = $('#momNextPlan');

        if (!$btn.length || !$mom.length) { return; }

        var original = null;

        function say(text, kind) {
            $status.removeClass('is-error is-done');
            if (kind) { $status.addClass(kind); }
            $status.text(text || '');
        }

        function busy(on) {
            $btn.prop('disabled', on);
            $btn.html(on
                ? '<i class="fa fa-spinner fa-spin"></i> Converting\u2026'
                : '<i class="fa fa-language"></i> Convert into English');
        }

        $btn.on('click', function () {
            var mom  = $.trim($mom.val());
            var plan = $.trim($plan.val());

            if (mom === '' && plan === '') {
                say('Write or dictate the MOM first, then press Convert into English.', 'is-error');
                return;
            }

            busy(true);
            say('Converting\u2026');

            $.ajax({
                url: '<?php echo page_url; ?>ServiceLeads/translate_visit_update',
                type: 'POST',
                dataType: 'json',
                data: { mom_points: mom, next_plan: plan }
            }).done(function (res) {
                if (!res || !res.ok) {
                    say((res && res.message) ? res.message : 'Could not convert that text.', 'is-error');
                    return;
                }

                // Kept so Undo can put the engineer's own words back. Captured
                // on success only, so a failed attempt never arms a misleading
                // Undo.
                original = { mom: $mom.val(), plan: $plan.val() };

                $mom.val(res.mom_points);
                if ($plan.length && typeof res.next_plan === 'string') { $plan.val(res.next_plan); }

                $undo.show();
                say('Converted into English. Please read it before saving.', 'is-done');
            }).fail(function (xhr) {
                var message = 'Could not convert that text. Please try again.';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                say(message, 'is-error');
            }).always(function () {
                busy(false);
            });
        });

        $undo.on('click', function () {
            if (!original) { return; }
            $mom.val(original.mom);
            if ($plan.length) { $plan.val(original.plan); }
            original = null;
            $undo.hide();
            say('Your original text is back.');
        });
    })(jQuery);
    </script>
<?php endif; ?>
</body>
</html>
