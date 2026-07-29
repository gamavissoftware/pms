<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Meeting Form</title>

    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>

    <style>
        :root {
            --meeting-ink: #132238;
            --meeting-muted: #66778f;
            --meeting-line: rgba(19, 34, 56, 0.12);
            --meeting-surface: #ffffff;
            --meeting-bg: #f5f7fb;
            --meeting-teal: #0f766e;
            --meeting-orange: #f59e0b;
            --meeting-blue: #1d4ed8;
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.11), transparent 20%),
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.15), transparent 28%),
                var(--meeting-bg);
            color: var(--meeting-ink);
        }

        .meeting-form-page {
            padding-top: 20px;
            padding-bottom: 30px;
            padding-left: 14px;
            padding-right: 14px;
        }

        .hero-card,
        .form-shell,
        .intelligence-shell,
        .mini-shell {
            background: var(--meeting-surface);
            border: 1px solid var(--meeting-line);
            border-radius: 24px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.07);
        }

        .hero-card {
            padding: 32px;
            margin-bottom: 24px;
            background:
                linear-gradient(135deg, rgba(19, 34, 56, 0.97), rgba(15, 118, 110, 0.97)),
                var(--meeting-surface);
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .hero-card:before,
        .hero-card:after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.07);
        }

        .hero-card:before {
            width: 240px;
            height: 240px;
            right: -60px;
            top: -90px;
        }

        .hero-card:after {
            width: 150px;
            height: 150px;
            bottom: -35px;
            right: 140px;
        }

        .hero-card > .row {
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow {
            display: inline-block;
            margin-bottom: 10px;
            font-size: 12px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.72);
        }

        .hero-card h2 {
            margin: 0 0 10px;
            font-size: 34px;
            line-height: 1.18;
            color: #fff;
            font-weight: 700;
        }

        .hero-card p {
            margin: 0;
            color: rgba(255, 255, 255, 0.84);
            line-height: 1.7;
            font-size: 15px;
            max-width: 720px;
        }

        .hero-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .hero-actions .btn {
            border-radius: 999px;
            font-weight: 700;
            padding: 11px 18px;
            border: none;
        }

        .hero-actions .btn-default {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .hero-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        .form-shell {
            padding: 28px;
            margin-bottom: 24px;
        }

        .section-block + .section-block {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px dashed rgba(19, 34, 56, 0.12);
        }

        .section-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--meeting-ink);
        }

        .section-subtitle {
            margin-top: 6px;
            color: var(--meeting-muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .field-label {
            margin-bottom: 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--meeting-muted);
            font-weight: 700;
        }

        .field-error {
            color: #dc2626;
            font-size: 12px;
            font-weight: 700;
            margin-left: 4px;
            text-transform: none;
            letter-spacing: normal;
        }

        .form-shell .form-control,
        .form-shell .select2-container .select2-selection--single {
            min-height: 48px;
            border-radius: 14px !important;
            border: 1px solid rgba(19, 34, 56, 0.12);
            box-shadow: none !important;
        }

        .form-shell .form-control:focus {
            border-color: rgba(15, 118, 110, 0.45);
        }

        .form-shell .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 46px;
            padding-left: 14px;
            color: var(--meeting-ink);
        }

        .form-shell .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 10px;
        }

        .info-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 700;
            background: rgba(15, 118, 110, 0.07);
            color: var(--meeting-teal);
            margin-top: 10px;
            margin-right: 8px;
        }

        .duration-card {
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.08), rgba(29, 78, 216, 0.06));
            border: 1px solid rgba(15, 118, 110, 0.12);
            border-radius: 20px;
            padding: 20px;
            margin-top: 10px;
        }

        .duration-clock {
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: var(--meeting-ink);
            margin-bottom: 8px;
        }

        .duration-note {
            color: var(--meeting-muted);
            font-size: 13px;
            line-height: 1.7;
        }

        .intelligence-shell,
        .mini-shell {
            padding: 24px;
            margin-bottom: 24px;
        }

        .intelligence-shell {
            background:
                linear-gradient(180deg, rgba(15, 118, 110, 0.06), transparent 50%),
                var(--meeting-surface);
        }

        .intel-kicker {
            font-size: 12px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--meeting-muted);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .intel-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--meeting-ink);
        }

        .live-value {
            font-size: 42px;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: var(--meeting-ink);
            margin-bottom: 12px;
        }

        .mode-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .mode-pill.auto {
            background: rgba(14, 165, 233, 0.12);
            color: #0369a1;
        }

        .mode-pill.manual {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
        }

        .intel-copy {
            color: var(--meeting-muted);
            font-size: 14px;
            line-height: 1.8;
            margin-top: 14px;
        }

        .intel-divider {
            margin: 20px 0;
            border-top: 1px dashed rgba(19, 34, 56, 0.12);
        }

        .spot-item + .spot-item {
            margin-top: 14px;
        }

        .spot-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--meeting-muted);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .spot-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--meeting-ink);
        }

        .checklist {
            margin: 0;
            padding-left: 18px;
            color: var(--meeting-muted);
        }

        .checklist li {
            margin-bottom: 10px;
            line-height: 1.7;
        }

        .attachment-note {
            margin-top: 8px;
            color: var(--meeting-muted);
            font-size: 12px;
        }

        .field-support-note {
            margin-top: 8px;
            color: var(--meeting-muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .submit-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px dashed rgba(19, 34, 56, 0.12);
        }

        .submit-wrap .btn {
            border-radius: 999px;
            padding: 12px 24px;
            font-weight: 700;
            min-width: 160px;
        }

        .submit-note {
            color: var(--meeting-muted);
            font-size: 13px;
            line-height: 1.7;
            max-width: 420px;
        }

        #pageloader {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 99999;
            background: rgba(255, 255, 255, 0.76);
            text-align: center;
        }

        #pageloader img {
            position: relative;
            top: 42%;
            width: 90px;
        }

        @media (max-width: 991px) {
            .hero-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 767px) {
            .hero-card,
            .form-shell,
            .intelligence-shell,
            .mini-shell {
                padding: 22px 18px;
            }

            .hero-card h2 {
                font-size: 28px;
            }

            .live-value,
            .duration-clock {
                font-size: 32px;
            }

            .submit-wrap {
                flex-direction: column;
                align-items: stretch;
            }

            .submit-wrap .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div id="pageloader">
        <img src="https://pms.shubhampack.in/assets/images/loading.gif" alt="processing..." />
    </div>

    <div class="wrapper">
        <div class="container-fluid meeting-form-page">
            <div class="hero-card">
                <div class="row">
                    <div class="col-md-8">
                        <span class="hero-eyebrow">Meeting Log</span>
                        <h2>Capture Meetings with more clarity and less friction.</h2>
                        <p>The workflow now supports both live timing and manual start-end capture. Teams can log meetings quickly while management gets cleaner time intelligence in the dashboard.</p>
                        <div class="hero-pills">
                            <span class="hero-pill"><i class="fa fa-calendar"></i> <?php echo date('d M Y'); ?></span>
                            <span class="hero-pill"><i class="fa fa-clock-o"></i> Logged at <?php echo date('h:i A'); ?></span>
                            <span class="hero-pill"><i class="fa fa-line-chart"></i> Dashboard-ready entries</span>
                        </div>
                    </div>
                    <div class="col-md-4 hero-actions">
                        <a href="<?php echo page_url; ?>Maintenance_support/listmeetings" class="btn btn-warning"><i class="fa fa-bar-chart"></i> Open Dashboard</a>
                        <a href="<?php echo page_url; ?>Maintenance_support/meetinglog" class="btn btn-default"><i class="fa fa-refresh"></i> Fresh Form</a>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('message')) { ?>
                <div class="alert alert-info" style="border-radius:16px; border:none; box-shadow:0 10px 30px rgba(15,118,110,0.10);">
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="form-shell">
                        <form id="meetingLogForm" method="post" action="<?php echo page_url; ?>Maintenance_support/addmeetinginfo" enctype="multipart/form-data">
                            <div class="section-block">
                                <h3 class="section-title">Meeting Context</h3>
                                <p class="section-subtitle">Start with the basic timing layer. If you leave manual start and end empty, the system will continue using the live timer automatically.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="field-label">Meeting Date</label>
                                            <input type="text" class="form-control" name="meetingdate" id="meetingdate" value="<?php echo date('d-m-Y'); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="field-label">Meeting Logged Time</label>
                                            <input type="text" class="form-control" name="meetingtime" id="meetingtime" value="<?php echo date('h:i A'); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="field-label">Meeting Start Time <span class="field-error">(Optional)</span></label>
                                            <input type="time" class="form-control" name="manual_meeting_start_time" id="manual_meeting_start_time" value="<?php echo set_value('manual_meeting_start_time'); ?>" step="1">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="field-label">Meeting End Time <span class="field-error">(Optional)</span> <span id="error_manual_meeting_time" class="field-error"></span></label>
                                            <input type="time" class="form-control" name="manual_meeting_end_time" id="manual_meeting_end_time" value="<?php echo set_value('manual_meeting_end_time'); ?>" step="1">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="duration-card">
                                            <div class="field-label" style="margin-bottom:10px;">Total Time Spent</div>
                                            <input type="text" class="form-control" id="timerInput" name="total_time_spend" value="00:00:00" readonly style="font-size:30px; font-weight:700; letter-spacing:0.08em; text-align:center; background:#fff;">
                                            <div class="duration-note" id="timeDurationNote">The live timer is running now. Add both manual times only when you want the system to calculate the duration from an exact range.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-block">
                                <h3 class="section-title">Collaboration Details</h3>
                                <p class="section-subtitle">Start with department selection, then connect the meeting to the right DF and person so the management dashboard can tell a complete story.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="field-label">Department <span id="error_department" class="field-error"><?php echo form_error('department'); ?></span></label>
                                            <select class="form-control select2" id="department" name="department" required>
                                                <option value="">Select Department</option>
                                                <?php
                                                $q = $this->db->select('department_id, department')->from('departments')->where('business_loc_id', 2)->where('status', 1)->get();
                                                foreach ($q->result() as $rows) {
                                                ?>
                                                    <option value="<?php echo $rows->department_id; ?>" <?php echo set_select('department', $rows->department_id); ?>>
                                                        <?php echo strtoupper($rows->department); ?>
                                                    </option>
                                                <?php } ?>
                                                <option value="OTHERS" <?php echo set_select('department', 'OTHERS'); ?>>OTHERS</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="field-label">Select DF <span id="error_dfno" class="field-error"><?php echo form_error('dfno'); ?></span></label>
                                            <select class="form-control select2" name="dfno" id="dfno">
                                                <option value="">Select DF No</option>
                                                <?php
                                                $q = $this->db->select('id, df_no, df_description')->from('df_release')->where('df_status', 0)->get();
                                                foreach ($q->result() as $row) {
                                                ?>
                                                    <option value="<?php echo $row->id; ?>" <?php echo set_select('dfno', $row->id); ?>>
                                                        <?php echo $row->df_no; ?> <?php echo $row->df_description; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <div class="field-support-note" id="dfSelectionHint">Choose department first. DF is required for internal meetings and optional only when department is Others.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="participantSelectRow">
                                        <div class="form-group">
                                            <label class="field-label">Meeting With <span id="error_user_id" class="field-error"><?php echo form_error('user_id'); ?></span></label>
                                            <select class="form-control select2" id="user_id" name="user_id" required></select>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="participantOtherRow" style="display:none;">
                                        <div class="form-group">
                                            <label class="field-label">Outside Participant Name <span id="error_other_person_name" class="field-error"><?php echo form_error('other_person_name'); ?></span></label>
                                            <input type="text" class="form-control" id="other_person_name" name="other_person_name" value="<?php echo set_value('other_person_name'); ?>" placeholder="Type outside person name">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="attachment-note" id="meetingWithHint">Choose a department first so the right team members can be loaded.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-block">
                                <h3 class="section-title">Discussion & Evidence</h3>
                                <p class="section-subtitle">Capture the meeting outcome clearly. Attach a file only when it strengthens the record.</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="field-label">Discussion Point (Remarks) <span class="field-error"><?php echo form_error('remarks'); ?></span></label>
                                            <textarea class="form-control" name="remarks" id="remarks" required><?php echo set_value('remarks'); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="field-label">Attachment</label>
                                            <input type="file" name="screen_shot" id="screen_shot" class="form-control">
                                            <div class="attachment-note" id="attachmentName">Screenshots or supporting files can be attached when they add context.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="submit-wrap">
                                <div class="submit-note">This log will be visible in the meeting dashboard and can trigger the same email workflow already configured in the system.</div>
                                <div>
                                    <button type="submit" id="save" class="btn btn-success"><i class="fa fa-check-circle"></i> Submit Meeting Log</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="intelligence-shell">
                        <div class="intel-kicker">Live Meeting Intelligence</div>
                        <div class="intel-title">Timing preview</div>
                        <div class="live-value" id="liveDurationHeadline">00:00:00</div>
                        <span class="mode-pill auto" id="meetingModePill"><i class="fa fa-bolt"></i> Auto Timer Active</span>
                        <div class="intel-copy" id="meetingModeCopy">The timer started when you opened this form. It keeps running until you submit, unless you switch to a manual start-end range.</div>

                        <div class="intel-divider"></div>

                        <div class="spot-item">
                            <div class="spot-label">Time Window</div>
                            <div class="spot-value" id="timeWindowPreview">Live tracking from page open</div>
                        </div>
                        <div class="spot-item">
                            <div class="spot-label">Selected Participant</div>
                            <div class="spot-value" id="participantPreview">No participant selected yet</div>
                        </div>
                        <div class="spot-item">
                            <div class="spot-label">Current Department</div>
                            <div class="spot-value" id="departmentPreview">No department selected yet</div>
                        </div>
                        <div class="spot-item">
                            <div class="spot-label">DF Link Mode</div>
                            <div class="spot-value" id="dfRequirementPreview">Choose department first</div>
                        </div>
                    </div>

                    <div class="mini-shell">
                        <div class="intel-kicker">Logging Checklist</div>
                        <div class="intel-title" style="font-size:22px;">What makes a strong meeting entry?</div>
                        <ul class="checklist">
                            <li>Use manual start and end only when you want exact range-based time instead of the live timer.</li>
                            <li>Pick the correct department and person so the management dashboard groups collaboration accurately.</li>
                            <li>Select Others when the meeting happened with an outside person, then type the name manually.</li>
                            <li>Write remarks that explain the decision, blocker, or next step, not just the topic.</li>
                            <li>Add an attachment only when it improves traceability or follow-up quality.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <?php $this->load->view('common/footer'); ?>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

    <script>
        var timerInterval;
        var startTime;
        var manualMeetingStartInput;
        var manualMeetingEndInput;
        var timerDisplayInput;
        var selectedMeetingUserId = '<?php echo set_value('user_id'); ?>';
        var otherDepartmentValue = 'OTHERS';

        function normalizeTimeValue(value) {
            return value.length === 5 ? value + ':00' : value;
        }

        function formatDuration(totalSeconds) {
            var hours = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
            var minutes = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
            var seconds = (totalSeconds % 60).toString().padStart(2, '0');
            return hours + ':' + minutes + ':' + seconds;
        }

        function hasCompleteManualMeetingTime() {
            return manualMeetingStartInput.value !== '' && manualMeetingEndInput.value !== '';
        }

        function getManualMeetingDuration() {
            if (!hasCompleteManualMeetingTime()) {
                return null;
            }

            var startDate = new Date('1970-01-01T' + normalizeTimeValue(manualMeetingStartInput.value));
            var endDate = new Date('1970-01-01T' + normalizeTimeValue(manualMeetingEndInput.value));

            if (isNaN(startDate.getTime()) || isNaN(endDate.getTime()) || endDate < startDate) {
                return false;
            }

            return formatDuration(Math.floor((endDate.getTime() - startDate.getTime()) / 1000));
        }

        function setManualMeetingTimeError(message) {
            $('#error_manual_meeting_time').html(message);
        }

        function updateMeetingModeUI() {
            var modePill = $('#meetingModePill');
            var modeCopy = $('#meetingModeCopy');
            var timeWindowPreview = $('#timeWindowPreview');

            if (hasCompleteManualMeetingTime()) {
                modePill.removeClass('auto').addClass('manual').html('<i class="fa fa-hourglass-end"></i> Manual Range Active');
                modeCopy.text('Duration is now being calculated directly from the selected start and end time. This gives management an exact timed record.');
                timeWindowPreview.text(manualMeetingStartInput.value + ' to ' + manualMeetingEndInput.value);
            } else {
                modePill.removeClass('manual').addClass('auto').html('<i class="fa fa-bolt"></i> Auto Timer Active');
                modeCopy.text('The timer started when you opened this form. It keeps running until you submit, unless you switch to a manual start-end range.');
                timeWindowPreview.text('Live tracking from page open');
            }
        }

        function updateDepartmentPreview() {
            var departmentText = $('#department option:selected').text();
            $('#departmentPreview').text(departmentText && $('#department').val() !== '' ? departmentText : 'No department selected yet');
        }

        function updateDfRequirementUI() {
            var departmentId = $('#department').val();
            var isOtherDepartment = isOtherDepartmentSelected();
            var dfHint = $('#dfSelectionHint');
            var dfSelect = $('#dfno');
            var dfRequirementPreview = $('#dfRequirementPreview');

            if (departmentId === '') {
                dfSelect.prop('disabled', true).trigger('change.select2');
                dfHint.text('Choose department first. DF is required for internal meetings and optional only when department is Others.');
                dfRequirementPreview.text('Choose department first');
                return;
            }

            dfSelect.prop('disabled', false).trigger('change.select2');

            if (isOtherDepartment) {
                dfHint.text('DF is optional here. Select it only if this outside meeting still belongs to a specific DF.');
                dfRequirementPreview.text('DF optional for Others');
            } else {
                dfHint.text('DF is required for internal meetings so the meeting stays mapped to the right project.');
                dfRequirementPreview.text('DF required for internal meeting');
            }
        }

        function isOtherDepartmentSelected() {
            return $('#department').val() === otherDepartmentValue;
        }

        function updateParticipantPreview() {
            if (isOtherDepartmentSelected()) {
                var outsideParticipantName = $.trim($('#other_person_name').val());
                $('#participantPreview').text(outsideParticipantName !== '' ? outsideParticipantName : 'Outside participant name not entered yet');
                return;
            }

            var participantText = $('#user_id option:selected').text();
            $('#participantPreview').text(participantText && $('#user_id').val() !== '' ? participantText : 'No participant selected yet');
        }

        function updateAttachmentPreview() {
            var fileName = $('#screen_shot').val().split('\\').pop();
            $('#attachmentName').text(fileName !== '' ? 'Selected file: ' + fileName : 'Screenshots or supporting files can be attached when they add context.');
        }

        function initializeTimer() {
            localStorage.removeItem('timerStartTime');
            startTime = Date.now();
            localStorage.setItem('timerStartTime', startTime);
            syncMeetingDurationDisplay();
        }

        function updateAutoTimerDisplay() {
            var elapsedMilliseconds = Date.now() - startTime;
            var totalSeconds = Math.floor(elapsedMilliseconds / 1000);
            var formattedValue = formatDuration(totalSeconds);
            timerDisplayInput.value = formattedValue;
            $('#liveDurationHeadline').text(formattedValue);
        }

        function syncMeetingDurationDisplay() {
            var manualDuration = getManualMeetingDuration();

            if (manualDuration === false) {
                timerDisplayInput.value = '00:00:00';
                $('#liveDurationHeadline').text('00:00:00');
                setManualMeetingTimeError('End time should be greater than or equal to start time.');
                updateMeetingModeUI();
                return false;
            }

            setManualMeetingTimeError('');
            updateMeetingModeUI();

            if (manualDuration) {
                timerDisplayInput.value = manualDuration;
                $('#liveDurationHeadline').text(manualDuration);
                $('#timeDurationNote').text('Total duration is being calculated from the exact manual time range you selected.');
                return true;
            }

            $('#timeDurationNote').text('The live timer is running now. Add both manual times only when you want the system to calculate the duration from an exact range.');
            updateAutoTimerDisplay();
            return true;
        }

        function startTimer() {
            if (timerInterval) {
                return;
            }
            timerInterval = setInterval(function() {
                if (!hasCompleteManualMeetingTime()) {
                    updateAutoTimerDisplay();
                }
            }, 1000);
        }

        function toggleParticipantInputs() {
            var departmentId = $('#department').val();
            var isOtherDepartment = isOtherDepartmentSelected();

            updateDfRequirementUI();

            if (isOtherDepartment) {
                $('#participantSelectRow').hide();
                $('#participantOtherRow').show();
                $('#user_id').prop('disabled', true).html('').trigger('change');
                $('#other_person_name').prop('disabled', false);
                $('#meetingWithHint').text('Outside meeting selected. Type the participant name manually for dashboard and email visibility.');
            } else {
                $('#participantSelectRow').show();
                $('#participantOtherRow').hide();
                $('#user_id').prop('disabled', departmentId === '');
                $('#other_person_name').prop('disabled', true);

                if (departmentId === '') {
                    $('#meetingWithHint').text('Choose a department first so the right team members can be loaded.');
                } else {
                    $('#meetingWithHint').text('Team members will be loaded for the selected department.');
                }
            }

            updateParticipantPreview();
        }

        function loadDepartmentUsers(selectedUserId) {
            var departmentId = $('#department').val();

            if (departmentId === '') {
                $('#user_id').html('');
                updateParticipantPreview();
                $('#meetingWithHint').text('Choose a department first so the right team members can be loaded.');
                return;
            }

            if (departmentId === otherDepartmentValue) {
                $('#user_id').html('');
                updateParticipantPreview();
                $('#meetingWithHint').text('Outside meeting selected. Type the participant name manually for dashboard and email visibility.');
                return;
            }

            $.ajax({
                type: 'post',
                url: '<?php echo page_url; ?>Delegation/user_list_new',
                data: { department: departmentId },
                success: function(data) {
                    $('#user_id').html(data);
                    if (selectedUserId) {
                        $('#user_id').val(selectedUserId).trigger('change');
                    } else {
                        $('#user_id').trigger('change');
                    }
                    $('#meetingWithHint').text('Team members loaded for the selected department.');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('An error occurred while fetching user data. Please try again.');
                }
            });
        }

        $(document).ready(function() {
            $('.select2').select2({ width: '100%' });

            manualMeetingStartInput = document.getElementById('manual_meeting_start_time');
            manualMeetingEndInput = document.getElementById('manual_meeting_end_time');
            timerDisplayInput = document.getElementById('timerInput');

            initializeTimer();
            startTimer();
            updateDepartmentPreview();
            updateDfRequirementUI();
            toggleParticipantInputs();
            updateParticipantPreview();
            updateAttachmentPreview();

            if ($('#department').val() !== '' && !isOtherDepartmentSelected()) {
                loadDepartmentUsers(selectedMeetingUserId);
            }

            manualMeetingStartInput.addEventListener('input', syncMeetingDurationDisplay);
            manualMeetingEndInput.addEventListener('input', syncMeetingDurationDisplay);

            $('#department').on('change', function() {
                updateDepartmentPreview();
                toggleParticipantInputs();
                loadDepartmentUsers(selectedMeetingUserId);
                selectedMeetingUserId = '';
            });

            $('#user_id').on('change', updateParticipantPreview);
            $('#other_person_name').on('input', updateParticipantPreview);
            $('#screen_shot').on('change', updateAttachmentPreview);

            CKEDITOR.replace('remarks', {
                height: 240
            });

            $('#meetingLogForm').on('submit', function() {
                var dfno = $('#dfno').val();
                var department = $('#department').val();
                var userId = $('#user_id').val();
                var otherPersonName = $.trim($('#other_person_name').val());
                var isOtherDepartment = isOtherDepartmentSelected();
                var manualStartTime = $('#manual_meeting_start_time').val();
                var manualEndTime = $('#manual_meeting_end_time').val();

                $('#error_dfno').html('');
                $('#error_department').html('');
                $('#error_user_id').html('');
                $('#error_other_person_name').html('');
                setManualMeetingTimeError('');

                if (!isOtherDepartment && dfno === '') {
                    $('#error_dfno').html('Required!');
                }

                if (department === '') {
                    $('#error_department').html('Required!');
                }

                if (!isOtherDepartment && (userId === '' || userId === null)) {
                    $('#error_user_id').html('Required!');
                }

                if (isOtherDepartment && otherPersonName === '') {
                    $('#error_other_person_name').html('Required!');
                }

                if ((manualStartTime !== '' && manualEndTime === '') || (manualStartTime === '' && manualEndTime !== '')) {
                    setManualMeetingTimeError('Please select both start and end time.');
                    return false;
                }

                if (manualStartTime !== '' && manualEndTime !== '' && syncMeetingDurationDisplay() === false) {
                    return false;
                }

                if (
                    department === '' ||
                    (!isOtherDepartment && dfno === '') ||
                    (!isOtherDepartment && (userId === '' || userId === null)) ||
                    (isOtherDepartment && otherPersonName === '')
                ) {
                    return false;
                }

                $('#pageloader').fadeIn();
                return true;
            });
        });

        window.onbeforeunload = function() {
            clearInterval(timerInterval);
        };
    </script>
</body>
</html>
