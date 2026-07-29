<?php 
$business_location = $this->session->userdata['logged_in']['business_location'];
$user_id = $this->session->userdata['logged_in']['user_id'];

/*
|--------------------------------------------------------------------------
| Financial Year Filter Logic
|--------------------------------------------------------------------------
| Indian FY: 01-April to 31-March
*/
$current_month = date('m');
$current_year  = date('Y');

if ($current_month >= 4) {
    $default_fy_start = $current_year;
    $default_fy_end   = $current_year + 1;
} else {
    $default_fy_start = $current_year - 1;
    $default_fy_end   = $current_year;
}

$selected_fy = $this->input->get('financial_year');

if (empty($selected_fy)) {
    $selected_fy = $default_fy_start . '-' . $default_fy_end;
}

$fy_parts = explode('-', $selected_fy);

$fy_start_year = !empty($fy_parts[0]) ? $fy_parts[0] : $default_fy_start;
$fy_end_year   = !empty($fy_parts[1]) ? $fy_parts[1] : $default_fy_end;

$financial_year_start_date = $fy_start_year . '-04-01 00:00:00';
$financial_year_end_date   = $fy_end_year . '-03-31 23:59:59';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright;?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle;?> M/cs Dispatch Report</title>

    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

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
        .dispatch-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
            border-top: 4px solid <?php echo $theme_color;?>;
        }

        .dispatch-header-box {
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

        .dispatch-header-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .dispatch-header-subtitle {
            font-size: 12px;
            opacity: 0.95;
            margin-top: 4px;
        }

        .summary-pill {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.28);
            color: #ffffff;
            border-radius: 30px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-left: 5px;
        }

        .filter-box {
            background: #f8fafc;
            border: 1px solid #d9e2f2;
            border-radius: 10px;
            padding: 14px 15px;
            margin-bottom: 18px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 700;
            color: #253858;
            margin-bottom: 6px;
        }

        .filter-select {
            height: 38px;
            border-radius: 6px;
            border: 1px solid #ccd4dd;
            font-size: 13px;
        }

        .filter-btn {
            margin-top: 24px;
            border-radius: 20px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
        }

        .active-fy-badge {
            display: inline-block;
            background: #e7f1ff;
            color: #084298;
            border-radius: 20px;
            padding: 7px 13px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 24px;
        }

        .reset-filter-link {
            display: inline-block;
            margin-top: 31px;
            font-size: 12px;
            font-weight: 600;
            color: #b42318;
        }

        table.dispatch-table {
            width: 100% !important;
            border-collapse: collapse;
            font-size: 12px;
            white-space: nowrap;
        }

        table.dispatch-table thead th {
            background: <?php echo $theme_color;?>;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            padding: 10px 8px;
            border: 1px solid #d9e2f2;
            font-size: 12px;
        }

        table.dispatch-table tbody td {
            text-align: center;
            vertical-align: middle;
            padding: 8px 6px;
            border: 1px solid #e6e9ef;
            color: #333333;
        }

        table.dispatch-table tbody tr:nth-child(even) {
            background: #fbfcff;
        }

        table.dispatch-table tbody tr:hover {
            background: #f3f8ff;
        }

        .df-cell {
            font-weight: 700;
            color: <?php echo $theme_color;?> !important;
        }

        .name-cell {
            text-align: left !important;
            font-weight: 600;
            min-width: 180px;
            color: #253858 !important;
        }

        .model-cell {
            text-align: left !important;
            min-width: 170px;
            white-space: normal !important;
            line-height: 1.4;
        }

        .payment-term-cell {
            text-align: left !important;
            min-width: 260px;
            max-width: 360px;
            white-space: normal !important;
            line-height: 1.5;
            color: #334155 !important;
        }

        .inline-edit {
            width: 100%;
            min-width: 100px;
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
            border: 1px solid <?php echo $theme_color;?>;
            outline: none;
            box-shadow: 0 0 0 2px rgba(72, 114, 184, 0.15);
        }

        .amount-input {
            text-align: right;
            color: #0f5132;
            min-width: 115px;
        }

        .balance-input {
            text-align: right;
            color: #b42318;
            min-width: 115px;
            background: #fff8f8;
            cursor: not-allowed;
        }

        .remarks-input {
            min-width: 220px;
            text-align: left;
        }

        .status-select {
            min-width: 115px;
        }

        .update-info {
            font-size: 11px;
            color: #6b7280;
            min-width: 150px;
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

        .table-note {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }

        .export-value {
            display: none !important;
        }

        @media screen and (max-width: 767px) {
            .filter-btn,
            .active-fy-badge,
            .reset-filter-link {
                margin-top: 10px;
            }

            .dispatch-header-box {
                display: block;
            }

            .summary-pill {
                margin-top: 8px;
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
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-primary waves-effect waves-light">
                            M/cs Dispatch Report
                        </button>
                    </div>

                    <h4 class="page-title">M/cs Dispatch Report</h4>
                </div>
            </div>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-info">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="card-box table-responsive dispatch-card">

                    <div class="dispatch-header-box">
                        <div>
                            <h3 class="dispatch-header-title">M/cs Dispatch Report</h3>
                            <div class="dispatch-header-subtitle">
                                Dispatch, invoice, payment, due date and commissioning tracking report
                            </div>
                        </div>

                        <div>
                            <span class="summary-pill">FY Filter</span>
                            <span class="summary-pill">Inline Editable</span>
                            <span class="summary-pill">Auto Balance</span>
                            <span class="summary-pill">Export Ready</span>
                        </div>
                    </div>

                    <!-- Financial Year Filter -->
                    <div class="filter-box">
                        <form method="get" action="">
                            <div class="row">

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <label class="filter-label">Select Financial Year</label>
                                    <select name="financial_year" class="form-control filter-select">
                                        <?php
                                        $start_year_for_dropdown = date('Y') + 1;
                                        $end_year_for_dropdown   = date('Y') - 8;

                                        for ($year = $start_year_for_dropdown; $year >= $end_year_for_dropdown; $year--) {
                                            $fy_value = ($year - 1) . '-' . $year;
                                        ?>
                                            <option value="<?php echo $fy_value; ?>" <?php echo ($selected_fy == $fy_value) ? 'selected' : ''; ?>>
                                                FY <?php echo $fy_value; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-2 col-sm-6 col-xs-12">
                                    <button type="submit" class="btn btn-primary filter-btn">
                                        Apply Filter
                                    </button>
                                </div>

                                <div class="col-md-4 col-sm-12 col-xs-12">
                                    <span class="active-fy-badge">
                                        Showing: FY <?php echo $selected_fy; ?>
                                        |
                                        <?php echo date('d-M-Y', strtotime($financial_year_start_date)); ?>
                                        to
                                        <?php echo date('d-M-Y', strtotime($financial_year_end_date)); ?>
                                    </span>
                                </div>

                                <div class="col-md-2 col-sm-12 col-xs-12">
                                    <a href="<?php echo current_url(); ?>" class="reset-filter-link">
                                        Reset Filter
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>

                    <table id="dispatchTable" class="table table-striped table-bordered dispatch-table">
                        <thead>
                            <tr>
                                <th>SR</th>
                                <th>DF Release Date</th>
                                <th>DF No</th>
                                <th>Nos of Machines</th>
                                <th>Name</th>
                                <th>Model</th>
                                <th>PO No.</th>
                                <th>PO Date</th>
                                <th>Payment Term</th>
                                <th>Inv No.</th>
                                <th>Invoice Date</th>
                                <th>Inv Amt</th>
                                <th>Taxable Sale</th>
                                <th>Payment Recd</th>
                                <th>Balance Amt</th>
                                <th>Marketing Person</th>
                                <th>Payment Due/Not Due</th>
                                <th>Due Dates</th>
                                <th>Remarks</th>
                                <th>Commissioning Status & Planning</th>
                                <th>Date of I&C</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php 
                            $dispatch_query = $this->db
                                ->select('
                                    a.id,
                                    a.df_no,
                                    a.added_on,
                                    a.df_description,
                                    b.company_name,
                                    b.pono,
                                    b.podate as po_date,
                                    b.order_value,
                                    p.payment_terms,

                                    d.nos_of_machines,
                                    d.invoice_no,
                                    d.invoice_date,
                                    d.invoice_amount,
                                    d.taxable_sale,
                                    d.payment_received,
                                    d.balance_amount,
                                    d.marketing_person,
                                    d.payment_due_status,
                                    d.due_date,
                                    d.remarks,
                                    d.commissioning_status,
                                    d.inc_date,
                                    d.updated_by,
                                    d.updated_on,
                                    u.first_name as updated_by_name,
                                    mark.first_name,
                                    mark.last_name,
                                    tds.task_completed_on
                                ')
                                ->from('df_release a')
                                ->join('poreceived b', 'a.id = b.df_id', 'left')
                                ->join('quotation_customer_data q', 'b.lead_id = q.lead_id', 'left')
                                ->join('quotation_other_information qo', 'q.id = qo.record_id', 'left')
                                ->join('payment_terms p', 'qo.terms_value = p.id', 'left')
                                ->join('mcs_dispatch_report_tracking d', 'a.id = d.df_id', 'left')
                                ->join('system_users u', 'd.updated_by = u.user_id', 'left')
                                ->join('system_users mark', 'b.added_by = mark.user_id', 'left')
                                ->join('task_department_wise_scheduling tds', 'a.id = tds.df_id', 'left')
                                ->where('a.df_status', 1)
                                ->where('tds.task_status', 1)
                                ->where('tds.taskid', 103)
                                ->where('tds.task_completed_on >=', $financial_year_start_date)
                                ->where('tds.task_completed_on <=', $financial_year_end_date)
                                ->group_by('a.id')
                                ->order_by('tds.task_completed_on', 'DESC')
                                ->get();

                            if ($dispatch_query->num_rows() > 0) {
                                $i = 1;

                                foreach ($dispatch_query->result() as $row) {

                                    $invoice_amount = !empty($row->invoice_amount) ? (float)$row->invoice_amount : 0;
                                    $payment_received = !empty($row->payment_received) ? (float)$row->payment_received : 0;

                                    if ($row->balance_amount !== null && $row->balance_amount !== '') {
                                        $balance_amount = (float)$row->balance_amount;
                                    } else {
                                        $balance_amount = $invoice_amount - $payment_received;
                                    }

                                    $nos_of_machines = !empty($row->nos_of_machines) ? $row->nos_of_machines : '1';
                                    $invoice_no = !empty($row->invoice_no) ? $row->invoice_no : '';
                                    $invoice_date = !empty($row->invoice_date) ? date('Y-m-d', strtotime($row->invoice_date)) : '';
                                    $taxable_sale = !empty($row->taxable_sale) ? number_format((float)$row->taxable_sale, 2, '.', '') : '0.00';
                                    $payment_due_status = !empty($row->payment_due_status) ? $row->payment_due_status : '';
                                    $due_date = !empty($row->due_date) ? date('Y-m-d', strtotime($row->due_date)) : '';
                                    $remarks = !empty($row->remarks) ? $row->remarks : '';
                                    $commissioning_status = !empty($row->commissioning_status) ? $row->commissioning_status : '';
                                    $inc_date = !empty($row->inc_date) ? date('Y-m-d', strtotime($row->inc_date)) : '';

                                    $marketing_person = '';
                                    if (!empty($row->first_name)) {
                                        $marketing_person .= ucwords(strtolower($row->first_name));
                                    }
                                    if (!empty($row->last_name)) {
                                        $marketing_person .= ' ' . ucwords(strtolower($row->last_name));
                                    }
                                    $marketing_person = trim($marketing_person);
                            ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>

                                    <td>
                                        <?php 
                                        if (!empty($row->added_on) && $row->added_on != '0000-00-00') {
                                            echo date('d-m-Y', strtotime($row->added_on));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>

                                    <td class="df-cell">
                                        <?php echo !empty($row->df_no) ? $row->df_no : '-'; ?>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="nos_of_machines"
                                            value="<?php echo htmlspecialchars($nos_of_machines, ENT_QUOTES, 'UTF-8'); ?>">
                                        <span class="export-value"><?php echo htmlspecialchars($nos_of_machines, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td class="name-cell">
                                        <?php echo !empty($row->company_name) ? ucwords(strtolower($row->company_name)) : '-'; ?>
                                    </td>

                                    <td class="model-cell">
                                        <?php echo !empty($row->df_description) ? htmlspecialchars($row->df_description, ENT_QUOTES, 'UTF-8') : '-'; ?>
                                    </td>

                                    <td>
                                        <?php echo !empty($row->pono) ? htmlspecialchars($row->pono, ENT_QUOTES, 'UTF-8') : '-'; ?>
                                    </td>

                                    <td>
                                        <?php 
                                        if (!empty($row->po_date) && $row->po_date != '0000-00-00') {
                                            echo date('d-m-Y', strtotime($row->po_date));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>

                                    <td class="payment-term-cell">
                                        <?php echo !empty($row->payment_terms) ? htmlspecialchars($row->payment_terms, ENT_QUOTES, 'UTF-8') : '-'; ?>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="invoice_no"
                                            value="<?php echo htmlspecialchars($invoice_no, ENT_QUOTES, 'UTF-8'); ?>"
                                            placeholder="Inv No">
                                        <span class="export-value"><?php echo htmlspecialchars($invoice_no, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="date"
                                            class="inline-edit"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="invoice_date"
                                            value="<?php echo htmlspecialchars($invoice_date, ENT_QUOTES, 'UTF-8'); ?>">
                                        <span class="export-value"><?php echo htmlspecialchars($invoice_date, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit amount-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="invoice_amount"
                                            value="<?php echo number_format((float)$invoice_amount, 2, '.', ''); ?>">
                                        <span class="export-value"><?php echo number_format((float)$invoice_amount, 2, '.', ''); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit amount-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="taxable_sale"
                                            value="<?php echo htmlspecialchars($taxable_sale, ENT_QUOTES, 'UTF-8'); ?>">
                                        <span class="export-value"><?php echo htmlspecialchars($taxable_sale, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit amount-input payment-received"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="payment_received"
                                            value="<?php echo number_format((float)$payment_received, 2, '.', ''); ?>">
                                        <span class="export-value"><?php echo number_format((float)$payment_received, 2, '.', ''); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit balance-input balance-amount"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="balance_amount"
                                            value="<?php echo number_format((float)$balance_amount, 2, '.', ''); ?>"
                                            readonly>
                                        <span class="export-value"><?php echo number_format((float)$balance_amount, 2, '.', ''); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="marketing_person"
                                            value="<?php echo htmlspecialchars($marketing_person, ENT_QUOTES, 'UTF-8'); ?>"
                                            placeholder="Person" readonly>
                                        <span class="export-value"><?php echo htmlspecialchars($marketing_person, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td>
                                        <select 
                                            class="inline-edit status-select"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="payment_due_status">
                                            <option value="">Select</option>
                                            <option value="Due" <?php echo ($payment_due_status == 'Due') ? 'selected' : ''; ?>>Due</option>
                                            <option value="Not Due" <?php echo ($payment_due_status == 'Not Due') ? 'selected' : ''; ?>>Not Due</option>
                                            <option value="Received" <?php echo ($payment_due_status == 'Received') ? 'selected' : ''; ?>>Received</option>
                                            <option value="Hold" <?php echo ($payment_due_status == 'Hold') ? 'selected' : ''; ?>>Hold</option>
                                        </select>
                                        <span class="export-value"><?php echo htmlspecialchars($payment_due_status, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="date"
                                            class="inline-edit"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="due_date"
                                            value="<?php echo htmlspecialchars($due_date, ENT_QUOTES, 'UTF-8'); ?>">
                                        <span class="export-value"><?php echo htmlspecialchars($due_date, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit remarks-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="remarks"
                                            value="<?php echo htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8'); ?>"
                                            placeholder="Remarks">
                                        <span class="export-value"><?php echo htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="text"
                                            class="inline-edit remarks-input"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="commissioning_status"
                                            value="<?php echo htmlspecialchars($commissioning_status, ENT_QUOTES, 'UTF-8'); ?>"
                                            placeholder="Commissioning">
                                        <span class="export-value"><?php echo htmlspecialchars($commissioning_status, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>

                                    <td>
                                        <input 
                                            type="date"
                                            class="inline-edit"
                                            data-df-id="<?php echo $row->id; ?>"
                                            data-field="inc_date"
                                            value="<?php echo htmlspecialchars($inc_date, ENT_QUOTES, 'UTF-8'); ?>">
                                        <span class="export-value"><?php echo htmlspecialchars($inc_date, ENT_QUOTES, 'UTF-8'); ?></span>
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
                                    <td colspan="22" class="text-center">
                                        No dispatch report data found for FY <?php echo $selected_fy; ?>.
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="table-note">
                        Note: Balance Amount is calculated automatically as Invoice Amount - Payment Received.
                        Current filter: FY <?php echo $selected_fy; ?>.
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

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script>
$(document).ready(function() {

    /*
    |--------------------------------------------------------------------------
    | Export Formatter
    |--------------------------------------------------------------------------
    | Priority:
    | 1. Hidden .export-value
    | 2. Live input value
    | 3. Selected dropdown text
    | 4. Cell text
    */
    function exportInputValue(data, row, column, node) {

        if (node) {

            var exportValue = $(node).find('.export-value');

            if (exportValue.length > 0) {
                return exportValue.first().text().trim();
            }

            var input = $(node).find('input');
            var select = $(node).find('select');
            var textarea = $(node).find('textarea');

            if (input.length > 0) {
                return input.val();
            }

            if (select.length > 0) {
                return select.find('option:selected').text();
            }

            if (textarea.length > 0) {
                return textarea.val();
            }

            return $(node).text().trim();
        }

        if (typeof data === 'string') {

            var html = $('<div>').html(data);

            var hiddenExportValue = html.find('.export-value');

            if (hiddenExportValue.length > 0) {
                return hiddenExportValue.first().text().trim();
            }

            var htmlInput = html.find('input');
            var htmlSelect = html.find('select');
            var htmlTextarea = html.find('textarea');

            if (htmlInput.length > 0) {
                return htmlInput.attr('value') || '';
            }

            if (htmlSelect.length > 0) {
                var selectedOption = htmlSelect.find('option[selected]');

                if (selectedOption.length > 0) {
                    return selectedOption.text().trim();
                }

                return htmlSelect.find('option:first').text().trim();
            }

            if (htmlTextarea.length > 0) {
                return htmlTextarea.text().trim();
            }

            return html.text().trim();
        }

        return data;
    }

    $('#dispatchTable').DataTable({
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
                targets: [3, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                orderable: false
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'Mcs Dispatch Report FY <?php echo $selected_fy; ?>',
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false,
                    format: {
                        body: exportInputValue
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'Export PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Mcs Dispatch Report FY <?php echo $selected_fy; ?>',
                orientation: 'landscape',
                pageSize: 'A3',
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false,
                    format: {
                        body: exportInputValue
                    }
                }
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary btn-sm',
                title: 'Mcs Dispatch Report FY <?php echo $selected_fy; ?>',
                exportOptions: {
                    columns: ':visible',
                    stripHtml: false,
                    format: {
                        body: exportInputValue
                    }
                }
            }
        ]
    });

    $(document).on('focus', '.inline-edit', function() {
        $(this).attr('data-old-value', $(this).val());
    });

    /*
    |--------------------------------------------------------------------------
    | Live Balance Calculation
    |--------------------------------------------------------------------------
    */
    $(document).on('keyup change blur', 'input[data-field="invoice_amount"], input[data-field="payment_received"]', function() {

        var df_id = $(this).data('df-id');

        var invoiceAmount = $('input[data-df-id="' + df_id + '"][data-field="invoice_amount"]').val();
        var paymentReceived = $('input[data-df-id="' + df_id + '"][data-field="payment_received"]').val();

        invoiceAmount = invoiceAmount.replace(/,/g, '');
        paymentReceived = paymentReceived.replace(/,/g, '');

        invoiceAmount = parseFloat(invoiceAmount) || 0;
        paymentReceived = parseFloat(paymentReceived) || 0;

        var balanceAmount = invoiceAmount - paymentReceived;

        var balanceInput = $('input[data-df-id="' + df_id + '"][data-field="balance_amount"]');

        balanceInput.val(balanceAmount.toFixed(2));
        balanceInput.closest('td').find('.export-value').text(balanceAmount.toFixed(2));
    });

    /*
    |--------------------------------------------------------------------------
    | Update Hidden Export Value On Every Edit
    |--------------------------------------------------------------------------
    */
    $(document).on('keyup change', '.inline-edit', function() {

        var input = $(this);
        var field = input.data('field');

        if (field == 'balance_amount') {
            return false;
        }

        if (input.is('select')) {
            input.closest('td').find('.export-value').text(input.find('option:selected').text().trim());
        } else {
            input.closest('td').find('.export-value').text(input.val());
        }
    });

    $(document).on('blur change', '.inline-edit', function() {

        var input = $(this);

        if (input.data('field') == 'balance_amount') {
            return false;
        }

        if (input.prop('readonly')) {
            return false;
        }

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
            url: "<?php echo page_url.'Machine/update_mcs_dispatch_report'; ?>",
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

                    if (input.is('select')) {
                        td.find('.export-value').text(input.find('option:selected').text().trim());
                    } else {
                        td.find('.export-value').text(input.val());
                    }

                    if (typeof response.balance_amount !== 'undefined') {
                        var balanceInput = $('input[data-df-id="' + df_id + '"][data-field="balance_amount"]');
                        balanceInput.val(response.balance_amount);
                        balanceInput.closest('td').find('.export-value').text(response.balance_amount);
                    }

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