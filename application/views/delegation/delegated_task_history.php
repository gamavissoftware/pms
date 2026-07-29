<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Delegated Task History</title>

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
            font-weight:800;
            color:#fff;
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
            max-width:520px;
            white-space:normal !important;
            word-break:break-word;
            font-weight:600;
            color:#243447;
        }

        table.manglesh tbody td:nth-child(10) {
            min-width:340px;
            max-width:520px;
            white-space:normal !important;
            word-break:break-word;
            background:#fffdf5;
            line-height:21px;
        }

        .history-status-chip {
            display:inline-block;
            padding:5px 10px;
            border-radius:20px;
            font-size:10px;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.4px;
        }

        .chip-completed {
            background:#eafaf1;
            color:#18864b;
            border:1px solid #9be7bd;
        }

        .chip-remark {
            background:#fff4dd;
            color:#d35400;
            border:1px solid #f5c16c;
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

        .history-row td {
            background:#ffffff !important;
        }

        .history-row-with-remarks td {
            background:#f7fff9 !important;
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
                    <h3><i class="fa fa-history"></i> Task Delegated History</h3>
                    <p>Review completed, closed, and historical delegated tasks with remarks, responsible users, and completion trail.</p>

                    <div class="hero-chip-wrap">
                        <span class="hero-chip"><i class="fa fa-archive"></i> History Archive</span>
                        <span class="hero-chip"><i class="fa fa-clock-o"></i> Last Refreshed: <?php echo date('d-M-Y h:i A'); ?></span>
                        <span class="hero-chip"><i class="fa fa-filter"></i> Search + Export Enabled</span>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $this->session->flashdata('message'); ?>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-blue active-kpi" onclick="applyHistoryFilter('all', this)">
                    <div class="kpi-icon"><i class="fa fa-list"></i></div>
                    <h3 id="kpi_total_history">0</h3>
                    <p>Total History</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-green" onclick="applyHistoryFilter('with_remarks', this)">
                    <div class="kpi-icon"><i class="fa fa-comments"></i></div>
                    <h3 id="kpi_with_remarks">0</h3>
                    <p>With Remarks</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-orange" onclick="applyHistoryFilter('today_closed', this)">
                    <div class="kpi-icon"><i class="fa fa-calendar-check-o"></i></div>
                    <h3 id="kpi_today_closed">0</h3>
                    <p>Today Entries</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card kpi-purple" onclick="applyHistoryFilter('this_month', this)">
                    <div class="kpi-icon"><i class="fa fa-bar-chart"></i></div>
                    <h3 id="kpi_this_month">0</h3>
                    <p>This Month</p>
                </div>
            </div>
        </div>

        <div class="summary-alert">
            <strong>Note:</strong> This history section is designed for management review. Use search, filters, Excel export, and print to verify completed delegated work with user remarks.
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="modern-card">
                    <div class="section-title"><i class="fa fa-filter"></i> Smart History Filters</div>

                    <button type="button" class="btn btn-default btn-xs quick-filter-btn" onclick="applyHistoryFilter('all')">
                        All History
                    </button>

                    <button type="button" class="btn btn-success btn-xs quick-filter-btn" onclick="applyHistoryFilter('with_remarks')">
                        With Remarks
                    </button>

                    <button type="button" class="btn btn-warning btn-xs quick-filter-btn" onclick="applyHistoryFilter('today_closed')">
                        Today Entries
                    </button>

                    <button type="button" class="btn btn-primary btn-xs quick-filter-btn" onclick="applyHistoryFilter('this_month')">
                        This Month
                    </button>

                    <button type="button" class="btn btn-info btn-xs quick-filter-btn" onclick="reloadHistoryDashboard()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="modern-card">
                    <div class="section-title"><i class="fa fa-search"></i> Search History</div>
                    <input type="text" id="historySearchBox" class="form-control search-box" placeholder="Search by task, case no, user or remarks...">
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="section-title"><i class="fa fa-table"></i> Delegated Task History Records</div>

            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered manglesh">
                    <thead>
                    <tr>
                        <th>SR NO.</th>
                        <th>TIMESTAMP</th>
                        <th>ASSIGNED BY</th>
                        <th>ASSIGNED TO</th>
                        <th>CASE NO</th>
                        <th>WORK DELEGATED</th>
                        <th>WORK PRIORITY DATE</th>
                        <th>2ND DATE</th>
                        <th>3RD DATE</th>
                        <th>USER REMARKS</th>
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

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>
    var historyTable = null;
    var activeHistoryFilter = 'all';

    $(document).ready(function() {
        $('.select2').select2({});
        $('.select3').select2({});
        $('.select4').select2({});

        historyTable = $('#example').DataTable({
            processing: false,
            fixedHeader: true,
            pagination: true,
            pageLength: 50,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            ajax: {
                url: "<?php echo page_url;?>Delegation/delegated_task_history_list/<?php echo $this->uri->segment(3);?>",
                dataSrc: "aaData"
            },
            columns: [
                { data: 'sr_no' },
                { data: 'timestamp' },
                { data: 'delegatedby' },
                { data: 'delegated_to' },
                { data: 'caseno' },
                { data: 'task' },
                { data: 'delegated_date' },
                { data: 'second_date' },
                { data: 'third_date' },
                { data: 'response' }
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                    title: 'Delegated Task History'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print'
                }
            ],
            order: [[1, 'desc']],
            createdRow: function(row, data, dataIndex) {
                decorateHistoryRow(row, data);
            },
            initComplete: function(settings, json) {
                updateHistoryKPIs(json);
            },
            drawCallback: function(settings) {
                updateVisibleHistoryCount();
            }
        });

        $('#historySearchBox').on('keyup', function() {
            historyTable.search(this.value).draw();
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'example') {
                return true;
            }

            if (activeHistoryFilter === 'all') {
                return true;
            }

            var rowData = historyTable.row(dataIndex).data();

            if (!rowData) {
                return true;
            }

            if (activeHistoryFilter === 'with_remarks') {
                return cleanText(rowData.response) !== '';
            }

            if (activeHistoryFilter === 'today_closed') {
                return isTodayDate(cleanText(rowData.timestamp));
            }

            if (activeHistoryFilter === 'this_month') {
                return isCurrentMonth(cleanText(rowData.timestamp));
            }

            return true;
        });
    });

    function applyHistoryFilter(type, element) {
        activeHistoryFilter = type;

        $('.kpi-card').removeClass('active-kpi');

        if (element) {
            $(element).addClass('active-kpi');
        }

        if (historyTable) {
            historyTable.draw();
        }
    }

    function reloadHistoryDashboard() {
        activeHistoryFilter = 'all';

        $('#historySearchBox').val('');
        $('.kpi-card').removeClass('active-kpi');
        $('.kpi-card').first().addClass('active-kpi');

        if (historyTable) {
            historyTable.search('').draw();
            historyTable.ajax.reload(function(json) {
                updateHistoryKPIs(json);
            }, false);
        }
    }

    function decorateHistoryRow(row, data) {
        var responseText = cleanText(data.response);

        if (responseText !== '') {
            $(row).addClass('history-row-with-remarks');
        } else {
            $(row).addClass('history-row');
        }
    }

    function updateHistoryKPIs(json) {
        if (!json || !json.aaData) {
            $('#kpi_total_history').text(0);
            $('#kpi_with_remarks').text(0);
            $('#kpi_today_closed').text(0);
            $('#kpi_this_month').text(0);
            return;
        }

        var total = json.aaData.length;
        var withRemarks = 0;
        var todayEntries = 0;
        var thisMonth = 0;

        $.each(json.aaData, function(index, item) {
            if (cleanText(item.response) !== '') {
                withRemarks++;
            }

            if (isTodayDate(cleanText(item.timestamp))) {
                todayEntries++;
            }

            if (isCurrentMonth(cleanText(item.timestamp))) {
                thisMonth++;
            }
        });

        $('#kpi_total_history').text(total);
        $('#kpi_with_remarks').text(withRemarks);
        $('#kpi_today_closed').text(todayEntries);
        $('#kpi_this_month').text(thisMonth);
    }

    function updateVisibleHistoryCount() {
        if (!historyTable) {
            return;
        }

        var visibleCount = historyTable.rows({ filter: 'applied' }).count();

        if (activeHistoryFilter === 'all') {
            $('#kpi_total_history').text(visibleCount);
        }
    }

    function cleanText(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return $('<div>').html(value).text().replace(/\s+/g, ' ').trim();
    }

    function parseHistoryDate(value) {
        value = cleanText(value);

        if (value === '') {
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

    function isTodayDate(value) {
        var date = parseHistoryDate(value);

        if (!date) {
            return false;
        }

        var today = new Date();

        return (
            date.getFullYear() === today.getFullYear() &&
            date.getMonth() === today.getMonth() &&
            date.getDate() === today.getDate()
        );
    }

    function isCurrentMonth(value) {
        var date = parseHistoryDate(value);

        if (!date) {
            return false;
        }

        var today = new Date();

        return (
            date.getFullYear() === today.getFullYear() &&
            date.getMonth() === today.getMonth()
        );
    }
</script>

</body>
</html>