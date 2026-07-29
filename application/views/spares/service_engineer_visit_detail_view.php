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
        .visit-version-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .visit-version-table thead th { background: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; padding: 12px 10px; }
        .visit-version-table tbody td { border-bottom: 1px solid #edf2f7; padding: 12px 10px; vertical-align: top; font-size: 12px; color: #0f172a; }
        .visit-version-table tbody tr:last-child td { border-bottom: none; }
        .table-current-badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 3px 8px; background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-left: 8px; }
        .version-meta { color: #64748b; font-size: 11px; line-height: 1.6; margin-top: 4px; }
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
                        <h3><?php echo $visit->op_no; ?></h3>
                        <p><?php echo $visit->customer_name; ?> | Engineer: <?php echo $visit->engineer_full_name; ?></p>
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
                        <?php if ($can_create_service_payment): ?>
                            <a href="<?php echo page_url; ?>ServiceLeads/service_payment_request_form?basis=VISIT&opportunity_id=<?php echo (int) $visit->opportunity_id; ?>&visit_id=<?php echo (int) $visit->visit_id; ?>" class="btn btn-primary">
                                <i class="fa fa-credit-card"></i> Request Payment
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo page_url; ?>ServiceLeads/view_visit_mom_pdf/<?php echo (int) $visit->visit_id; ?>" target="_blank" class="btn btn-primary">
                            <i class="fa fa-file-pdf-o"></i> View PDF
                        </a>
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
                                        <input type="text" name="next_plan" class="form-control" placeholder="Tomorrow plan / pending action">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>MOM / Work Done</label>
                                    <textarea name="mom_points" class="form-control" required placeholder="Enter the work completed, discussions held, observations, and site actions for the day."></textarea>
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
                </div>

                <div class="col-lg-5">
                    <div class="card-box">
                        <h4 class="section-title">Visit Snapshot</h4>
                        <p class="section-text">A clean summary for customer, assigned engineer, and closure status.</p>

                        <div class="info-note">
                            <strong>Customer:</strong> <?php echo $visit->customer_name; ?><br>
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
                                <button type="submit" class="btn btn-primary btn-submit" onclick="return confirm('Extend this engineer visit and capture a new version?');">
                                    <i class="fa fa-plus-circle"></i> Extend Visit
                                </button>
                                <div class="form-note">The extension will be blocked automatically if the engineer already has another overlapping assignment in the revised window.</div>
                            </form>
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
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
</body>
</html>
