<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// --- PRE-CALCULATION FOR AI-STYLE INSIGHTS ---
$total_logs         = !empty($logs) ? count($logs) : 0;
$image_logs_count   = 0;
$last_log           = null;
$first_log          = null;
$last_progress      = 0;
$days_spent         = 0;
$avg_daily_progress = 0;
$latest_image_date  = null;
$projected_days_to_complete = null;

if (!empty($logs)) {
    // The controller supplies logs newest-first.
    $last_log  = reset($logs);    // latest entry
    $first_log = end($logs);      // oldest entry

    foreach ($logs as $lg) {
        if (!empty($lg->image_path)) {
            $image_logs_count++;
            if ($latest_image_date === null) {
                $latest_image_date = $lg->log_date;
            }
        }
    }

    $last_progress = (int) $last_log->progress_percent;

    // Days between job release and last log
    $job_release_date = !empty($job->release_date) ? $job->release_date : $last_log->log_date;
    $days_spent = (int) floor((strtotime($last_log->log_date) - strtotime($job_release_date)) / (60 * 60 * 24)) + 1;
    if ($days_spent < 1) $days_spent = 1;

    $avg_daily_progress = round($last_progress / $days_spent, 1);

    if ($avg_daily_progress > 0 && $last_progress < 100) {
        $remaining = 100 - $last_progress;
        $projected_days_to_complete = ceil($remaining / $avg_daily_progress);
    }
}

// Simple rule-based risk note for "AI" style message
$ai_risk_note = "Visual cadence looks healthy based on available updates.";
if ($total_logs > 0 && $avg_daily_progress < 3 && $last_progress < 70) {
    $ai_risk_note = "Progress velocity appears low. Consider reviewing resource allocation on this job.";
} elseif ($total_logs > 0 && $last_progress >= 90) {
    $ai_risk_note = "Job is visually nearing completion. Plan for final inspection and closure.";
} elseif ($total_logs == 0) {
    $ai_risk_note = "No visual logs available. Encourage the supervisor to start daily image-based updates.";
}

