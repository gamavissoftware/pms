<?php 
$CIA =& get_instance();
$CIA->load->model('Task_model');

$allowed_report_users = [61, 62, 162, 161, 139, 189, 114];
$current_user_id = $this->session->userdata['logged_in']['user_id'];

$company_q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
$LOGO = ($company_q->num_rows() > 0) ? $company_q->row() : null;
$themeColor = (!empty($LOGO->colorcode)) ? $LOGO->colorcode : '#4872b8';

function safeDatePurchaseForecast($date, $format = 'd-m-Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '';
    }

    return date($format, strtotime($date));
}

function safeNamePurchaseForecast($name)
{
    $name = trim((string)$name);

    if ($name == '') {
        return '-';
    }

    return ucwords(strtolower($name));
}

$reportid = $this->uri->segment(3);

$rowsData = array();
$exportRows = array();

$totalDf = 0;
$totalItems = 0;
$totalPurchasedItems = 0;
$totalPendingItems = 0;
$totalDueDf = 0;
$totalProgress = 0;

$todayDate = date('Y-m-d');

/*
|--------------------------------------------------------------------------
| Optimized Main Query
|--------------------------------------------------------------------------
| Department 13 = Purchase department task data
|--------------------------------------------------------------------------
*/
$this->db->select(
    "
    df.id,
    df.df_no,
    df.added_on,
    df.df_upload,
    df.on_hold,
    df.machine_id,

    po.podate,
    po.po_attachment,
    po.lead_id,
    po.id as po_id,
    po.df_number as po_df_number,

    CONCAT(u.title, ' ', u.first_name, ' ', u.last_name) as dfowner,
    u.user_id as owner_id,

    (
        SELECT DATE_SUB(t103.start_date, INTERVAL 1 MONTH)
        FROM task_department_wise_scheduling t103
        WHERE t103.df_id = df.id
        AND t103.taskid = 103
        AND t103.on_hold = 0
        ORDER BY t103.id DESC
        LIMIT 1
    ) as projected_completion_date,

    COUNT(DISTINCT tasks.id) as total_tasks,

    SUM(
        CASE 
            WHEN tasks.task_status = 1 
            THEN 1 
            ELSE 0 
        END
    ) as completed_tasks,

    (
        SELECT COUNT(DISTINCT id)
        FROM communication_ticket_system
        WHERE df_id = df.id
        AND ticket_status = 0
    ) as open_ticket_count,

    GROUP_CONCAT(
        CASE 
            WHEN tasks.task_status = 1
            THEN CONCAT(tasks.task_completed_on, '|', tasks.end_date)
            ELSE NULL
        END
    SEPARATOR ';') as completed_tasks_data,

    MAX(
        CASE 
            WHEN tasks.task_status = 1
            THEN tasks.task_completed_on
            ELSE NULL
        END
    ) as max_completion_date
    ",
    false
);

$this->db->from('df_release df');
$this->db->join('poreceived po', 'po.df_id = df.id', 'left');
$this->db->join('system_users u', 'po.added_by = u.user_id', 'left');
$this->db->join(
    'task_department_wise_scheduling tasks',
    'tasks.df_id = df.id 
    AND tasks.department_id = 13
    AND tasks.on_hold = 0',
    'inner'
);

$this->db->where('df.df_status', 0);
$this->db->where('df.on_hold', 0);
$this->db->group_by('df.id');
$this->db->order_by('df.id', 'DESC');

$query = $this->db->get();

