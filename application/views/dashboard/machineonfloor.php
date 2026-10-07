<?php 
$business_location = $this->session->userdata['logged_in']['business_location'];
$user_id = $this->session->userdata['logged_in']['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright;?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle;?> Machine On Floor</title>

    <!-- Bootstrap -->
    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme CSS -->
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

    <?php 
    $logo_query = $this->db
        ->select('company_name, logo, colorcode')
        ->from('company_information')
        ->get();

    foreach ($logo_query->result() as $LOGO);

    $theme_color = !empty($LOGO->colorcode) ? $LOGO->colorcode : '#4872b8';
    ?>

    <style>
        .page-title-box {
            padding-bottom: 12px;
        }

        .machine-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
            border-top: 4px solid <?php echo $theme_color;?>;
        }

        .machine-header-box {
            background: linear-gradient(135deg, <?php echo $theme_color;?>, #1f3f7a);
            color: #ffffff;
            padding: 16px 18px;
            border-radius: 10px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .machine-header-title {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.4px;
            margin: 0;
        }

        .machine-header-subtitle {
            font-size: 12px;
            opacity: 0.95;
            margin-top: 4px;
        }

        .machine-summary {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .summary-pill {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.28);
            color: #ffffff;
            border-radius: 30px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 600;
        }

        table.machine-table {
            width: 100% !important;
            border-collapse: collapse;
            font-size: 12px;
            white-space: nowrap;
        }

        table.machine-table thead th {
            background: <?php echo $theme_color;?>;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            padding: 11px 8px;
            border: 1px solid #d9e2f2;
            font-size: 12px;
        }

        table.machine-table tbody td {
            text-align: center;
            vertical-align: middle;
            padding: 8px 6px;
            border: 1px solid #e6e9ef;
            color: #333333;
        }

        table.machine-table tbody tr:nth-child(even) {
            background: #fbfcff;
        }

        table.machine-table tbody tr:hover {
            background: #f3f8ff;
        }

        .customer-cell {
            text-align: left !important;
            font-weight: 600;
            min-width: 180px;
            color: #253858 !important;
        }

        .payment-text {
            text-align: left !important;
            min-width: 260px;
            max-width: 380px;
            white-space: normal !important;
            line-height: 1.5;
            color: #334155 !important;
        }

        .amount-cell {
            text-align: right !important;
            font-weight: 700;
            color: #0f5132 !important;
            min-width: 110px;
        }

        .df-cell {
            font-weight: 700;
            color: <?php echo $theme_color;?> !important;
        }

        .inline-edit {
            width: 100%;
            min-width: 95px;
            border: 1px solid #d8dee9;
            background: #ffffff;
            text-align: center;
            padding: 6px 7px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #1f2937;
        }

        .inline-edit:focus {
            background: #ffffff;
            border: 1px solid <?php echo $theme_color;?>;
            outline: none;
            box-shadow: 0 0 0 2px rgba(72, 114, 184, 0.15);
        }

        .amount-input {
            text-align: right;
            color: #0f5132;
            min-width: 115px;
        }

        .pending-input {
            text-align: right;
            color: #b42318;
            min-width: 115px;
        }

        .update-info {
            font-size: 11px;
            color: #6b7280;
            min-width: 155px;
            line-height: 1.4;
            white-space: normal !important;
        }

        .update-info strong {
            color: #253858;
        }

        .saving-cell {
            background: #fff7e6 !important;
        }

        .saved-cell {
            background: #e9f7ef !important;
        }

        .error-cell {
            background: #fdecea !important;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom: 12px;
        }

        .dataTables_wrapper .dt-buttons .btn {
            margin-right: 6px;
            border-radius: 20px;
            font-size: 12px;
            padding: 6px 13px;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 20px;
            border: 1px solid #ccd4dd;
            padding: 6px 12px;
            margin-left: 8px;
            outline: none;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 6px;
            padding: 4px 8px;
            border: 1px solid #ccd4dd;
        }

        .dataTables_info {
            font-size: 12px;
            color: #555555;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            margin: 0 2px;
        }

        .table-note {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }

        .mini-help {
            font-size: 11px;
            color: #ffffff;
            opacity: 0.95;
            margin-top: 5px;
        }

        @media screen and (max-width: 767px) {
            .machine-header-box {
                display: block;
            }

            .machine-summary {
                margin-top: 12px;
            }

            .machine-header-title {
                font-size: 17px;
            }
        }
    </style>
</head>

<body>

<!-- Navigation Bar -->
<header id="topnav">
    <?php $this->load->view('common/nav-menu');?>
</header>
<!-- End Navigation Bar -->

<?php $this->load->view('common/info-section.php');?>

<div class="wrapper">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row" style="margin-top:20px;">
            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                <div class="page-title-box">

                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-primary waves-effect waves-light">
                            Machine On Floor Report
                        </button>
                    </div>

                    <h4 class="page-title">MACHINE ON FLOOR</h4>

                </div>
            </div>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-info">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
        <?php } ?>

        <!-- Machine Table -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box table-responsive machine-card">

                    <div class="machine-header-box">
                        <div>
                            <h3 class="machine-header-title">MACHINE ON FLOOR AS ON 06-04-2026</h3>
                            <div class="machine-header-subtitle">
                                Dispatch planning, payment status and machine floor tracking report
                            </div>
                            <div class="mini-help">
                                You can directly edit Location, Machine Status and Payment columns. Data will save automatically.
                            </div>
                        </div>

                        <div class="machine-summary">
                            <span class="summary-pill">Inline Editable</span>
                            <span class="summary-pill">Export Ready</span>
                            <span class="summary-pill">Audit Tracking</span>
                        </div>
                    </div>

                    <table id="machineTable" class="table table-striped table-bordered machine-table">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>DF No.</th>
                                <th>Plan Dispatch</th>
                                <th>Location</th>
                                <th>Status of M/cs</th>
                                <th>DF Release Date</th>
                                <th>PO Date</th>
                                <th>Model</th>
                                <th>Customer</th>
                                <th>Payment Term</th>
                                <th>Value</th>
                                <th>Adv. Recd</th>
                                <th>Adv. Pending</th>
                                <th>Before Dispatch</th>
                                <th>After Dispatch</th>
                                <th>After IOC</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php 
                            $machine_query = $this->db
                                ->select('
                                    a.id,
                                    a.df_no,
                                    b.company_name,
                                    b.pono,
                                    b.podate,
                                    a.added_on,
                                    a.df_description,
                                    b.order_value,
                                    p.payment_terms,
                                    m.location,
                                    m.machine_status,
                                    m.adv_received,
                                    m.adv_pending,
                                    m.before_dispatch,
                                    m.after_dispatch,
                                    m.after_ioc,
                                    m.updated_by,
                                    m.updated_on,
                                    u.first_name as updated_by_name
                                ')
                                ->from('df_release a')
                                ->join('poreceived b', 'a.id = b.df_id', 'left')
                                ->join('quotation_customer_data q', 'b.lead_id = q.lead_id', 'left')
                                ->join('quotation_other_information qo', 'q.id = qo.record_id', 'left')
                                ->join('payment_terms p', 'qo.terms_value = p.id', 'left')
                                ->join('machine_floor_tracking m', 'a.id = m.df_id', 'left')
                                ->join('system_users u', 'm.updated_by = u.user_id', 'left')
                                ->where('a.df_status', 0)
                                ->get();

                            if ($machine_query->num_rows() > 0) {
                                $i = 1;

                                foreach ($machine_query->result() as $row) {
                            ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>

                                    <td class="df-cell">
                                        <?php echo !empty($row->df_no) ? $row->df_no : '-'; ?>
                                    </td>

                                    <td>
                                        <?php 
                                        $dispatch_query = $this->db
                                            ->select('end_date')
                                            ->from('task_department_wise_scheduling')
                                            ->where('df_id', $row->id)
                                            ->where('taskid', 103)
                                            ->get();

                                        if ($dispatch_query->num_rows() > 0) {
                                            $dispatchinfo = $dispatch_query->row();

                                            if (!empty($dispatchinfo->end_date) && $dispatchinfo->end_date != '0000-00-00') {
                                                echo date('d-m-Y', strtotime($dispatchinfo->end_date));
                                            } else {
                                                echo '-';
                                            }
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="location"
                                            value="<?php echo !empty($row->location) ? htmlspecialchars($row->location, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                            placeholder="Location">
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="machine_status"
                                            value="<?php echo !empty($row->machine_status) ? htmlspecialchars($row->machine_status, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                            placeholder="Status">
                                    </td>

                                    <td>
                                        <?php 
                                        if (!empty($row->added_on) && $row->added_on != '0000-00-00') {
                                            echo date('d-m-Y', strtotime($row->added_on));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        if (!empty($row->podate) && $row->podate != '0000-00-00' && $row->podate != '0000-00-00 00:00:00') {
                                            echo date('d-m-Y', strtotime($row->podate));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php echo !empty($row->df_description) ? htmlspecialchars($row->df_description, ENT_QUOTES, 'UTF-8') : '-'; ?>
                                    </td>

                                    <td class="customer-cell">
                                        <?php echo !empty($row->company_name) ? ucwords(strtolower($row->company_name)) : '-'; ?>
                                    </td>

                                    <td class="payment-text">
                                        <?php echo !empty($row->payment_terms) ? htmlspecialchars($row->payment_terms, ENT_QUOTES, 'UTF-8') : '-'; ?>
                                    </td>

                                    <td class="amount-cell">
                                        <?php echo !empty($row->order_value) ? number_format((float)$row->order_value, 2) : '0.00'; ?>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit amount-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="adv_received"
                                            value="<?php echo !empty($row->adv_received) ? number_format((float)$row->adv_received, 2, '.', '') : '0.00'; ?>">
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit pending-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="adv_pending"
                                            value="<?php echo !empty($row->adv_pending) ? number_format((float)$row->adv_pending, 2, '.', '') : '0.00'; ?>">
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit amount-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="before_dispatch"
                                            value="<?php echo !empty($row->before_dispatch) ? number_format((float)$row->before_dispatch, 2, '.', '') : '0.00'; ?>">
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit amount-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="after_dispatch"
                                            value="<?php echo !empty($row->after_dispatch) ? number_format((float)$row->after_dispatch, 2, '.', '') : '0.00'; ?>">
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit amount-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="after_ioc"
                                            value="<?php echo !empty($row->after_ioc) ? number_format((float)$row->after_ioc, 2, '.', '') : '0.00'; ?>">
                                    </td>

                                    <td class="update-info" id="updated-info-<?php echo $row->id; ?>">
                                        <?php if (!empty($row->updated_on)) { ?>
                                            <strong>
                                                <?php 
                                                if (!empty($row->updated_by_name)) {
                                                    echo htmlspecialchars($row->updated_by_name, ENT_QUOTES, 'UTF-8');
                                                } elseif (!empty($row->updated_by)) {
                                                    echo 'User ID: '.$row->updated_by;
                                                } else {
                                                    echo 'Updated';
                                                }
                                                ?>
                                            </strong>
                                            <br>
                                            <?php echo date('d-m-Y h:i A', strtotime($row->updated_on)); ?>
                                        <?php } else { ?>
                                            Not updated yet
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="17" class="text-center">
                                        No machine floor data found.
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="table-note">
                        Note: Editable fields are saved automatically after leaving the input field or pressing Enter.
                    </div>

                </div>
            </div>
        </div>

        <?php $this->load->view('common/footer');?>

    </div>
</div>

<!-- jQuery -->
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

<!-- DataTables -->
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

<!-- App JS -->
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script>
$(document).ready(function() {

    $('#machineTable').DataTable({
        processing: true,
        fixedHeader: true,
        responsive: false,
        scrollX: true,
        pageLength: 100,
        lengthMenu: [
            [100, 200, 500, 1000, -1],
            [100, 200, 500, 1000, "All"]
        ],
        ordering: true,
        searching: true,
        columnDefs: [
            {
                targets: [3, 4, 11, 12, 13, 14, 15],
                orderable: false
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'Machine On Floor Report'
            },
            {
                extend: 'pdfHtml5',
                text: 'Export PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Machine On Floor Report',
                orientation: 'landscape',
                pageSize: 'A3'
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary btn-sm',
                title: 'Machine On Floor Report'
            }
        ]
    });

    $(document).on('focus', '.inline-edit', function() {
        $(this).attr('data-old-value', $(this).val());
    });

    $(document).on('blur', '.inline-edit', function() {
        var input = $(this);
        var oldValue = input.attr('data-old-value');
        var newValue = input.val();

        if (oldValue == newValue) {
            return false;
        }

        saveInlineValue(input);
    });

    $(document).on('keypress', '.inline-edit', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $(this).blur();
        }
    });

    function saveInlineValue(input) {

        var df_id = input.data('df-id');
        var field = input.data('field');
        var value = input.val();
        var td = input.closest('td');

        if (df_id == '' || field == '') {
            return false;
        }

        td.removeClass('saved-cell error-cell').addClass('saving-cell');

        $.ajax({
            url: "<?php echo page_url.'Machine/update_machine_floor_tracking'; ?>",
            type: "POST",
            dataType: "json",
            data: {
                df_id: df_id,
                field: field,
                value: value
            },
            success: function(response) {

                td.removeClass('saving-cell');

                if (response.status == true) {

                    td.addClass('saved-cell');

                    $('#updated-info-' + df_id).html(
                        '<strong>Updated</strong><br>' + response.updated_on
                    );

                    setTimeout(function() {
                        td.removeClass('saved-cell');
                    }, 1200);

                    if (typeof $.Notification !== 'undefined') {
                        $.Notification.notify(
                            'success',
                            'top right',
                            'Updated',
                            response.message
                        );
                    }

                } else {

                    td.addClass('error-cell');

                    if (typeof $.Notification !== 'undefined') {
                        $.Notification.notify(
                            'error',
                            'top right',
                            'Error',
                            response.message
                        );
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function() {

                td.removeClass('saving-cell').addClass('error-cell');

                if (typeof $.Notification !== 'undefined') {
                    $.Notification.notify(
                        'error',
                        'top right',
                        'Error',
                        'Unable to update record.'
                    );
                } else {
                    alert('Unable to update record.');
                }
            }
        });
    }

});
</script>

</body>
</html>
