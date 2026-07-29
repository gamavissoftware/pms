<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Work Delegation Form</title>

    <script src="<?php echo assets_url;?>js/angular.min.js"></script>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        body {
            background:#eef3f9;
            color:#25364a;
            font-family:'Segoe UI', Arial, sans-serif;
        }

        .delegate-hero {
            background:linear-gradient(135deg, #243447 0%, #4872b8 100%);
            color:#fff;
            border-radius:18px;
            padding:24px 26px;
            margin-bottom:22px;
            box-shadow:0 12px 32px rgba(36,52,71,.22);
            position:relative;
            overflow:hidden;
        }

        .delegate-hero:after {
            content:"";
            position:absolute;
            right:-80px;
            top:-90px;
            width:230px;
            height:230px;
            background:rgba(255,255,255,.12);
            border-radius:50%;
        }

        .delegate-hero h3 {
            margin:0;
            color:#fff;
            font-weight:800;
            letter-spacing:.4px;
        }

        .delegate-hero p {
            margin:8px 0 0;
            color:rgba(255,255,255,.86);
            font-size:14px;
        }

        .hero-chip-wrap {
            margin-top:16px;
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }

        .hero-chip {
            display:inline-block;
            padding:7px 12px;
            border-radius:20px;
            background:rgba(255,255,255,.16);
            color:#fff;
            font-size:12px;
            font-weight:700;
            border:1px solid rgba(255,255,255,.18);
        }

        .kpi-card {
            background:#fff;
            border-radius:16px;
            padding:18px 16px;
            min-height:112px;
            box-shadow:0 8px 24px rgba(36,52,71,.08);
            border:1px solid #e4ebf3;
            margin-bottom:18px;
            position:relative;
            overflow:hidden;
        }

        .kpi-card:before {
            content:"";
            position:absolute;
            left:0;
            top:0;
            width:6px;
            height:100%;
        }

        .kpi-blue:before { background:#4872b8; }
        .kpi-green:before { background:#2ecc71; }
        .kpi-orange:before { background:#f39c12; }
        .kpi-purple:before { background:#8e44ad; }

        .kpi-card h3 {
            margin:0;
            font-size:27px;
            font-weight:800;
            color:#243447;
        }

        .kpi-card p {
            margin:8px 0 0;
            font-size:12px;
            font-weight:800;
            text-transform:uppercase;
            color:#738196;
            letter-spacing:.5px;
        }

        .kpi-icon {
            position:absolute;
            right:16px;
            top:18px;
            width:42px;
            height:42px;
            border-radius:50%;
            background:#f3f7fb;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#4872b8;
            font-size:18px;
        }

        .main-form-card {
            background:#fff;
            border-radius:18px;
            padding:24px;
            box-shadow:0 8px 24px rgba(36,52,71,.08);
            border:1px solid #e4ebf3;
            margin-bottom:22px;
        }

        .section-title {
            font-size:15px;
            font-weight:800;
            color:#243447;
            text-transform:uppercase;
            letter-spacing:.5px;
            margin-bottom:18px;
            display:flex;
            align-items:center;
            gap:8px;
        }

        .section-title i {
            color:#4872b8;
        }

        .form-section {
            background:#f8fbff;
            border:1px solid #e4ebf3;
            border-radius:16px;
            padding:18px;
            margin-bottom:18px;
        }

        .form-section-title {
            font-size:13px;
            font-weight:800;
            color:#243447;
            text-transform:uppercase;
            letter-spacing:.4px;
            margin-bottom:15px;
            border-bottom:1px solid #e4ebf3;
            padding-bottom:10px;
        }

        .form-group label {
            font-size:12px;
            font-weight:800;
            color:#243447;
            text-transform:uppercase;
            letter-spacing:.3px;
        }

        .form-control {
            border-radius:12px;
            border:1px solid #d8e2ee;
            box-shadow:none;
            min-height:40px;
            font-size:13px;
        }

        .form-control:focus {
            border-color:#4872b8;
            box-shadow:0 0 0 3px rgba(72,114,184,.12);
        }

        textarea.form-control {
            min-height:130px;
            resize:vertical;
            line-height:22px;
            font-size:14px;
            padding:12px;
        }

        #email_url {
            min-height:95px;
        }

        .required-star,
        .error-text {
            color:#e74c3c;
            font-weight:800;
            font-size:12px;
        }

        .helper-note {
            color:#718096;
            font-size:12px;
            margin-top:5px;
        }

        .select2-container {
            width:100% !important;
        }

        .select2-container .select2-choice,
        .select2-container--default .select2-selection--single {
            border-radius:12px !important;
            min-height:40px !important;
            border:1px solid #d8e2ee !important;
        }

        .select2-container-multi .select2-choices {
            border-radius:12px !important;
            border:1px solid #d8e2ee !important;
            min-height:44px !important;
            padding:4px !important;
            background:#fff !important;
        }

        .upload-box {
            border:2px dashed #cbd7e6;
            border-radius:16px;
            padding:16px;
            background:#fff;
            transition:.2s;
        }

        .upload-box:hover {
            border-color:#4872b8;
            background:#f3f8ff;
        }

        .upload-box input {
            border:none;
            padding:0;
            background:transparent;
        }

        .summary-alert {
            border-radius:14px;
            border:1px solid #d8e2ee;
            background:#f8fbff;
            padding:14px 16px;
            color:#4a5568;
            font-size:13px;
            margin-bottom:18px;
        }

        .summary-alert strong {
            color:#243447;
        }

        .btn-submit-delegation {
            background:linear-gradient(135deg, #2ecc71, #179b56);
            color:#fff;
            border:none;
            border-radius:24px;
            padding:12px 28px;
            font-weight:800;
            letter-spacing:.3px;
            box-shadow:0 8px 18px rgba(46,204,113,.25);
        }

        .btn-submit-delegation:hover,
        .btn-submit-delegation:focus {
            color:#fff;
            opacity:.95;
        }

        .btn-reset-delegation {
            background:#eef3f9;
            color:#243447;
            border:1px solid #d8e2ee;
            border-radius:24px;
            padding:12px 22px;
            font-weight:800;
        }

        .form-footer {
            display:flex;
            justify-content:flex-end;
            gap:10px;
            padding-top:10px;
        }

        #pageloader {
            background:rgba(255,255,255,0.85);
            display:none;
            height:100%;
            position:fixed;
            width:100%;
            z-index:9999;
            left:0;
            top:0;
        }

        #pageloader .loader-card {
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%, -50%);
            background:#fff;
            border-radius:18px;
            padding:26px 34px;
            text-align:center;
            box-shadow:0 12px 32px rgba(36,52,71,.22);
            border:1px solid #e4ebf3;
        }

        #pageloader img {
            width:45px;
            margin-bottom:12px;
        }

        #pageloader p {
            margin:0;
            font-weight:800;
            color:#243447;
        }

        .message-area {
            margin-bottom:15px;
        }

        .message-area span,
        .message-area div {
            display:block;
        }

        .field-error {
            border-color:#e74c3c !important;
            box-shadow:0 0 0 3px rgba(231,76,60,.12) !important;
        }

        @media(max-width:768px) {
            .delegate-hero {
                padding:20px;
            }

            .delegate-hero h3 {
                font-size:20px;
            }

            .form-footer {
                display:block;
            }

            .btn-submit-delegation,
            .btn-reset-delegation {
                width:100%;
                margin-bottom:8px;
            }

            .kpi-card {
                min-height:auto;
            }
        }
    </style>
