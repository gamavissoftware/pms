<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="NJ Media">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Task Delegated To You</title>

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

    <style>
        body {
            background:#f4f7fb;
        }

        .page-title-box {
            padding-bottom:10px;
        }

        .dashboard-heading {
            font-weight:700;
            color:#243447;
            letter-spacing:.3px;
            margin-bottom:5px;
        }

        .dashboard-subtitle {
            text-align:center;
            color:#7a8797;
            font-size:13px;
            margin-bottom:15px;
        }

        .kpi-card {
            background:#fff;
            border-radius:14px;
            padding:18px 16px;
            min-height:118px;
            box-shadow:0 6px 20px rgba(28,39,60,0.08);
            border:1px solid #e8edf3;
            cursor:pointer;
            transition:all .22s ease;
            position:relative;
            overflow:hidden;
            margin-bottom:18px;
        }

        .kpi-card:hover {
            transform:translateY(-4px);
            box-shadow:0 10px 26px rgba(28,39,60,0.14);
        }

        .kpi-card.active-kpi {
            border:2px solid #243447;
            box-shadow:0 10px 26px rgba(28,39,60,0.18);
        }

        .kpi-card:before {
            content:"";
            position:absolute;
            top:0;
            left:0;
            width:6px;
            height:100%;
        }

        .kpi-blue:before { background:#4872b8; }
        .kpi-orange:before { background:#f39c12; }
        .kpi-red:before { background:#e74c3c; }
        .kpi-green:before { background:#2ecc71; }
        .kpi-purple:before { background:#8e44ad; }
        .kpi-dark:before { background:#34495e; }

        .kpi-card h3 {
            margin:0;
            font-size:30px;
            font-weight:800;
            color:#243447;
        }

        .kpi-card p {
            margin:7px 0 0;
            font-size:12px;
            font-weight:700;
            color:#6b7785;
            text-transform:uppercase;
            letter-spacing:.5px;
        }

        .kpi-icon {
            position:absolute;
            right:14px;
            top:14px;
            width:38px;
            height:38px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#f3f6fa;
            color:#243447;
            font-size:17px;
        }

        .info-panel {
            background:#fff;
            border-radius:14px;
            padding:16px;
            box-shadow:0 6px 20px rgba(28,39,60,0.08);
            border:1px solid #e8edf3;
            margin-bottom:18px;
        }

        .section-title {
            font-size:15px;
            font-weight:800;
            color:#243447;
            margin-bottom:12px;
            text-transform:uppercase;
            letter-spacing:.4px;
        }

        .quick-filter-btn {
            margin-right:6px;
            margin-bottom:8px;
            border-radius:20px;
            padding:6px 13px;
            font-weight:600;
        }

        .search-box {
            height:38px;
            border-radius:20px;
            border:1px solid #d8e0ea;
            padding-left:16px;
        }

        table.manglesh thead th {
            background:#243447;
            color:#fff;
            font-weight:bold;
            font-size:11px;
            vertical-align:middle !important;
            white-space:nowrap;
            border:1px solid #172331 !important;
            text-align:center;
        }

        #example {
            border-collapse:collapse !important;
            border:2px solid #243447 !important;
            width:100% !important;
            background:#fff;
        }

        #example tbody td {
            border:1px solid #c9d3df !important;
            vertical-align:top !important;
            font-size:12px;
            line-height:20px;
            color:#2d3748;
        }

        #example tbody tr:hover td {
            background:#f8fbff !important;
        }

        #example tbody td:nth-child(5) {
            min-width:360px;
            white-space:normal !important;
            word-break:break-word;
        }

        #example tbody td:nth-child(13) {
            min-width:320px;
            max-width:460px;
            white-space:normal !important;
            word-break:break-word;
            background:#fffdf5;
        }

        .task-title {
            font-weight:700;
            color:#243447;
            font-size:13px;
            line-height:22px;
        }

        .task-meta {
            margin-top:8px;
            display:flex;
            gap:8px;
            flex-wrap:wrap;
            align-items:center;
            font-size:11px;
            color:#6b7785;
        }

        .priority-badge,
        .status-badge,
        .due-badge {
            display:inline-block;
            padding:5px 9px;
            border-radius:20px;
            font-size:10px;
            font-weight:800;
            letter-spacing:.4px;
            text-transform:uppercase;
        }

        .priority-high {
            background:#ffe6e6;
            color:#d63031;
            border:1px solid #fab1a0;
        }

        .priority-medium {
            background:#fff4dd;
            color:#d35400;
            border:1px solid #f5c16c;
        }

        .priority-low {
            background:#eafaf1;
            color:#18864b;
            border:1px solid #9be7bd;
        }

        .priority-normal {
            background:#eef2f7;
            color:#34495e;
            border:1px solid #d8e0ea;
        }

        .status-done {
            background:#eafaf1;
            color:#18864b;
            border:1px solid #9be7bd;
        }

        .status-pending {
            background:#ffe6e6;
            color:#d63031;
            border:1px solid #fab1a0;
        }

        .due-overdue {
            background:#d63031;
            color:#fff;
        }

        .due-today {
            background:#f39c12;
            color:#fff;
        }

        .due-upcoming {
            background:#4872b8;
            color:#fff;
        }

        .due-normal {
            background:#636e72;
            color:#fff;
        }

        .remarks-preview {
            background:#fff8e8;
            border:1px solid #f3d28b;
            border-radius:10px;
            padding:10px;
            min-height:60px;
            font-size:12px;
            line-height:21px;
            color:#3d3d3d;
        }

        .remark-action-btn {
            border-radius:18px;
            padding:6px 12px;
            font-weight:700;
            width:auto !important;
            white-space:nowrap;
        }

        .attach-btn {
            border-radius:16px;
            margin-bottom:4px;
        }

        .task-overdue td {
            background:#fff0f0 !important;
        }

        .task-due-today td {
            background:#fff9e6 !important;
        }

        .task-done-response td {
            background:#f1fff7 !important;
        }

        .modal-lg {
            width:850px;
            max-width:95%;
        }

        #remarks {
            min-height:170px;
            resize:vertical;
            font-size:14px;
            line-height:22px;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom:10px;
        }

        .dataTables_filter {
            display:none;
        }

        @media(max-width:768px) {
            .kpi-card {
                min-height:auto;
            }

            .dashboard-heading {
                font-size:18px;
            }

            #example tbody td:nth-child(5),
            #example tbody td:nth-child(13) {
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
                <div class="page-title-box">
                    <h4 class="page-title text-center dashboard-heading">TASK DELEGATED TO YOU</h4>
                    <div class="dashboard-subtitle">
                        Track your assigned tasks, due dates, priority and latest remarks in one place.
                    </div>
                </div>
            </div>
        </div>

        <?php echo $this->session->flashdata('message'); ?>

        <!-- KPI SECTION -->
        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="kpi-card kpi-blue active-kpi" onclick="applyDashboardFilter('all', this)">
                    <div class="kpi-icon"><i class="fa fa-list"></i></div>
                    <h3 id="kpi_total_open">0</h3>
                    <p>Total Open</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="kpi-card kpi-orange" onclick="applyDashboardFilter('today', this)">
                    <div class="kpi-icon"><i class="fa fa-calendar"></i></div>
                    <h3 id="kpi_due_today">0</h3>
                    <p>Due Today</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="kpi-card kpi-red" onclick="applyDashboardFilter('overdue', this)">
                    <div class="kpi-icon"><i class="fa fa-warning"></i></div>
                    <h3 id="kpi_overdue">0</h3>
                    <p>Overdue</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="kpi-card kpi-purple" onclick="applyDashboardFilter('high_priority', this)">
                    <div class="kpi-icon"><i class="fa fa-bolt"></i></div>
                    <h3 id="kpi_high_priority">0</h3>
                    <p>High Priority</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="kpi-card kpi-dark" onclick="applyDashboardFilter('pending_response', this)">
                    <div class="kpi-icon"><i class="fa fa-comments"></i></div>
                    <h3 id="kpi_pending_response">0</h3>
                    <p>Pending Response</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="kpi-card kpi-green" onclick="applyDashboardFilter('responded', this)">
                    <div class="kpi-icon"><i class="fa fa-check"></i></div>
                    <h3 id="kpi_responded">0</h3>
                    <p>Responded</p>
                </div>
            </div>
        </div>

        <!-- FILTER + SEARCH SECTION -->
        <div class="row">
            <div class="col-md-8">
                <div class="info-panel">
                    <div class="section-title">Quick Filters</div>

                    <button type="button" class="btn btn-default btn-xs quick-filter-btn" onclick="applyDashboardFilter('all')">All Tasks</button>
                    <button type="button" class="btn btn-warning btn-xs quick-filter-btn" onclick="applyDashboardFilter('today')">Due Today</button>
                    <button type="button" class="btn btn-danger btn-xs quick-filter-btn" onclick="applyDashboardFilter('overdue')">Overdue</button>
                    <button type="button" class="btn btn-primary btn-xs quick-filter-btn" onclick="applyDashboardFilter('upcoming')">Upcoming</button>
                    <button type="button" class="btn btn-info btn-xs quick-filter-btn" onclick="applyDashboardFilter('high_priority')">High Priority</button>
                    <button type="button" class="btn btn-success btn-xs quick-filter-btn" onclick="reloadDashboard()">Refresh</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-panel">
                    <div class="section-title">Search Your Task</div>
                    <input type="text" id="customSearchBox" class="form-control search-box" placeholder="Search task, case no, remarks or delegated by...">
                </div>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box table-responsive">
                    <div class="section-title">Your Live Task Dashboard</div>

                    <table id="example" class="table table-striped table-bordered manglesh">
                        <thead>
                        <tr>
                            <th>SR NO.</th>
                            <th>TIMESTAMP</th>
                            <th>DELEGATED BY</th>
                            <th>CASE NO</th>
                            <th>WORK DELEGATED</th>
                            <th>PRIORITY</th>
                            <th>ATTACHMENT</th>
                            <th>WORK DUE DATE</th>
                            <th>2ND DATE</th>
                            <th>3RD DATE</th>
                            <th>ACTION</th>
                            <th>STATUS</th>
                            <th>YOUR LATEST REMARKS</th>
                        </tr>
                        </thead>

                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php $this->load->view('common/footer');?>

    </div>
</div>

<!-- Existing Modal Kept Safe -->
<div id="reject_so" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <form method="post" action="<?php echo page_url; ?>/Delegation/responebyuser/" enctype="multipart/form-data">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="modal_title1">PENDING DELEGATE TASK REMARK</h4>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Status</label><br>
                                <select id="status" name="status" class="form-control">
                                    <option value="">---Select---</option>
                                    <option value="1">Done</option>
                                    <option value="2">Pending</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Remarks</label><br>
                                <textarea id="remarks" name="remarks" class="form-control" placeholder="Write your detailed task update here..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Attachment</label><br>
                                <input type="file" id="workproof" name="workproof" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" name="hidden_id1" id="hidden_id1" value="">
                    <input type="submit" id="save" class="btn btn-info" value="Submit">
                </div>

            </div>
        </div>
    </form>
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
    var taskTable = null;
    var activeDashboardFilter = 'all';

    $(document).ready(function() {
        $('.select2').select2({});
        $('.select3').select2({});
        $('.select4').select2({});

        loadUserDashboardKPIs();

        taskTable = $('#example').DataTable({
            processing: false,
            fixedHeader: true,
            pagination: true,
            pageLength: 50,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            ajax: {
                url: "<?php echo page_url;?>Delegation/task_delegated_to_you/",
                dataSrc: "aaData"
            },
            columns: [
                { data: 'sr_no' },
                { data: 'timestamp' },
                { data: 'delegated_to' },
                { data: 'caseno' },
                { data: 'task' },
                { data: 'priority' },
                { data: 'attachment' },
                { data: 'delegated_date' },
                { data: 'second_date' },
                { data: 'third_date' },
                { data: 'remarkdone' },
                { data: 'response' },
                { data: 'remarks' }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Task Delegated To You'
                },
                {
                    extend: 'print',
                    text: 'Print'
                }
            ],
            order: [[1, 'desc']],
            createdRow: function(row, data, dataIndex) {
                applyRowColor(row, data);
            }
        });

        $('#customSearchBox').on('keyup', function() {
            taskTable.search(this.value).draw();
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'example') {
                return true;
            }

            if (activeDashboardFilter === 'all') {
                return true;
            }

            var rowData = taskTable.row(dataIndex).data();

            if (!rowData) {
                return true;
            }

            if (activeDashboardFilter === 'today') {
                return parseInt(rowData.due_today_filter || 0) === 1;
            }

            if (activeDashboardFilter === 'overdue') {
                return parseInt(rowData.overdue_filter || 0) === 1;
            }

            if (activeDashboardFilter === 'upcoming') {
                return parseInt(rowData.upcoming_filter || 0) === 1;
            }

            if (activeDashboardFilter === 'high_priority') {
                return parseInt(rowData.urgency_raw || 0) === 1;
            }

            if (activeDashboardFilter === 'pending_response') {
                return parseInt(rowData.pending_response_filter || 0) === 1;
            }

            if (activeDashboardFilter === 'responded') {
                return parseInt(rowData.response_filter || 0) === 1;
            }

            return true;
        });
    });

    function applyDashboardFilter(type, element) {
        activeDashboardFilter = type;

        $('.kpi-card').removeClass('active-kpi');

        if (element) {
            $(element).addClass('active-kpi');
        }

        if (taskTable) {
            taskTable.draw();
        }
    }

    function reloadDashboard() {
        activeDashboardFilter = 'all';

        $('#customSearchBox').val('');
        $('.kpi-card').removeClass('active-kpi');
        $('.kpi-card').first().addClass('active-kpi');

        if (taskTable) {
            taskTable.search('').draw();
            taskTable.ajax.reload(null, false);
        }

        loadUserDashboardKPIs();
    }

    function loadUserDashboardKPIs() {
        $.ajax({
            type: "GET",
            url: "<?php echo page_url;?>Delegation/task_delegated_to_you_summary/",
            dataType: "json",
            success: function(res) {
                $('#kpi_total_open').text(res.total_open || 0);
                $('#kpi_due_today').text(res.due_today || 0);
                $('#kpi_overdue').text(res.overdue || 0);
                $('#kpi_high_priority').text(res.high_priority || 0);
                $('#kpi_pending_response').text(res.pending_response || 0);
                $('#kpi_responded').text(res.responded || 0);
            }
        });
    }

    function applyRowColor(row, data) {
        if (parseInt(data.overdue_filter || 0) === 1) {
            $(row).addClass('task-overdue');
        } else if (parseInt(data.due_today_filter || 0) === 1) {
            $(row).addClass('task-due-today');
        } else if (parseInt(data.done_response_filter || 0) === 1) {
            $(row).addClass('task-done-response');
        }
    }

    function showModalreject(id) {
        $("#reject_so").modal('show');
        $("#modal_title1").text('Delegation ID Number: ' + id);
        $("#hidden_id1").val(id);
    }
</script>

</body>
</html>