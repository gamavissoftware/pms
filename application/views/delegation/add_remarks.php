<?php $is_delegator_view = !empty($is_delegator_view); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Delegation Task Remark</title>

    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <style>
        body {
            background: #eef3f9;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #25364a;
        }

        .page-hero {
            background: linear-gradient(135deg, #243447 0%, #4872b8 100%);
            border-radius: 18px;
            padding: 24px 26px;
            color: #fff;
            margin-bottom: 22px;
            box-shadow: 0 12px 32px rgba(36, 52, 71, 0.22);
            position: relative;
            overflow: hidden;
        }

        .page-hero:after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            right: -80px;
            top: -90px;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
        }

        .page-hero h3 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .4px;
            color: #fff;
        }

        .page-hero p {
            margin: 8px 0 0;
            color: rgba(255,255,255,0.85);
            font-size: 14px;
        }

        .hero-meta {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hero-chip {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            background: rgba(255,255,255,0.16);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(255,255,255,0.18);
        }

        .kpi-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px 16px;
            box-shadow: 0 8px 24px rgba(36, 52, 71, 0.08);
            border: 1px solid #e4ebf3;
            margin-bottom: 18px;
            min-height: 112px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 6px;
            height: 100%;
        }

        .kpi-blue:before { background: #4872b8; }
        .kpi-green:before { background: #2ecc71; }
        .kpi-orange:before { background: #f39c12; }
        .kpi-red:before { background: #e74c3c; }

        .kpi-card h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #243447;
        }

        .kpi-card p {
            margin: 8px 0 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #738196;
            font-weight: 700;
        }

        .kpi-icon {
            position: absolute;
            right: 16px;
            top: 18px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #f3f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4872b8;
            font-size: 18px;
        }

        .modern-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 24px rgba(36, 52, 71, 0.08);
            border: 1px solid #e4ebf3;
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 800;
            color: #243447;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: #4872b8;
        }

        table.manglesh {
            border-collapse: collapse !important;
            border: 2px solid #243447 !important;
            width: 100% !important;
            background: #fff;
        }

        table.manglesh thead th {
            background: #243447;
            color: #fff;
            font-weight: 800;
            font-size: 11px;
            vertical-align: middle !important;
            white-space: nowrap;
            border: 1px solid #172331 !important;
            text-align: center;
            padding: 10px 8px !important;
        }

        table.manglesh tbody td {
            border: 1px solid #c9d3df !important;
            vertical-align: top !important;
            font-size: 12px;
            line-height: 20px;
            color: #2d3748;
            padding: 10px 8px !important;
        }

        table.manglesh tbody tr:hover td {
            background: #f8fbff !important;
        }

        table.manglesh tbody td:nth-child(6) {
            min-width: 360px;
            white-space: normal !important;
            word-break: break-word;
            font-weight: 600;
            color: #243447;
        }

        .dataTables_filter {
            display: none;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom: 10px;
        }

        .search-box {
            height: 40px;
            border-radius: 22px;
            border: 1px solid #d8e2ee;
            padding: 0 16px;
            box-shadow: none;
        }

        .search-box:focus {
            border-color: #4872b8;
            box-shadow: 0 0 0 3px rgba(72,114,184,0.12);
        }

        .task-form-panel {
            background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
            border-radius: 16px;
            padding: 22px;
            border: 1px solid #e4ebf3;
            box-shadow: 0 8px 24px rgba(36, 52, 71, 0.08);
            margin-bottom: 22px;
        }

        .form-label {
            font-weight: 800;
            color: #243447;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .required-star {
            color: #e74c3c;
            font-weight: 800;
        }

        .remarks-textarea {
            min-height: 190px;
            resize: vertical;
            border-radius: 14px;
            border: 1px solid #d8e2ee;
            padding: 14px;
            font-size: 14px;
            line-height: 23px;
            box-shadow: none;
        }

        .remarks-textarea:focus {
            border-color: #4872b8;
            box-shadow: 0 0 0 3px rgba(72,114,184,0.12);
        }

        .attachment-box {
            border: 2px dashed #cbd7e6;
            border-radius: 14px;
            padding: 16px;
            background: #f8fbff;
            transition: .2s;
        }

        .attachment-box:hover {
            border-color: #4872b8;
            background: #f3f8ff;
        }

        .attachment-box input {
            border: none;
            background: transparent;
            padding: 0;
        }

        .status-toggle-area {
            background: #f8fbff;
            border: 1px solid #e1eaf4;
            border-radius: 14px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 72px;
        }

        .status-text strong {
            color: #243447;
            display: block;
            font-size: 14px;
        }

        .status-text small {
            color: #718096;
            font-size: 12px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 54px;
            height: 28px;
            margin: 0;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            background-color: #ccd6e2;
            transition: .4s;
            border-radius: 30px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,.18);
        }

        input:checked + .slider {
            background-color: #2ecc71;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .btn-submit-update {
            background: linear-gradient(135deg, #2ecc71, #179b56);
            color: #fff;
            border: none;
            border-radius: 24px;
            padding: 11px 24px;
            font-weight: 800;
            letter-spacing: .3px;
            box-shadow: 0 8px 18px rgba(46,204,113,.25);
        }

        .btn-submit-update:hover,
        .btn-submit-update:focus {
            color: #fff;
            opacity: .94;
        }

        .btn-reset-form {
            background: #eef3f9;
            color: #243447;
            border: 1px solid #d8e2ee;
            border-radius: 24px;
            padding: 11px 20px;
            font-weight: 700;
        }

        .chat-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 24px rgba(36, 52, 71, 0.08);
            border: 1px solid #e4ebf3;
            margin-bottom: 22px;
        }

        .chat-box {
            max-height: 480px;
            overflow-y: auto;
            padding: 18px;
            background:
                radial-gradient(circle at 20% 20%, rgba(72,114,184,.08) 0 10px, transparent 11px),
                radial-gradient(circle at 80% 30%, rgba(46,204,113,.08) 0 10px, transparent 11px),
                #eef3f9;
            border-radius: 16px;
            border: 1px solid #dde7f2;
        }

        .chat-msg {
            display: flex;
            margin-bottom: 14px;
            align-items: flex-end;
        }

        .chat-left {
            justify-content: flex-start;
        }

        .chat-right {
            justify-content: flex-end;
        }

        .chat-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 8px;
            border: 2px solid #fff;
            box-shadow: 0 3px 10px rgba(0,0,0,.12);
        }

        .chat-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-bubble {
            min-width: 280px;
            max-width: 74%;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 13px;
            position: relative;
            box-shadow: 0 4px 12px rgba(36,52,71,.08);
            line-height: 22px;
        }

        .chat-left .chat-bubble {
            background: #fff;
            border-top-left-radius: 4px;
        }

        .chat-right .chat-bubble {
            background: #dcf8c6;
            border-top-right-radius: 4px;
        }

        .chat-name {
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 4px;
            color: #243447;
        }

        .chat-time {
            font-size: 10px;
            color: #667085;
            margin-top: 6px;
            text-align: right;
        }

        .chat-attach a {
            font-size: 12px;
            color: #4872b8;
            font-weight: 700;
        }

        .empty-remarks {
            background: #fff;
            border-radius: 14px;
            padding: 25px;
            text-align: center;
            color: #718096;
            border: 1px dashed #cbd7e6;
        }

        .copy-url-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .copy-url-wrap input {
            max-width: 180px;
            height: 30px;
            font-size: 11px;
        }

        .summary-alert {
            border-radius: 14px;
            border: 1px solid #d8e2ee;
            background: #f8fbff;
            padding: 14px 16px;
            color: #4a5568;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .summary-alert strong {
            color: #243447;
        }

        @media(max-width: 768px) {
            .page-hero {
                padding: 20px;
            }

            .page-hero h3 {
                font-size: 20px;
            }

            .kpi-card {
                min-height: auto;
            }

            table.manglesh tbody td:nth-child(6) {
                min-width: 260px;
            }

            .chat-bubble {
                min-width: auto;
                max-width: 92%;
            }

            .status-toggle-area {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
</header>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-hero">
                    <h3><i class="fa fa-tasks"></i> <?php echo $is_delegator_view ? 'Delegated Task Conversation' : 'Task Progress Workspace'; ?></h3>
                    <p><?php echo $is_delegator_view ? 'Review the delegated task, reply to the assignee, share attachments, and follow the complete conversation.' : 'Review assigned task details, submit progress updates, attach proof, and track previous remarks in one professional view.'; ?></p>

                    <div class="hero-meta">
                        <span class="hero-chip"><i class="fa fa-hashtag"></i> Task ID: <?php echo $this->uri->segment(3); ?></span>
                        <span class="hero-chip"><i class="fa fa-clock-o"></i> Last Refreshed: <?php echo date('d-M-Y h:i A'); ?></span>
                        <span class="hero-chip"><i class="fa fa-user"></i> <?php echo $is_delegator_view ? 'Delegator Dashboard' : 'User Dashboard'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $this->session->flashdata('message'); ?>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-blue">
                    <div class="kpi-icon"><i class="fa fa-list"></i></div>
                    <h3 id="kpi_total_rows">0</h3>
                    <p>Task Records</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-orange">
                    <div class="kpi-icon"><i class="fa fa-calendar"></i></div>
                    <h3 id="kpi_due_date">--</h3>
                    <p>Due Date</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-green">
                    <div class="kpi-icon"><i class="fa fa-comments"></i></div>
                    <h3 id="kpi_total_remarks">0</h3>
                    <p>Remarks Loaded</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-red">
                    <div class="kpi-icon"><i class="fa fa-check-circle"></i></div>
                    <h3 id="kpi_status_text">Open</h3>
                    <p>Current Status</p>
                </div>
            </div>
        </div>

        <div class="summary-alert">
            <strong>Tip:</strong> <?php echo $is_delegator_view ? 'Use this conversation to ask questions, provide guidance, and respond directly to the person handling the task.' : 'Write clear progress remarks with actual status, dependency, completion percentage, and proof attachment wherever required. This will help management track the task without repeated follow-ups.'; ?>
        </div>

        <div class="modern-card">
            <div class="row">
                <div class="col-md-6">
                    <div class="section-title"><i class="fa fa-info-circle"></i> Task Details</div>
                </div>
                <div class="col-md-6">
                    <input type="text" id="taskSearchBox" class="form-control search-box" placeholder="Search in task details...">
                </div>
            </div>

            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered manglesh">
                    <thead>
                    <tr>
                        <th>SR NO.</th>
                        <th>TIMESTAMP</th>
                        <th>DELEGATED BY</th>
                        <th>DELEGATED TO</th>
                        <th>CASE NO</th>
                        <th>WORK DELEGATED</th>
                        <th>ATTACHMENT</th>
                        <th>EMAIL URL</th>
                        <th>2ND DATE</th>
                        <th>3RD DATE</th>
                        <th>WORK DUE DATE</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="row">

            <div class="col-md-5">
                <div class="task-form-panel">
                    <div class="section-title"><i class="fa fa-pencil-square-o"></i> <?php echo $is_delegator_view ? 'Reply to Assignee' : 'Submit Progress Update'; ?></div>

                    <form id="add_task" enctype="multipart/form-data">
                        <input type="hidden" name="task_id" id="task_id" value="<?php echo $this->uri->segment(3); ?>">

                        <div class="form-group">
                            <label class="form-label"><?php echo $is_delegator_view ? 'Reply / Comment' : 'Progress Update / Remarks'; ?> <span class="required-star">*</span></label>
                            <textarea name="remarks" id="remarks" class="form-control remarks-textarea" placeholder="<?php echo $is_delegator_view ? 'Write your reply, question, instruction, or feedback for the assignee.' : 'Example: Work is 70% completed. Pending dependency is approval from purchase team. Expected completion by tomorrow.'; ?>"></textarea>
                            <small class="text-muted"><?php echo $is_delegator_view ? 'Your reply will appear in the same conversation and notify the assignee.' : 'Please write meaningful remarks so task progress is clearly visible.'; ?></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Attachment / Proof</label>
                            <div class="attachment-box">
                                <input type="file" class="form-control" name="attachment" id="attachment">
                                <small class="text-muted">Upload screenshot, document, proof, or supporting file if available.</small>
                            </div>
                        </div>

                        <?php if (!$is_delegator_view) { ?>
                        <div class="form-group">
                            <label class="form-label">Task Status</label>

                            <div class="status-toggle-area">
                                <label class="switch">
                                    <input type="checkbox" name="status" id="status" value="1">
                                    <span class="slider round"></span>
                                </label>

                                <div class="status-text">
                                    <strong id="status_label">Still Pending</strong>
                                    <small>Enable this only when the assigned task is completed.</small>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="form-group text-right">
                            <button type="button" class="btn btn-reset-form" onclick="resetProgressForm()">
                                <i class="fa fa-refresh"></i> Reset
                            </button>

                            <button class="btn btn-submit-update" type="submit" id="submitBtn">
                                <i class="fa fa-paper-plane"></i> <?php echo $is_delegator_view ? 'Send Reply' : 'Submit Update'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="chat-card">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="section-title"><i class="fa fa-comments"></i> Previous Remarks Timeline</div>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="btn btn-primary btn-xs" onclick="manualReloadRemarks()">
                                <i class="fa fa-refresh"></i> Reload Remarks
                            </button>
                        </div>
                    </div>

                    <div class="chat-box" id="remarks_list">
                        <div class="empty-remarks">
                            <i class="fa fa-spinner fa-spin"></i> Loading previous remarks...
                        </div>
                    </div>
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

<script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>
<script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

<script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    var taskTable = null;

    $(document).ready(function() {
        $('.select2').select2({});
        $('.select3').select2({});
        $('.select4').select2({});

        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: "3500"
        };

        taskTable = $('#example').DataTable({
            processing: false,
            fixedHeader: true,
            pagination: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            ajax: {
                url: "<?php echo page_url; ?>Delegation/<?php echo $is_delegator_view ? 'user_wise_by_delegated_task_list' : 'user_wise_delegated_task_list'; ?>/<?php echo $this->uri->segment(3); ?>",
                dataSrc: "aaData"
            },
            columns: [
                { data: 'sr_no' },
                { data: 'timestamp' },
                { data: 'delegated_by' },
                { data: 'delegated_to' },
                { data: 'caseno' },
                { data: 'task' },
                { data: 'attachment' },
                { data: 'email_url' },
                { data: 'second_date' },
                { data: 'third_date' },
                { data: 'delegated_date' }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                    title: 'Delegation Task Detail'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print'
                }
            ],
            order: [[1, 'desc']],
            initComplete: function(settings, json) {
                updateTaskKPIs(json);
            },
            drawCallback: function(settings) {
                var api = this.api();
                $('#kpi_total_rows').text(api.rows({ filter: 'applied' }).count());
            }
        });

        $('#taskSearchBox').on('keyup', function() {
            taskTable.search(this.value).draw();
        });

        let task_id = $("#task_id").val();

        if (task_id && task_id != '') {
            loadRemarks(task_id);
        } else {
            $("#remarks_list").html('<div class="empty-remarks">Task ID missing.</div>');
        }

        $("#status").on("change", function() {
            if ($(this).is(":checked")) {
                $("#status_label").text("Marking as Completed");
                $("#kpi_status_text").text("Done");
            } else {
                $("#status_label").text("Still Pending");
                $("#kpi_status_text").text("Open");
            }
        });

        $("#add_task").on("submit", function(e) {
            e.preventDefault();

            let remarks = $("#remarks").val();

            if (remarks.trim() == "") {
                toastr.error("Please enter progress update / remarks.");
                $("#remarks").focus();
                return false;
            }

            let formData = new FormData(this);

            $("#submitBtn").prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

            $.ajax({
                url: "<?php echo page_url; ?>Delegation/save_response_ajax",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",

                success: function(res) {
                    if (res.status == "success") {
                        toastr.success(res.message);

                        $("#add_task")[0].reset();
                        <?php if (!$is_delegator_view) { ?>
                        $("#status_label").text("Still Pending");
                        $("#kpi_status_text").text("Open");
                        <?php } ?>

                        if (taskTable) {
                            taskTable.ajax.reload(function(json) {
                                updateTaskKPIs(json);
                            }, false);
                        }

                        loadRemarks(task_id);

                    } else {
                        toastr.error(res.message);
                    }
                },

                error: function() {
                    toastr.error("Something went wrong. Please try again.");
                },

                complete: function() {
                    $("#submitBtn").prop("disabled", false).html('<i class="fa fa-paper-plane"></i> <?php echo $is_delegator_view ? 'Send Reply' : 'Submit Update'; ?>');
                }
            });
        });
    });

    function updateTaskKPIs(json) {
        if (!json || !json.aaData) {
            $('#kpi_total_rows').text(0);
            $('#kpi_due_date').text('--');
            return;
        }

        $('#kpi_total_rows').text(json.aaData.length);

        if (json.aaData.length > 0) {
            var firstRow = json.aaData[0];

            if (firstRow.delegated_date && $.trim(firstRow.delegated_date) != '') {
                $('#kpi_due_date').text(stripHtml(firstRow.delegated_date));
            } else {
                $('#kpi_due_date').text('--');
            }
        }
    }

    function stripHtml(html) {
        return $('<div>').html(html).text().replace(/\s+/g, ' ').trim();
    }

    function loadRemarks(task_id) {
        $("#remarks_list").html('<div class="empty-remarks"><i class="fa fa-spinner fa-spin"></i> Loading previous remarks...</div>');

        $.ajax({
            url: "<?php echo page_url; ?>Delegation/get_task_remarks",
            type: "POST",
            data: {
                task_id: task_id
            },

            success: function(res) {
                if ($.trim(res) == '') {
                    $("#remarks_list").html('<div class="empty-remarks"><i class="fa fa-comments-o"></i><br>No previous remarks found.</div>');
                    $('#kpi_total_remarks').text(0);
                } else {
                    $("#remarks_list").html(res);

                    var remarkCount = $("#remarks_list").find(".chat-msg").length;

                    if (remarkCount <= 0) {
                        remarkCount = $("#remarks_list").children().length;
                    }

                    $('#kpi_total_remarks').text(remarkCount);

                    let box = document.getElementById("remarks_list");
                    box.scrollTop = box.scrollHeight;
                }
            },

            error: function() {
                $("#remarks_list").html('<div class="empty-remarks text-danger">Error loading remarks.</div>');
            }
        });
    }

    function manualReloadRemarks() {
        let task_id = $("#task_id").val();

        if (task_id && task_id != '') {
            loadRemarks(task_id);
            toastr.success("Remarks refreshed.");
        }
    }

    function resetProgressForm() {
        $("#add_task")[0].reset();
        <?php if (!$is_delegator_view) { ?>
        $("#status_label").text("Still Pending");
        $("#kpi_status_text").text("Open");
        <?php } ?>
        toastr.info("Form reset successfully.");
    }

    function copyUrl(id) {
        let copyText = document.getElementById(id);

        if (!copyText) {
            toastr.error("URL field not found.");
            return false;
        }

        copyText.select();
        copyText.setSelectionRange(0, 99999);

        document.execCommand("copy");

        toastr.success("URL copied!");
    }
</script>

</body>
</html>