if ($query->num_rows() > 0) {

    foreach ($query->result() as $rows) {

        $dfId = (int)$rows->id;

        $displayDfNo = $rows->df_no;

        if (empty($displayDfNo) || $displayDfNo == 0) {
            $displayDfNo = $rows->po_df_number;
        }

        $displayDfNo = strtoupper(trim($displayDfNo));

        $poDate = safeDatePurchaseForecast($rows->podate);
        $dfReleaseDate = safeDatePurchaseForecast($rows->added_on);
        $marketingPerson = safeNamePurchaseForecast($rows->dfowner);

        $projectedRaw = '';
        $projectedDisplay = 'No task found';
        $dueDays = 0;
        $dueStatus = 'On Time';

        if (!empty($rows->projected_completion_date) && $rows->projected_completion_date != '0000-00-00') {
            $projectedRaw = date('Y-m-d', strtotime($rows->projected_completion_date));
            $projectedDisplay = date('d-m-Y', strtotime($rows->projected_completion_date));

            if ($projectedRaw < $todayDate) {
                if (method_exists($CIA->Task_model, 'getDays')) {
                    $dueDays = (int)$CIA->Task_model->getDays($projectedRaw, $todayDate, 1);
                } else {
                    $dueDays = abs(round((strtotime($todayDate) - strtotime($projectedRaw)) / 86400));
                }

                $dueStatus = 'Due';
            }
        }

        $poAttachment = '';

        if (
            (int)$current_user_id == (int)$rows->owner_id &&
            !empty($rows->po_attachment)
        ) {
            $poAttachment = '
                <a href="'.sfdocument.'Taskdocument/'.$rows->po_attachment.'" download class="btn btn-info btn-xs btn-action">
                    <i class="fa fa-download"></i> PO
                </a>
            ';
        }

        $dfDownload = '';

        if (!empty($rows->df_upload)) {
            $dfDownload = '
                <a href="'.sfdocument.'Taskdocument/dfattachment/'.$rows->df_upload.'" download class="btn btn-primary btn-xs btn-action">
                    <i class="fa fa-download"></i> DF
                </a>
            ';
        }

        $completionPercentage = 0;

        if ((int)$rows->total_tasks > 0) {
            $completionPercentage = round(((int)$rows->completed_tasks * 100) / (int)$rows->total_tasks);
        }

        $delayCount = 0;
        $maxDelayDays = 0;

        if (!empty($rows->completed_tasks_data)) {

            $completedTasksList = explode(';', $rows->completed_tasks_data);

            foreach ($completedTasksList as $taskData) {

                $parts = explode('|', $taskData);

                if (count($parts) < 2) {
                    continue;
                }

                $completedOn = $parts[0];
                $endDate = $parts[1];

                if (
                    !empty($completedOn) &&
                    $completedOn != '0000-00-00' &&
                    $completedOn != '0000-00-00 00:00:00' &&
                    !empty($endDate) &&
                    $endDate != '0000-00-00'
                ) {
                    $completedDateObj = date('Y-m-d', strtotime($completedOn));

                    if ($completedDateObj > $endDate) {

                        $delayCount++;

                        if (method_exists($CIA->Task_model, 'getDays')) {
                            $delayDays = (int)$CIA->Task_model->getDays($endDate, $completedDateObj, 1);
                        } else {
                            $delayDays = abs(round((strtotime($completedDateObj) - strtotime($endDate)) / 86400));
                        }

                        if ($delayDays > $maxDelayDays) {
                            $maxDelayDays = $delayDays;
                        }
                    }
                }
            }
        }

        $delayPercentage = 0;

        if ((int)$rows->completed_tasks > 0) {
            $delayPercentage = round(($delayCount * 100) / (int)$rows->completed_tasks);
        }

        $show = 1;

        if ($reportid != '') {
            $show = ($delayPercentage > 0) ? 1 : 0;
        }

        if ($show != 1) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Bought Out Items
        |--------------------------------------------------------------------------
        */
        $quotation = $this->db
            ->select('id')
            ->from('quotation_customer_data')
            ->where('lead_id', $rows->lead_id)
            ->get()
            ->row();

        $items = array();
        $itemTotal = 0;
        $itemPurchased = 0;
        $itemPending = 0;
        $purchaseStatus = 'No Quotation';

        if (!empty($quotation)) {

            $botoutData = $this->db
                ->select('
                    qbd.id,
                    qphm.name as description,
                    qphmo.name as brand_name
                ')
                ->from('quotation_brand_data qbd')
                ->join('quote_parts_heading_master qphm', 'qphm.id = qbd.head_id', 'left')
                ->join('quote_parts_heading_master_options qphmo', 'qphmo.id = qbd.value_id', 'left')
                ->where('qbd.record_id', $quotation->id)
                ->get();

            if ($botoutData->num_rows() > 0) {

                $purchaseStatus = 'Pending';

                foreach ($botoutData->result() as $botout) {

                    $checkedData = $this->db
                        ->select("
                            qba.id,
                            qba.checked_on,
                            qba.checked_by,
                            CONCAT(su.first_name, ' ', su.last_name) as checked_by_name
                        ", false)
                        ->from('quotation_brand_action qba')
                        ->join('system_users su', 'su.user_id = qba.checked_by', 'left')
                        ->where('qba.quotation_brand_id', $botout->id)
                        ->get();

                    $isPurchased = false;
                    $checkedOn = '';
                    $checkedBy = '';

                    if ($checkedData->num_rows() > 0) {
                        $checkedRow = $checkedData->row();
                        $isPurchased = true;
                        $checkedOn = safeDatePurchaseForecast($checkedRow->checked_on, 'd-m-Y h:i A');
                        $checkedBy = safeNamePurchaseForecast($checkedRow->checked_by_name);
                    }

                    $itemTotal++;

                    if ($isPurchased) {
                        $itemPurchased++;
                    } else {
                        $itemPending++;
                    }

                    $items[] = array(
                        'id' => (int)$botout->id,
                        'description' => strtoupper(trim($botout->description)),
                        'brand_name' => strtoupper(trim($botout->brand_name)),
                        'is_purchased' => $isPurchased,
                        'checked_on' => $checkedOn,
                        'checked_by' => $checkedBy
                    );
                }

                if ($itemTotal > 0 && $itemPurchased == $itemTotal) {
                    $purchaseStatus = 'Completed';
                } elseif ($itemPurchased > 0) {
                    $purchaseStatus = 'Partial';
                }

            } else {
                $purchaseStatus = 'No Items';
            }
        }

        $purchasePercentage = 0;

        if ($itemTotal > 0) {
            $purchasePercentage = round(($itemPurchased / $itemTotal) * 100);
        }

        $totalDf++;
        $totalItems += $itemTotal;
        $totalPurchasedItems += $itemPurchased;
        $totalPendingItems += $itemPending;
        $totalProgress += $purchasePercentage;

        if ($dueStatus == 'Due') {
            $totalDueDf++;
        }

        $rowsData[] = array(
            'df_id' => $dfId,
            'df_no' => $displayDfNo,
            'df_download' => $dfDownload,
            'po_date' => $poDate,
            'po_attachment' => $poAttachment,
            'marketing_person' => $marketingPerson,
            'df_release_date' => $dfReleaseDate,
            'projected_display' => $projectedDisplay,
            'projected_raw' => $projectedRaw,
            'due_days' => $dueDays,
            'due_status' => $dueStatus,
            'total_tasks' => (int)$rows->total_tasks,
            'completed_tasks' => (int)$rows->completed_tasks,
            'purchase_percentage' => $purchasePercentage,
            'item_total' => $itemTotal,
            'item_purchased' => $itemPurchased,
            'item_pending' => $itemPending,
            'purchase_status' => $purchaseStatus,
            'open_ticket_count' => (int)$rows->open_ticket_count,
            'items' => $items
        );

        $exportRows[] = array(
            'type' => 'df',
            'df_no' => $displayDfNo,
            'po_date' => $poDate,
            'marketing_person' => $marketingPerson,
            'df_release_date' => $dfReleaseDate,
            'purchase_target_date' => $projectedDisplay,
            'item_type' => 'DF DETAILS',
            'description' => '',
            'brand_make' => '',
            'purchase_status' => $purchaseStatus,
            'purchased_on' => '',
            'checked_by' => ''
        );

        if (!empty($items)) {
            foreach ($items as $item) {
                $exportRows[] = array(
                    'type' => 'item',
                    'df_no' => '',
                    'po_date' => '',
                    'marketing_person' => '',
                    'df_release_date' => '',
                    'purchase_target_date' => '',
                    'item_type' => 'BOUGHTOUT ITEM',
                    'description' => $item['description'],
                    'brand_make' => $item['brand_name'],
                    'purchase_status' => $item['is_purchased'] ? 'Purchased' : 'Pending',
                    'purchased_on' => $item['checked_on'],
                    'checked_by' => $item['checked_by']
                );
            }
        } else {
            $exportRows[] = array(
                'type' => 'item',
                'df_no' => '',
                'po_date' => '',
                'marketing_person' => '',
                'df_release_date' => '',
                'purchase_target_date' => '',
                'item_type' => 'NO DATA',
                'description' => $purchaseStatus,
                'brand_make' => '',
                'purchase_status' => '',
                'purchased_on' => '',
                'checked_by' => ''
            );
        }
    }
}

$avgPurchaseProgress = ($totalDf > 0) ? round($totalProgress / $totalDf) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <title><?php echo sitetitle; ?> Purchase Forecasting for Running DF</title>

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

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

        .filter-card {
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

        .export-table {
            display: none;
        }

        .df-badge {
            background: #eef4ff;
            color: <?php echo $themeColor;?>;
            border: 1px solid rgba(72,114,184,0.18);
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 900;
            display: inline-block;
            white-space: nowrap;
        }

        .date-stack {
            min-width: 100px;
            font-weight: 800;
        }

        .date-muted {
            color: #6b7280;
            font-size: 11px;
            font-weight: 600;
            margin-top: 4px;
        }

        .owner-name {
            font-weight: 900;
            color: #111827;
            min-width: 170px;
        }

        .progress-wrap {
            min-width: 180px;
        }

        .progress {
            height: 9px;
            margin-bottom: 5px;
            background: #edf0f5;
            border-radius: 20px;
            box-shadow: none;
        }

        .progress-bar {
            border-radius: 20px;
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

        .boughtout-box {
            min-width: 430px;
            text-align: left;
        }

        .boughtout-table {
            width: 100%;
            margin-bottom: 0;
            background: #fff;
        }

        .boughtout-table th {
            background: #111827 !important;
            color: #fff !important;
            text-align: center !important;
            font-size: 11px !important;
        }

        .boughtout-table td {
            font-size: 11px !important;
            text-align: left !important;
            vertical-align: top !important;
        }

        .toggle-boughtout {
            border-radius: 20px;
            font-weight: 800;
            margin-top: 8px;
        }

        .summary-strip {
            background: #f8fafc;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 14px;
            color: #374151;
            font-weight: 700;
        }

        .dataTables_wrapper .dt-buttons .btn {
            border-radius: 20px !important;
            margin-right: 5px;
            font-weight: 800;
        }

        .dataTables_filter input,
        .dataTables_length select,
        .filter-card input,
        .filter-card select {
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
                            <h3 class="report-title">Purchase Forecasting for Running DF</h3>
                            <div class="report-subtitle">
                                DF-wise purchase target, bought-out item status, pending purchase visibility and due tracking.
                            </div>
                            <div class="report-pill">
                                <i class="fa fa-shopping-cart"></i> Purchase department forecasting report
                            </div>
                        </div>

                        <div class="col-md-4 text-right">
                            <div style="font-size:13px; opacity:.85;">Visible Running DF</div>
                            <div style="font-size:28px; font-weight:900; margin-top:5px;">
                                <?php echo $totalDf; ?> DF
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (in_array($current_user_id, $allowed_report_users)) { ?>
            <div class="filter-card" style="display:none;">
                <div class="filter-title">
                    <i class="fa fa-file-text-o"></i> Management Report Filter
                </div>

                <form method="post" action="<?php echo page_url; ?>Dashboard/management_report" class="form-inline" target="_blank">
                    <div class="form-group m-r-10">
                        <label for="start_date" class="m-r-10">From:</label>
                        <input type="text" class="form-control datepicker" name="start_date" id="start_date" placeholder="Start Date" required>
                    </div>

                    <div class="form-group m-r-10">
                        <label for="end_date" class="m-r-10">To:</label>
                        <input type="text" class="form-control datepicker" name="end_date" id="end_date" placeholder="End Date" required>
                    </div>

                    <button type="submit" class="btn btn-primary waves-effect waves-light" style="border-radius:20px; font-weight:800;">
                        <i class="fa fa-search"></i> Generate Report
                    </button>
                </form>
            </div>
        <?php } ?>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-info" style="border-radius:12px;">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-folder-open"></i></div>
                    <div class="kpi-label">Running DF</div>
                    <div class="kpi-value"><?php echo $totalDf; ?></div>
                    <div class="kpi-hint">DF having purchase tasks</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-list"></i></div>
                    <div class="kpi-label">Bought-Out Items</div>
                    <div class="kpi-value"><?php echo $totalItems; ?></div>
                    <div class="kpi-hint">Total items in visible DF</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-check-circle"></i></div>
                    <div class="kpi-label">Purchased</div>
                    <div class="kpi-value"><?php echo $totalPurchasedItems; ?></div>
                    <div class="kpi-hint"><?php echo $totalPendingItems; ?> items pending</div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-icon"><i class="fa fa-line-chart"></i></div>
                    <div class="kpi-label">Avg. Purchase Progress</div>
                    <div class="kpi-value"><?php echo $avgPurchaseProgress; ?>%</div>
                    <div class="kpi-hint"><?php echo $totalDueDf; ?> DF due for purchase target</div>
                </div>
            </div>
        </div>

        <div class="filter-card">
            <div class="filter-title">
                <i class="fa fa-filter"></i> Smart Filters
            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <label>Search</label>
                    <input type="text" id="customSearch" class="form-control" placeholder="Search DF, person, item...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label>Marketing Person</label>
                    <select id="ownerFilter" class="form-control">
                        <option value="">All Marketing Person</option>
                        <?php
                        $owners = array();

                        foreach ($rowsData as $row) {
                            if (!empty($row['marketing_person']) && $row['marketing_person'] != '-') {
                                $owners[$row['marketing_person']] = $row['marketing_person'];
                            }
                        }

                        if (!empty($owners)) {
                            ksort($owners);
                            foreach ($owners as $owner) {
                                echo '<option value="'.htmlspecialchars($owner).'">'.$owner.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Purchase Status</label>
                    <select id="purchaseStatusFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Completed">Completed</option>
                        <option value="Partial">Partial</option>
                        <option value="Pending">Pending</option>
                        <option value="No Items">No Items</option>
                        <option value="No Quotation">No Quotation</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label>Due Status</label>
                    <select id="dueStatusFilter" class="form-control">
                        <option value="">All</option>
                        <option value="Due">Due</option>
                        <option value="On Time">On Time</option>
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
            <div class="summary-strip">
                <i class="fa fa-info-circle"></i>
                Purchase target date is calculated as one month before dispatch task start date. Bought-out item progress is based on checked purchase actions.
            </div>

            <table id="example5" class="table manglesh table-striped table-bordered" width="100%">
                <thead>
                    <tr>
                        <th>S. No.</th>
                        <th>DF No.</th>
                        <th>Download</th>
                        <th>PO Date</th>
                        <th>Marketing Person</th>
                        <th>DF Release Date</th>
                        <th>Purchase Target Date</th>
                        <th>Bought-Out Items</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($rowsData)) { ?>
                        <?php $m = 1; foreach ($rowsData as $row) {

                            $progressClass = 'progress-bar-success';

                            if ($row['purchase_percentage'] < 50) {
                                $progressClass = 'progress-bar-warning';
                            }

                            if ($row['purchase_percentage'] == 0) {
                                $progressClass = 'progress-bar-danger';
                            }

                            $statusClass = 'pill-warning';

                            if ($row['purchase_status'] == 'Completed') {
                                $statusClass = 'pill-success';
                            } elseif ($row['purchase_status'] == 'No Items' || $row['purchase_status'] == 'No Quotation') {
                                $statusClass = 'pill-danger';
                            }

                            $dueClass = ($row['due_status'] == 'Due') ? 'pill-danger' : 'pill-success';
                        ?>
                            <tr
                                data-owner="<?php echo htmlspecialchars($row['marketing_person']); ?>"
                                data-purchase-status="<?php echo $row['purchase_status']; ?>"
                                data-due-status="<?php echo $row['due_status']; ?>"
                            >
                                <td><?php echo $m; ?></td>

                                <td>
                                    <span class="df-badge">
                                        <?php echo $row['df_no']; ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo $row['df_download']; ?>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['po_date']) ? $row['po_date'] : '-'; ?>
                                    </div>
                                    <?php echo $row['po_attachment']; ?>
                                </td>

                                <td>
                                    <div class="owner-name">
                                        <?php echo strtoupper($row['marketing_person']); ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo !empty($row['df_release_date']) ? $row['df_release_date'] : '-'; ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="date-stack">
                                        <?php echo $row['projected_display']; ?>
                                    </div>

                                    <div style="margin-top:7px;">
                                        <span class="status-pill <?php echo $dueClass; ?>">
                                            <?php echo $row['due_status']; ?>
                                        </span>
                                    </div>

                                    <?php if ($row['due_days'] > 0) { ?>
                                        <div class="date-muted">
                                            Due for <?php echo $row['due_days']; ?> days
                                        </div>
                                    <?php } ?>
                                </td>

                                <td>
                                    <div class="boughtout-box">

                                        <div class="row" style="margin-bottom:8px;">
                                            <div class="col-xs-6">
                                                <span class="status-pill <?php echo $statusClass; ?>">
                                                    <?php echo $row['purchase_status']; ?>
                                                </span>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <strong>
                                                    <?php echo $row['item_purchased']; ?>/<?php echo $row['item_total']; ?>
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="progress-wrap">
                                            <div class="progress">
                                                <div class="progress-bar <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo $row['purchase_percentage']; ?>%;"></div>
                                            </div>
                                            <strong><?php echo $row['purchase_percentage']; ?>%</strong>
                                            <div class="date-muted">
                                                <?php echo $row['item_pending']; ?> pending item(s)
                                            </div>
                                        </div>

                                        <?php if (!empty($row['items'])) { ?>
                                            <?php $collapseId = 'boughtout_'.$row['df_id']; ?>

                                            <button
                                                type="button"
                                                class="btn btn-info btn-xs toggle-boughtout"
                                                data-target="<?php echo $collapseId; ?>"
                                            >
                                                Show Bought-Out Items
                                            </button>

                                            <div id="<?php echo $collapseId; ?>" style="display:none; margin-top:10px;">
                                                <table class="table table-bordered table-striped boughtout-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:55px;">S. No.</th>
                                                            <th>Description</th>
                                                            <th>Brand / Make</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php $b = 1; foreach ($row['items'] as $item) { ?>
                                                            <tr>
                                                                <td><?php echo $b; ?></td>

                                                                <td><?php echo $item['description']; ?></td>

                                                                <td><?php echo $item['brand_name']; ?></td>

                                                                <td>
                                                                    <?php if ($item['is_purchased']) { ?>
                                                                        <span class="status-pill pill-success">Purchased</span>
                                                                        <div class="date-muted">
                                                                            <?php echo $item['checked_on']; ?>
                                                                        </div>
                                                                        <div class="date-muted">
                                                                            Checked By:
                                                                            <strong><?php echo $item['checked_by']; ?></strong>
                                                                        </div>
                                                                    <?php } else { ?>
                                                                        <div id="purchase_area_<?php echo $item['id']; ?>">
                                                                            <label style="font-weight:800; cursor:pointer;">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    class="brand_checkbox"
                                                                                    data-id="<?php echo $item['id']; ?>"
                                                                                >
                                                                                Mark Purchased
                                                                            </label>
                                                                        </div>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                        <?php $b++; } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <div style="margin-top:10px;">
                                                <span class="status-pill pill-danger">
                                                    <?php echo $row['purchase_status']; ?>
                                                </span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php $m++; } ?>
                    <?php } ?>
                </tbody>
            </table>

            <table id="export_table" class="table export-table">
                <thead>
                    <tr>
                        <th>S. No.</th>
                        <th>DF No.</th>
                        <th>PO Date</th>
                        <th>Marketing Person</th>
                        <th>DF Release Date</th>
                        <th>Purchase Target Date</th>
                        <th>Item Type</th>
                        <th>Description</th>
                        <th>Brand / Make</th>
                        <th>Purchase Status</th>
                        <th>Purchased On</th>
                        <th>Checked By</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $ex = 1; foreach ($exportRows as $exportRow) { ?>
                        <tr>
                            <td><?php echo ($exportRow['type'] == 'df') ? $ex : ''; ?></td>
                            <td><?php echo $exportRow['df_no']; ?></td>
                            <td><?php echo $exportRow['po_date']; ?></td>
                            <td><?php echo $exportRow['marketing_person']; ?></td>
                            <td><?php echo $exportRow['df_release_date']; ?></td>
                            <td><?php echo $exportRow['purchase_target_date']; ?></td>
                            <td><?php echo $exportRow['item_type']; ?></td>
                            <td><?php echo $exportRow['description']; ?></td>
                            <td><?php echo $exportRow['brand_make']; ?></td>
                            <td><?php echo $exportRow['purchase_status']; ?></td>
                            <td><?php echo $exportRow['purchased_on']; ?></td>
                            <td><?php echo $exportRow['checked_by']; ?></td>
                        </tr>
                    <?php if ($exportRow['type'] == 'df') { $ex++; } } ?>
                </tbody>
            </table>
        </div>

        <?php $this->load->view('common/footer');?>

    </div>
</div>

<div class="modal fade" id="ticket-modal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="ticketModalLabel">Open Tickets</h4>
            </div>

            <div class="modal-body">
                <div id="ticket-modal-loader" style="text-align:center; padding:30px;">
                    <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                    <p>Loading tickets...</p>
                </div>

                <div id="ticket-modal-content" style="max-height:60vh; overflow-y:auto;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" style="border-radius:20px; font-weight:800;">
                    Close
                </button>
            </div>

        </div>
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

<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

<script>
function stripHtmlPurchaseForecast(html) {
    if (html === null || html === undefined) {
        return '';
    }

    var div = document.createElement('div');
    div.innerHTML = html;

    return div.textContent || div.innerText || '';
}

$(document).ready(function () {

    $('.datepicker').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
    });

    var mainTable = $('#example5').DataTable({
        processing: true,
        fixedHeader: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 250, 500, -1], [25, 50, 100, 250, 500, 'All']],
        responsive: false,
        scrollX: true,
        ordering: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                title: 'Purchase Forecasting Report',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            return stripHtmlPurchaseForecast(data);
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                title: 'Purchase Forecasting Report',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            return stripHtmlPurchaseForecast(data);
                        }
                    }
                }
            }
        ]
    });

    $('#export_table').DataTable({
        destroy: true,
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Hidden Export',
                title: 'Purchase Forecasting Detailed Report'
            }
        ]
    });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'example5') {
            return true;
        }

        var rowNode = mainTable.row(dataIndex).node();

        var ownerFilter = $('#ownerFilter').val();
        var purchaseStatusFilter = $('#purchaseStatusFilter').val();
        var dueStatusFilter = $('#dueStatusFilter').val();

        var rowOwner = $(rowNode).data('owner');
        var rowPurchaseStatus = $(rowNode).data('purchase-status');
        var rowDueStatus = $(rowNode).data('due-status');

        if (ownerFilter !== '' && rowOwner !== ownerFilter) {
            return false;
        }

        if (purchaseStatusFilter !== '' && rowPurchaseStatus !== purchaseStatusFilter) {
            return false;
        }

        if (dueStatusFilter !== '' && rowDueStatus !== dueStatusFilter) {
            return false;
        }

        return true;
    });

    $('#customSearch').on('keyup change', function () {
        mainTable.search(this.value).draw();
    });

    $('#ownerFilter, #purchaseStatusFilter, #dueStatusFilter').on('change', function () {
        mainTable.draw();
    });

    $('#resetFilters').on('click', function () {
        $('#customSearch').val('');
        $('#ownerFilter').val('');
        $('#purchaseStatusFilter').val('');
        $('#dueStatusFilter').val('');

        mainTable.search('');
        mainTable.columns().search('');
        mainTable.draw();
    });

    $('.wrapper').on('click', '.btn-view-tickets', function() {
        var $modal = $('#ticket-modal');
        var $loader = $('#ticket-modal-loader');
        var $content = $('#ticket-modal-content');

        var dfId = $(this).data('df-id');
        var dfNo = $(this).data('df-no');

        $modal.find('#ticketModalLabel').text('Open Tickets for DF: ' + dfNo);
        $content.empty();
        $loader.show();
        $modal.modal('show');

        $.ajax({
            url: '<?php echo page_url; ?>Task/ajax_get_df_tickets/' + dfId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $loader.hide();

                if (response.success && response.tickets.length > 0) {
                    var html = '';

                    $.each(response.tickets, function(index, ticket) {
                        html += '<div style="border-bottom:1px solid #eee; padding:12px 0;">';
                        html += '  <div style="color:#337ab7; font-weight:bold;">Task: ' + (ticket.task_name || 'N/A') + '</div>';
                        html += '  <div style="font-size:12px; color:#777;"><strong>Ticket #:</strong> ' + ticket.help_ticket_no + '</div>';
                        html += '  <div style="font-size:12px; color:#777;"><strong>By:</strong> ' + ticket.added_by_name + ' | <strong>On:</strong> ' + ticket.added_date + '</div>';
                        html += '  <div style="margin-top:6px; padding-left:10px; border-left:3px solid #f0ad4e;">' + ticket.remarks + '</div>';
                        html += '</div>';
                    });

                    $content.html(html);
                } else {
                    $content.html('<div class="alert alert-info">No open tickets found for this DF.</div>');
                }
            },
            error: function() {
                $loader.hide();
                $content.html('<div class="alert alert-danger">Error loading ticket details.</div>');
            }
        });
    });

});