// Image coverage text
if ($total_logs > 0) {
    $image_coverage = round(($image_logs_count / $total_logs) * 100);
} else {
    $image_coverage = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Progress Report - <?php echo $job->df_number; ?></title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { 
            background: #f3f4f6; 
            font-family: 'Poppins', sans-serif; 
        }

        .wrapper { padding-top: 25px; }

        .page-header-back {
            font-size: 13px;
            font-weight: 500;
        }

        /* AI Job Header */
        .ai-card { 
            background: linear-gradient(135deg, #4872b8 0%, #312e81 100%); 
            color: #fff; 
            border-radius: 16px; 
            padding: 22px 22px 20px; 
            margin-bottom: 30px; 
            box-shadow: 0 16px 40px rgba(15,23,42,0.35);
        }
        .ai-card h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .ai-card p {
            font-size: 13px;
            margin-bottom: 0;
            opacity: 0.9;
        }
        .ai-badges .badge {
            font-size: 11px;
            font-weight: 500;
            border-radius: 999px;
            padding: 5px 10px;
        }
        .ai-stat {
            background: rgba(255,255,255,0.12); 
            padding: 12px 14px; 
            border-radius: 12px; 
            text-align: right; 
            font-size: 12px;
        }
        .ai-stat small {
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: 0.85;
        }
        .status-badge { 
            font-size: 11px; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            padding: 5px 10px; 
            border-radius: 999px; 
            background: #fff; 
        }
        .status-badge.success { color: #16a34a; }
        .status-badge.danger  { color: #dc2626; }
        .status-badge.warning { color: #f97316; }
        .ai-stat p {
            margin-top: 8px;
            margin-bottom: 0;
        }

        /* Main layout */
        .section-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            font-weight: 600;
        }

        /* Timeline Styles */
        .timeline { 
            position: relative; 
            padding: 15px 0 0; 
            list-style: none; 
        }
        .timeline:before { 
            content: " "; 
            position: absolute; 
            top: 0; 
            bottom: 0; 
            left: 32px; 
            width: 3px; 
            background-color: #e5e7eb; 
        }
        .timeline-item { 
            position: relative; 
            margin-bottom: 22px; 
        }
        .time-icon { 
            position: absolute; 
            left: 16px; 
            top: 3px; 
            width: 34px; 
            height: 34px; 
            border-radius: 50%; 
            text-align: center; 
            line-height: 32px; 
            background: #ffffff; 
            border: 2px solid #4872b8; 
            color: #4872b8; 
            font-weight: 700; 
            font-size: 13px; 
            z-index: 2; 
        }
        .timeline-content { 
            margin-left: 70px; 
            background: #ffffff; 
            padding: 14px 16px; 
            border-radius: 12px; 
            box-shadow: 0 8px 24px rgba(15,23,42,0.12); 
            position: relative; 
        }
        .timeline-content:after { 
            content: " "; 
            position: absolute; 
            top: 15px; 
            right: 100%; 
            border: 9px solid transparent; 
            border-right-color: #ffffff; 
        }

        .timeline-content-header h6 {
            font-size: 13px;
            margin: 0;
        }
        .timeline-content-header small {
            font-size: 11px;
        }

        .badge-progress {
            font-size: 11px;
            border-radius: 999px;
            padding: 4px 9px;
        }

        .log-remarks {
            font-size: 13px;
            margin-top: 8px;
            margin-bottom: 0;
        }

        /* Image Style */
        .log-image { 
            width: 100%; 
            max-width: 420px; 
            border-radius: 12px; 
            margin-top: 10px; 
            border: 1px solid #e5e7eb; 
            padding: 3px; 
            cursor: zoom-in; 
            background-color: #f9fafb;
        }

        /* AI Insights side card */
        .ai-insights-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px 18px 16px;
            box-shadow: 0 16px 40px rgba(15,23,42,0.1);
        }
        .ai-insights-card h5 {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 4px;
        }
        .ai-insights-card .small-sub {
            font-size: 11px;
            color: #9ca3af;
            margin-bottom: 10px;
        }
        .ai-metric {
            margin-bottom: 10px;
        }
        .ai-metric-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .ai-metric-value {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }
        .ai-progress-bar {
            height: 7px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
            margin-top: 4px;
        }
        .ai-progress-bar-inner {
            height: 100%;
            background: linear-gradient(90deg, #22c55e, #16a34a);
        }

        .ai-note {
            font-size: 12px;
            color: #4b5563;
            margin-top: 8px;
        }
        .ai-note i {
            font-size: 10px;
            margin-right: 5px;
            color: #f59e0b;
        }

        /* AI Image Analysis modal */
        .modal-content-ai {
            border-radius: 18px;
            border: none;
            overflow: hidden;
        }
        .modal-header-ai {
            background: linear-gradient(135deg, #4872b8, #312e81);
            color: #ffffff;
            padding: 12px 16px;
        }
        .modal-header-ai .modal-title {
            font-size: 14px;
            font-weight: 600;
        }
        .modal-header-ai .close {
            color: #ffffff;
            opacity: 0.85;
        }
        .modal-body-ai {
            padding: 16px;
        }
        .modal-body-ai img {
            border-radius: 12px;
            width: 100%;
            max-height: 420px;
            object-fit: contain;
            background-color: #000;
        }
        .ai-image-meta {
            font-size: 12px;
            margin-top: 8px;
        }
        .ai-image-meta strong {
            font-weight: 600;
        }
        .ai-image-analysis {
            margin-top: 10px;
            font-size: 12px;
            color: #374151;
        }
        .ai-image-analysis-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 3px;
        }
        .ai-tag-pill {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            background: #eff6ff;
            color: #1d4ed8;
            margin-right: 4px;
            margin-top: 3px;
        }
        .report-attachment-card {
    display: flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #f8fafc, #eef4ff);
    border: 1px solid #dbeafe;
    border-radius: 12px;
    padding: 12px 14px;
    max-width: 520px;
    box-shadow: 0 6px 16px rgba(15,23,42,0.08);
}

.report-attachment-icon {
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 12px;
    background: #4872b8;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.report-attachment-info {
    flex: 1;
    min-width: 0;
}

.report-attachment-title {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.report-attachment-meta {
    font-size: 11px;
    color: #6b7280;
    margin-top: 2px;
}

.report-view-btn {
    border-radius: 8px;
    font-size: 12px;
    white-space: nowrap;
}
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-12 d-flex align-items-center justify-content-between">
                    <a href="<?php echo page_url.'Masters/manage_jobs'; ?>" class="btn btn-light btn-sm page-header-back">
                        <i class="fa fa-arrow-left"></i> Back to Jobs
                    </a>
                </div>
            </div>

            <!-- AI Job Summary Card -->
            <div class="ai-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="m-0 text-white"><?php echo $job->df_number; ?></h3>
                        <p class="mb-1">
                            <?php echo $job->df_description ? $job->df_description : 'No description available for this DF.'; ?>
                        </p>
                        <div class="ai-badges mt-1">
                            <span class="badge badge-light text-dark">
                                <i class="fa fa-map-marker"></i> <?php echo $job->line_name; ?>
                            </span>
                            <span class="badge badge-light text-dark ml-1">
                                <i class="fa fa-cogs"></i> <?php echo $job->machine_name; ?>
                            </span>
                            <span class="badge badge-light text-dark ml-1">
                                <i class="fa fa-user"></i> <?php echo $job->supervisor_name; ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <div class="ai-stat">
                            <small>AI STATUS MONITOR</small><br>
                            <span class="status-badge <?php echo !empty($ai_color) ? $ai_color : 'warning'; ?>">
                                <?php echo !empty($ai_status) ? $ai_status : 'Monitoring'; ?>
                            </span>
                            <p class="mt-2 mb-0">
                                <?php echo !empty($ai_message) ? $ai_message : 'System is tracking visual and progress data for this job.'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content: Timeline + AI Insights -->
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <span class="section-title">Daily Visual Progress Log</span>
                    </div>

                    <?php if(empty($logs)): ?>
                        <div class="alert alert-warning text-center">
                            <h4 class="mb-1"><i class="fa fa-camera"></i> No Updates Yet</h4>
                            <p class="mb-0">Ask the supervisor to upload the first daily progress photo.</p>
                        </div>
                    <?php else: ?>
                        
                        <ul class="timeline">
                            <?php $imgIndex = 0; ?>
                            <?php foreach($logs as $log): ?>
                            <?php
                                $log_date_str   = date('d M Y', strtotime($log->log_date));
                                $log_time_str   = date('h:i A', strtotime($log->log_time));
                                $remarks_clean  = trim($log->remarks);
                                $remarks_for_attr = htmlspecialchars($remarks_clean, ENT_QUOTES, 'UTF-8');
                                $img_src        = !empty($log->image_path) ? uploadsurl.'daily_logs/'.$log->image_path : '';
                                // who updated – if you have a specific field use that, else fallback to supervisor
                                $updated_by     = !empty($log->updated_by_name) ? $log->updated_by_name : $job->supervisor_name;
                            ?>
                            <li class="timeline-item">
                                <div class="time-icon">
                                    <?php echo date('d', strtotime($log->log_date)); ?>
                                </div>
                                
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center timeline-content-header">
                                        <h6 class="text-primary font-weight-bold mb-0">
                                            <?php echo date('F Y', strtotime($log->log_date)); ?>
                                            <small class="text-muted ml-2">
                                                <i class="fa fa-clock-o"></i> <?php echo $log_time_str; ?>
                                            </small>
                                        </h6>
                                        <span class="badge badge-info badge-progress">
                                            <?php echo $log->progress_percent; ?>% Complete
                                        </span>
                                    </div>
                                    
                                    <?php if(!empty($remarks_clean)): ?>
                                        <p class="log-remarks text-dark"><?php echo nl2br($remarks_clean); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if($img_src): ?>
                                        <a href="<?php echo $img_src; ?>" 
                                           class="ai-image-trigger"
                                           data-index="<?php echo $imgIndex; ?>"
                                           data-log-date="<?php echo $log_date_str; ?>"
                                           data-log-time="<?php echo $log_time_str; ?>"
                                           data-progress="<?php echo $log->progress_percent; ?>"
                                           data-remarks="<?php echo $remarks_for_attr; ?>"
                                           data-updated-by="<?php echo htmlspecialchars($updated_by, ENT_QUOTES, 'UTF-8'); ?>"
                                           data-img="<?php echo $img_src; ?>">
                                            <img src="<?php echo $img_src; ?>" class="log-image shadow-sm" alt="Progress image">
                                        </a>
                                        <?php $imgIndex++; ?>
                                    <?php endif; ?>


                                    <?php if (!empty($log->report_attachment)): ?>
    <?php
        $report_url  = uploadsurl . 'daily_logs/' . $log->report_attachment;
        $report_name = !empty($log->report_original_name) ? $log->report_original_name : $log->report_attachment;
        $report_type = !empty($log->report_file_type) ? $log->report_file_type : 'Attachment';
        $report_size = !empty($log->report_file_size) ? round($log->report_file_size, 2) . ' KB' : '';

        $file_ext = strtolower(pathinfo($report_name, PATHINFO_EXTENSION));
        $file_icon = 'fa-paperclip';

        if (in_array($file_ext, ['pdf'])) {
            $file_icon = 'fa-file-pdf-o';
        } elseif (in_array($file_ext, ['xls', 'xlsx', 'csv'])) {
            $file_icon = 'fa-file-excel-o';
        } elseif (in_array($file_ext, ['doc', 'docx'])) {
            $file_icon = 'fa-file-word-o';
        } elseif (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $file_icon = 'fa-file-image-o';
        }
    ?>

    <div class="report-attachment-card mt-3">
        <div class="report-attachment-icon">
            <i class="fa <?php echo $file_icon; ?>"></i>
        </div>

        <div class="report-attachment-info">
            <div class="report-attachment-title">
                <?php echo htmlspecialchars($report_name, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="report-attachment-meta">
                <?php echo htmlspecialchars($report_type, ENT_QUOTES, 'UTF-8'); ?>
                <?php if (!empty($report_size)): ?>
                    · <?php echo $report_size; ?>
                <?php endif; ?>
            </div>
        </div>

        <a href="<?php echo $report_url; ?>" 
           target="_blank" 
           class="btn btn-sm btn-outline-primary report-view-btn">
            <i class="fa fa-eye"></i> View
        </a>
    </div>
<?php endif; ?>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                    <?php endif; ?>
                </div>

                <!-- AI Insights Right Panel -->
                <div class="col-md-4 mt-4 mt-md-0">
                    <div class="ai-insights-card">
                        <h5>AI Visual Insights</h5>
                        <div class="small-sub">Job-level summary using image and progress logs.</div>

                        <div class="ai-metric">
                            <div class="ai-metric-label">Last reported progress</div>
                            <div class="ai-metric-value">
                                <?php echo $last_progress; ?>% 
                                <?php if($last_log): ?>
                                    <span class="text-muted" style="font-size:11px;">
                                        (<?php echo date('d M Y', strtotime($last_log->log_date)); ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="ai-progress-bar">
                                <div class="ai-progress-bar-inner" style="width: <?php echo min($last_progress, 100); ?>%;"></div>
                            </div>
                        </div>

                        <div class="ai-metric">
                            <div class="ai-metric-label">Days in production</div>
                            <div class="ai-metric-value">
                                <?php echo $total_logs > 0 ? $days_spent.' day(s)' : 'N/A'; ?>
                            </div>
                        </div>

                        <div class="ai-metric">
                            <div class="ai-metric-label">Average progress per day</div>
                            <div class="ai-metric-value">
                                <?php echo $total_logs > 0 ? $avg_daily_progress.'% / day' : 'Awaiting first update'; ?>
                            </div>
                        </div>

                        <div class="ai-metric">
                            <div class="ai-metric-label">Projected days to 100%</div>
                            <div class="ai-metric-value">
                                <?php 
                                if ($projected_days_to_complete !== null) {
                                    echo $projected_days_to_complete . ' day(s) (AI estimate)';
                                } elseif ($last_progress >= 100) {
                                    echo 'Marked as complete';
                                } else {
                                    echo 'Not enough data';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="ai-metric">
                            <div class="ai-metric-label">Image coverage</div>
                            <div class="ai-metric-value">
                                <?php echo $image_coverage; ?>% of logs have images
                            </div>
                            <?php if($latest_image_date): ?>
                                <div style="font-size:11px; color:#9ca3af;">
                                    Last image on <?php echo date('d M Y', strtotime($latest_image_date)); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="ai-note">
                            <i class="fa fa-lightbulb-o"></i>
                            <?php echo $ai_risk_note; ?>
                        </div>

                        <?php if($total_logs == 0): ?>
                            <div class="mt-2" style="font-size:11px; color:#9ca3af;">
                                Tip: Aim for at least one image-based log per working day for accurate visual tracking.
                            </div>
                        <?php elseif($image_coverage < 60): ?>
                            <div class="mt-2" style="font-size:11px; color:#9ca3af;">
                                Tip: Increase image uploads to improve visual analysis quality.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div> <!-- /container-fluid -->
    </div> <!-- /wrapper -->

    <!-- AI Image Analysis Modal -->
    <div class="modal fade" id="aiImageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-content-ai">
                <div class="modal-header modal-header-ai">
                    <h5 class="modal-title">AI Visual Review</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        ×
                    </button>
                </div>
                <div class="modal-body modal-body-ai">
                    <div class="row">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <img src="" alt="Progress image" id="aiModalImage">
                        </div>
                        <div class="col-md-5 d-flex flex-column">
                            <div class="ai-image-meta">
                                <div><strong>Date:</strong> <span id="aiMetaDate"></span></div>
                                <div><strong>Time:</strong> <span id="aiMetaTime"></span></div>
                                <div><strong>Progress:</strong> <span id="aiMetaProgress"></span>%</div>
                                <div><strong>Updated by:</strong> <span id="aiMetaUpdatedBy"></span></div>
                            </div>

                            <div class="ai-image-analysis mt-3">
                                <div class="ai-image-analysis-title">Supervisor Remarks</div>
                                <div id="aiMetaRemarks" style="white-space:pre-wrap;"></div>
                            </div>

                            <div class="ai-image-analysis mt-3">
                                <div class="ai-image-analysis-title">AI-style observations</div>
                                <div id="aiMetaAiText" class="mb-1"></div>
                                <div id="aiMetaAiTags"></div>
                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <small id="aiMetaCounter" class="text-muted">Image 1 of 1</small>
                                <div>
                                    <button type="button" class="btn btn-sm btn-light" id="aiPrevBtn">
                                        ‹ Previous
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" id="aiNextBtn">
                                        Next ›
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <!-- Scripts -->
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        var aiImageData = [];
        var currentIndex = 0;

        // Build array of images + metadata
        $('.ai-image-trigger').each(function(idx) {
            var $el = $(this);
            $el.attr('data-index', idx);
            aiImageData.push({
                img:       $el.data('img'),
                date:      $el.data('log-date'),
                time:      $el.data('log-time'),
                progress:  parseInt($el.data('progress'), 10) || 0,
                remarks:   $el.data('remarks') || '',
                updatedBy: $el.data('updated-by') || ''
            });
        });

        function renderAiModal(index) {
            if (index < 0 || index >= aiImageData.length) return;
            var item = aiImageData[index];

            $('#aiModalImage').attr('src', item.img);
            $('#aiMetaDate').text(item.date || '');
            $('#aiMetaTime').text(item.time || '');
            $('#aiMetaProgress').text(item.progress);
            $('#aiMetaUpdatedBy').text(item.updatedBy || 'N/A');
            $('#aiMetaRemarks').text(item.remarks || 'No remarks added.');

            // Simple rule-based "AI" text and tags per image
            var aiText = '';
            var aiTags = [];

            if (item.progress < 30) {
                aiText = 'Early stage visual – structure/setup work appears to be in initial phase.';
                aiTags = ['early-stage', 'setup', 'baseline'];
            } else if (item.progress < 70) {
                aiText = 'Mid-stage progress – compare this with previous images to confirm consistent build-up.';
                aiTags = ['mid-stage', 'in-progress', 'consistency-check'];
            } else if (item.progress < 100) {
                aiText = 'Near completion – focus on finishing quality checks in the next updates.';
                aiTags = ['finishing', 'quality-focus', 'pre-handover'];
            } else {
                aiText = 'Marked as fully complete – use this image as visual reference for standard output.';
                aiTags = ['completed', 'reference', 'benchmark'];
            }

            $('#aiMetaAiText').text(aiText);

            var tagsHtml = '';
            aiTags.forEach(function(t) {
                tagsHtml += '<span class="ai-tag-pill">' + t + '</span>';
            });
            $('#aiMetaAiTags').html(tagsHtml);

            // Counter + prev/next button state
            $('#aiMetaCounter').text('Image ' + (index + 1) + ' of ' + aiImageData.length);
            $('#aiPrevBtn').prop('disabled', index === 0);
            $('#aiNextBtn').prop('disabled', index === aiImageData.length - 1);

            $('#aiImageModal').modal('show');
        }

        // Open modal on image click
        $('.ai-image-trigger').on('click', function(e) {
            e.preventDefault();
            var idx = parseInt($(this).data('index'), 10) || 0;
            currentIndex = idx;
            renderAiModal(currentIndex);
        });

        // Prev / Next buttons
        $('#aiPrevBtn').on('click', function() {
            if (currentIndex > 0) {
                currentIndex--;
                renderAiModal(currentIndex);
            }
        });

        $('#aiNextBtn').on('click', function() {
            if (currentIndex < aiImageData.length - 1) {
                currentIndex++;
                renderAiModal(currentIndex);
            }
        });

        // Keyboard navigation when modal open
        $(document).on('keydown', function(e) {
            if (!$('#aiImageModal').hasClass('show')) return;

            if (e.key === 'ArrowLeft') {
                if (currentIndex > 0) {
                    currentIndex--;
                    renderAiModal(currentIndex);
                }
            } else if (e.key === 'ArrowRight') {
                if (currentIndex < aiImageData.length - 1) {
                    currentIndex++;
                    renderAiModal(currentIndex);
                }
            }
        });
    });
    </script>
</body>
</html>
