<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Delegation Dashboard</title>

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

    if($this->uri->segment(3) == '') {
        $dashboard_user_id = $this->session->userdata['logged_in']['user_id'];
    } else {
        $dashboard_user_id = $this->uri->segment(3);
    }
    ?>

    <style>
        table.manglesh thead th {
            background: <?php echo $LOGO->colorcode;?>;
            color:#fff;
            font-weight:bold;
            font-size:10px;
            vertical-align: middle !important;
            white-space: nowrap;
        }

        table.taskdetail thead th {
            background: #ffeb3b;
            color:#000;
            font-weight:bold;
            font-size:12px;
        }

        .backgroundcolor {
            background-color:#fff3cd !important;
            color:#000;
            font-weight:bold;
        }

        .dashboard-card {
            background:#fff;
            border-radius:10px;
            padding:16px;
            min-height:105px;
            box-shadow:0 2px 12px rgba(0,0,0,0.08);
            border-left:5px solid #4872b8;
            margin-bottom:18px;
        }

        .dashboard-card h3 {
            margin:0;
            font-size:28px;
            font-weight:700;
        }

        .dashboard-card p {
            margin:5px 0 0;
            color:#666;
            font-size:13px;
            text-transform:uppercase;
            letter-spacing:.4px;
        }

        .card-blue { border-left-color:#4872b8; }
        .card-green { border-left-color:#2ecc71; }
        .card-orange { border-left-color:#f39c12; }
        .card-red { border-left-color:#e74c3c; }
        .card-purple { border-left-color:#8e44ad; }
        .card-dark { border-left-color:#34495e; }

        .kpi-filter-card {
            cursor:pointer;
            transition:all 0.20s ease-in-out;
        }

        .kpi-filter-card:hover {
            transform:translateY(-3px);
            box-shadow:0 5px 18px rgba(0,0,0,0.15);
        }

        .active-kpi {
            border:2px solid #222;
            box-shadow:0 5px 18px rgba(0,0,0,0.18);
        }

        .dashboard-section-title {
            font-size:15px;
            font-weight:700;
            margin-bottom:12px;
            color:#333;
            text-transform:uppercase;
        }

        .filter-box {
            background:#fff;
            border-radius:10px;
            padding:15px;
            box-shadow:0 2px 12px rgba(0,0,0,0.07);
            margin-bottom:18px;
        }

        .quick-filter-btn {
            margin-right:6px;
            margin-bottom:6px;
        }

        .task-overdue td {
            background:#ffe6e6 !important;
        }

        .task-due-today td {
            background:#fff8d8 !important;
        }

        .task-normal td {
            background:#ffffff !important;
        }

        .assignee-item {
            padding:8px 0;
            border-bottom:1px solid #eee;
            font-size:13px;
        }

        .assignee-item:last-child {
            border-bottom:none;
        }

        .assignee-count {
            float:right;
            background:#4872b8;
            color:#fff;
            padding:2px 8px;
            border-radius:10px;
            font-size:11px;
        }

        .table-toolbar {
            margin-bottom:10px;
        }

        .table-toolbar input {
            height:34px;
        }

        #example {
            border-collapse:collapse !important;
            border:2px solid #333 !important;
            width:100% !important;
        }

        #example thead th {
            border:1px solid #222 !important;
            vertical-align:middle !important;
            text-align:center;
        }

        #example tbody td {
            border:1px solid #555 !important;
            vertical-align:top !important;
            font-size:11px;
            line-height:18px;
        }

        #example tbody td:nth-child(6) {
            min-width:350px;
            white-space:normal !important;
            word-break:break-word;
        }

        #example tbody td:nth-child(13) {
            min-width:300px;
            max-width:420px;
            white-space:normal !important;
            word-break:break-word;
            background:#fdfdfd;
            font-size:12px;
            line-height:20px;
        }

        .delegation-remarks-box {
            width:100% !important;
            min-height:180px !important;
            resize:vertical;
            font-size:14px;
            line-height:22px;
        }

        .modal-lg {
            width:900px;
            max-width:95%;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom:10px;
        }

        @media(max-width:768px) {
            .dashboard-card {
                min-height:auto;
            }

            .page-title {
                font-size:18px;
            }

            .btn-group.pull-right {
                float:none !important;
                margin-top:10px !important;
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
                    <div class="btn-group pull-right" style="margin-top:30px">
                        <a href="<?php echo page_url;?>Delegation/delegated_task_history">
                            <span class="btn btn-danger btn-xs">View History</span>
                        </a>
                    </div>

                    <h4 class="page-title text-center">YOUR DELEGATION DASHBOARD</h4>
                </div>
            </div>
        </div>

        <?php echo $this->session->flashdata('message'); ?>

        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="dashboard-card card-blue kpi-filter-card active-kpi" onclick="applyQuickFilter('all', this)">
                    <h3 id="total_open">0</h3>
                    <p>Total Open Tasks</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="dashboard-card card-orange kpi-filter-card" onclick="applyQuickFilter('today', this)">
                    <h3 id="due_today">0</h3>
                    <p>Due Today</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="dashboard-card card-red kpi-filter-card" onclick="applyQuickFilter('overdue', this)">
                    <h3 id="overdue">0</h3>
                    <p>Overdue Tasks</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="dashboard-card card-purple kpi-filter-card" onclick="applyQuickFilter('high_priority', this)">
                    <h3 id="high_priority">0</h3>
                    <p>High Priority</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="dashboard-card card-dark kpi-filter-card" onclick="applyQuickFilter('no_response', this)">
                    <h3 id="no_response">0</h3>
                    <p>No Response</p>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="dashboard-card card-green kpi-filter-card" onclick="applyQuickFilter('followup_pending', this)">
                    <h3 id="followup_pending">0</h3>
                    <p>Follow-up Pending</p>
                </div>
            </div>
        </div>

        <!-- RESPONSIBILITY MASTER -->
        <!-- <div class="row">
            <div class="col-sm-12">
                <div class="card-box table-responsive">
                    <div class="dashboard-section-title">Assigned Responsibility Detail</div>

                    <table class="table table-striped table-bordered taskdetail">
                        <thead>
                        <?php
                        $query = $this->db
                            ->select('a.*, c.user_id, c.title, c.first_name, c.last_name')
                            ->from('delegation_master a')
                            ->join('system_users c','a.assigned_to=c.user_id','left')
                            ->where('a.assigned_to',$dashboard_user_id)
                            ->get();

                        if($query->num_rows() > 0) {
                            foreach($query->result() as $row);
                        }
                        ?>

                        <tr>
                            <th>WHO</th>
                            <th>WHAT</th>
                            <th>WHEN</th>
                        </tr>

                        <?php if($query->num_rows() > 0) { ?>
                            <tr>
                                <td>
                                    <?php echo strtoupper($row->title); ?>
                                    <?php echo strtoupper($row->first_name); ?>
                                    <?php echo strtoupper($row->last_name); ?>
                                </td>
                                <td><?php echo $row->what_to_do; ?></td>
                                <td><?php echo $row->when_to_do; ?></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3" class="text-center">No responsibility master found for this user.</td>
                            </tr>
                        <?php } ?>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div> -->

        <!-- FILTERS -->
        <div class="row">
            <div class="col-md-8">
                <div class="filter-box">
                    <div class="dashboard-section-title">Smart Filters</div>

                    <button type="button" class="btn btn-default btn-xs quick-filter-btn" onclick="applyQuickFilter('all')">
                        All Open
                    </button>

                    <button type="button" class="btn btn-warning btn-xs quick-filter-btn" onclick="applyQuickFilter('today')">
                        Due Today
                    </button>

                    <button type="button" class="btn btn-danger btn-xs quick-filter-btn" onclick="applyQuickFilter('overdue')">
                        Overdue
                    </button>

                    <button type="button" class="btn btn-primary btn-xs quick-filter-btn" onclick="applyQuickFilter('high_priority')">
                        High Priority
                    </button>

                    <button type="button" class="btn btn-default btn-xs quick-filter-btn" onclick="applyQuickFilter('no_response')">
                        No Response
                    </button>

                    <button type="button" class="btn btn-success btn-xs quick-filter-btn" onclick="applyQuickFilter('followup_pending')">
                        Follow-up Pending
                    </button>

                    <button type="button" class="btn btn-info btn-xs quick-filter-btn" onclick="applyQuickFilter('response')">
                        With User Response
                    </button>

                    <button type="button" class="btn btn-purple btn-xs quick-filter-btn" onclick="applyQuickFilter('remarks')">
                        With Remarks
                    </button>

                    <button type="button" class="btn btn-success btn-xs quick-filter-btn" onclick="reloadDashboard()">
                        Refresh Dashboard
                    </button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="filter-box">
                    <div class="dashboard-section-title">Top Pending Assignees</div>
                    <div id="top_assignees">
                        <div class="text-muted">Loading...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box table-responsive">

                    <div class="row table-toolbar">
                        <div class="col-md-6">
                            <div class="dashboard-section-title">Live Delegated Task Monitoring</div>
                        </div>

                        <div class="col-md-6">
                            <input type="text" id="customSearchBox" class="form-control" placeholder="Search by task, case no, assigned person, status or remarks...">
                        </div>
                    </div>

                    <table id="example" class="table table-bordered table-striped manglesh">
                        <thead>
                        <tr>
                            <th style="width:3%">SR NO.</th>
                            <th style="width:5%">TIMESTAMP</th>
                            <th style="width:5%">DELEGATED BY</th>
                            <th style="width:5%">DELEGATED TO</th>
                            <th style="width:5%">CASE NO</th>
                            <th style="width:35%">WORK DELEGATED</th>
                            <th style="width:5%">ATTACHMENT</th>
                            <th style="width:7%">WORK DUE DATE</th>
                            <th style="width:5%">2ND DATE</th>
                            <th style="width:5%">3RD DATE</th>
                            <th style="width:7%">STATUS</th>
                            <th style="width:8%">USER WORK STATUS</th>
                            <th style="width:25%">REMARKS</th>
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
    var delegationTable = null;
    var activeQuickFilter = 'all';

    $(document).ready(function() {
        $('.select2').select2({});
        $('.select3').select2({});
        $('.select4').select2({});

        loadDashboardSummary();

        delegationTable = $('#example').DataTable({
            processing: false,
            fixedHeader: true,
            pagination: true,
            pageLength: 100,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            ajax: {
                url: "<?php echo page_url;?>Delegation/delegated_task_list/<?php echo $this->uri->segment(3);?>",
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
                { data: 'second_date' },
                { data: 'third_date' },
                { data: 'status_on_thirddate' },
                { data: 'response' },
                { data: 'remarks' }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    title: 'Delegation Dashboard'
                },
                {
                    extend: 'print',
                    text: 'Print'
                }
            ],
            order: [[1, 'desc']],
            createdRow: function(row, data, dataIndex) {
                applyRowIntelligence(row, data);
            },
            drawCallback: function(settings) {
                getcolor();
            }
        });

        $('#customSearchBox').on('keyup', function() {
            delegationTable.search(this.value).draw();
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'example') {
                return true;
            }

            if (activeQuickFilter === 'all') {
                return true;
            }

            var rowData = delegationTable.row(dataIndex).data();

            if (!rowData) {
                return true;
            }

            if (activeQuickFilter === 'today') {
                return parseInt(rowData.due_today_filter || 0) === 1;
            }

            if (activeQuickFilter === 'overdue') {
                return parseInt(rowData.overdue_filter || 0) === 1;
            }

            if (activeQuickFilter === 'high_priority') {
                return parseInt(rowData.urgency || 0) === 1;
            }

            if (activeQuickFilter === 'no_response') {
                return parseInt(rowData.no_response_filter || 0) === 1;
            }

            if (activeQuickFilter === 'followup_pending') {
                return parseInt(rowData.followup_pending_filter || 0) === 1;
            }

            if (activeQuickFilter === 'response') {
                var response = cleanText(rowData.response);
                return response !== '';
            }

            if (activeQuickFilter === 'remarks') {
                var remarks = cleanText(rowData.remarks);
                return remarks !== '';
            }

            return true;
        });
    });

    function cleanText(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return $('<div>').html(value).text().replace(/\s+/g, ' ').trim();
    }

    function parseDashboardDate(value) {
        value = cleanText(value);

        if (value === '' || value === '0000-00-00') {
            return null;
        }

        var months = {
            'Jan': 0,
            'Feb': 1,
            'Mar': 2,
            'Apr': 3,
            'May': 4,
            'Jun': 5,
            'Jul': 6,
            'Aug': 7,
            'Sep': 8,
            'Oct': 9,
            'Nov': 10,
            'Dec': 11
        };

        var match = value.match(/(\d{1,2})-([A-Za-z]{3})-(\d{4})/);

        if (!match) {
            return null;
        }

        return new Date(parseInt(match[3], 10), months[match[2]], parseInt(match[1], 10));
    }

    function getTodayDateOnly() {
        var now = new Date();
        return new Date(now.getFullYear(), now.getMonth(), now.getDate());
    }

    function isTodayDate(value) {
        var date = parseDashboardDate(value);

        if (!date) {
            return false;
        }

        return date.getTime() === getTodayDateOnly().getTime();
    }

    function isTaskOverdue(firstDate, secondDate, thirdDate) {
        var today = getTodayDateOnly();

        var first = parseDashboardDate(firstDate);
        var second = parseDashboardDate(secondDate);
        var third = parseDashboardDate(thirdDate);

        var compareDate = first;

        if (third) {
            compareDate = third;
        } else if (second) {
            compareDate = second;
        }

        if (!compareDate) {
            return false;
        }

        return compareDate.getTime() < today.getTime();
    }

    function applyRowIntelligence(row, data) {
        var delegatedDate = data.delegated_date;
        var secondDate = data.second_date;
        var thirdDate = data.third_date;

        if (isTaskOverdue(delegatedDate, secondDate, thirdDate)) {
            $(row).addClass('task-overdue');
        } else if (isTodayDate(delegatedDate) || isTodayDate(secondDate) || isTodayDate(thirdDate)) {
            $(row).addClass('task-due-today');
        } else {
            $(row).addClass('task-normal');
        }
    }

    function applyQuickFilter(type, element) {
        activeQuickFilter = type;

        $('.kpi-filter-card').removeClass('active-kpi');
        $('.quick-filter-btn').removeClass('active');

        if (element) {
            $(element).addClass('active-kpi');
        }

        delegationTable.draw();
    }

    function reloadDashboard() {
        activeQuickFilter = 'all';

        $('#customSearchBox').val('');
        $('.kpi-filter-card').removeClass('active-kpi');
        $('.kpi-filter-card').first().addClass('active-kpi');
        $('.quick-filter-btn').removeClass('active');

        delegationTable.search('').draw();
        delegationTable.ajax.reload(null, false);
        loadDashboardSummary();
    }

    function loadDashboardSummary() {
        $.ajax({
            type: "GET",
            url: "<?php echo page_url;?>Delegation/delegated_dashboard_summary/<?php echo $this->uri->segment(3);?>",
            dataType: "json",
            success: function(res) {
                $('#total_open').text(res.total_open || 0);
                $('#due_today').text(res.due_today || 0);
                $('#overdue').text(res.overdue || 0);
                $('#high_priority').text(res.high_priority || 0);
                $('#no_response').text(res.no_response || 0);

                var followupPending = parseInt(res.second_followup_pending || 0) + parseInt(res.third_followup_pending || 0);
                $('#followup_pending').text(followupPending);

                var assigneeHtml = '';

                if (res.top_assignees && res.top_assignees.length > 0) {
                    $.each(res.top_assignees, function(index, item) {
                        assigneeHtml += '<div class="assignee-item">' +
                            item.name +
                            '<span class="assignee-count">' + item.count + '</span>' +
                            '</div>';
                    });
                } else {
                    assigneeHtml = '<div class="text-muted">No pending assignee data found.</div>';
                }

                $('#top_assignees').html(assigneeHtml);
            },
            error: function() {
                $('#top_assignees').html('<div class="text-danger">Unable to load summary.</div>');
            }
        });
    }

    function getcolor() {
        $("#example tr").each(function() {
            var todaydate = "<?php echo date('d-M-Y');?>";
            var currentRow = $(this);

            var col1_value = currentRow.find("td:eq(6)").text();
            var col2_value = currentRow.find("td:eq(7)").text();
            var col3_value = currentRow.find("td:eq(8)").text();
            var col4_value = currentRow.find("td:eq(9)").text();

            if (
                col1_value == todaydate ||
                col2_value == todaydate ||
                col3_value == todaydate ||
                col4_value == todaydate
            ) {
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
            type: "post",
            url: "<?php echo page_url; ?>Delegation/update_task_status",
            data: {
                status: status,
                recordid: recordid
            },
            success: function(data) {
                if (data === 'redirect') {
                    window.location.href = "<?php echo page_url; ?>Delegation/re_assign_task/" + recordid;
                } else if (data === 'refresh') {
                    alert('Thank you! Task Marked as completed.');

                    setTimeout(function() {
                        window.location.reload(true);
                    }, 500);
                } else {
                    alert(data);
                }
            },
            error: function(xhr, status, error) {
                alert("An error occurred while updating the status. Please try again.");
            }
        });
    }
</script>

</body>
</html>