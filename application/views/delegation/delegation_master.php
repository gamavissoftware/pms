<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Delegation Master</title>

    <script src="<?php echo assets_url;?>js/angular.min.js"></script>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>

    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <?php
    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    foreach($q->result() as $LOGO);
    ?>

    <style>
        body {
            background:#eef3f9;
            color:#25364a;
            font-family:'Segoe UI', Arial, sans-serif;
        }

        .master-hero {
            background:linear-gradient(135deg, #243447 0%, #4872b8 100%);
            color:#fff;
            border-radius:18px;
            padding:24px 26px;
            margin-bottom:22px;
            box-shadow:0 12px 32px rgba(36,52,71,.22);
            position:relative;
            overflow:hidden;
        }

        .master-hero:after {
            content:"";
            position:absolute;
            right:-80px;
            top:-90px;
            width:230px;
            height:230px;
            background:rgba(255,255,255,.12);
            border-radius:50%;
        }

        .master-hero h3 {
            margin:0;
            color:#fff;
            font-weight:800;
            letter-spacing:.4px;
        }

        .master-hero p {
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

        .hero-action {
            position:absolute;
            right:26px;
            bottom:24px;
            z-index:2;
        }

        .btn-add-master {
            background:linear-gradient(135deg, #2ecc71, #179b56);
            color:#fff;
            border:none;
            border-radius:24px;
            padding:11px 20px;
            font-weight:800;
            box-shadow:0 8px 20px rgba(46,204,113,.25);
        }

        .btn-add-master:hover,
        .btn-add-master:focus {
            color:#fff;
            opacity:.95;
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
            font-size:29px;
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

        .modern-card {
            background:#fff;
            border-radius:16px;
            padding:20px;
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
            margin-bottom:15px;
            display:flex;
            align-items:center;
            gap:8px;
        }

        .section-title i {
            color:#4872b8;
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

        .search-box {
            height:40px;
            border-radius:22px;
            border:1px solid #d8e2ee;
            padding:0 16px;
            box-shadow:none;
        }

        .search-box:focus {
            border-color:#4872b8;
            box-shadow:0 0 0 3px rgba(72,114,184,.12);
        }

        table.manglesh {
            border-collapse:collapse !important;
            border:2px solid #243447 !important;
            width:100% !important;
            background:#fff;
        }

        table.manglesh thead th {
            background:<?php echo $LOGO->colorcode;?>;
            color:#fff;
            font-weight:800;
            font-size:11px;
            vertical-align:middle !important;
            white-space:nowrap;
            border:1px solid #172331 !important;
            text-align:center;
            padding:10px 8px !important;
        }

        table.manglesh tbody td {
            border:1px solid #c9d3df !important;
            vertical-align:top !important;
            font-size:12px;
            line-height:20px;
            color:#2d3748;
            padding:10px 8px !important;
        }

        table.manglesh tbody tr:hover td {
            background:#f8fbff !important;
        }

        table.manglesh tbody td:nth-child(4) {
            min-width:320px;
            max-width:520px;
            white-space:normal !important;
            word-break:break-word;
            font-weight:600;
            color:#243447;
        }

        table.manglesh tbody td:nth-child(5) {
            min-width:220px;
            white-space:normal !important;
            word-break:break-word;
            background:#fffdf5;
        }

        .dataTables_filter {
            display:none;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom:10px;
        }

        .dt-buttons .btn {
            border-radius:20px;
            font-weight:700;
            margin-right:6px;
        }

        .modal-content {
            border-radius:18px;
            overflow:hidden;
            border:none;
            box-shadow:0 16px 42px rgba(36,52,71,.28);
        }

        .modal-header {
            background:linear-gradient(135deg, #243447 0%, #4872b8 100%);
            color:#fff;
            border-bottom:none;
            padding:18px 22px;
        }

        .modal-header .modal-title {
            color:#fff;
            font-weight:800;
            letter-spacing:.3px;
        }

        .modal-header .close {
            color:#fff;
            opacity:.9;
        }

        .modal-body {
            background:#f8fbff;
            padding:22px;
        }

        .modal-footer {
            background:#fff;
            border-top:1px solid #e4ebf3;
            padding:15px 22px;
        }

        .form-panel {
            background:#fff;
            border-radius:14px;
            border:1px solid #e4ebf3;
            padding:18px;
            margin-bottom:12px;
        }

        .form-panel label {
            font-size:12px;
            font-weight:800;
            color:#243447;
            text-transform:uppercase;
            letter-spacing:.3px;
        }

        .form-panel .form-control {
            height:40px;
            border-radius:10px;
            border:1px solid #d8e2ee;
            box-shadow:none;
            font-size:13px;
        }

        .form-panel .form-control:focus {
            border-color:#4872b8;
            box-shadow:0 0 0 3px rgba(72,114,184,.12);
        }

        .required-text {
            color:#e74c3c;
            font-weight:800;
            font-size:12px;
        }

        .btn-submit-master {
            background:linear-gradient(135deg, #2ecc71, #179b56);
            color:#fff;
            border:none;
            border-radius:22px;
            padding:10px 22px;
            font-weight:800;
        }

        .btn-submit-master:hover,
        .btn-submit-master:focus {
            color:#fff;
            opacity:.95;
        }

        .btn-close-master {
            background:#eef3f9;
            color:#243447;
            border:1px solid #d8e2ee;
            border-radius:22px;
            padding:10px 20px;
            font-weight:800;
        }

        .select2-container .select2-choice,
        .select2-container--default .select2-selection--single {
            border-radius:10px !important;
            height:40px !important;
            border:1px solid #d8e2ee !important;
        }

        .helper-note {
            color:#718096;
            font-size:12px;
            margin-top:5px;
        }

        @media(max-width:768px) {
            .master-hero {
                padding:20px;
            }

            .master-hero h3 {
                font-size:20px;
            }

            .hero-action {
                position:relative;
                right:auto;
                bottom:auto;
                margin-top:18px;
            }

            .btn-add-master {
                width:100%;
            }

            .kpi-card {
                min-height:auto;
            }

            table.manglesh tbody td:nth-child(4),
            table.manglesh tbody td:nth-child(5) {
                min-width:250px;
            }
        }
    </style>
</head>

<body>

<header id="topnav">
    <?php $this->load->view('common/nav-menu');?>
</header>

<?php $this->load->view('common/info-section.php');?>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div class="master-hero">
                    <h3><i class="fa fa-sitemap"></i> Delegation Master Setup</h3>
                    <p>Create and manage delegation rules that define who is responsible, what work they handle, and when it should be performed.</p>

                    <div class="hero-chip-wrap">
                        <span class="hero-chip"><i class="fa fa-cogs"></i> Master Configuration</span>
                        <span class="hero-chip"><i class="fa fa-users"></i> User-wise Responsibility</span>
                        <span class="hero-chip"><i class="fa fa-clock-o"></i> Last Refreshed: <?php echo date('d-M-Y h:i A'); ?></span>
                    </div>

                    <div class="hero-action">
                        <button class="btn btn-add-master waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal">
                            <i class="fa fa-plus-circle"></i> Add Delegation Master
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $this->session->flashdata('message'); ?>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-blue">
                    <div class="kpi-icon"><i class="fa fa-list"></i></div>
                    <h3 id="kpi_total_master">0</h3>
                    <p>Total Master Records</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-green">
                    <div class="kpi-icon"><i class="fa fa-users"></i></div>
                    <h3 id="kpi_assigned_users">0</h3>
                    <p>Assigned Users</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-orange">
                    <div class="kpi-icon"><i class="fa fa-building"></i></div>
                    <h3 id="kpi_locations">0</h3>
                    <p>Business Locations</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-purple">
                    <div class="kpi-icon"><i class="fa fa-video-camera"></i></div>
                    <h3 id="kpi_video_links">0</h3>
                    <p>Video Links</p>
                </div>
            </div>
        </div>

        <div class="summary-alert">
            <strong>Note:</strong> Delegation Master is the foundation of your delegation dashboard. Keep the “What” and “When” fields clear so users can understand their responsibility without confusion.
        </div>

        <div class="modern-card">
            <div class="row">
                <div class="col-md-7">
                    <div class="section-title"><i class="fa fa-table"></i> Delegation Master Records</div>
                </div>

                <div class="col-md-5">
                    <input type="text" id="masterSearchBox" class="form-control search-box" placeholder="Search business location, assigned user, what, when...">
                </div>
            </div>

            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered manglesh">
                    <thead>
                    <tr>
                        <th>SR NO.</th>
                        <th>BUSINESS LOCATION</th>
                        <th>ASSIGNED TO</th>
                        <th>WHAT</th>
                        <th>WHEN</th>
                        <th>VIDEO</th>
                        <th>EDIT</th>
                    </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- ADD MASTER MODAL -->
        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none;">
            <form id="loginForm" method="post" action="<?php echo page_url;?>Delegation/delegation_master">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title"><i class="fa fa-plus-circle"></i> Add Delegation Master</h4>
                        </div>

                        <div class="modal-body">
                            <div class="form-panel">
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Business Location <span class="required-text">*</span></label>
                                            <span id="error_business_loc" class="required-text"></span>

                                            <select class="form-control" id="business_loc" name="business_loc">
                                                <option value="">-- SELECT BUSINESS LOCATION --</option>
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

                                                if($query->num_rows() > 0) {
                                                    $res = $query->result();

                                                    foreach($res as $row) {
                                                ?>
                                                        <option value="<?php echo $row->business_loc_id;?>">
                                                            <?php echo strtoupper($row->company_name);?>
                                                        </option>
                                                <?php } } ?>
                                            </select>

                                            <div class="helper-note">Select location first to load available users.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Who / Assigned To <span class="required-text">*</span></label>
                                            <span id="error_user_id" class="required-text"></span>

                                            <select class="form-control select3" id="user_id" name="user_id">
                                                <option value="">-- SELECT WHO --</option>
                                            </select>

                                            <div class="helper-note">This user will be mapped with the responsibility.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>What <span class="required-text">*</span></label>
                                            <span id="error_what" class="required-text"></span>
                                            <input class="form-control" type="text" name="what" id="what" value="" placeholder="Example: Review pending production tasks">
                                            <div class="helper-note">Write clear responsibility or action area.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>When <span class="required-text">*</span></label>
                                            <span id="error_when" class="required-text"></span>
                                            <input class="form-control" type="text" name="when" id="when" value="" placeholder="Example: Daily by 5 PM / Every Monday">
                                            <div class="helper-note">Define frequency or deadline clearly.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-8" style="display:none;">
                                        <div class="form-group">
                                            <label>Form Video Link</label>
                                            <input class="form-control" type="text" name="form_video_link" id="form_video_link" value="">
                                        </div>
                                    </div>

                                    <div class="col-md-12" style="display:none;">
                                        <div class="form-group">
                                            <label>Dashboard Video Link</label>
                                            <input class="form-control" type="text" name="dashboard_video" id="dashboard_video" value="">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-close-master waves-effect" data-dismiss="modal">
                                <i class="fa fa-times"></i> Close
                            </button>

                            <input type="submit" id="delesave" class="btn btn-submit-master" value="Submit">
                        </div>

                    </div>
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
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>
    var delegationMasterTable = null;

    $(document).ready(function() {
        $('.select2').select2({});
        $('.select3').select2({
            dropdownParent: $('#con-close-modal')
        });
        $('.select4').select2({});

        delegationMasterTable = $('#example').DataTable({
            processing: false,
            pagination: true,
            fixedHeader: true,
            pageLength: 50,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            ajax: {
                url: "<?php echo page_url;?>Delegation/delegation_list/",
                dataSrc: "aaData"
            },
            columns: [
                { data: 'sr_no' },
                { data: 'business_location' },
                { data: 'assigned_to' },
                { data: 'what_to_do' },
                { data: 'when_to_do' },
                { data: 'video' },
                { data: 'edit' }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                    title: 'Delegation Master List'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print'
                }
            ],
            order: [[0, 'asc']],
            initComplete: function(settings, json) {
                updateMasterKPIs(json);
            },
            drawCallback: function(settings) {
                updateVisibleMasterCount();
            }
        });

        $('#masterSearchBox').on('keyup', function() {
            delegationMasterTable.search(this.value).draw();
        });

        $("#delesave").attr('disabled', false);
        $("#delesave").val('Submit');

        $("#loginForm").on("submit", function() {
            if (!validateDelegationMasterForm()) {
                return false;
            }

            $("#delesave").attr('disabled', true);
            $("#delesave").val('Please Wait...');
        });

        $("#business_loc").change(function() {
            var business_loc = $("#business_loc").val();

            $("#error_business_loc").html('');
            $("#user_id").html('<option value="">Loading users...</option>');

            $.ajax({
                type: "post",
                url: "<?php echo page_url;?>Delegation/user_list",
                data: "business_loc=" + business_loc,
                success: function(data) {
                    $("#user_id").html(data);
                    $("#user_id").trigger('change');
                },
                error: function() {
                    $("#user_id").html('<option value="">Unable to load users</option>');
                }
            });

            $.ajax({
                type: "post",
                url: "<?php echo page_url;?>Delegation/user_list",
                data: "business_loc=" + business_loc,
                success: function(data) {
                    $("#reporting_head").html(data);
                }
            });
        });

        $("#user_id").change(function() {
            $("#error_user_id").html('');
        });

        $("#what").keyup(function() {
            $("#error_what").html('');
        });

        $("#when").keyup(function() {
            $("#error_when").html('');
        });
    });

    function updateMasterKPIs(json) {
        if (!json || !json.aaData) {
            $('#kpi_total_master').text(0);
            $('#kpi_assigned_users').text(0);
            $('#kpi_locations').text(0);
            $('#kpi_video_links').text(0);
            return;
        }

        var total = json.aaData.length;
        var users = {};
        var locations = {};
        var videos = 0;

        $.each(json.aaData, function(index, item) {
            var assignedTo = cleanText(item.assigned_to);
            var location = cleanText(item.business_location);
            var video = cleanText(item.video);

            if (assignedTo !== '') {
                users[assignedTo] = true;
            }

            if (location !== '') {
                locations[location] = true;
            }

            if (video !== '') {
                videos++;
            }
        });

        $('#kpi_total_master').text(total);
        $('#kpi_assigned_users').text(Object.keys(users).length);
        $('#kpi_locations').text(Object.keys(locations).length);
        $('#kpi_video_links').text(videos);
    }

    function updateVisibleMasterCount() {
        if (!delegationMasterTable) {
            return;
        }

        $('#kpi_total_master').text(delegationMasterTable.rows({ filter: 'applied' }).count());
    }

    function validateDelegationMasterForm() {
        var business_loc = $("#business_loc").val();
        var user_id = $("#user_id").val();
        var what = $("#what").val();
        var when = $("#when").val();

        var hasError = false;

        $("#error_business_loc").html('');
        $("#error_user_id").html('');
        $("#error_what").html('');
        $("#error_when").html('');

        if (business_loc == '') {
            $("#error_business_loc").html(' Required!');
            hasError = true;
        }

        if (user_id == '') {
            $("#error_user_id").html(' Required!');
            hasError = true;
        }

        if ($.trim(what) == '') {
            $("#error_what").html(' Required!');
            hasError = true;
        }

        if ($.trim(when) == '') {
            $("#error_when").html(' Required!');
            hasError = true;
        }

        if (hasError) {
            return false;
        }

        return true;
    }

    function cleanText(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return $('<div>').html(value).text().replace(/\s+/g, ' ').trim();
    }
</script>

</body>
</html>