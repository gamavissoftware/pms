<!DOCTYPE html>
<html>
<head>
    <title>Service Masters</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header-title { text-transform: uppercase; letter-spacing: 1px; color: #333; font-weight: 600; }
        .table-responsive { overflow-x: visible !important; }
        .nav-tabs.service-master-tabs { border-bottom: 0; margin-bottom: 20px; }
        .nav-tabs.service-master-tabs > li > a {
            border-radius: 999px;
            border: 1px solid #d5dbe3;
            color: #334155;
            font-weight: 600;
            margin-right: 10px;
            padding: 10px 18px;
            background: #fff;
        }
        .nav-tabs.service-master-tabs > li.active > a,
        .nav-tabs.service-master-tabs > li.active > a:hover,
        .nav-tabs.service-master-tabs > li.active > a:focus {
            background: #003366;
            border-color: #003366;
            color: #fff;
        }
        .tab-pane { padding-top: 5px; }
        .master-helper {
            background: #eef6ff;
            border: 1px solid #c8dcff;
            border-radius: 10px;
            color: #1d4ed8;
            font-size: 12px;
            margin-bottom: 16px;
            padding: 12px 14px;
        }
        .master-meta {
            color: #64748b;
            font-size: 12px;
            display: block;
            margin-top: 4px;
        }
        .status-pill {
            border-radius: 999px;
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
        }
        .status-pill.active-pill {
            background: #dcfce7;
            color: #166534;
        }
        .status-pill.inactive-pill {
            background: #fee2e2;
            color: #991b1b;
        }
        .action-btn-wrap .btn {
            margin-right: 4px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <?php
    $service_master_read_only = isset($this->master_profile_guard) && $this->master_profile_guard->is_master_read_only();
    $active_tab = !empty($active_tab) && $active_tab === 'companies' ? 'companies' : 'charges';
    $company_form = isset($company_form) && is_array($company_form) ? $company_form : [];
    ?>

    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Service Masters</h4>
                    </div>
                </div>
            </div>

            <?php echo $this->session->flashdata('message'); ?>

            <ul class="nav nav-tabs service-master-tabs">
                <li class="<?php echo $active_tab === 'charges' ? 'active' : ''; ?>">
                    <a href="#charge_master_tab" id="charge_master_tab_link" data-toggle="tab">
                        <i class="fa fa-money"></i> Charge Master
                    </a>
                </li>
                <li class="<?php echo $active_tab === 'companies' ? 'active' : ''; ?>">
                    <a href="#company_master_tab" id="company_master_tab_link" data-toggle="tab">
                        <i class="fa fa-building"></i> Company Master
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane <?php echo $active_tab === 'charges' ? 'active' : ''; ?>" id="charge_master_tab">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-20">Manage Charge Details</h4>
                                <div class="master-helper">
                                    Define the service charge catalogue used while preparing service quotations.
                                </div>

                                <?php if ($service_master_read_only) { ?>
                                    <div class="alert alert-info">This EA profile can review service charges but cannot change them.</div>
                                <?php } ?>

                                <?php if (!$service_master_read_only) { ?>
                                <form action="<?php echo page_url; ?>ServiceMaster/save_charge" method="post">
                                    <input type="hidden" name="id" id="charge_id">

                                    <div class="alert alert-info" style="padding: 10px; font-size: 12px;">
                                        <i class="fa fa-info-circle"></i> Current Conversion Base:
                                        <strong>1 USD = ₹<span id="base_rate_display">94.00</span></strong>
                                        <input type="hidden" id="internal_conv_rate" value="94.00">
                                    </div>

                                    <div class="form-group">
                                        <label>Charge Name</label>
                                        <input type="text" name="charge_name" id="charge_name" class="form-control" required placeholder="e.g. MECHANICAL ENGINEER CHARGES">
                                    </div>

                                    <div class="form-group">
                                        <label>SAC Code</label>
                                        <input type="text" name="sac_code" id="sac_code" class="form-control" value="9987">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Rate (INR ₹)</label>
                                                <input type="number" step="0.01" name="default_rate_inr" id="rate_inr" class="form-control" required placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Rate (USD $)</label>
                                                <input type="number" step="0.01" name="default_rate_usd" id="rate_usd" class="form-control" readonly style="background-color: #eee; font-weight: bold;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Charge Type</label>
                                        <select name="charge_type" id="charge_type" class="form-control">
                                            <option value="Per Day">Per Day (Multiplies by Days & Engineers)</option>
                                            <option value="Fixed">Fixed (Lump sum)</option>
                                        </select>
                                    </div>

                                    <div class="p-t-10 text-center">
                                        <button type="submit" class="btn btn-primary btn-block waves-effect waves-light">Save Service Charge</button>
                                        <button type="reset" class="btn btn-default btn-block waves-effect m-t-10" onclick="clear_charge_form(); return false;">Reset / Clear</button>
                                    </div>
                                </form>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-20">Existing Service Charges</h4>
                                <div class="table-responsive">
                                    <table id="serviceMasterTable" class="table table-hover m-0">
                                        <thead>
                                            <tr>
                                                <th>Service Description</th>
                                                <th>Type</th>
                                                <th>Rate (INR)</th>
                                                <th>Rate (USD)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($charges)) { foreach ($charges as $row) { ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo html_escape($row->charge_name); ?></strong>
                                                    <span class="master-meta">SAC: <?php echo html_escape($row->sac_code); ?></span>
                                                </td>
                                                <td>
                                                    <span class="label label-<?php echo $row->charge_type === 'Per Day' ? 'info' : 'success'; ?>">
                                                        <?php echo html_escape($row->charge_type); ?>
                                                    </span>
                                                </td>
                                                <td>₹ <?php echo number_format((float) $row->default_rate_inr, 2); ?></td>
                                                <td>$ <?php echo number_format((float) $row->default_rate_usd, 2); ?></td>
                                                <td class="action-btn-wrap">
                                                    <?php if (!$service_master_read_only) { ?>
                                                        <button type="button" class="btn btn-xs btn-warning" onclick='edit_charge(<?php echo json_encode($row); ?>)' title="Edit"><i class="fa fa-pencil"></i></button>
                                                    <?php } ?>
                                                    <button type="button" class="btn btn-xs btn-info" onclick="view_history(<?php echo (int) $row->id; ?>)" title="Rate History"><i class="fa fa-history"></i></button>
                                                </td>
                                            </tr>
                                            <?php } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane <?php echo $active_tab === 'companies' ? 'active' : ''; ?>" id="company_master_tab">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-20">Manage Service Company</h4>
                                <div class="master-helper">
                                    Create the list of Shubham Pack companies across countries with their contact, tax, and currency details.
                                </div>

                                <?php if ($service_master_read_only) { ?>
                                    <div class="alert alert-info">This EA profile can review service company records but cannot change them.</div>
                                <?php } ?>

                                <?php if (!$service_master_read_only) { ?>
                                <form action="<?php echo page_url; ?>ServiceMaster/save_company" method="post" id="serviceCompanyForm">
                                    <input type="hidden" name="company_id" id="service_company_id" value="<?php echo html_escape((string) ($company_form['company_id'] ?? '')); ?>">

                                    <div class="form-group">
                                        <label>Company Name</label>
                                        <input type="text" name="company_name" id="service_company_name" class="form-control" required value="<?php echo html_escape((string) ($company_form['company_name'] ?? '')); ?>" placeholder="Enter service company name">
                                    </div>

                                    <div class="form-group">
                                        <label>Country</label>
                                        <select name="country_id" id="service_company_country" class="form-control" required>
                                            <option value="0">Select Country</option>
                                            <?php foreach ($country_options as $country) { ?>
                                                <option
                                                    value="<?php echo (int) $country['country_id']; ?>"
                                                    data-currency="<?php echo html_escape((string) ($country['suggested_currency'] ?? '')); ?>"
                                                    data-tax-label="<?php echo html_escape((string) ($country['taxtype'] ?? '')); ?>"
                                                    <?php echo (int) ($company_form['country_id'] ?? 0) === (int) $country['country_id'] ? 'selected' : ''; ?>
                                                >
                                                    <?php echo html_escape($country['country_name']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Currency</label>
                                                <input type="text" name="currency" id="service_company_currency" class="form-control" maxlength="10" required value="<?php echo html_escape((string) ($company_form['currency'] ?? 'INR')); ?>" placeholder="INR / USD / AED">
                                                <span class="master-meta">Auto-suggested from the selected country. You can still change it manually if needed.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" id="service_company_status" class="form-control">
                                                    <option value="1" <?php echo (string) ($company_form['status'] ?? '1') === '1' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="0" <?php echo (string) ($company_form['status'] ?? '1') === '0' ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea name="address" id="service_company_address" class="form-control" rows="3" required placeholder="Enter company address"><?php echo html_escape((string) ($company_form['address'] ?? '')); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Contact No</label>
                                        <input type="text" name="contact_no" id="service_company_contact" class="form-control" required value="<?php echo html_escape((string) ($company_form['contact_no'] ?? '')); ?>" placeholder="Enter contact number">
                                    </div>

                                    <div class="form-group">
                                        <label>Email ID</label>
                                        <input type="email" name="email" id="service_company_email" class="form-control" required value="<?php echo html_escape((string) ($company_form['email'] ?? '')); ?>" placeholder="Enter email address">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Tax Label</label>
                                                <input type="text" name="tax_label" id="service_company_tax_label" class="form-control" value="<?php echo html_escape((string) ($company_form['tax_label'] ?? '')); ?>" placeholder="GSTIN / VAT / Tax ID">
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label>Tax Number</label>
                                                <input type="text" name="tax_number" id="service_company_tax_number" class="form-control" value="<?php echo html_escape((string) ($company_form['tax_number'] ?? '')); ?>" placeholder="Optional tax number">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-t-10 text-center">
                                        <button type="submit" class="btn btn-primary btn-block waves-effect waves-light" id="service_company_submit_btn">Save Service Company</button>
                                        <button type="button" class="btn btn-default btn-block waves-effect m-t-10" onclick="clear_company_form();">Reset / Clear</button>
                                    </div>
                                </form>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card-box">
                                <h4 class="header-title m-t-0 m-b-20">Existing Service Companies</h4>
                                <div class="table-responsive">
                                    <table id="serviceCompanyTable" class="table table-hover m-0">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Country</th>
                                                <th>Contact</th>
                                                <th>Currency</th>
                                                <th>Tax</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($companies)) { foreach ($companies as $company) { ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo html_escape($company->company_name); ?></strong>
                                                    <span class="master-meta"><?php echo nl2br(html_escape((string) $company->address)); ?></span>
                                                    <span class="master-meta"><?php echo html_escape((string) $company->email); ?></span>
                                                </td>
                                                <td><?php echo html_escape((string) ($company->country_name ?: '-')); ?></td>
                                                <td><?php echo html_escape((string) $company->contact_no); ?></td>
                                                <td><?php echo html_escape((string) strtoupper((string) $company->currency)); ?></td>
                                                <td>
                                                    <?php
                                                    $tax_text = '-';
                                                    if (!empty($company->tax_number)) {
                                                        $label = !empty($company->tax_label) ? $company->tax_label : 'Tax ID';
                                                        $tax_text = $label . ': ' . $company->tax_number;
                                                    }
                                                    echo html_escape($tax_text);
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="status-pill <?php echo (int) $company->status === 1 ? 'active-pill' : 'inactive-pill'; ?>">
                                                        <?php echo (int) $company->status === 1 ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td class="action-btn-wrap">
                                                    <?php if (!$service_master_read_only) { ?>
                                                        <button type="button" class="btn btn-xs btn-warning" onclick='edit_company(<?php echo json_encode($company); ?>)' title="Edit"><i class="fa fa-pencil"></i></button>
                                                        <a href="<?php echo page_url; ?>ServiceMaster/toggle_company_status/<?php echo (int) $company->id; ?>" class="btn btn-xs <?php echo (int) $company->status === 1 ? 'btn-danger' : 'btn-success'; ?>" onclick="return confirm('Are you sure you want to <?php echo (int) $company->status === 1 ? 'inactivate' : 'activate'; ?> this service company?');">
                                                            <i class="fa <?php echo (int) $company->status === 1 ? 'fa-ban' : 'fa-check'; ?>"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <span class="text-muted">View only</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#serviceMasterTable').DataTable({
            pageLength: 10,
            order: [[0, 'asc']],
            language: {
                search: 'Search Charges:',
                paginate: {
                    previous: "<i class='fa fa-angle-left'></i>",
                    next: "<i class='fa fa-angle-right'></i>"
                }
            }
        });

        $('#serviceCompanyTable').DataTable({
            pageLength: 10,
            order: [[0, 'asc']],
            language: {
                search: 'Search Companies:',
                paginate: {
                    previous: "<i class='fa fa-angle-left'></i>",
                    next: "<i class='fa fa-angle-right'></i>"
                }
            }
        });

        function calculateUSD() {
            var inrValue = parseFloat($('#rate_inr').val()) || 0;
            var convRate = parseFloat($('#internal_conv_rate').val()) || 94.00;

            if (inrValue > 0) {
                $('#rate_usd').val((inrValue / convRate).toFixed(2));
            } else {
                $('#rate_usd').val('0.00');
            }
        }

        $('#rate_inr').on('input', function() {
            calculateUSD();
        });

        $('#service_company_currency').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        function syncCompanyCountryDefaults(forceCurrency) {
            var selectedOption = $('#service_company_country option:selected');
            var suggestedCurrency = String(selectedOption.data('currency') || '').toUpperCase();
            var suggestedTaxLabel = String(selectedOption.data('tax-label') || '');
            var currentCurrency = String($('#service_company_currency').val() || '').toUpperCase();
            var currentTaxLabel = String($('#service_company_tax_label').val() || '');

            if (forceCurrency || currentCurrency === '' || currentCurrency === 'INR' || currentCurrency === 'USD') {
                if (suggestedCurrency !== '') {
                    $('#service_company_currency').val(suggestedCurrency);
                }
            }

            if (currentTaxLabel === '' && suggestedTaxLabel !== '') {
                $('#service_company_tax_label').val(suggestedTaxLabel);
            }
        }

        $('#service_company_country').on('change', function() {
            syncCompanyCountryDefaults(true);
        });

        window.edit_charge = function(data) {
            $('#charge_id').val(data.id);
            $('#charge_name').val(data.charge_name);
            $('#sac_code').val(data.sac_code);
            $('#rate_inr').val(data.default_rate_inr);
            calculateUSD();
            $('#charge_type').val(data.charge_type);
            $('#charge_master_tab_link').tab('show');
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        };

        window.clear_charge_form = function() {
            $('#charge_id').val('');
            $('#charge_name').val('');
            $('#sac_code').val('9987');
            $('#rate_inr').val('');
            $('#rate_usd').val('');
            $('#charge_type').val('Per Day');
            $('#charge_master_tab_link').tab('show');
        };

        window.edit_company = function(data) {
            $('#service_company_id').val(data.id || '');
            $('#service_company_name').val(data.company_name || '');
            $('#service_company_country').val(data.country_id || '0');
            $('#service_company_currency').val((data.currency || 'INR').toUpperCase());
            $('#service_company_status').val(typeof data.status !== 'undefined' ? String(data.status) : '1');
            $('#service_company_address').val(data.address || '');
            $('#service_company_contact').val(data.contact_no || '');
            $('#service_company_email').val(data.email || '');
            $('#service_company_tax_label').val(data.tax_label || '');
            $('#service_company_tax_number').val(data.tax_number || '');
            $('#service_company_submit_btn').text('Update Service Company');
            $('#company_master_tab_link').tab('show');
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        };

        window.clear_company_form = function() {
            $('#service_company_id').val('');
            $('#service_company_name').val('');
            $('#service_company_country').val('101');
            $('#service_company_currency').val('');
            $('#service_company_status').val('1');
            $('#service_company_address').val('');
            $('#service_company_contact').val('');
            $('#service_company_email').val('');
            $('#service_company_tax_label').val('');
            $('#service_company_tax_number').val('');
            $('#service_company_submit_btn').text('Save Service Company');
            syncCompanyCountryDefaults(true);
            $('#company_master_tab_link').tab('show');
        };

        window.view_history = function(charge_id) {
            alert('Rate change history for ID ' + charge_id + ' will be displayed in the next module update.');
        };

        <?php if ($active_tab === 'companies') { ?>
        $('#company_master_tab_link').tab('show');
        <?php } else { ?>
        $('#charge_master_tab_link').tab('show');
        <?php } ?>

        syncCompanyCountryDefaults(false);

        <?php if (!empty($company_form['company_id'])) { ?>
        $('#service_company_submit_btn').text('Update Service Company');
        $('#company_master_tab_link').tab('show');
        <?php } ?>
    });
    </script>
</body>
</html>
