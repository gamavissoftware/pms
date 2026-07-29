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

if (!function_exists('customerMasterDatetimeInput')) {
    function customerMasterDatetimeInput($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00 00:00:00') {
            return '';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false || $timestamp <= 0) {
            return '';
        }

        return date('Y-m-d\TH:i', $timestamp);
    }
}

if (!function_exists('customerMasterActionLabel')) {
    function customerMasterActionLabel($value)
    {
        $value = strtoupper(trim((string) $value));
        if ($value === 'UPDATED_CUSTOMER') {
            return 'Customer Updated';
        }

        return ucwords(strtolower(str_replace('_', ' ', $value)));
    }
}

$status_meta = customerMasterStatusMeta(isset($customer['status']) ? $customer['status'] : 0);
$current_history = !empty($history) ? $history[0] : array();
$history_changed_fields = !empty($current_history['changed_fields_list']) ? implode(', ', $current_history['changed_fields_list']) : '';
$marketing_brand_value = isset($customer['company_brand']) ? trim((string) $customer['company_brand']) : '';
$marketing_brand_label = isset($customer['brand_name']) && trim((string) $customer['brand_name']) !== ''
    ? trim((string) $customer['brand_name'])
    : $marketing_brand_value;
$spares_brand_value = isset($customer['brand_id']) ? trim((string) $customer['brand_id']) : '';
$spares_brand_label = isset($customer['brand_name']) && trim((string) $customer['brand_name']) !== ''
    ? trim((string) $customer['brand_name'])
    : $spares_brand_value;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Customer Master Edit</title>

    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
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

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .summary-card {
            background: #f8fbff;
            border: 1px solid #dce7f2;
            border-radius: 16px;
            padding: 16px 18px;
            min-height: 120px;
        }

        .summary-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        .summary-value {
            margin-top: 8px;
            color: #17365d;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.5;
        }

        .summary-meta {
            margin-top: 8px;
            color: #475569;
            line-height: 1.6;
        }

        .form-section {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 18px;
            background: #fbfdff;
        }

        .form-section h5 {
            margin: 0 0 14px;
            color: #17365d;
            font-size: 16px;
            font-weight: 700;
        }

        .form-group label {
            font-weight: 600;
            color: #334155;
        }

        .contact-row {
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 14px;
            background: #fff;
        }

        .contact-row:last-child {
            margin-bottom: 0;
        }

        .contact-toolbar {
            margin-bottom: 14px;
        }

        .helper-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .history-table th {
            background: #17365d;
            color: #fff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single,
        .select2-container .select2-selection--multiple {
            min-height: 34px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 34px;
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
                        <div class="btn-group pull-right">
                            <a href="<?php echo page_url; ?>Customer_master_control" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back To List
                            </a>
                        </div>
                        <h4 class="page-title text-center">Customer Master Edit</h4>
                    </div>
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            </div>

            <?php if (!$module_ready) { ?>
                <div class="alert alert-warning">
                    <strong>History support pending:</strong> run <code><?php echo customerMasterSafe($migration_file); ?></code> to start logging edits from this module. The customer update form is still active.
                </div>
            <?php } ?>

            <div class="panel-card">
                <div class="panel-head">
                    <h4><?php echo customerMasterSafe($customer['company_name']); ?></h4>
                    <p>
                        Source:
                        <span class="label <?php echo customerMasterSafe($status_meta['class']); ?>"><?php echo customerMasterSafe($status_meta['label']); ?></span>
                        <span style="margin-left:8px;" class="label label-info"><?php echo customerMasterSafe($customer['source_label']); ?></span>
                    </p>
                </div>
                <div class="panel-body">
                    <div class="summary-grid">
                        <div class="summary-card">
                            <div class="summary-label">Source Record</div>
                            <div class="summary-value">
                                <?php if ($source === 'marketing') { ?>
                                    ID <?php echo (int) $customer['id']; ?><?php echo !empty($customer['customer_ref_no']) ? ' / Ref ' . customerMasterSafe($customer['customer_ref_no']) : ''; ?>
                                <?php } else { ?>
                                    Customer ID <?php echo (int) $customer['customer_id']; ?>
                                <?php } ?>
                            </div>
                            <div class="summary-meta">
                                Brand: <?php echo customerMasterSafe(($source === 'marketing' ? $marketing_brand_label : $spares_brand_label) !== '' ? ($source === 'marketing' ? $marketing_brand_label : $spares_brand_label) : '-'); ?>
                            </div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Created</div>
                            <div class="summary-value">
                                <?php echo customerMasterDate($source === 'marketing' ? $customer['added_on'] : $customer['created_at']); ?>
                            </div>
                            <div class="summary-meta">
                                <?php if ($source === 'marketing') { ?>
                                    By: <?php echo customerMasterSafe($customer['created_by_name'] !== '' ? $customer['created_by_name'] : 'Not captured'); ?>
                                <?php } else { ?>
                                    Creator is not stored in <code>spares_customers</code>.
                                <?php } ?>
                            </div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Latest Update</div>
                            <div class="summary-value">
                                <?php
                                if (!empty($current_history['changed_on'])) {
                                    echo customerMasterDate($current_history['changed_on']);
                                } elseif ($source === 'marketing' && !empty($customer['updated_on']) && $customer['updated_on'] !== '0000-00-00 00:00:00') {
                                    echo customerMasterDate($customer['updated_on']);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </div>
                            <div class="summary-meta">
                                <?php
                                if (!empty($current_history['changed_by_name'])) {
                                    echo customerMasterSafe($current_history['changed_by_name']);
                                } elseif ($source === 'marketing' && !empty($customer['updated_by_name'])) {
                                    echo customerMasterSafe($customer['updated_by_name']);
                                } else {
                                    echo 'No captured update yet';
                                }
                                ?>
                                <?php if ($history_changed_fields !== '') { ?>
                                    <br><?php echo customerMasterSafe($history_changed_fields); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head">
                    <h4>Edit customer information</h4>
                    <p>Update the source record below. Search is enabled on the dropdowns so large user, country, brand, and shipping lists stay easy to handle.</p>
                </div>
                <div class="panel-body">
                    <div class="helper-note">
                        Use the <strong>Change Note</strong> field at the bottom whenever you want the next person to understand why this customer was updated.
                    </div>

                    <form method="post" action="<?php echo page_url; ?>Customer_master_control/update/<?php echo customerMasterSafe($source); ?>/<?php echo (int) ($source === 'marketing' ? $customer['id'] : $customer['customer_id']); ?>">
                        <?php if ($source === 'marketing') { ?>
                            <div class="form-section">
                                <h5>Basic Information</h5>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Company Link</label>
                                            <input type="number" name="company_id" class="form-control" value="<?php echo customerMasterSafe($customer['company_id']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Customer Ref No</label>
                                            <input type="number" name="customer_ref_no" class="form-control" value="<?php echo customerMasterSafe($customer['customer_ref_no']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <select name="title" class="form-control customer-master-select">
                                                <option value="">Select</option>
                                                <?php foreach (array('Mr.', 'Mrs.', 'Miss.', 'Ms.', 'MR.', 'MRS.') as $title_option) { ?>
                                                    <option value="<?php echo customerMasterSafe($title_option); ?>" <?php echo trim((string) $customer['title']) === $title_option ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($title_option); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Company Name <span style="color:red;">*</span></label>
                                            <input type="text" name="company_name" class="form-control" value="<?php echo customerMasterSafe($customer['company_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Customer Alias</label>
                                            <input type="text" name="customer_alias" class="form-control" value="<?php echo customerMasterSafe($customer['customer_alias']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Primary Contact</label>
                                            <input type="text" name="customer_name" class="form-control" value="<?php echo customerMasterSafe($customer['customer_name']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Customer Designation</label>
                                            <input type="text" name="customer_designation" class="form-control" value="<?php echo customerMasterSafe($customer['customer_designation']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Brand</label>
                                            <select name="company_brand" class="form-control customer-master-brand-select">
                                                <option value="">Select or type brand</option>
                                                <?php if ($marketing_brand_value !== '') { ?>
                                                    <option value="<?php echo customerMasterSafe($marketing_brand_value); ?>" selected="selected">
                                                        <?php echo customerMasterSafe($marketing_brand_label); ?>
                                                    </option>
                                                <?php } ?>
                                                <?php foreach ($marketing_brand_options as $brand_option) { ?>
                                                    <?php if ((string) $brand_option['id'] === $marketing_brand_value) { continue; } ?>
                                                    <option value="<?php echo (int) $brand_option['id']; ?>">
                                                        <?php echo customerMasterSafe($brand_option['name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="text" name="email" class="form-control" value="<?php echo customerMasterSafe($customer['email']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Primary Contact Designation</label>
                                            <input type="text" name="designation" class="form-control" value="<?php echo customerMasterSafe($customer['designation']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Branch Location</label>
                                            <input type="text" name="branchlocation" class="form-control" value="<?php echo customerMasterSafe($customer['branchlocation']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Contact No</label>
                                            <input type="text" name="contact_no" class="form-control" value="<?php echo customerMasterSafe($customer['contact_no']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Alternate Contact No</label>
                                            <input type="text" name="alt_contact" class="form-control" value="<?php echo customerMasterSafe($customer['alt_contact']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control customer-master-select">
                                                <option value="1" <?php echo (int) $customer['status'] === 1 ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo (int) $customer['status'] === 0 ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h5>Address And Tax</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="country" id="customer_country" class="form-control customer-master-select">
                                                <option value="0">Select Country</option>
                                                <?php foreach ($country_options as $country_option) { ?>
                                                    <option value="<?php echo (int) $country_option['country_id']; ?>" <?php echo (int) $customer['country'] === (int) $country_option['country_id'] ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($country_option['country_name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>State</label>
                                            <select name="state" id="customer_state" class="form-control customer-master-state-select">
                                                <option value="0">Select State</option>
                                                <?php foreach ($state_options as $state_option) { ?>
                                                    <option value="<?php echo (int) $state_option['state_id']; ?>" <?php echo (int) $customer['state'] === (int) $state_option['state_id'] ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($state_option['state_name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>City</label>
                                            <input type="text" name="city" class="form-control" value="<?php echo customerMasterSafe($customer['city']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Pincode</label>
                                            <input type="text" name="pincode" class="form-control" value="<?php echo customerMasterSafe($customer['pincode']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>GST</label>
                                            <input type="text" name="gst" class="form-control" value="<?php echo customerMasterSafe($customer['gst']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>PAN</label>
                                            <input type="text" name="pan" class="form-control" value="<?php echo customerMasterSafe($customer['pan']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Primary Address</label>
                                            <textarea name="address" class="form-control" rows="3"><?php echo customerMasterSafe($customer['address']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>MSME Number</label>
                                            <input type="text" name="msme_number" class="form-control" value="<?php echo customerMasterSafe($customer['msme_number']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h5>Billing And Shipping</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Billing Address</label>
                                            <textarea name="bill_address" class="form-control" rows="3"><?php echo customerMasterSafe($customer['bill_address']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Shipping Address</label>
                                            <textarea name="ship_address" class="form-control" rows="3"><?php echo customerMasterSafe($customer['ship_address']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Billing State</label>
                                            <select name="bill_state" id="bill_state" class="form-control customer-master-state-select">
                                                <option value="0">Select State</option>
                                                <?php foreach ($state_options as $state_option) { ?>
                                                    <option value="<?php echo (int) $state_option['state_id']; ?>" <?php echo (int) $customer['bill_state'] === (int) $state_option['state_id'] ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($state_option['state_name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Billing City</label>
                                            <input type="text" name="bill_city" class="form-control" value="<?php echo customerMasterSafe($customer['bill_city']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Billing Pincode</label>
                                            <input type="text" name="bill_pincode" class="form-control" value="<?php echo customerMasterSafe($customer['bill_pincode']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Billing Email</label>
                                            <input type="text" name="bill_email" class="form-control" value="<?php echo customerMasterSafe($customer['bill_email']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Shipping State</label>
                                            <select name="ship_state" id="ship_state" class="form-control customer-master-state-select">
                                                <option value="0">Select State</option>
                                                <?php foreach ($state_options as $state_option) { ?>
                                                    <option value="<?php echo (int) $state_option['state_id']; ?>" <?php echo (int) $customer['ship_state'] === (int) $state_option['state_id'] ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($state_option['state_name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Shipping City</label>
                                            <input type="text" name="ship_city" class="form-control" value="<?php echo customerMasterSafe($customer['ship_city']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Shipping Pincode</label>
                                            <input type="text" name="ship_pincode" class="form-control" value="<?php echo customerMasterSafe($customer['ship_pincode']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Shipping Email</label>
                                            <input type="text" name="ship_email" class="form-control" value="<?php echo customerMasterSafe($customer['ship_email']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h5>Commercial And System Flags</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Credit Period</label>
                                            <input type="number" name="credit_period" class="form-control" value="<?php echo customerMasterSafe($customer['credit_period']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Credit Limit</label>
                                            <input type="number" step="0.01" name="credit_limit" class="form-control" value="<?php echo customerMasterSafe($customer['credit_limit']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Order Max Limit</label>
                                            <input type="number" step="0.01" name="order_max_limit" class="form-control" value="<?php echo customerMasterSafe($customer['order_max_limit']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Opening Balance</label>
                                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="<?php echo customerMasterSafe($customer['opening_balance']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Payment Type</label>
                                            <select name="payment_type" class="form-control customer-master-select">
                                                <option value="0" <?php echo (int) $customer['payment_type'] === 0 ? 'selected' : ''; ?>>Not Set</option>
                                                <option value="1" <?php echo (int) $customer['payment_type'] === 1 ? 'selected' : ''; ?>>Cheque</option>
                                                <option value="2" <?php echo (int) $customer['payment_type'] === 2 ? 'selected' : ''; ?>>Cash</option>
                                                <option value="3" <?php echo (int) $customer['payment_type'] === 3 ? 'selected' : ''; ?>>Online / NEFT</option>
                                                <option value="4" <?php echo (int) $customer['payment_type'] === 4 ? 'selected' : ''; ?>>PDC</option>
                                                <option value="5" <?php echo (int) $customer['payment_type'] === 5 ? 'selected' : ''; ?>>Credit</option>
                                                <option value="6" <?php echo (int) $customer['payment_type'] === 6 ? 'selected' : ''; ?>>Advance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Credit Days</label>
                                            <input type="number" name="credit_days" class="form-control" value="<?php echo customerMasterSafe($customer['credit_days']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>TDS Applicable</label>
                                            <select name="tds_appl" class="form-control customer-master-select">
                                                <option value="0" <?php echo (int) $customer['tds_appl'] === 0 ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo (int) $customer['tds_appl'] === 1 ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>TDS %</label>
                                            <input type="number" step="0.01" name="tds_per" class="form-control" value="<?php echo customerMasterSafe($customer['tds_per']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Customer Type</label>
                                            <select name="customer_type" class="form-control customer-master-select">
                                                <option value="0" <?php echo (int) $customer['customer_type'] === 0 ? 'selected' : ''; ?>>Normal</option>
                                                <option value="1" <?php echo (int) $customer['customer_type'] === 1 ? 'selected' : ''; ?>>Dormant</option>
                                                <option value="2" <?php echo (int) $customer['customer_type'] === 2 ? 'selected' : ''; ?>>No Follow-up</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Payment Term Approval</label>
                                            <select name="payment_term_approval" class="form-control customer-master-select">
                                                <option value="1" <?php echo (int) $customer['payment_term_approval'] === 1 ? 'selected' : ''; ?>>Approved</option>
                                                <option value="0" <?php echo (int) $customer['payment_term_approval'] === 0 ? 'selected' : ''; ?>>Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Payment Approved On</label>
                                            <input type="datetime-local" name="payment_approved_On" class="form-control" value="<?php echo customerMasterSafe(customerMasterDatetimeInput($customer['payment_approved_On'])); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Payment Approved By</label>
                                            <select name="payment_approved_By" class="form-control customer-master-select">
                                                <option value="0">Select User</option>
                                                <?php foreach ($marketing_user_options as $user_option) { ?>
                                                    <?php $user_name = trim($user_option['title'] . ' ' . $user_option['first_name'] . ' ' . $user_option['last_name']); ?>
                                                    <option value="<?php echo (int) $user_option['user_id']; ?>" <?php echo (int) $customer['payment_approved_By'] === (int) $user_option['user_id'] ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($user_name); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Assigned To</label>
                                            <select name="assigned_to" class="form-control customer-master-select">
                                                <option value="0">Select User</option>
                                                <?php foreach ($marketing_user_options as $user_option) { ?>
                                                    <?php $user_name = trim($user_option['title'] . ' ' . $user_option['first_name'] . ' ' . $user_option['last_name']); ?>
                                                    <option value="<?php echo (int) $user_option['user_id']; ?>" <?php echo (int) $customer['assigned_to'] === (int) $user_option['user_id'] ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($user_name); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Assign Customer For Trial</label>
                                            <select name="assign_customer_for_trail" class="form-control customer-master-select">
                                                <option value="0" <?php echo (int) $customer['assign_customer_for_trail'] === 0 ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo (int) $customer['assign_customer_for_trail'] === 1 ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>GST Verified</label>
                                            <select name="gst_verified" class="form-control customer-master-select">
                                                <option value="1" <?php echo (int) $customer['gst_verified'] === 1 ? 'selected' : ''; ?>>Yes</option>
                                                <option value="0" <?php echo (int) $customer['gst_verified'] === 0 ? 'selected' : ''; ?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Intro Email Sent</label>
                                            <select name="exhibitiion_email_sent" class="form-control customer-master-select">
                                                <option value="0" <?php echo (int) $customer['exhibitiion_email_sent'] === 0 ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo (int) $customer['exhibitiion_email_sent'] === 1 ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Bangalore Exhibition</label>
                                            <select name="banglore_exhibition" class="form-control customer-master-select">
                                                <option value="0" <?php echo (int) $customer['banglore_exhibition'] === 0 ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?php echo (int) $customer['banglore_exhibition'] === 1 ? 'selected' : ''; ?>>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="contact-toolbar clearfix">
                                    <h5 style="float:left;">Additional Contacts</h5>
                                    <button type="button" id="addContactRow" class="btn btn-sm btn-primary pull-right">
                                        <i class="fa fa-plus"></i> Add Contact
                                    </button>
                                </div>
                                <div id="contactRows">
                                    <?php if (!empty($customer['additional_contacts'])) { ?>
                                        <?php foreach ($customer['additional_contacts'] as $contact_row) { ?>
                                            <div class="contact-row">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Contact Person</label>
                                                            <input type="text" name="contactpersonname_extra[]" class="form-control" value="<?php echo customerMasterSafe($contact_row['contactpersonname']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Contact No</label>
                                                            <input type="text" name="personcontactno_extra[]" class="form-control" value="<?php echo customerMasterSafe($contact_row['personcontactno']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input type="text" name="personemailid_extra[]" class="form-control" value="<?php echo customerMasterSafe($contact_row['personemailid']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Designation</label>
                                                            <input type="text" name="designation_extra[]" class="form-control" value="<?php echo customerMasterSafe($contact_row['designation']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Branch</label>
                                                            <div class="input-group">
                                                                <input type="text" name="branchlocation_extra[]" class="form-control" value="<?php echo customerMasterSafe($contact_row['branchlocation']); ?>">
                                                                <span class="input-group-btn">
                                                                    <button type="button" class="btn btn-danger remove-contact-row"><i class="fa fa-trash"></i></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="form-section">
                                <h5>Spares Customer Information</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Company Name <span style="color:red;">*</span></label>
                                            <input type="text" name="company_name" class="form-control" value="<?php echo customerMasterSafe($customer['company_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Brand</label>
                                            <select name="brand_id" class="form-control customer-master-brand-select">
                                                <option value="">Select or type brand</option>
                                                <?php if ($spares_brand_value !== '') { ?>
                                                    <option value="<?php echo customerMasterSafe($spares_brand_value); ?>" selected="selected">
                                                        <?php echo customerMasterSafe($spares_brand_label); ?>
                                                    </option>
                                                <?php } ?>
                                                <?php foreach ($spares_brand_options as $brand_option) { ?>
                                                    <?php if ((string) $brand_option['id'] === $spares_brand_value) { continue; } ?>
                                                    <option value="<?php echo (int) $brand_option['id']; ?>">
                                                        <?php echo customerMasterSafe($brand_option['name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="country_id" class="form-control customer-master-select">
                                                <option value="0">Select Country</option>
                                                <?php foreach ($country_options as $country_option) { ?>
                                                    <option value="<?php echo (int) $country_option['country_id']; ?>" <?php echo (int) $customer['country_id'] === (int) $country_option['country_id'] ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($country_option['country_name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control customer-master-select">
                                                <option value="1" <?php echo (int) $customer['status'] === 1 ? 'selected' : ''; ?>>Active</option>
                                                <option value="0" <?php echo (int) $customer['status'] === 0 ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Shipping Customer</label>
                                            <select name="shipping_customer_id" class="form-control customer-master-select">
                                                <option value="0">Not linked</option>
                                                <?php foreach ($shipping_customer_options as $shipping_customer) { ?>
                                                    <option value="<?php echo (int) $shipping_customer['customer_id']; ?>" <?php echo (int) $customer['shipping_customer_id'] === (int) $shipping_customer['customer_id'] ? 'selected' : ''; ?>>
                                                        <?php echo customerMasterSafe($shipping_customer['company_name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="address" class="form-control" rows="4"><?php echo customerMasterSafe($customer['address']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <textarea name="email" class="form-control" rows="4"><?php echo customerMasterSafe($customer['email']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Contact Person</label>
                                            <input type="text" name="contact_person" class="form-control" value="<?php echo customerMasterSafe($customer['contact_person']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Contact Person No</label>
                                            <input type="text" name="contact_person_no" class="form-control" value="<?php echo customerMasterSafe($customer['contact_person_no']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Alternate Contact No</label>
                                            <input type="text" name="alternate_contact_no" class="form-control" value="<?php echo customerMasterSafe($customer['alternate_contact_no']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tax Number / GST</label>
                                            <input type="text" name="tax_number" class="form-control" value="<?php echo customerMasterSafe($customer['tax_number']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Created At</label>
                                            <input type="text" class="form-control" value="<?php echo customerMasterSafe(customerMasterDate($customer['created_at'])); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-section">
                            <h5>Change Note</h5>
                            <div class="form-group">
                                <label>Why is this customer being updated?</label>
                                <textarea name="change_note" class="form-control" rows="3" placeholder="Example: corrected billing GST, updated shipping address, reassigned customer owner"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Save Customer Updates
                                </button>
                                <a href="<?php echo page_url; ?>Customer_master_control" class="btn btn-default">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head">
                    <h4>Customer update history</h4>
                    <p>This section shows edits captured through the customer master control module.</p>
                </div>
                <div class="panel-body">
                    <?php if (empty($history)) { ?>
                        <div class="alert alert-info" style="margin-bottom:0;">
                            No module history has been captured for this customer yet.
                        </div>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-bordered history-table">
                                <thead>
                                    <tr>
                                        <th>Changed On</th>
                                        <th>Changed By</th>
                                        <th>Action</th>
                                        <th>Changed Fields</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $history_row) { ?>
                                        <tr>
                                            <td><?php echo customerMasterDate($history_row['changed_on']); ?></td>
                                            <td><?php echo customerMasterSafe($history_row['changed_by_name'] !== '' ? $history_row['changed_by_name'] : 'Not captured'); ?></td>
                                            <td><?php echo customerMasterSafe(customerMasterActionLabel($history_row['action_type'])); ?></td>
                                            <td><?php echo customerMasterSafe(!empty($history_row['changed_fields_list']) ? implode(', ', $history_row['changed_fields_list']) : '-'); ?></td>
                                            <td><?php echo customerMasterSafe(trim((string) $history_row['change_note']) !== '' ? $history_row['change_note'] : '-'); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js"></script>

    <script>
    function initialiseSelects() {
        $('.customer-master-select').select2();
        $('.customer-master-state-select').select2();
        $('.customer-master-brand-select').select2({
            tags: true
        });
    }

    function renderContactRow() {
        return '' +
            '<div class="contact-row">' +
                '<div class="row">' +
                    '<div class="col-md-3">' +
                        '<div class="form-group">' +
                            '<label>Contact Person</label>' +
                            '<input type="text" name="contactpersonname_extra[]" class="form-control">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                        '<div class="form-group">' +
                            '<label>Contact No</label>' +
                            '<input type="text" name="personcontactno_extra[]" class="form-control">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-3">' +
                        '<div class="form-group">' +
                            '<label>Email</label>' +
                            '<input type="text" name="personemailid_extra[]" class="form-control">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                        '<div class="form-group">' +
                            '<label>Designation</label>' +
                            '<input type="text" name="designation_extra[]" class="form-control">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                        '<div class="form-group">' +
                            '<label>Branch</label>' +
                            '<div class="input-group">' +
                                '<input type="text" name="branchlocation_extra[]" class="form-control">' +
                                '<span class="input-group-btn">' +
                                    '<button type="button" class="btn btn-danger remove-contact-row"><i class="fa fa-trash"></i></button>' +
                                '</span>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    function reloadStateOptions(countryId, selectedStateIds) {
        $.ajax({
            type: 'POST',
            url: '<?php echo page_url; ?>Customer_master_control/get_states',
            data: {
                country_id: countryId,
                selected_state_id: 0
            },
            success: function (html) {
                $('#customer_state').html(html).trigger('change.select2');
                $('#bill_state').html(html).trigger('change.select2');
                $('#ship_state').html(html).trigger('change.select2');

                if (selectedStateIds) {
                    if (selectedStateIds.customer) {
                        $('#customer_state').val(selectedStateIds.customer).trigger('change');
                    }
                    if (selectedStateIds.bill) {
                        $('#bill_state').val(selectedStateIds.bill).trigger('change');
                    }
                    if (selectedStateIds.ship) {
                        $('#ship_state').val(selectedStateIds.ship).trigger('change');
                    }
                }
            }
        });
    }

    $(document).ready(function () {
        initialiseSelects();

        $('#addContactRow').on('click', function () {
            $('#contactRows').append(renderContactRow());
        });

        $(document).on('click', '.remove-contact-row', function () {
            $(this).closest('.contact-row').remove();
        });

        $('#customer_country').on('change', function () {
            var countryId = $(this).val();
            reloadStateOptions(countryId, null);
        });
    });
    </script>
</body>
</html>