</head>

<body>

<header id="topnav">
    <?php $this->load->view('common/nav-menu');?>
</header>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div class="delegate-hero">
                    <h3><i class="fa fa-share-square-o"></i> Work Delegation Form</h3>
                    <p>Create a clear task assignment with responsible users, due date, email reference, and supporting attachment.</p>

                    <div class="hero-chip-wrap">
                        <span class="hero-chip"><i class="fa fa-users"></i> Multi-user Delegation</span>
                        <span class="hero-chip"><i class="fa fa-calendar"></i> Due Date Tracking</span>
                        <span class="hero-chip"><i class="fa fa-paperclip"></i> Attachment Supported</span>
                        <span class="hero-chip"><i class="fa fa-clock-o"></i> <?php echo date('d-M-Y h:i A'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="message-area">
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
        </div>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-blue">
                    <div class="kpi-icon"><i class="fa fa-user"></i></div>
                    <h3>1</h3>
                    <p>Task Creator</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-green">
                    <div class="kpi-icon"><i class="fa fa-users"></i></div>
                    <h3 id="selected_user_count">0</h3>
                    <p>Selected Users</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-orange">
                    <div class="kpi-icon"><i class="fa fa-calendar"></i></div>
                    <h3 id="selected_due_date"><?php echo date('d-M'); ?></h3>
                    <p>Due Date</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-purple">
                    <div class="kpi-icon"><i class="fa fa-check-circle"></i></div>
                    <h3>Ready</h3>
                    <p>Form Status</p>
                </div>
            </div>
        </div>

        <div class="summary-alert">
            <strong>Tip:</strong> Write the delegated work clearly with expected output, deadline, dependency, and priority. This helps the assigned user update remarks properly and avoids repeated follow-ups.
        </div>

        <div class="main-form-card">
            <div class="section-title"><i class="fa fa-pencil-square-o"></i> Create New Delegated Task</div>

            <form id="loginForm" method="post" action="<?php echo page_url;?>Delegation/save_delegation_task" enctype="multipart/form-data">

                <div id="pageloader">
                    <div class="loader-card">
                        <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                        <p>Please wait, task is being delegated...</p>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title"><i class="fa fa-user"></i> Basic Assignment Detail</div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Your Name <span class="required-star">*</span></label>
                                <span id="error_your_name" class="error-text"></span>

                                <select class="form-control" id="your_name" name="your_name">
                                    <?php
                                    $user_id = $this->session->userdata['logged_in']['user_id'];

                                    $query = $this->db
                                        ->select('a.assigned_to, b.user_id, b.title, b.first_name, b.last_name')
                                        ->from('delegation_master a')
                                        ->join('system_users b', 'a.assigned_to=b.user_id', 'left')
                                        ->where('b.user_id', $user_id)
                                        ->get();

                                    foreach($query->result() as $users) { ?>
                                        <option value="<?php echo $users->assigned_to;?>">
                                            <?php echo strtoupper($users->title." ".$users->first_name." ".$users->last_name);?>
                                        </option>
                                    <?php } ?>

                                    <?php if($user_id == 114) { ?>
                                        <option value="61">Virendra Sharma</option>
                                    <?php } ?>
                                </select>

                                <div class="helper-note">Task will be delegated from this user.</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Select Company <span class="required-star">*</span></label>
                                <span id="error_business_loc" class="error-text"></span>

                                <select class="form-control" id="business_loc" name="business_loc">
                                    <?php
                                    $this->db
                                        ->select('a.company_name, a.business_loc_id, a.state_id, a.city_id, b.state_id, b.state_name, c.city_id, c.city_name')
                                        ->from('business_location a')
                                        ->join('states b', 'a.state_id=b.state_id', 'left')
                                        ->join('cities c', 'a.city_id=c.city_id', 'left')
                                        ->where('business_loc_status', '1')
                                        ->where('business_loc_id', 2);

                                    $this->db->order_by('a.company_name', 'asc');
                                    $query = $this->db->get();
                                    $res = $query->result();

                                    foreach($res as $row) { ?>
                                        <option value="<?php echo $row->business_loc_id;?>">
                                            <?php echo strtoupper($row->company_name);?>(<?php echo strtoupper($row->state_name);?>, <?php echo strtoupper($row->city_name);?>)
                                        </option>
                                    <?php } ?>
                                </select>

                                <div class="helper-note">Company / location for this task.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Delegated To <span class="required-star">*</span></label>
                                <span id="error_delegate_to" class="error-text"></span>

                                <select class="form-control select3" id="delegate_to" name="delegate_to[]" multiple>
                                    <?php
                                    $this->db
                                        ->select('u.user_id, u.first_name, u.last_name, d.department')
                                        ->from('system_users u')
                                        ->join('departments d', 'u.department_id = d.department_id', 'left')
                                        ->where('u.user_status', 1);

                                    $users = $this->db->get()->result();

                                    foreach($users as $row) { ?>
                                        <option value="<?php echo $row->user_id; ?>">
                                            <?php echo strtoupper($row->first_name.' '.$row->last_name); ?>
                                            (<?php echo strtoupper($row->department); ?>)
                                        </option>
                                    <?php } ?>
                                </select>

                                <div class="helper-note">You can select multiple users for the same delegated task.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title"><i class="fa fa-tasks"></i> Task Information</div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Work Delegated <span class="required-star">*</span></label>
                                <span id="error_task" class="error-text"></span>

                                <textarea class="form-control" name="task" id="task" placeholder="Write task details clearly. Example: Please check pending vendor payment list and update final status with remarks by today evening." required></textarea>

                                <div class="helper-note">
                                    Mention expected output, task objective, dependency, and priority clearly.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Email URL / Reference</label>

                                <textarea name="email_url" id="email_url" class="form-control" placeholder="Paste email URL, reference link, or supporting URL if any."></textarea>

                                <div class="helper-note">
                                    Use this field for email thread link or any reference URL related to the task.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title"><i class="fa fa-calendar-check-o"></i> Due Date & Attachment</div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Upload Image / Attachment</label>

                                <div class="upload-box">
                                    <input type="file" class="form-control" name="image" id="image" value="">
                                    <div class="helper-note">Upload screenshot, document, or task reference file if available.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Due Date <span class="required-star">*</span></label>
                                <span id="error_datepicker1" class="error-text"></span>

                                <input type="date" id="work_completion_date" name="work_completion_date" class="form-control" autocomplete="off" value="<?php echo date('Y-m-d');?>" required>

                                <div class="helper-note">Select the expected task completion date.</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Quick Status</label>

                                <div class="summary-alert" style="margin-bottom:0; min-height:74px;">
                                    <strong id="form_readiness_text">Ready to Submit</strong><br>
                                    <span id="form_readiness_subtext">Please fill required fields.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button type="button" class="btn btn-reset-delegation" onclick="resetDelegationForm()">
                        <i class="fa fa-refresh"></i> Reset
                    </button>

                    <button type="submit" id="submitBtn" class="btn btn-submit-delegation">
                        <i class="fa fa-paper-plane"></i> Submit Delegation
                    </button>
                </div>

            </form>
        </div>

        <?php $this->load->view('common/footer');?>

    </div>
</div>

<script src="<?php echo assets_url;?>js/jquery.min.js"></script>
<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/detect.js"></script>
<script src="<?php echo assets_url;?>js/fastclick.js"></script>
<script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url;?>js/waves.js"></script>
<script src="<?php echo assets_url;?>js/wow.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

<script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({});
        $('.select3').select2({
            placeholder: "Select delegated users",
            allowClear: true
        });
        $('.select4').select2({});

        updateSelectedUserCount();
        updateDueDateKPI();
        updateFormReadiness();

        $("#delegate_to").on("change", function() {
            $("#error_delegate_to").html("");
            $("#delegate_to").removeClass("field-error");
            updateSelectedUserCount();
            updateFormReadiness();
        });

        $("#work_completion_date").on("change", function() {
            $("#error_datepicker1").html("");
            $("#work_completion_date").removeClass("field-error");
            updateDueDateKPI();
            updateFormReadiness();
        });

        $("#your_name, #business_loc").on("change", function() {
            $(this).removeClass("field-error");
            $("#error_" + $(this).attr("id")).html("");
            updateFormReadiness();
        });

        $("#task").on("keyup", function() {
            $("#error_task").html("");
            $("#task").removeClass("field-error");
            updateFormReadiness();
        });

        $("#loginForm").on("submit", function(e) {
            var isValid = true;

            var your_name = $("#your_name").val();
            var business_loc = $("#business_loc").val();
            var delegate_to = $("#delegate_to").val();
            var task = $("#task").val();
            var work_completion_date = $("#work_completion_date").val();

            clearErrors();

            if (your_name == '') {
                $("#error_your_name").html(" Required!");
                $("#your_name").addClass("field-error");
                isValid = false;
            }

            if (business_loc == '') {
                $("#error_business_loc").html(" Required!");
                $("#business_loc").addClass("field-error");
                isValid = false;
            }

            if (!delegate_to || delegate_to.length == 0) {
                $("#error_delegate_to").html(" Required!");
                $("#delegate_to").addClass("field-error");
                isValid = false;
            }

            if ($.trim(task) == '') {
                $("#error_task").html(" Required!");
                $("#task").addClass("field-error");
                isValid = false;
            }

            if (work_completion_date == '') {
                $("#error_datepicker1").html(" Required!");
                $("#work_completion_date").addClass("field-error");
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                $("#form_readiness_text").text("Missing Required Fields");
                $("#form_readiness_subtext").text("Please complete all required fields before submitting.");
                return false;
            }

            $("#pageloader").fadeIn();
            $("#submitBtn").prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
        });
    });

    function clearErrors() {
        $("#error_your_name").html("");
        $("#error_business_loc").html("");
        $("#error_delegate_to").html("");
        $("#error_task").html("");
        $("#error_datepicker1").html("");

        $("#your_name").removeClass("field-error");
        $("#business_loc").removeClass("field-error");
        $("#delegate_to").removeClass("field-error");
        $("#task").removeClass("field-error");
        $("#work_completion_date").removeClass("field-error");
    }

    function updateSelectedUserCount() {
        var selectedUsers = $("#delegate_to").val();

        if (!selectedUsers) {
            $("#selected_user_count").text(0);
        } else {
            $("#selected_user_count").text(selectedUsers.length);
        }
    }

    function updateDueDateKPI() {
        var dueDate = $("#work_completion_date").val();

        if (dueDate) {
            var parts = dueDate.split("-");
            if (parts.length === 3) {
                $("#selected_due_date").text(parts[2] + "-" + getMonthShort(parts[1]));
            }
        } else {
            $("#selected_due_date").text("--");
        }
    }

    function getMonthShort(monthNumber) {
        var months = {
            "01": "Jan",
            "02": "Feb",
            "03": "Mar",
            "04": "Apr",
            "05": "May",
            "06": "Jun",
            "07": "Jul",
            "08": "Aug",
            "09": "Sep",
            "10": "Oct",
            "11": "Nov",
            "12": "Dec"
        };

        return months[monthNumber] || monthNumber;
    }

    function updateFormReadiness() {
        var your_name = $("#your_name").val();
        var business_loc = $("#business_loc").val();
        var delegate_to = $("#delegate_to").val();
        var task = $("#task").val();
        var work_completion_date = $("#work_completion_date").val();

        if (
            your_name != '' &&
            business_loc != '' &&
            delegate_to &&
            delegate_to.length > 0 &&
            $.trim(task) != '' &&
            work_completion_date != ''
        ) {
            $("#form_readiness_text").text("Ready to Submit");
            $("#form_readiness_subtext").text("All required fields are filled.");
        } else {
            $("#form_readiness_text").text("Draft Mode");
            $("#form_readiness_subtext").text("Please complete all required fields.");
        }
    }

    function resetDelegationForm() {
        $("#loginForm")[0].reset();
        $("#delegate_to").val(null).trigger("change");
        $("#work_completion_date").val("<?php echo date('Y-m-d');?>");

        clearErrors();
        updateSelectedUserCount();
        updateDueDateKPI();
        updateFormReadiness();

        $("#submitBtn").prop("disabled", false).html('<i class="fa fa-paper-plane"></i> Submit Delegation');
    }
</script>

</body>
</html>