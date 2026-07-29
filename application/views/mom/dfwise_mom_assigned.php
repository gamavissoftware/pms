<?php
$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> DF Wise MOM Assigned To You</title>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        body {
            background: #f3f6fb;
        }
        h3{
            color:#fff !important;
        }
        .report-hero {
            background: linear-gradient(135deg, <?php echo $themeColor;?> 0%, #111827 100%);
            border-radius: 18px;
            padding: 26px;
            margin-bottom: 22px;
            color: #fff;
            box-shadow: 0 12px 35px rgba(0,0,0,0.13);
            position: relative;
            overflow: hidden;
        }

        .report-hero:before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            right: -90px;
            top: -100px;
        }

        .report-title {
            font-size: 27px;
            font-weight: 900;
            margin: 0;
            letter-spacing: .2px;
        }

        .report-subtitle {
            margin-top: 8px;
            opacity: .9;
            font-size: 14px;
        }

        .report-pill {
            display: inline-block;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            padding: 8px 14px;
            border-radius: 30px;
            font-weight: 700;
            margin-top: 12px;
        }

        .kpi-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.07);
            min-height: 118px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card:after {
            content: "";
            position: absolute;
            right: -25px;
            bottom: -25px;
            width: 92px;
            height: 92px;
            border-radius: 50%;
            background: rgba(72,114,184,0.08);
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: <?php echo $themeColor;?>;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            right: 16px;
            top: 16px;
            font-size: 19px;
        }

        .kpi-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .kpi-value {
            font-size: 30px;
            font-weight: 900;
            color: #111827;
            margin-top: 9px;
            line-height: 1.1;
        }

        .kpi-hint {
            color: #8a94a6;
            font-size: 12px;
            margin-top: 7px;
        }

        .filter-panel {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 24px rgba(31,41,55,0.05);
        }

        .filter-title {
            font-size: 15px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 12px;
        }

        .modern-table-card {
            background: #fff;
            border-radius: 18px;
            padding: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 10px 28px rgba(31,41,55,0.07);
        }

        table.manglesh thead th {
            background: <?php echo $themeColor;?> !important;
            color: #fff !important;
            font-weight: 800;
            text-align: center;
            vertical-align: middle !important;
            white-space: nowrap;
            font-size: 12px;
        }

        table.manglesh tbody td {
            text-align: center;
            vertical-align: middle !important;
            font-size: 12px;
            color: #374151;
        }

        .agenda-box {
            text-align: left;
            min-width: 260px;
            max-width: 420px;
            white-space: normal;
            line-height: 1.5;
            font-weight: 700;
            color: #111827;
        }

        .participant-box {
            text-align: left;
            min-width: 220px;
            max-width: 360px;
            white-space: normal;
            line-height: 1.5;
        }

        .task-box {
            text-align: left;
            min-width: 240px;
            max-width: 420px;
            white-space: normal;
            line-height: 1.5;
            font-weight: 700;
            color: #374151;
        }

        .particular-task-box {
            min-width: 520px;
            max-width: 760px;
            text-align: left;
            white-space: normal;
            overflow-x: auto;
            padding: 2px;
        }

        .particular-task-box table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 0 !important;
            background: #fff;
            font-size: 12px;
        }

        .particular-task-box table th {
            background: #111827 !important;
            color: #fff !important;
            padding: 7px 8px !important;
            text-align: center !important;
            border: 1px solid #d1d5db !important;
            font-weight: 800 !important;
            white-space: nowrap;
            vertical-align: middle !important;
        }

        .particular-task-box table td {
            padding: 7px 8px !important;
            border: 1px solid #e5e7eb !important;
            color: #374151 !important;
            vertical-align: top !important;
            text-align: left !important;
            line-height: 1.5;
            white-space: normal !important;
        }

        .particular-task-box table tr:nth-child(even) {
            background: #f9fafb;
        }

        .particular-task-box .btn,
        .particular-task-box button {
            border-radius: 16px;
            font-size: 11px;
            padding: 4px 8px;
            font-weight: 800;
        }

        .created-by-box {
            font-weight: 800;
            color: #111827;
            white-space: nowrap;
        }

        .date-stack {
            min-width: 90px;
            font-weight: 800;
        }

        .status-pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .pill-success {
            background: #ecfdf3;
            color: #15803d;
        }

        .pill-warning {
            background: #fff7ed;
            color: #c2410c;
        }

        .pill-danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .pill-info {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .dataTables_wrapper .dt-buttons .btn {
            border-radius: 20px !important;
            margin-right: 5px;
            font-weight: 800;
        }

        .dataTables_filter input,
        .dataTables_length select,
        .filter-panel input,
        .filter-panel select {
            border-radius: 20px;
            border: 1px solid #d8dee9;
            padding: 6px 12px;
        }

        .ajax-error-box {
            display: none;
            padding: 12px;
            border-radius: 12px;
            background: #fff7ed;
            color: #c2410c;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .mom-summary-strip {
            background: #f8fafc;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 14px;
            color: #374151;
            font-weight: 700;
        }

        @media(max-width: 767px) {
            .report-title {
                font-size: 22px;
            }

            .kpi-value {
                font-size: 25px;
            }

            .report-hero {
                padding: 20px;
            }

            .particular-task-box {
                min-width: 420px;
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

        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <div class="report-hero">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="report-title">DF Wise MOM Assigned To You</h3>
                            <div class="report-subtitle">
                                Review MOM points assigned to you with meeting date, agenda, participants, task details and creator information.
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-tasks"></i> Your assigned MOM action dashboard
                            </div>
                        </div>

                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Visible Assigned MOM</div>
                            <div id="visibleMomRecords" style="font-size:28px; font-weight:900; margin-top:5px;">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-info" style="border-radius:12px;">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-list"></i></div>
                    <div class="kpi-label">Assigned MOM</div>
                    <div class="kpi-value" id="kpiTotalMom">0</div>
                    <div class="kpi-hint">Total visible assigned MOM points</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-calendar"></i></div>
                    <div class="kpi-label">Today</div>
                    <div class="kpi-value" id="kpiTodayMom">0</div>
                    <div class="kpi-hint">MOM records dated today</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-users"></i></div>
                    <div class="kpi-label">Participants</div>
                    <div class="kpi-value" id="kpiParticipants">0</div>
                    <div class="kpi-hint">Approx. visible participants count</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-user"></i></div>
                    <div class="kpi-label">Created By</div>
                    <div class="kpi-value" id="kpiCreators">0</div>
                    <div class="kpi-hint">Unique creators in visible records</div>
                </div>
            </div>
        </div>

        <div class="filter-panel">
            <div class="filter-title">
                <i class="fa fa-filter"></i> Smart Filters
            </div>

            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <label>Search</label>
                    <input type="text" id="customSearch" class="form-control" placeholder="Search agenda, participant, task...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Created By</label>
                    <select id="createdByFilter" class="form-control">
                        <option value="">All Created By</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Meeting Date</label>
                    <select id="dateFilter" class="form-control">
                        <option value="">All Dates</option>
                        <option value="Today">Today</option>
                        <option value="Past">Past Dates</option>
                        <option value="Future">Future Dates</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>&nbsp;</label>
                    <button type="button" id="resetFilters" class="btn btn-default btn-block" style="border-radius:20px; font-weight:800;">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="modern-table-card table-responsive">
            <div class="mom-summary-strip">
                <i class="fa fa-info-circle"></i>
                This page shows MOM points assigned to you. Use filters to quickly check task details by creator and meeting date.
            </div>

            <div id="momAjaxError" class="ajax-error-box"></div>

            <table id="example" class="table table-striped table-bordered manglesh" width="100%">
                <thead>
                    <tr>
                        <th>SR No.</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Agenda of Meeting</th>
                        <th>Participants</th>
                        <th>Particular Task</th>
                        <th>Created By</th>
                        <th>Created On</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
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

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script>
function stripHtml(html) {
    if (html === null || html === undefined) {
        return '';
    }

    var div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
}

function renderParticularTask(data) {
    if (data === null || data === undefined || data === '') {
        return '<div class="particular-task-box">-</div>';
    }

    var rawData = String(data);

    /*
     * Important:
     * Do not strip HTML here.
     * Controller may already send an HTML table in particulardata.
     */
    if (rawData.toLowerCase().indexOf('<table') !== -1) {
        return '<div class="particular-task-box">' + rawData + '</div>';
    }

    /*
     * Fallback for plain text data.
     */
    rawData = rawData
        .replace(/SR NO\./gi, '<strong>SR NO.</strong>')
        .replace(/PARTICULAR/gi, '<strong>PARTICULAR</strong>')
        .replace(/DUE DATE/gi, '<strong>DUE DATE</strong>')
        .replace(/Update Status/gi, '<br><strong>Update Status</strong>')
        .replace(/Completion Time/gi, '<br><strong>Completion Time</strong>')
        .replace(/UPDATE REMARKS/gi, '<br><strong>UPDATE REMARKS</strong>');

    return '<div class="particular-task-box" style="line-height:1.7;">' + rawData + '</div>';
}

function normalizeDateForCompare(value) {
    var text = stripHtml(value).trim();

    if (text === '' || text === '-') {
        return '';
    }

    text = text.replace(/\//g, '-');

    var parts = text.split('-');

    if (parts.length !== 3) {
        return '';
    }

    if (parts[0].length === 4) {
        return parts[0] + '-' + parts[1] + '-' + parts[2];
    }

    return parts[2] + '-' + parts[1] + '-' + parts[0];
}

function getTodayYmd() {
    var d = new Date();
    var month = String(d.getMonth() + 1).padStart(2, '0');
    var day = String(d.getDate()).padStart(2, '0');

    return d.getFullYear() + '-' + month + '-' + day;
}

function countParticipants(value) {
    var text = stripHtml(value).trim();

    if (text === '' || text === '-') {
        return 0;
    }

    var commaParts = text.split(',');
    if (commaParts.length > 1) {
        var count = 0;

        for (var i = 0; i < commaParts.length; i++) {
            if ($.trim(commaParts[i]) !== '') {
                count++;
            }
        }

        return count;
    }

    var lineParts = text.split(/\n+/);
    var lineCount = 0;

    for (var j = 0; j < lineParts.length; j++) {
        if ($.trim(lineParts[j]) !== '') {
            lineCount++;
        }
    }

    return lineCount > 0 ? lineCount : 1;
}

$(document).ready(function () {

    var table = $('#example').DataTable({
        processing: true,
        serverSide: false,
        fixedHeader: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 250, 500, -1], [25, 50, 100, 250, 500, 'All']],
        responsive: false,
        scrollX: true,
        ajax: {
            url: "<?php echo page_url;?>Mom/dfwise_mom_assigned_to_you_data/",
            type: "GET",
            cache: false,
            dataType: "json",
            dataSrc: function (json) {
                $('#momAjaxError').hide().html('');

                if (json && json.data) {
                    return json.data;
                }

                if (json && json.aaData) {
                    return json.aaData;
                }

                return [];
            },
            error: function (xhr, status, error) {
                console.log('MOM Assigned AJAX Error:', error);
                console.log('Response:', xhr.responseText);

                $('#momAjaxError').show().html(
                    '<i class="fa fa-warning"></i> MOM assigned report data is not loading. Please open this URL directly and check JSON response:<br>' +
                    '<strong><?php echo page_url;?>Mom/dfwise_mom_assigned_to_you_data/</strong>'
                );
            }
        },
        columns: [
            {
                data: 'sr_no',
                defaultContent: ''
            },
            {
                data: 'date',
                defaultContent: '',
                render: function(data) {
                    return '<div class="date-stack">' + (data || '-') + '</div>';
                }
            },
            {
                data: 'time',
                defaultContent: '',
                render: function(data) {
                    return '<span class="status-pill pill-info">' + (data || '-') + '</span>';
                }
            },
            {
                data: 'agenda',
                defaultContent: '',
                render: function(data) {
                    var text = stripHtml(data).trim();

                    if (text === '') {
                        text = '-';
                    }

                    return '<div class="agenda-box">' + text + '</div>';
                }
            },
            {
                data: 'participant',
                defaultContent: '',
                render: function(data) {
                    var text = stripHtml(data).trim();

                    if (text === '') {
                        text = '-';
                    }

                    return '<div class="participant-box">' + text + '</div>';
                }
            },
            {
                data: 'particulardata',
                defaultContent: '',
                render: function(data) {
                    return renderParticularTask(data);
                }
            },
            {
                data: 'employee_name',
                defaultContent: '',
                render: function(data) {
                    var text = stripHtml(data).trim();

                    if (text === '') {
                        text = '-';
                    }

                    return '<div class="created-by-box">' + text + '</div>';
                }
            },
            {
                data: 'added_time',
                defaultContent: '',
                render: function(data) {
                    return '<div class="date-stack">' + (data || '-') + '</div>';
                }
            }
        ],
        columnDefs: [
            {
                targets: [3, 4, 5],
                orderable: false
            },
            {
                targets: 5,
                width: "560px"
            }
        ],
        order: [[1, 'desc']],
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'DF Wise MOM Assigned To You',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            return stripHtml(data);
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'DF Wise MOM Assigned To You',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            return stripHtml(data);
                        }
                    }
                }
            }
        ],
        initComplete: function () {
            var api = this.api();
            populateCreatedByFilter(api);
            updateKpis(api);
        },
        drawCallback: function () {
            var api = this.api();
            updateKpis(api);
        }
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'example') {
            return true;
        }

        var row = table.row(dataIndex).data();

        if (!row) {
            return true;
        }

        var createdByFilter = $('#createdByFilter').val();
        var dateFilter = $('#dateFilter').val();

        var createdBy = stripHtml(row.employee_name).trim();
        var rowDate = normalizeDateForCompare(row.date);
        var today = getTodayYmd();

        if (createdByFilter !== '' && createdBy !== createdByFilter) {
            return false;
        }

        if (dateFilter !== '') {
            if (rowDate === '') {
                return false;
            }

            if (dateFilter === 'Today' && rowDate !== today) {
                return false;
            }

            if (dateFilter === 'Past' && rowDate >= today) {
                return false;
            }

            if (dateFilter === 'Future' && rowDate <= today) {
                return false;
            }
        }

        return true;
    });

    $('#customSearch').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#createdByFilter, #dateFilter').on('change', function () {
        table.draw();
    });

    $('#resetFilters').on('click', function () {
        $('#customSearch').val('');
        $('#createdByFilter').val('');
        $('#dateFilter').val('');

        table.search('');
        table.columns().search('');
        table.draw();
    });

    function populateCreatedByFilter(api) {
        var creators = {};

        $('#createdByFilter').find('option:not(:first)').remove();

        api.rows().every(function () {
            var row = this.data();
            var creator = stripHtml(row.employee_name).trim();

            if (creator !== '' && creator !== '-') {
                creators[creator] = true;
            }
        });

        Object.keys(creators).sort().forEach(function(value) {
            $('#createdByFilter').append('<option value="' + value + '">' + value + '</option>');
        });
    }

    function updateKpis(api) {
        var visibleRows = api.rows({ search: 'applied' }).data();

        var totalMom = visibleRows.length;
        var todayMom = 0;
        var participantCount = 0;
        var creators = {};
        var today = getTodayYmd();

        for (var i = 0; i < visibleRows.length; i++) {
            var row = visibleRows[i];

            if (normalizeDateForCompare(row.date) === today) {
                todayMom++;
            }

            participantCount += countParticipants(row.participant);

            var creator = stripHtml(row.employee_name).trim();

            if (creator !== '' && creator !== '-') {
                creators[creator] = true;
            }
        }

        $('#visibleMomRecords').text(totalMom);
        $('#kpiTotalMom').text(totalMom);
        $('#kpiTodayMom').text(todayMom);
        $('#kpiParticipants').text(participantCount);
        $('#kpiCreators').text(Object.keys(creators).length);
    }

});
</script>

</body>
</html>