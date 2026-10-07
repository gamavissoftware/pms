<?php
$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

$filter1 = $this->uri->segment(3);
$filter2 = $this->uri->segment(4);
$report_title = isset($report_title) ? $report_title : 'Received PO Report';
$received_po_data_action = isset($received_po_data_action) ? $received_po_data_action : 'receivedpodata';
$own_orders_only = !empty($own_orders_only);
$current_year = (int) date('Y');
$current_month = (int) date('n');
$current_financial_year_start = ($current_month >= 4) ? $current_year : ($current_year - 1);
$current_financial_year = $current_financial_year_start . '-' . substr((string) ($current_financial_year_start + 1), -2);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo copyright;?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle . ' ' . html_escape($report_title);?></title>

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
    <link href="<?php echo assets_url;?>css/our.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        body {
            background: #f3f6fb;
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
            letter-spacing: .2px;
            color: #fff !important;
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
            min-height: 120px;
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

        .btn-action {
            border-radius: 20px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            margin: 2px;
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
            text-align: center;
        }

        #pageloader1 {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,.75);
            text-align: center;
            padding-top: 18%;
        }

        #pageloader1 img {
            width: 70px;
        }

        .po-loader-box {
            display: none;
            padding: 12px;
            border-radius: 12px;
            background: #fff7ed;
            color: #c2410c;
            font-weight: 700;
            margin-bottom: 15px;
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

<?php $this->load->view('common/info-section.php');?>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <div class="report-hero">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="report-title"><?php echo html_escape($report_title);?></h3>
                            <div class="report-subtitle">
                                <?php if ($own_orders_only) { ?>Your PO tracking with DF mapping, order value, brand tagging and payment terms. Only orders created by you are included.<?php } else { ?>Complete PO tracking with DF mapping, order value, brand tagging, payment terms and action controls.<?php } ?>
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-file-text-o"></i> Live PO records from system
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Visible Records</div>
                            <div id="visibleRecords" style="font-size:28px; font-weight:900; margin-top:5px;">0</div>
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
                    <div class="kpi-label">Total PO</div>
                    <div class="kpi-value" id="kpiTotalPo">0</div>
                    <div class="kpi-hint">Total records loaded in this report</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-inr"></i></div>
                    <div class="kpi-label">Total Order Value</div>
                    <div class="kpi-value" id="kpiOrderValue" style="font-size:24px;">0</div>
                    <div class="kpi-hint">Calculated from visible records</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-tags"></i></div>
                    <div class="kpi-label">Brand Tagged</div>
                    <div class="kpi-value" id="kpiBrandTagged">0</div>
                    <div class="kpi-hint">Records having brand value</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-ban"></i></div>
                    <div class="kpi-label">Cancelled</div>
                    <div class="kpi-value" id="kpiCancelled">0</div>
                    <div class="kpi-hint">Records marked as cancelled</div>
                </div>
            </div>
        </div>

        <div class="filter-panel">
            <div class="filter-title">
                <i class="fa fa-filter"></i> Smart Search
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>Company / PO / DF Search</label>
                    <input type="text" id="customSearch" class="form-control" placeholder="Search company, PO no, DF no...">
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Financial Year</label>
                    <select id="financialYearFilter" class="form-control">
                        <option value="">All Financial Year</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Marketing Person</label>
                    <select id="marketingPersonFilter" class="form-control">
                        <option value="">All Marketing Person</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Brand</label>
                    <select id="brandFilter" class="form-control">
                        <option value="">All Brand</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>&nbsp;</label>
                    <button type="button" id="resetFilters" class="btn btn-default btn-block" style="border-radius:20px; font-weight:800;">
                        <i class="fa fa-refresh"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>

        <div class="modern-table-card table-responsive">
            <div id="poAjaxError" class="po-loader-box"></div>

            <table id="example" class="table table-striped table-bordered manglesh" width="100%">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Intro Email</th>
                        <th>DF No.</th>
                        <th>Financial Year</th>
                        <th>Company Name</th>
                        <th>PO No.</th>
                        <th>PO Date</th>
                        <th>Order Value</th>
                        <th>Export Value</th>
                        <th>Marketing Person</th>
                        <th>PO Attachment</th>
                        <th>Brand</th>
                        <th>Payment Terms</th>
                        <?php if ($own_orders_only) { ?><th>Spare Quotation</th><?php } ?>
                        <th>Edit PO</th>
                        <th>Cancel Order</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div id="updateprogress1" class="modal fade" role="dialog">
            <form id="updateprogressform" method="post" action="<?php echo page_url;?>Task/brandmapping" enctype="multipart/form-data">
                <div id="pageloader1">
                    <img src="<?php echo assets_url;?>images/loading.gif" alt="processing..." />
                </div>

                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
                            <h4 class="modal-title">Assign Brand</h4>
                            <h5 style="text-align:center; font-weight:bold;" id="currenctlyassigned"></h5>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Select Brand</label>
                                    <input type="hidden" id="poid" name="poid" value="">

                                    <select class="form-control brands" name="tagbrand" id="tagbrand" required>
                                        <option value="">Select Brand</option>
                                        <?php
                                        $brand_q = $this->db->select('id, name')->from('company_brand')->order_by('name','asc')->get();
                                        if ($brand_q->num_rows() > 0) {
                                            foreach ($brand_q->result() as $brand) {
                                        ?>
                                            <option value="<?php echo $brand->id;?>">
                                                <?php echo ucwords(strtolower($brand->name));?>
                                            </option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>

                            <div style="height:30px; clear:both"></div>

                            <div class="row">
                                <div class="col-md-4 col-md-offset-4">
                                    <button type="submit" class="btn btn-success btn-block" style="border-radius:20px; font-weight:800;">
                                        <i class="fa fa-check"></i> Submit
                                    </button>
                                </div>
                            </div>
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

<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

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

function escapeRegexValue(value) {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function parseAmount(value) {
    value = stripHtml(value);
    value = value.replace(/,/g, '');
    value = value.replace(/₹/g, '');
    value = value.replace(/Rs./gi, '');
    value = value.replace(/INR/gi, '');
    value = value.replace(/[^\d.-]/g, '');

    var amount = parseFloat(value);
    return isNaN(amount) ? 0 : amount;
}

function formatIndianAmount(amount) {
    amount = Math.round(amount);
    return amount.toLocaleString('en-IN');
}

function assignbrand(id, currentBrand) {
    $('#poid').val(id);

    if (currentBrand !== undefined && currentBrand !== '') {
        $('#currenctlyassigned').html('Currently Assigned: ' + currentBrand);
    } else {
        $('#currenctlyassigned').html('');
    }

    $('#updateprogress1').modal('show');
}

function confirmCancel(orderId) {
    if (confirm("Are you sure you want to cancel this order?")) {
        window.location.href = "<?php echo page_url; ?>Task/canceledorder/" + orderId;
    }
}

$(document).ready(function () {
    var defaultFinancialYear = <?php echo json_encode($own_orders_only ? $current_financial_year : ''); ?>;

    $('.brands').select2({
        tags: true,
        width: '100%',
        dropdownParent: $('#updateprogress1')
    });

    $('#updateprogressform').on('submit', function () {
        $('#pageloader1').fadeIn();
    });

    var table = $('#example').DataTable({
        processing: true,
        serverSide: false,
        fixedHeader: true,
        pageLength: 100,
        lengthMenu: [[25, 50, 100, 250, 500, 1000, -1], [25, 50, 100, 250, 500, 1000, 'All']],
        responsive: false,
        scrollX: true,
        ajax: {
            url: "<?php echo page_url;?>Task/<?php echo $received_po_data_action;?>/<?php echo $filter1;?>/<?php echo $filter2;?>",
            type: "GET",
            cache: false,
            dataType: "json",
            dataSrc: function (json) {
                $('#poAjaxError').hide().html('');

                if (json && json.data) {
                    return json.data;
                }

                if (json && json.aaData) {
                    return json.aaData;
                }

                return [];
            },
            error: function (xhr, status, error) {
                console.log('AJAX Error:', error);
                console.log('Response:', xhr.responseText);

                $('#poAjaxError').show().html(
                    '<i class="fa fa-warning"></i> PO report data is not loading. Please open this URL directly and check JSON response:<br>' +
                    '<strong><?php echo page_url;?>Task/<?php echo $received_po_data_action;?>/<?php echo $filter1;?>/<?php echo $filter2;?></strong>'
                );
            }
        },
        columns: [
            { data: 'sr_no', defaultContent: '' },
            { data: 'intro_email', defaultContent: '' },
            { data: 'df_no', defaultContent: '' },
            { data: 'financialyear', defaultContent: '' },
            { data: 'company_name', defaultContent: '' },
            { data: 'pono', defaultContent: '' },
            { data: 'podate', defaultContent: '' },
            { data: 'ordervalue', defaultContent: '' },
            { data: 'exportvalue', defaultContent: '' },
            { data: 'marketingperson', defaultContent: '' },
            { data: 'po_attachment', defaultContent: '' },
            { data: 'brandtag', defaultContent: '' },
            { data: 'milestone', defaultContent: '' },
            <?php if ($own_orders_only) { ?>{ data: 'spare_quotation', defaultContent: '' },<?php } ?>
            { data: 'edit', defaultContent: '' },
            { data: 'canceledorder', defaultContent: '' }
        ],
        columnDefs: [
            {
                targets: [1, 10, 13, 14<?php if ($own_orders_only) { ?>, 15<?php } ?>],
                orderable: false
            }
        ],
        order: [[0, 'desc']],
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                title: 'Received PO Report',
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
                title: 'Received PO Report',
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
            populateDropdownFilters(api);
            applyDefaultFinancialYear(api);
            updateKpisAndFilters(api);
        },
        drawCallback: function () {
            var api = this.api();
            updateKpisAndFilters(api);
        }
    });

    $('#customSearch').on('keyup change', function () {
        table.search(this.value).draw();
    });

    $('#financialYearFilter').on('change', function () {
        var value = this.value;

        if (value !== '') {
            table.column(3).search('^' + escapeRegexValue(value) + '$', true, false).draw();
        } else {
            table.column(3).search('').draw();
        }
    });

    $('#marketingPersonFilter').on('change', function () {
        var value = this.value;

        if (value !== '') {
            table.column(9).search('^' + escapeRegexValue(value) + '$', true, false).draw();
        } else {
            table.column(9).search('').draw();
        }
    });

    $('#brandFilter').on('change', function () {
        var value = this.value;

        if (value !== '') {
            table.column(11).search(escapeRegexValue(value), true, false).draw();
        } else {
            table.column(11).search('').draw();
        }
    });

    $('#resetFilters').on('click', function () {
        $('#customSearch').val('');
        $('#financialYearFilter').val('');
        $('#marketingPersonFilter').val('');
        $('#brandFilter').val('');

        table.search('');
        table.columns().search('');
        if (defaultFinancialYear !== '') {
            $('#financialYearFilter').val(defaultFinancialYear);
            table.column(3).search('^' + escapeRegexValue(defaultFinancialYear) + '$', true, false);
        }
        table.draw();
    });

    function populateDropdownFilters(api) {
        var financialYears = {};
        var marketingPersons = {};
        var brands = {};

        $('#financialYearFilter').find('option:not(:first)').remove();
        $('#marketingPersonFilter').find('option:not(:first)').remove();
        $('#brandFilter').find('option:not(:first)').remove();

        api.rows().every(function () {
            var row = this.data();

            var fy = stripHtml(row.financialyear).trim();
            var marketingPerson = stripHtml(row.marketingperson).trim();
            var brand = stripHtml(row.brandtag).trim();

            if (fy !== '' && fy !== '-') {
                financialYears[fy] = true;
            }

            if (marketingPerson !== '' && marketingPerson !== '-') {
                marketingPersons[marketingPerson] = true;
            }

            if (
                brand !== '' &&
                brand !== '-' &&
                brand.toLowerCase().indexOf('add brand') === -1
            ) {
                brands[brand] = true;
            }
        });

        Object.keys(financialYears).sort().forEach(function(value) {
            $('#financialYearFilter').append('<option value="' + value + '">' + value + '</option>');
        });

        Object.keys(marketingPersons).sort().forEach(function(value) {
            $('#marketingPersonFilter').append('<option value="' + value + '">' + value + '</option>');
        });

        Object.keys(brands).sort().forEach(function(value) {
            $('#brandFilter').append('<option value="' + value + '">' + value + '</option>');
        });
    }

    function applyDefaultFinancialYear(api) {
        if (defaultFinancialYear === '') {
            return;
        }

        if ($('#financialYearFilter option[value="' + defaultFinancialYear + '"]').length === 0) {
            $('#financialYearFilter').append('<option value="' + defaultFinancialYear + '">' + defaultFinancialYear + '</option>');
        }

        $('#financialYearFilter').val(defaultFinancialYear);
        api.column(3).search('^' + escapeRegexValue(defaultFinancialYear) + '$', true, false).draw();
    }

    function updateKpisAndFilters(api) {
        var visibleRows = api.rows({ search: 'applied' }).data();

        var totalPo = visibleRows.length;
        var totalOrderValue = 0;
        var brandTagged = 0;
        var cancelled = 0;

        for (var i = 0; i < visibleRows.length; i++) {
            var row = visibleRows[i];

            totalOrderValue += parseAmount(row.ordervalue);

            var brandText = stripHtml(row.brandtag).trim().toLowerCase();

            if (
                brandText !== '' &&
                brandText !== '-' &&
                brandText !== 'na' &&
                brandText !== 'n/a' &&
                brandText.indexOf('add brand') === -1
            ) {
                brandTagged++;
            }

            var cancelText = stripHtml(row.canceledorder).trim().toLowerCase();

            if (
                cancelText.indexOf('cancelled') !== -1 ||
                cancelText.indexOf('canceled') !== -1
            ) {
                cancelled++;
            }
        }

        $('#visibleRecords').text(totalPo);
        $('#kpiTotalPo').text(totalPo);
        $('#kpiOrderValue').html('<i class="fa fa-inr"></i> ' + formatIndianAmount(totalOrderValue));
        $('#kpiBrandTagged').text(brandTagged);
        $('#kpiCancelled').text(cancelled);
    }

});
</script>

</body>
</html>
