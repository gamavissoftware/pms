<?php 
    $CI =& get_instance();
    $CI->load->model('Salescrm_model');
    $getAllUsers = $CI->Salescrm_model->getAllUsers();
    $current_user_id = (int) $this->session->userdata['logged_in']['user_id'];
    $current_user_role = (int) $this->session->userdata['logged_in']['role'];
    $is_delegation_history_admin = ($current_user_role === 1);

    if (!$is_delegation_history_admin && !empty($getAllUsers)) {
        $getAllUsers = array_values(array_filter($getAllUsers, function($row) use ($current_user_id) {
            return (int) $row->user_id === $current_user_id;
        }));
    }

    if($this->uri->segment(3) != 'NA' && $this->uri->segment(3) != '' && $this->uri->segment(4) != 'NA' && $this->uri->segment(4) != '') {
        $start_date = date('d-m-Y', strtotime($this->uri->segment(3)));
        $end_date = date('d-m-Y', strtotime($this->uri->segment(4)));
    } else {
        $start_date = '';
        $end_date = '';
    }

    if($this->uri->segment(5) != '') {
        $user = $this->uri->segment(5);
    } else {
        $user = '';
    }

    if(!$is_delegation_history_admin) {
        $user = $current_user_id;
    }

    if($this->uri->segment(6) != '') {
        $task_status = $this->uri->segment(6);
    } else {
        $task_status = '';
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Delegation History</title>

    <script src="<?php echo assets_url;?>js/angular.min.js"></script>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
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

        .history-hero {
            background:linear-gradient(135deg, #243447 0%, #4872b8 100%);
            color:#fff;
            border-radius:18px;
            padding:24px 26px;
            margin-bottom:22px;
            box-shadow:0 12px 32px rgba(36,52,71,.22);
            position:relative;
            overflow:hidden;
        }

        .history-hero:after {
            content:"";
            position:absolute;
            right:-80px;
            top:-90px;
            width:230px;
            height:230px;
            background:rgba(255,255,255,.12);
            border-radius:50%;
        }

        .history-hero h3 {
            margin:0;
            color:#fff;
            font-weight:800;
            letter-spacing:.4px;
        }

        .history-hero p {
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
            cursor:pointer;
            transition:all .22s ease;
        }

        .kpi-card:hover {
            transform:translateY(-4px);
            box-shadow:0 12px 28px rgba(36,52,71,.14);
        }

        .kpi-card.active-kpi {
            border:2px solid #243447;
            box-shadow:0 12px 28px rgba(36,52,71,.18);
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
        .kpi-red:before { background:#e74c3c; }

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

        .filter-panel {
            background:#fff;
            border-radius:16px;
            padding:20px;
            box-shadow:0 8px 24px rgba(36,52,71,.08);
            border:1px solid #e4ebf3;
            margin-bottom:22px;
        }

        .filter-panel label {
            font-size:12px;
            font-weight:800;
            color:#243447;
            text-transform:uppercase;
            letter-spacing:.3px;
        }

        .filter-panel .form-control {
            height:38px;
            border-radius:10px;
            border:1px solid #d8e2ee;
            box-shadow:none;
            font-size:13px;
        }

        .filter-panel .form-control:focus {
            border-color:#4872b8;
            box-shadow:0 0 0 3px rgba(72,114,184,.12);
        }

        .btn-filter {
            background:linear-gradient(135deg, #2ecc71, #179b56);
            color:#fff;
            border:none;
            border-radius:20px;
            padding:9px 18px;
            font-weight:800;
            margin-top:23px;
            width:100%;
        }

        .btn-filter:hover,
        .btn-filter:focus {
            color:#fff;
            opacity:.94;
        }

        .btn-reset {
            background:#eef3f9;
            color:#243447;
            border:1px solid #d8e2ee;
            border-radius:20px;
            padding:9px 18px;
            font-weight:800;
            margin-top:23px;
            width:100%;
        }

        .quick-filter-btn {
            margin-right:7px;
            margin-bottom:8px;
            border-radius:20px;
            padding:7px 14px;
            font-weight:700;
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

        table.manglesh tbody td:nth-child(6) {
            min-width:380px;
            max-width:560px;
            white-space:normal !important;
            word-break:break-word;
            font-weight:600;
            color:#243447;
        }

        table.manglesh tbody td:nth-child(10) {
            min-width:320px;
            max-width:520px;
            white-space:normal !important;
            word-break:break-word;
            background:#fffdf5;
            line-height:21px;
        }

        .row-done td {
            background:#f1fff7 !important;
        }

        .row-pending td {
            background:#fff8e8 !important;
        }

        .status-pill {
            display:inline-block;
            padding:5px 10px;
            border-radius:20px;
            font-size:10px;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.4px;
        }

        .status-done {
            background:#eafaf1;
            color:#18864b;
            border:1px solid #9be7bd;
        }

        .status-pending {
            background:#fff4dd;
            color:#d35400;
            border:1px solid #f5c16c;
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

        .select2-container .select2-choice,
        .select2-container--default .select2-selection--single {
            border-radius:10px !important;
            height:38px !important;
            border:1px solid #d8e2ee !important;
        }

        .backgroundcolor {
            background-color:#fff3cd !important;
            color:#000;
        }

        @media(max-width:768px) {
            .history-hero {
                padding:20px;
            }

            .history-hero h3 {
                font-size:20px;
            }

            .kpi-card {
                min-height:auto;
            }

            table.manglesh tbody td:nth-child(6),
            table.manglesh tbody td:nth-child(10) {
                min-width:260px;
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
                <div class="history-hero">
                    <h3><i class="fa fa-bar-chart"></i> Delegation History Report</h3>
                    <p><?php echo $is_delegation_history_admin ? 'Filter, review, export, and verify delegated work history with user status, remarks, task ownership, and due dates.' : 'Review your own assigned delegation history with remarks, status trail, and due dates.'; ?></p>

                    <div class="hero-chip-wrap">
                        <span class="hero-chip"><i class="fa fa-filter"></i> Advanced Filters Enabled</span>
                        <span class="hero-chip"><i class="fa fa-file-excel-o"></i> Excel Export Ready</span>
                        <span class="hero-chip"><i class="fa fa-clock-o"></i> Last Refreshed: <?php echo date('d-M-Y h:i A'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $this->session->flashdata('message'); ?>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-blue active-kpi" onclick="applyDelegationHistoryFilter('all', this)">
                    <div class="kpi-icon"><i class="fa fa-list"></i></div>
                    <h3 id="kpi_total">0</h3>
                    <p>Total Records</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-green" onclick="applyDelegationHistoryFilter('done', this)">
                    <div class="kpi-icon"><i class="fa fa-check-circle"></i></div>
                    <h3 id="kpi_done">0</h3>
                    <p>Done Tasks</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-orange" onclick="applyDelegationHistoryFilter('pending', this)">
                    <div class="kpi-icon"><i class="fa fa-clock-o"></i></div>
                    <h3 id="kpi_pending">0</h3>
                    <p>Pending Tasks</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-red" onclick="applyDelegationHistoryFilter('with_remarks', this)">
                    <div class="kpi-icon"><i class="fa fa-comments"></i></div>
                    <h3 id="kpi_remarks">0</h3>
                    <p>With Remarks</p>
                </div>
            </div>
        </div>

        <div class="filter-panel">
            <div class="section-title"><i class="fa fa-sliders"></i> Report Filters</div>

            <form method="post" action="<?php echo page_url;?>Delegation/filter_history">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="text" name="start_date" id="start_date" class="form-control" value="<?php echo $start_date;?>" autocomplete="off" placeholder="DD-MM-YYYY"> 
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>End Date</label> 
                            <input type="text" id="end_date" name="end_date" class="form-control" value="<?php echo $end_date;?>" autocomplete="off" placeholder="DD-MM-YYYY">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Delegated To</label>
                            <select class="form-control select2" name="users" id="users" <?php if(!$is_delegation_history_admin) { echo 'disabled'; } ?>>
                                <?php if($is_delegation_history_admin) { ?>
                                    <option value="All">All Users</option>
                                <?php } ?>
                                <?php if($getAllUsers != '') {
                                    foreach($getAllUsers as $row) { ?>
                                        <option value="<?php echo $row->user_id;?>" <?php if($row->user_id == $user) { echo 'selected';}?>>
                                            <?php echo $row->first_name." ".$row->last_name;?>
                                        </option>
                                <?php } } ?>
                            </select>
                            <?php if(!$is_delegation_history_admin) { ?>
                                <input type="hidden" name="users" value="<?php echo $current_user_id; ?>">
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Task Status</label>
                            <select class="form-control select2" name="task_status" id="task_status">
                                <option value="All">All Status</option>
                                <option value="1" <?php if($task_status == 1) { echo 'selected';}?>>Done</option>
                                <option value="2" <?php if($task_status == 2) { echo 'selected';}?>>Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-filter">
                            <i class="fa fa-filter"></i> Apply Filter
                        </button>
                    </div>

                    <div class="col-md-1">
                        <a href="<?php echo page_url;?>Delegation/delegation_history/NA/NA/All/All" class="btn btn-reset">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="summary-alert">
            <strong><?php echo $is_delegation_history_admin ? 'Management Tip:' : 'User Tip:'; ?></strong>
            <?php echo $is_delegation_history_admin ? 'Use filters for date range, user, and task status. Use KPI cards for instant table-level filtering and Excel export for reporting.' : 'This history is limited to delegation tasks assigned to you. Use date and status filters to review your completed or pending response trail.'; ?>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="modern-card">
                    <div class="section-title"><i class="fa fa-filter"></i> Quick Filters</div>

                    <button type="button" class="btn btn-default btn-xs quick-filter-btn" onclick="applyDelegationHistoryFilter('all')">
                        All Records
                    </button>

                    <button type="button" class="btn btn-success btn-xs quick-filter-btn" onclick="applyDelegationHistoryFilter('done')">
                        Done
                    </button>

                    <button type="button" class="btn btn-warning btn-xs quick-filter-btn" onclick="applyDelegationHistoryFilter('pending')">
                        Pending
                    </button>

                    <button type="button" class="btn btn-danger btn-xs quick-filter-btn" onclick="applyDelegationHistoryFilter('with_remarks')">
                        With Remarks
                    </button>

                    <button type="button" class="btn btn-info btn-xs quick-filter-btn" onclick="reloadDelegationHistory()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="modern-card">
                    <div class="section-title"><i class="fa fa-search"></i> Search Report</div>
                    <input type="text" id="historySearchBox" class="form-control search-box" placeholder="Search task, case no, user, status or remarks...">
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="section-title"><i class="fa fa-table"></i> Delegation History Records</div>

            <div class="table-responsive">
                <table id="example" class="table table-bordered manglesh">
                    <thead>
                    <tr>
                        <th style="width:3%">SR NO.</th>
                        <th style="width:5%">TIMESTAMP</th>
                        <th style="width:5%">DELEGATED BY</th>
                        <th style="width:5%">DELEGATED TO</th>
                        <th style="width:5%">CASE NO</th>
                        <th style="width:40%">WORK DELEGATED</th>
                        <th style="width:5%">ATTACHMENT</th>
                        <th style="width:10%">WORK DUE DATE</th>
                        <th style="width:8%">USER WORK STATUS</th>
                        <th style="width:20%">REMARKS</th>
                    </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
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
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>
    var delegationHistoryTable = null;
    var activeDelegationHistoryFilter = 'all';

    $(document).ready(function() {
        $('.select2').select2({});

        jQuery('#start_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        jQuery('#end_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        delegationHistoryTable = $('#example').DataTable({
            processing: false,
            fixedHeader: true,
            pagination: true,
            pageLength: 100,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            ajax: {
                url: "<?php echo page_url;?>Delegation/delegation_history_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",
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
                { data: 'delegated_date' },
                { data: 'response' },
                { data: 'remarks' }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                    title: 'Delegation History Report'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print'
                }
            ],
            order: [[1, 'desc']],
            createdRow: function(row, data, dataIndex) {
                decorateDelegationHistoryRow(row, data);
            },
            initComplete: function(settings, json) {
                updateDelegationHistoryKPIs(json);
            },
            drawCallback: function(settings) {
                updateVisibleDelegationHistoryCount();
            }
        });

        $('#historySearchBox').on('keyup', function() {
            delegationHistoryTable.search(this.value).draw();
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'example') {
                return true;
            }

            if (activeDelegationHistoryFilter === 'all') {
                return true;
            }

            var rowData = delegationHistoryTable.row(dataIndex).data();

            if (!rowData) {
                return true;
            }

            var responseText = cleanText(rowData.response);
            var remarksText = cleanText(rowData.remarks);

            if (activeDelegationHistoryFilter === 'done') {
                return responseText.toLowerCase().indexOf('done') !== -1 || responseText.toLowerCase().indexOf('completed') !== -1;
            }

            if (activeDelegationHistoryFilter === 'pending') {
                return responseText.toLowerCase().indexOf('pending') !== -1;
            }

            if (activeDelegationHistoryFilter === 'with_remarks') {
                return remarksText !== '';
            }

            return true;
        });
    });

    function applyDelegationHistoryFilter(type, element) {
        activeDelegationHistoryFilter = type;

        $('.kpi-card').removeClass('active-kpi');

        if (element) {
            $(element).addClass('active-kpi');
        }

        if (delegationHistoryTable) {
            delegationHistoryTable.draw();
        }
    }

    function reloadDelegationHistory() {
        activeDelegationHistoryFilter = 'all';

        $('#historySearchBox').val('');
        $('.kpi-card').removeClass('active-kpi');
        $('.kpi-card').first().addClass('active-kpi');

        if (delegationHistoryTable) {
            delegationHistoryTable.search('').draw();
            delegationHistoryTable.ajax.reload(function(json) {
                updateDelegationHistoryKPIs(json);
            }, false);
        }
    }

    function decorateDelegationHistoryRow(row, data) {
        var responseText = cleanText(data.response).toLowerCase();

        if (responseText.indexOf('done') !== -1 || responseText.indexOf('completed') !== -1) {
            $(row).addClass('row-done');
        } else if (responseText.indexOf('pending') !== -1) {
            $(row).addClass('row-pending');
        }
    }

    function updateDelegationHistoryKPIs(json) {
        if (!json || !json.aaData) {
            $('#kpi_total').text(0);
            $('#kpi_done').text(0);
            $('#kpi_pending').text(0);
            $('#kpi_remarks').text(0);
            return;
        }

        var total = json.aaData.length;
        var done = 0;
        var pending = 0;
        var remarks = 0;

        $.each(json.aaData, function(index, item) {
            var responseText = cleanText(item.response).toLowerCase();
            var remarksText = cleanText(item.remarks);

            if (responseText.indexOf('done') !== -1 || responseText.indexOf('completed') !== -1) {
                done++;
            }

            if (responseText.indexOf('pending') !== -1) {
                pending++;
            }

            if (remarksText !== '') {
                remarks++;
            }
        });

        $('#kpi_total').text(total);
        $('#kpi_done').text(done);
        $('#kpi_pending').text(pending);
        $('#kpi_remarks').text(remarks);
    }

    function updateVisibleDelegationHistoryCount() {
        if (!delegationHistoryTable) {
            return;
        }

        var visibleCount = delegationHistoryTable.rows({ filter: 'applied' }).count();

        if (activeDelegationHistoryFilter === 'all') {
            $('#kpi_total').text(visibleCount);
        }
    }

    function cleanText(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return $('<div>').html(value).text().replace(/\s+/g, ' ').trim();
    }

    function getcolor() {
        $("#example tr").each(function(){
            var todaydate = "<?php echo date('d-M-Y');?>";
            var currentRow = $(this);

            var col1_value = currentRow.find("td:eq(6)").text();
            var col2_value = currentRow.find("td:eq(7)").text();
            var col3_value = currentRow.find("td:eq(8)").text();
            var col4_value = currentRow.find("td:eq(9)").text();

            if(col1_value == todaydate || col2_value == todaydate || col3_value == todaydate || col4_value == todaydate) {
                currentRow.find("td:eq(7)").addClass("backgroundcolor");
                currentRow.find("td:eq(8)").addClass("backgroundcolor");
                currentRow.find("td:eq(9)").addClass("backgroundcolor");
            }
        });
    }

    function changestatus(i) {
        var status = $('#markas' + i).val();
        var recordid = $('#recordid' + i).val();

        $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Delegation/update_task_status",
            data:"status=" + status + "&recordid=" + recordid,
            success:function(data) {
                if(data == 'redirect') {
                    window.location.href = "<?php echo page_url;?>Delegation/re_assign_task/" + recordid;
                } else {
                    alert(data);
                }
            }
        });
    }
</script>

</body>
</html>
