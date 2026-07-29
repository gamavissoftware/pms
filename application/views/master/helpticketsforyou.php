<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Help Tickets Assigned To You</title>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <?php
    $company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    $LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
    $themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

    $uriUserId = $this->uri->segment(3);
    $selectedDfId = $this->uri->segment(4);
    $selectedTaskRecordId = $this->uri->segment(5);
    ?>

    <style>
        body {
            background: #f3f6fb;
        }
        h3{
            color:#fff !important;
        }

        .select2-container {
            width: 100% !important;
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

        table.pretty5 thead th {
            background: <?php echo $themeColor;?> !important;
            color: #fff !important;
            font-size: 12px;
            font-weight: 800;
            text-align: center;
            vertical-align: middle !important;
            white-space: nowrap;
        }

        table.pretty5 tbody td {
            text-align: center;
            vertical-align: middle !important;
            font-size: 12px;
            color: #374151;
        }

        .ticket-no-badge {
            background: #eef4ff;
            color: <?php echo $themeColor;?>;
            border: 1px solid rgba(72,114,184,0.18);
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 900;
            display: inline-block;
            white-space: nowrap;
        }

        .remarks-box {
            max-width: 260px;
            min-width: 180px;
            text-align: left;
            white-space: normal;
            line-height: 1.5;
        }

        .latest-comment-box {
            text-align: left;
            max-width: 280px;
            min-width: 200px;
            white-space: normal;
        }

        .latest-comment-text {
            font-weight: 700;
            color: #111827;
        }

        .latest-comment-meta {
            margin-top: 5px;
            color: #6b7280;
            font-size: 11px;
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

        .btn-action {
            border-radius: 20px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            margin: 2px;
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

        .modal-content {
            border-radius: 18px;
            border: none;
            box-shadow: 0 20px 55px rgba(0,0,0,.18);
        }

        .modal-header {
            background: linear-gradient(135deg, <?php echo $themeColor;?> 0%, #111827 100%);
            color: #fff;
            border-radius: 18px 18px 0 0;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 900;
        }

        #pageloader1 {
            background: rgba(255,255,255,0.8);
            display: none;
            height: 100%;
            position: fixed;
            width: 100%;
            z-index: 9999;
            left: 0;
            top: 0;
            text-align: center;
            padding-top: 18%;
        }

        #pageloader1 img {
            width: 70px;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
            margin: 20px 0;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 30px;
            width: 2px;
            background: #ddd;
        }

        .timeline-item {
            margin-bottom: 20px;
            position: relative;
            padding-left: 60px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background: <?php echo $themeColor;?>;
            border-radius: 50%;
            left: 25px;
            top: 10px;
        }

        .timeline-item .timeline-content {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            position: relative;
        }

        .timeline-item .timeline-content h5 {
            margin: 0 0 5px;
            font-size: 15px;
            font-weight: 800;
        }

        .timeline-item .timeline-content .timestamp {
            font-size: 12px;
            color: #777;
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
                            <h3 class="report-title">Help Tickets Assigned To You</h3>
                            <div class="report-subtitle">
                                Track open DF-wise help tickets, pending comments, delay status and closure actions.
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-ticket"></i> Live open ticket dashboard
                            </div>
                        </div>

                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Visible Tickets</div>
                            <div id="visibleTickets" style="font-size:28px; font-weight:900; margin-top:5px;">0</div>
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
                    <div class="kpi-icon"><i class="fa fa-ticket"></i></div>
                    <div class="kpi-label">Open Tickets</div>
                    <div class="kpi-value" id="kpiTotalTickets">0</div>
                    <div class="kpi-hint">Total visible open tickets</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-clock-o"></i></div>
                    <div class="kpi-label">Delayed</div>
                    <div class="kpi-value" id="kpiDelayedTickets">0</div>
                    <div class="kpi-hint">Tickets older than 1 day</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-exclamation-triangle"></i></div>
                    <div class="kpi-label">Critical</div>
                    <div class="kpi-value" id="kpiCriticalTickets">0</div>
                    <div class="kpi-hint">Tickets older than 3 days</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-comments"></i></div>
                    <div class="kpi-label">With Comments</div>
                    <div class="kpi-value" id="kpiCommentedTickets">0</div>
                    <div class="kpi-hint">Tickets having comment history</div>
                </div>
            </div>
        </div>

        <div class="filter-panel">
            <div class="filter-title">
                <i class="fa fa-filter"></i> Smart Filters
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>Search</label>
                    <input type="text" id="customSearch" class="form-control" placeholder="Search DF, ticket, task, user...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Filter by DF</label>
                    <select class="form-control task_dfno_filter" name="dfidforfilter" id="dfidforfilter">
                        <option value="ALL">ALL</option>
                        <?php
                        $df_q = $this->db->select('id, df_no')->from('df_release')->where('df_status', 0)->order_by('df_no', 'ASC')->get();
                        if ($df_q->num_rows() > 0) {
                            foreach ($df_q->result() as $dfrow) {
                        ?>
                            <option value="<?php echo $dfrow->id; ?>" <?php if ($selectedDfId == $dfrow->id) { echo "selected"; } ?>>
                                <?php echo strtoupper($dfrow->df_no); ?>
                            </option>
                        <?php } } ?>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Delay Status</label>
                    <select id="delayStatusFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Critical">Critical</option>
                        <option value="Delayed">Delayed</option>
                        <option value="Fresh">Fresh</option>
                        <option value="Today">Today</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Department</label>
                    <select id="departmentFilter" class="form-control">
                        <option value="">All Department</option>
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
            <table id="example" class="table pretty5 table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>DF No</th>
                        <th>Ticket No</th>
                        <th>Task</th>
                        <th>Remark</th>
                        <th>Department</th>
                        <th>Assigned To</th>
                        <th>Added On</th>
                        <th>Added By</th>
                        <th>Delay</th>
                        <th>Status</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        </div>

        <div id="showcommentbox" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <form id="updateprogressform" method="post" action="<?php echo page_url;?>Task/addyourcommentonticket" enctype="multipart/form-data">
                <div id="pageloader1">
                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                </div>

                <input type="hidden" name="recordid" id="recordid" value="">

                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff; opacity:1;">×</button>
                            <h4 class="modal-title">Add Your Comment</h4>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label>Write Your Comment</label>
                                <textarea class="form-control" name="yourcomment" id="yourcomment" required rows="5"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" style="border-radius:20px; font-weight:800;">Close</button>
                            <input type="submit" id="depsave" class="btn btn-info" value="Submit" style="border-radius:20px; font-weight:800;">
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <div id="updateprogress" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff; opacity:1;">×</button>
                        <h4 class="modal-title">Communication History</h4>
                    </div>

                    <div class="modal-body">
                        <div id="loadingMessage" style="display: none; color:red;">Please wait...</div>
                        <div class="timeline" id="fetchdynamiccommunication"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" style="border-radius:20px; font-weight:800;">Close</button>
                    </div>

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

<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js"></script>

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

function showcommentbox(id) {
    $("#showcommentbox").modal('show');
    $("#recordid").val(id);
}

function showcommunicationhistorydata(id) {
    $("#updateprogress").modal('show');
    $("#loadingMessage").show();
    $("#fetchdynamiccommunication").html('');

    $.ajax({
        type: "POST",
        url: "<?php echo page_url;?>Task/getallcommunicationoftask",
        data: {
            taskid: id
        },
        success: function(data) {
            $("#fetchdynamiccommunication").html(data);
        },
        complete: function() {
            $("#loadingMessage").hide();
        }
    });
}

$(document).ready(function () {

    $('.task_dfno_filter').select2({
        width: '100%'
    });

    $("#updateprogressform").on("submit", function(){
        $("#pageloader1").fadeIn();
    });

    var table = $('#example').DataTable({
        processing: true,
        serverSide: false,
        fixedHeader: true,
        pageLength: 25,
        responsive: false,
        scrollX: true,
        ajax: {
            url: "<?php echo page_url;?>Task/helpticketsforyoulist/<?php echo $uriUserId; ?>/<?php echo $selectedDfId; ?>/<?php echo $selectedTaskRecordId; ?>",
            type: "GET",
            dataType: "json",
            dataSrc: function(json) {
                if (json && json.data) {
                    return json.data;
                }

                if (json && json.aaData) {
                    return json.aaData;
                }

                return [];
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Unable to load help ticket data. Please check helpticketsforyoulist response.');
            }
        },
        columns: [
            { data: 'sr_no', defaultContent: '' },
            { data: 'dfno', defaultContent: '' },
            { data: 'ticket_no', defaultContent: '' },
            { data: 'taskname', defaultContent: '' },
            { data: 'remarks', defaultContent: '' },
            { data: 'department', defaultContent: '' },
            { data: 'assignedto', defaultContent: '' },
            { data: 'addedon', defaultContent: '' },
            { data: 'addedby', defaultContent: '' },
            { data: 'delay', defaultContent: '' },
            { data: 'closestatus', defaultContent: '' },
            { data: 'comment', defaultContent: '' },
            { data: 'addcomment', defaultContent: '' }
        ],
        columnDefs: [
            {
                targets: [4, 10, 11, 12],
                orderable: false
            }
        ],
        order: [[0, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'Help Tickets Assigned To You',
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
                title: 'Help Tickets Assigned To You',
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
            populateDepartmentFilter(api);
            updateKpis(api);
        },
        drawCallback: function () {
            var api = this.api();
            updateKpis(api);
        }
    });

    $('#customSearch').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#dfidforfilter').on('change', function () {
        var userid = '<?php echo $uriUserId;?>';
        var dfid = $(this).val();

        if (dfid === 'ALL') {
            window.location.href = '<?php echo page_url;?>Task/filterticket/' + userid + '/ALL';
        } else {
            window.location.href = '<?php echo page_url;?>Task/filterticket/' + userid + '/' + dfid;
        }
    });

    $('#delayStatusFilter').on('change', function () {
        var value = this.value;
        if (value !== '') {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'example') {
                    return true;
                }

                var row = table.row(dataIndex).data();
                return row && row.delay_status === value;
            });
        }

        table.draw();

        $.fn.dataTable.ext.search.pop();
    });

    $('#departmentFilter').on('change', function () {
        table.column(5).search(this.value).draw();
    });

    $('#resetFilters').on('click', function () {
        $('#customSearch').val('');
        $('#delayStatusFilter').val('');
        $('#departmentFilter').val('');
        $('#dfidforfilter').val('ALL').trigger('change.select2');

        table.search('');
        table.columns().search('');
        table.draw();
    });

    function populateDepartmentFilter(api) {
        var departments = {};

        $('#departmentFilter').find('option:not(:first)').remove();

        api.rows().every(function () {
            var row = this.data();
            var department = stripHtml(row.department).trim();

            if (department !== '' && department !== '-') {
                departments[department] = true;
            }
        });

        Object.keys(departments).sort().forEach(function(value) {
            $('#departmentFilter').append('<option value="' + value + '">' + value + '</option>');
        });
    }

    function updateKpis(api) {
        var visibleRows = api.rows({ search: 'applied' }).data();

        var totalTickets = visibleRows.length;
        var delayedTickets = 0;
        var criticalTickets = 0;
        var commentedTickets = 0;

        for (var i = 0; i < visibleRows.length; i++) {
            var row = visibleRows[i];

            if (row.delay_status === 'Delayed' || row.delay_status === 'Critical') {
                delayedTickets++;
            }

            if (row.delay_status === 'Critical') {
                criticalTickets++;
            }

            var commentText = stripHtml(row.comment).trim().toLowerCase();
            if (
                commentText !== '' &&
                commentText.indexOf('no comment yet') === -1
            ) {
                commentedTickets++;
            }
        }

        $('#visibleTickets').text(totalTickets);
        $('#kpiTotalTickets').text(totalTickets);
        $('#kpiDelayedTickets').text(delayedTickets);
        $('#kpiCriticalTickets').text(criticalTickets);
        $('#kpiCommentedTickets').text(commentedTickets);
    }

});
</script>

</body>
</html>
