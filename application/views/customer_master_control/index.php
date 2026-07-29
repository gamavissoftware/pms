<?php
if (!function_exists('customerMasterSafe')) {
    function customerMasterSafe($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('customerMasterDate')) {
    function customerMasterDate($value, $with_time = true)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false || $timestamp <= 0) {
            return '-';
        }

        return $with_time ? date('d M Y, h:i A', $timestamp) : date('d M Y', $timestamp);
    }
}

if (!function_exists('customerMasterStatusMeta')) {
    function customerMasterStatusMeta($status)
    {
        return ((int) $status === 1)
            ? array('label' => 'Active', 'class' => 'label-success')
            : array('label' => 'Inactive', 'class' => 'label-danger');
    }
}

if (!function_exists('customerMasterOrderDate')) {
    function customerMasterOrderDate($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00 00:00:00' || $value === '0000-00-00') {
            return '';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false || $timestamp <= 0) {
            return '';
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}

$customer_master_read_only = isset($this->master_profile_guard) && $this->master_profile_guard->is_master_read_only();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Customer Master Control</title>

    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body {
            background: #eef3f9;
        }

        .panel-card {
            background: #fff;
            border: 1px solid #d8e1ec;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            margin-bottom: 22px;
            overflow: hidden;
        }

        .panel-head {
            padding: 18px 22px;
            border-bottom: 1px solid #e7edf4;
            background: #f7fbff;
        }

        .panel-head h4 {
            margin: 0;
            color: #17365d;
            font-size: 18px;
            font-weight: 700;
        }

        .panel-head p {
            margin: 6px 0 0;
            color: #64748b;
        }

        .panel-body {
            padding: 20px;
        }

        .filter-grid .form-group {
            margin-bottom: 16px;
        }

        .table > thead > tr > th {
            background: #17365d;
            color: #fff;
            border-color: #17365d !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        .source-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .source-badge.marketing {
            background: #e0f2fe;
            color: #075985;
        }

        .source-badge.spares {
            background: #dcfce7;
            color: #166534;
        }

        .company-name {
            font-weight: 700;
            color: #17365d;
        }

        .company-subline {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
        }

        .helper-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 34px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }

        .action-btn {
            min-width: 92px;
        }

        .muted-line {
            display: block;
            color: #94a3b8;
            font-size: 11px;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title text-center">Customer Master Control</h4>
                    </div>
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            </div>

            <?php if (!$module_ready) { ?>
                <div class="alert alert-warning">
                    <strong>History support pending:</strong> run <code><?php echo customerMasterSafe($migration_file); ?></code> to capture edit history from this module. Listing and updates will still work safely without it.
                </div>
            <?php } ?>

            <div class="panel-card">
                <div class="panel-head">
                    <h4>Unified customer list</h4>
                    <p>Marketing records come from <code>customer_detail</code> and spares records come from <code>spares_customers</code>. Use the filters below, then open any row to review and update customer information.</p>
                </div>
                <div class="panel-body">
                    <div class="helper-note">
                        Loaded <strong><?php echo count($customers); ?></strong> customer records in this view.
                        Marketing: <strong><?php echo (int) $source_counts['marketing']; ?></strong>.
                        Spares: <strong><?php echo (int) $source_counts['spares']; ?></strong>.
                    </div>

                    <form method="get" action="<?php echo page_url; ?>Customer_master_control">
                        <div class="row filter-grid">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Source</label>
                                    <select name="source" class="form-control customer-master-select">
                                        <option value="">All</option>
                                        <option value="marketing" <?php echo isset($filters['source']) && $filters['source'] === 'marketing' ? 'selected' : ''; ?>>Marketing</option>
                                        <option value="spares" <?php echo isset($filters['source']) && $filters['source'] === 'spares' ? 'selected' : ''; ?>>Spares</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control customer-master-select">
                                        <option value="">All</option>
                                        <option value="1" <?php echo isset($filters['status']) && $filters['status'] === '1' ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo isset($filters['status']) && $filters['status'] === '0' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label>Country</label>
                                    <select name="country_id" class="form-control customer-master-select">
                                        <option value="0">All</option>
                                        <?php foreach ($country_options as $country_option) { ?>
                                            <option value="<?php echo (int) $country_option['country_id']; ?>" <?php echo !empty($filters['country_id']) && (int) $filters['country_id'] === (int) $country_option['country_id'] ? 'selected' : ''; ?>>
                                                <?php echo customerMasterSafe($country_option['country_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label>Marketing Owner</label>
                                    <select name="marketing_user_id" class="form-control customer-master-select">
                                        <option value="0">All</option>
                                        <?php foreach ($marketing_user_options as $user_option) { ?>
                                            <?php $user_name = trim($user_option['title'] . ' ' . $user_option['first_name'] . ' ' . $user_option['last_name']); ?>
                                            <option value="<?php echo (int) $user_option['user_id']; ?>" <?php echo !empty($filters['marketing_user_id']) && (int) $filters['marketing_user_id'] === (int) $user_option['user_id'] ? 'selected' : ''; ?>>
                                                <?php echo customerMasterSafe($user_name); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="<?php echo customerMasterSafe(isset($filters['from_date']) ? $filters['from_date'] : ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="<?php echo customerMasterSafe(isset($filters['to_date']) ? $filters['to_date'] : ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Keyword</label>
                                    <input type="text" name="keyword" class="form-control" placeholder="Company, contact, email, GST, tax number, phone" value="<?php echo customerMasterSafe(isset($filters['keyword']) ? $filters['keyword'] : ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i> Apply Filters
                                </button>
                                <a href="<?php echo page_url; ?>Customer_master_control" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head">
                    <h4>Customer records</h4>
                    <p><?php echo $customer_master_read_only ? 'This EA profile can review customer master records here.' : 'Use <strong>Manage</strong> to open the source-aware edit screen for a specific customer.'; ?></p>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="customerMasterTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th>Company</th>
                                    <th>Primary Contact</th>
                                    <th>Brand</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Last Update</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer) { ?>
                                    <?php $status_meta = customerMasterStatusMeta(isset($customer['status']) ? $customer['status'] : 0); ?>
                                    <tr>
                                        <td>
                                            <span class="source-badge <?php echo customerMasterSafe($customer['source_type']); ?>">
                                                <?php echo customerMasterSafe($customer['source_label']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="company-name"><?php echo customerMasterSafe($customer['company_name']); ?></span>
                                            <?php if (!empty($customer['record_code'])) { ?>
                                                <span class="company-subline">Ref No: <?php echo customerMasterSafe($customer['record_code']); ?></span>
                                            <?php } ?>
                                            <?php if (!empty($customer['additional_contact_count'])) { ?>
                                                <span class="company-subline">Additional contacts: <?php echo (int) $customer['additional_contact_count']; ?></span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php echo customerMasterSafe($customer['contact_person'] !== '' ? $customer['contact_person'] : '-'); ?>
                                            <span class="company-subline"><?php echo customerMasterSafe($customer['contact_no'] !== '' ? $customer['contact_no'] : '-'); ?></span>
                                            <span class="company-subline"><?php echo customerMasterSafe($customer['email'] !== '' ? $customer['email'] : '-'); ?></span>
                                        </td>
                                        <td><?php echo customerMasterSafe($customer['brand_name'] !== '' ? $customer['brand_name'] : '-'); ?></td>
                                        <td><?php echo customerMasterSafe($customer['location_summary'] !== '' ? $customer['location_summary'] : '-'); ?></td>
                                        <td>
                                            <span class="label <?php echo customerMasterSafe($status_meta['class']); ?>">
                                                <?php echo customerMasterSafe($status_meta['label']); ?>
                                            </span>
                                        </td>
                                        <td data-order="<?php echo customerMasterSafe(customerMasterOrderDate(isset($customer['created_on']) ? $customer['created_on'] : '')); ?>">
                                            <?php echo customerMasterDate(isset($customer['created_on']) ? $customer['created_on'] : ''); ?>
                                            <span class="muted-line">
                                                <?php echo customerMasterSafe(!empty($customer['created_by_name']) ? $customer['created_by_name'] : 'Not captured in source'); ?>
                                            </span>
                                        </td>
                                        <td data-order="<?php echo customerMasterSafe(customerMasterOrderDate(isset($customer['last_action_on']) ? $customer['last_action_on'] : '')); ?>">
                                            <?php echo customerMasterDate(isset($customer['last_action_on']) ? $customer['last_action_on'] : ''); ?>
                                            <span class="muted-line">
                                                <?php echo customerMasterSafe(!empty($customer['last_action_by_name']) ? $customer['last_action_by_name'] : 'Not captured'); ?>
                                            </span>
                                            <?php if (!empty($customer['history_changed_fields'])) { ?>
                                                <span class="muted-line"><?php echo customerMasterSafe(implode(', ', $customer['history_changed_fields'])); ?></span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($customer_master_read_only) { ?>
                                                <span class="btn btn-default btn-sm action-btn disabled">View Only</span>
                                            <?php } else { ?>
                                                <a href="<?php echo page_url; ?>Customer_master_control/edit/<?php echo customerMasterSafe($customer['source_type']); ?>/<?php echo (int) $customer['record_id']; ?>" class="btn btn-primary btn-sm action-btn">
                                                    <i class="fa fa-pencil"></i> Manage
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function () {
        $('.customer-master-select').select2();

        $('#customerMasterTable').DataTable({
            responsive: true,
            pageLength: 50,
            order: [],
            dom: 'lBfrtip',
            buttons: ['excel', 'print'],
            columnDefs: [
                { orderable: false, targets: [8] }
            ]
        });
    });
    </script>
</body>
</html>
