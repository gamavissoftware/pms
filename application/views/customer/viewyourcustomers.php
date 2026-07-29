<?php
$CI =& get_instance();
$CI->load->model('Salescrm_model');
$max_customer_code = $CI->Salescrm_model->getHighestCustomerCode();

// Company branding
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);

// Countries for filter dropdown
$countries = $this->db->select('country_id, country_name')
    ->from('countries')
    ->order_by('country_name','ASC')
    ->get()
    ->result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle;?> Customers List</title>
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <!-- Bootstrap / Core -->
    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- Select2 -->
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <style>
        /* ---------- Page layout ---------- */
        .page-shell{ padding-top:18px; }
        .page-title{ font-weight:900; letter-spacing:.2px; margin: 6px 0 14px; color:#111827; }
        .subtle{ color:#6b7280; font-weight:700; }

        /* ---------- Cards ---------- */
        .filter-card, .dt-card{
            background:#fff;
            border:1px solid #e9ecef;
            border-radius:14px;
            box-shadow:0 6px 18px rgba(16,24,40,.06);
        }
        .filter-card{ padding:14px; margin-bottom:14px; }
        .dt-card{ overflow:hidden; }

        /* ---------- Filter controls ---------- */
        .label-title{ font-weight:900; font-size:12px; color:#6b7280; letter-spacing:.4px; margin-bottom:6px; }
        .btn-soft{ border-radius:10px; font-weight:800; }
        .select2-container--default .select2-selection--single{ border-radius:10px; height:38px; border:1px solid #e5e7eb; }
        .select2-container--default .select2-selection--single .select2-selection__rendered{ line-height:36px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow{ height:36px; }

        /* ---------- Main table ---------- */
        table.manglesh thead th{
            background: linear-gradient(90deg, <?php echo $LOGO->colorcode;?> 0%, #111827 140%);
            color:#fff;
            font-weight:900;
            text-align:center;
            padding:14px 10px !important;
            border-bottom:0 !important;
            vertical-align:middle !important;
            white-space:nowrap;
        }
        #example tbody td{
            padding:14px 10px !important;
            vertical-align:top !important;
        }
        #example tbody tr:hover{ background:#f9fafb !important; }

        /* ---------- Expand control ---------- */
        td.dt-control{
            cursor:pointer;
            text-align:center;
            vertical-align:middle !important;
            width:44px;
        }
        .expander{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:30px;height:30px;
            border-radius:10px;
            border:1px solid #e5e7eb;
            background:#fff;
            color:#111827;
            font-weight:900;
        }
        tr.shown td.dt-control .expander{
            background:#111827;
            color:#fff;
            border-color:#111827;
        }

        /* ---------- Company cell ---------- */
        .company-title{
            font-weight:900;
            letter-spacing:.3px;
            text-transform:uppercase;
            font-size:13px;
            color:#111827;
            line-height:1.2;
        }

        /* ---------- Status chips ---------- */
        .status-chip{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:6px 10px;
            border-radius:999px;
            font-weight:900;
            font-size:11px;
            border:1px solid #e5e7eb;
            white-space:nowrap;
        }
        .status-chip.active{ background:#ecfdf5; color:#065f46; border-color:#d1fae5; }
        .status-chip.inactive{ background:#fef2f2; color:#991b1b; border-color:#fecaca; }

        /* ---------- Quick actions ---------- */
        .qa-wrap{
            display:flex;
            align-items:center;
            gap:8px;
            flex-wrap:wrap;
        }
        .qa-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:34px;height:34px;
            border-radius:10px;
            border:1px solid #e5e7eb;
            background:#fff;
            color:#111827;
            text-decoration:none !important;
        }
        .qa-btn:hover{ background:#f3f4f6; }
        .qa-btn.disabled{
            pointer-events:none;
            opacity:.45;
        }

        /* ---------- Child row (expanded details) ---------- */
        .child-wrap{
            padding:14px;
            background:#f9fafb;
            border-radius:12px;
            border:1px solid #e5e7eb;
        }
        .child-grid{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:12px;
        }
        .child-card{
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:12px;
            overflow:hidden;
        }
        .child-card .child-head{
            padding:10px 12px;
            background:#f3f4f6;
            font-weight:900;
            color:#111827;
            letter-spacing:.3px;
            text-transform:uppercase;
            font-size:11px;
        }
        .child-card .child-body{
            padding:10px 12px;
        }

        /* Child mini tables */
        .child-card .table{ margin:0 !important; }
        .child-card .table thead th{
            background:#f3f4f6 !important;
            color:#111827 !important;
            font-weight:900 !important;
            font-size:11px !important;
            text-transform:uppercase;
            letter-spacing:.4px;
            padding:10px 8px !important;
            border:0 !important;
        }
        .child-card .table td{
            padding:10px 8px !important;
            border-top:1px solid #eef2f7 !important;
            font-size:12px;
        }

        /* DT controls */
        .dataTables_wrapper .dataTables_filter input{
            border-radius:12px !important;
            border:1px solid #e5e7eb !important;
            padding:8px 10px !important;
        }
        .dataTables_wrapper .dataTables_length select{
            border-radius:12px !important;
            border:1px solid #e5e7eb !important;
        }

        @media(max-width: 991px){
            .child-grid{ grid-template-columns: 1fr; }
        }
        @media(max-width: 767px){
            .filter-actions{ margin-top:10px; text-align:left !important; }
            .page-title{ font-size:18px; }
        }
    </style>
</head>

<body>
<header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
</header>

<div class="wrapper">
    <div class="container-fluid page-shell">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title text-center">
                        View <?php if($_SESSION['logged_in']['role']!=12 && $_SESSION['logged_in']['role']!=41){?> Your <?php }else{?> All <?php }?> Customer List
                    </h4>
                    <div class="text-center subtle">Modern customer directory with country filter, quick actions and expandable contact details.</div>
                </div>

                <?php if($this->session->flashdata('message')){ ?>
                    <div class="alert alert-info" style="border-radius:12px;">
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="row">
            <div class="col-sm-12">
                <div class="filter-card">
                    <div class="row">
                        <div class="col-md-6 col-sm-8 col-xs-12">
                            <div class="label-title">FILTER BY COUNTRY</div>
                            <select id="filter_country" class="form-control">
                                <option value="">All Countries</option>
                                <?php foreach($countries as $c){ ?>
                                    <option value="<?php echo (int)$c->country_id; ?>">
                                        <?php echo htmlspecialchars($c->country_name); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-6 col-sm-4 col-xs-12 filter-actions" style="text-align:right;padding-top:22px;">
                            <button type="button" id="btn_reset" class="btn btn-default btn-soft">Reset</button>
                            <button type="button" id="btn_refresh" class="btn btn-primary btn-soft" style="margin-left:8px;">Refresh</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="row">
            <div class="col-sm-12">
                <div class="dt-card">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered manglesh" style="width:100%;">
                            <thead>
                            <tr>
                                <th style="width:48px;"></th>
                                <th style="width:90px;">SR No</th>
                                <th style="min-width:260px;">Company</th>
                                <th style="min-width:260px;">Email</th>
                                <th style="min-width:140px;">Mobile</th>
                                <th style="min-width:140px;">Status</th>
                                <th style="min-width:160px;">Quick Actions</th>
                                <th style="min-width:140px;">GSTN</th>
                                <th style="min-width:160px;">Country</th>
                                <th style="min-width:240px;">Address</th>
                                <th style="min-width:170px;">Added By</th>
                                <th style="min-width:130px;">Added On</th>

                                <!-- Hidden (used in expand view only) -->
                                <th style="display:none;">Primary</th>
                                <th style="display:none;">Additional</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div style="height:14px;"></div>
            </div>
        </div>

        <?php $this->load->view('common/footer'); ?>

    </div>
</div>

<!-- jQuery (KEEP ONLY ONE) -->
<script src="<?php echo assets_url;?>js/jquery.min.js"></script>
<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>

<!-- DataTables -->
<script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>

<!-- DataTables Buttons (ORDER MATTERS) -->
<script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
<script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>

<!-- Select2 -->
<script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    // Select2
    $('#filter_country').select2({ width: '100%' });

    function sanitizePhone(p){
        if(!p) return '';
        return (''+p).replace(/[^0-9]/g,'');
    }

    function formatStatusChip(statusHtml){
        if(!statusHtml) return "<span class='status-chip inactive'>Inactive</span>";

        var lower = (''+statusHtml).toLowerCase();
        var isActive = (lower.indexOf('btn-success') !== -1) || (lower.indexOf('active') !== -1 && lower.indexOf('inactive') === -1);

        var hrefMatch = (''+statusHtml).match(/href=['"]([^'"]+)['"]/i);
        var href = hrefMatch ? hrefMatch[1] : null;

        if(href){
            return "<a href='"+href+"' class='status-chip "+(isActive ? "active" : "inactive")+"' style='text-decoration:none;'>"
                 + (isActive ? "Active" : "Inactive")
                 + "</a>";
        }
        return "<span class='status-chip "+(isActive ? "active" : "inactive")+"'>"+(isActive ? "Active" : "Inactive")+"</span>";
    }

    function renderQuickActions(row){
        var email = row.email || '';
        var mobile = row.mobile || row.contact_no || '';
        var phoneDigits = sanitizePhone(mobile);

        var telLink = phoneDigits ? ("tel:"+phoneDigits) : "#";
        var waLink  = phoneDigits ? ("https://wa.me/"+phoneDigits) : "#";
        var mailLink= email ? ("mailto:"+email) : "#";

        var telCls  = phoneDigits ? "" : "disabled";
        var waCls   = phoneDigits ? "" : "disabled";
        var mailCls = email ? "" : "disabled";

        return "<div class='qa-wrap'>"
            + "<a class='qa-btn "+telCls+"' href='"+telLink+"' title='Call'><i class='fa fa-phone'></i></a>"
            + "<a class='qa-btn "+waCls+"' href='"+waLink+"' target='_blank' title='WhatsApp'><i class='fa fa-whatsapp'></i></a>"
            + "<a class='qa-btn "+mailCls+"' href='"+mailLink+"' title='Email'><i class='fa fa-envelope'></i></a>"
            + "</div>";
    }

    function formatChild(row){
        var primary = row.primarycontact ? row.primarycontact : "<div class='subtle'>No primary contact found.</div>";
        var additional = row.additional_contacts ? row.additional_contacts : "<div class='subtle'>No additional contacts found.</div>";

        return "<div class='child-wrap'>"
            + " <div class='child-grid'>"
            + "   <div class='child-card'>"
            + "     <div class='child-head'>Primary Contact</div>"
            + "     <div class='child-body'>"+primary+"</div>"
            + "   </div>"
            + "   <div class='child-card'>"
            + "     <div class='child-head'>Additional Contacts</div>"
            + "     <div class='child-body'>"+additional+"</div>"
            + "   </div>"
            + " </div>"
            + "</div>";
    }

    var table = $('#example').DataTable({
        processing: true,
        paging: true,
        pageLength: 50,
        lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]],
        autoWidth: false,
        deferRender: true,
        order: [],
        responsive: false,
        language: {
            search: "",
            searchPlaceholder: "Search customer / email / mobile...",
            processing: "Loading customers..."
        },

        dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"l><"col-md-6"p>>',

        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                className: 'btn btn-success btn-soft',
                title: 'Customers_List_<?php echo date("Ymd_His"); ?>',
                exportOptions: {
                    columns: [1,2,3,4,5,7,8,9,10,11],
                    modifier: { search: 'applied', order: 'applied', page: 'all' }
                },
                action: function (e, dt, button, config) {
                    var self = this;
                    var oldStart = dt.settings()[0]._iDisplayStart;

                    dt.one('preXhr', function (e, s, data) {
                        data.start = 0;
                        data.length = -1; // request all rows
                        data.country_id = $('#filter_country').val();
                    });

                    dt.one('xhr', function (e, s, json) {
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);

                        dt.one('preXhr', function (e, s, data) {
                            data.start = oldStart;
                            data.length = dt.page.len();
                            data.country_id = $('#filter_country').val();
                        });

                        setTimeout(function () {
                            dt.ajax.reload(null, false);
                        }, 0);
                    });

                    dt.ajax.reload();
                }
            }
        ],

        ajax: {
            url: "<?php echo page_url;?>Customer/viewyourcustomerslist/<?php echo $this->uri->segment(3);?>",
            type: "GET",
            data: function (d) {
                d.country_id = $('#filter_country').val();
            }
        },

        columns: [
            { data: null, className: 'dt-control', orderable: false, defaultContent: "<span class='expander'>+</span>" },
            { data: 'sr_no' },
            {
                data: 'company_name',
                render: function(data, type, row){
                    if(!data) return "<div class='company-title'>—</div>";
                    var hasHtml = /<\/?[a-z][\s\S]*>/i.test(data);
                    if(hasHtml) return data;
                    return "<div class='company-title'>"+data+"</div>";
                }
            },
            { data: 'email', defaultContent: "<span class='subtle'>—</span>" },
            { data: 'mobile', defaultContent: "<span class='subtle'>—</span>" },
            { data: 'status', render: function(data){ return formatStatusChip(data); } },
            { data: null, orderable: false, render: function(d,t,row){ return renderQuickActions(row); } },
            { data: 'gst', defaultContent: "<span class='subtle'>—</span>" },
            { data: 'country', defaultContent: "<span class='subtle'>—</span>" },
            { data: 'address', defaultContent: "<span class='subtle'>—</span>" },
            { data: 'addedbyperson', defaultContent: "<span class='subtle'>—</span>" },
            { data: 'customer_added_on', defaultContent: "<span class='subtle'>—</span>" },

            // hidden for child
            { data: 'primarycontact', visible:false, searchable:false },
            { data: 'additional_contacts', visible:false, searchable:false }
        ],
        columnDefs: [
            { targets: [1,10,11], className: 'text-center' }
        ]
    });

    // Expand/collapse
    $('#example tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            $(this).find('.expander').text('+');
        } else {
            row.child(formatChild(row.data())).show();
            tr.addClass('shown');
            $(this).find('.expander').text('–');
        }
    });

    // Filter
    $('#filter_country').on('change', function () {
        table.ajax.reload(null, true);
    });
    $('#btn_refresh').on('click', function () {
        table.ajax.reload(null, true);
    });
    $('#btn_reset').on('click', function () {
        $('#filter_country').val('').trigger('change');
    });

});
</script>
</body>
</html>