$(document).on('change', '.brand_checkbox', function () {

    var checkbox = $(this);
    var quotationBrandId = checkbox.data('id');

    if (checkbox.is(':checked')) {

        $.ajax({
            url: "<?php echo page_url;?>Task/save_brand_action",
            type: "POST",
            data: {
                quotation_brand_id: quotationBrandId
            },
            success: function () {

                var currentDate = new Date();

                var day = ("0" + currentDate.getDate()).slice(-2);
                var month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
                var year = currentDate.getFullYear();

                var hours = currentDate.getHours();
                var minutes = ("0" + currentDate.getMinutes()).slice(-2);

                var ampm = hours >= 12 ? 'PM' : 'AM';

                hours = hours % 12;
                hours = hours ? hours : 12;

                var formattedDate =
                    day + '-' + month + '-' + year + ' ' +
                    hours + ':' + minutes + ' ' + ampm;

                $('#purchase_area_' + quotationBrandId).html(
                    '<span class="status-pill pill-success">Purchased</span>' +
                    '<div class="date-muted">' + formattedDate + '</div>'
                );
            },
            error: function () {
                alert('Unable to save purchase action. Please try again.');
                checkbox.prop('checked', false);
            }
        });

    }
});

$(document).on('click', '.toggle-boughtout', function () {

    var target = $(this).data('target');

    $('#' + target).slideToggle(200);

    if ($.trim($(this).text()) === 'Show Bought-Out Items') {
        $(this).html('<i class="fa fa-eye-slash"></i> Hide Bought-Out Items');
    } else {
        $(this).html('<i class="fa fa-eye"></i> Show Bought-Out Items');
    }

});
</script>

</body>
</html>