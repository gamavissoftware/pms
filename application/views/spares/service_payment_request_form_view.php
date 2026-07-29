<?php
defined('BASEPATH') or exit('No direct script access allowed');

$selected_request_type = trim((string) ($selected_request_type ?? ''));
if ($selected_request_type === '') {
    $selected_request_type = 'FOC';
}
$is_order_won_request = $selected_request_type === 'Order Won';
$is_foc_request = strtoupper($selected_request_type) === 'FOC';

$selected_order = !empty($context['order']) ? $context['order'] : null;
$selected_visit = !empty($context['visit']) ? $context['visit'] : null;
$service_hod_name = !empty($service_hod->full_name) ? trim((string) $service_hod->full_name) : 'Service HOD not mapped';
$service_hod_email = !empty($service_hod->email) ? trim((string) $service_hod->email) : '';
$service_hod_is_mapped = !empty($service_hod->user_id);
$won_order_count = !empty($won_orders) ? count($won_orders) : 0;
$can_submit_request = $service_hod_is_mapped && $won_order_count > 0;
$payment_permissions = isset($payment_permissions) && is_array($payment_permissions) ? $payment_permissions : array();
$can_approve_service_payment = !empty($payment_permissions['can_approve']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Service Payment Request</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        :root {
            --request-primary: #0f766e;
            --request-primary-dark: #115e59;
            --request-accent: #f59e0b;
            --request-ink: #16324f;
            --request-muted: #64748b;
            --request-bg: #eef5fb;
            --request-card: #ffffff;
            --request-border: #dbe5f0;
            --request-soft: #eff6ff;
            --request-soft-green: #ecfdf5;
            --request-soft-amber: #fffbeb;
            --request-soft-rose: #fff1f2;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.14), transparent 24%),
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.12), transparent 26%),
                var(--request-bg);
            color: var(--request-ink);
        }

        .wrapper {
            padding-top: 74px;
            padding-bottom: 16px;
        }

        .request-card,
        .side-panel {
            background: var(--request-card);
            border-radius: 14px;
            border: 1px solid rgba(219, 229, 240, 0.95);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .request-card {
            padding: 18px;
            margin-bottom: 16px;
        }

        .page-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--request-primary);
        }

        .toolbar-title {
            margin: 6px 0 0;
            font-size: 24px;
            line-height: 1.15;
            font-weight: 700;
            color: var(--request-ink);
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .toolbar-actions .btn {
            border-radius: 999px;
            padding: 8px 16px;
            font-weight: 600;
        }

        .section-title {
            margin: 0 0 10px;
            font-size: 20px;
            font-weight: 700;
            color: var(--request-ink);
        }

        .compact-note {
            margin: 0 0 12px;
            color: var(--request-muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .field-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--request-muted);
            font-weight: 700;
            margin-bottom: 6px;
        }

        .form-control,
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border-radius: 10px !important;
            border-color: #d7e2ee !important;
            min-height: 40px;
            box-shadow: none !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 12px;
            color: var(--request-ink);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        textarea.form-control {
            min-height: 110px;
            resize: vertical;
        }

        .basis-toggle {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .basis-option {
            position: relative;
            display: block;
            width: 100%;
            margin: 0;
        }

        .basis-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .basis-pill {
            display: block;
            width: 100%;
            border: 1px solid var(--request-border);
            border-radius: 12px;
            padding: 12px 14px;
            background: #fff;
            min-height: 78px;
            transition: 0.25s ease;
            cursor: pointer;
        }

        .basis-pill strong {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
            color: var(--request-ink);
        }

        .basis-pill span {
            display: block;
            color: var(--request-muted);
            font-size: 11px;
            line-height: 1.45;
        }

        .basis-option input:checked + .basis-pill {
            border-color: rgba(15, 118, 110, 0.42);
            background: linear-gradient(145deg, rgba(15, 118, 110, 0.12), rgba(29, 78, 216, 0.06));
            box-shadow: 0 12px 28px rgba(15, 118, 110, 0.14);
        }

        .summary-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(96px, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .summary-box {
            border-radius: 12px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .summary-box small {
            display: block;
            color: var(--request-muted);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 9px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .summary-box strong {
            display: block;
            font-size: 18px;
            line-height: 1.1;
            color: var(--request-ink);
        }

        .side-panel {
            padding: 16px;
            margin-bottom: 16px;
        }

        .side-panel h5 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 15px;
            font-weight: 700;
        }

        .meta-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .meta-list li {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 12px;
        }

        .meta-list li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .meta-key {
            color: var(--request-muted);
            font-weight: 600;
        }

        .meta-value {
            color: var(--request-ink);
            font-weight: 600;
            text-align: right;
            max-width: 65%;
        }

        .request-chain {
            max-height: 260px;
            overflow-y: auto;
            margin-top: 8px;
        }

        .chain-row {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 12px;
            margin-bottom: 8px;
            background: #f8fafc;
        }

        .chain-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 4px;
        }

        .chain-code {
            font-weight: 700;
            color: var(--request-ink);
        }

        .chain-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .status-pending-hod-approval {
            background: var(--request-soft-amber);
            color: #b45309;
        }

        .status-approved {
            background: var(--request-soft-green);
            color: #15803d;
        }

        .status-rejected {
            background: var(--request-soft-rose);
            color: #be123c;
        }

        .chain-meta {
            color: var(--request-muted);
            font-size: 11px;
            line-height: 1.5;
        }

        .submit-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .submit-note {
            color: var(--request-muted);
            font-size: 11px;
            line-height: 1.5;
            flex: 1 1 240px;
        }

        .btn-submit {
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 600;
            background: linear-gradient(135deg, var(--request-primary) 0%, #1d4ed8 100%);
            border: none;
            min-width: 190px;
        }

        .empty-state {
            border-radius: 12px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 12px;
            color: var(--request-muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .hidden {
            display: none !important;
        }

        @media (max-width: 991px) {
            .wrapper {
                padding-top: 70px;
            }

            .request-card,
            .side-panel {
                padding: 16px;
            }

            .toolbar-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <div class="request-card page-toolbar">
                <div>
                    <div class="toolbar-eyebrow">
                        <i class="fa fa-credit-card"></i>
                        <span>Service Payment Request</span>
                    </div>
                    <h2 class="toolbar-title">Raise Service Payment Request</h2>
                </div>
                <div class="toolbar-actions">
                    <a href="<?php echo page_url; ?>ServiceLeads/service_payment_requests" class="btn btn-primary">
                        <i class="fa fa-list-alt"></i> Track Requests
                    </a>
                    <?php if ($can_approve_service_payment): ?>
                        <a href="<?php echo page_url; ?>ServiceLeads/service_payment_approvals" class="btn btn-default">
                            <i class="fa fa-check-square-o"></i> Approval Queue
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="request-card">
                        <h4 class="section-title">Request Details</h4>
                        <div class="compact-note">Select the order, enter the amount, and submit for HOD approval.</div>

                        <?php if (!$service_hod_is_mapped): ?>
                            <div class="alert alert-warning" style="margin-top:12px;">
                                Service HOD is not mapped yet, so requests cannot be routed for approval. Please update the service team leader mapping first.
                            </div>
                        <?php elseif ($won_order_count === 0): ?>
                            <div class="alert alert-info" style="margin-top:12px;">
                                No won service orders with PO details are available right now.
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?php echo page_url; ?>ServiceLeads/save_service_payment_request" enctype="multipart/form-data" id="servicePaymentRequestForm">
                            <input type="hidden" name="visit_id" id="visit_id" value="<?php echo !empty($selected_visit_id) ? (int) $selected_visit_id : 0; ?>">
                            <input type="hidden" name="requested_for_user_id" id="requested_for_user_id" value="<?php echo $is_foc_request ? (int)($selected_engineer_id ?? 0) : (!empty($selected_visit->engineer_id) ? (int) $selected_visit->engineer_id : 0); ?>">

                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="field-label">Request Code</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($request_code); ?>" readonly>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="field-label">Request Date</label>
                                    <input type="text" class="form-control" value="<?php echo date('d M Y'); ?>" readonly>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="field-label">Approval Owner</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($service_hod_name); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group <?php echo $is_foc_request ? 'hidden' : ''; ?>" id="requestBasisWrap">
                                <label class="field-label">Request Basis</label>
                                <div class="basis-toggle">
                                    <label class="basis-option <?php echo !$is_order_won_request ? 'hidden' : ''; ?>" id="orderBasisOption">
                                        <input type="radio" name="request_basis" value="ORDER" <?php echo $request_basis === 'ORDER' ? 'checked' : ''; ?>>
                                        <span class="basis-pill">
                                            <strong>Order-wise Request</strong>
                                            <span>Against won order</span>
                                        </span>
                                    </label>
                                    <label class="basis-option" id="visitBasisOption">
                                        <input type="radio" name="request_basis" value="VISIT" <?php echo $request_basis === 'VISIT' ? 'checked' : ''; ?>>
                                        <span class="basis-pill">
                                            <strong>Engineer Visit Request</strong>
                                            <span>Against engineer visit</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group <?php echo (!$is_order_won_request && !$is_foc_request) ? 'hidden' : ''; ?>" id="orderSelectWrap">
                                    <label class="field-label">Won Order <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="opportunity_id" id="opportunity_id" <?php echo ($is_order_won_request || $is_foc_request) ? 'required' : ''; ?>>
                                        <option value="">Select won order</option>
                                        <?php foreach ($won_orders as $order): ?>
                                            <option value="<?php echo (int) $order->opportunity_id; ?>" <?php echo (int) $selected_opportunity_id === (int) $order->opportunity_id ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($order->company_name . ' | ' . $order->op_no . ' | PO ' . $order->po_number); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group <?php echo $is_foc_request ? 'hidden' : ($is_order_won_request ? 'col-md-6' : 'col-md-12'); ?>" id="visitSelectWrap">
                                    <label class="field-label">Engineer Visit</label>
                                    <select class="form-control select2" id="visit_selector">
                                        <option value="">Select visit</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group <?php echo $is_foc_request ? '' : 'hidden'; ?>" id="engineerSelectWrap">
                                    <label class="field-label">Engineer <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="engineer_selector" <?php echo $is_foc_request ? 'required' : ''; ?>>
                                        <option value="">Select engineer</option>
                                        <?php foreach ($engineer_options as $engineer): ?>
                                            <option value="<?php echo (int) $engineer->user_id; ?>" <?php echo (int)($selected_engineer_id ?? 0) === (int)$engineer->user_id ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars(trim($engineer->first_name . ' ' . $engineer->last_name)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">FOC requests are assigned directly and do not require an Engineer Visit.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="field-label">Request Type <span class="text-danger">*</span></label>
                                    <select class="form-control" name="request_type" id="request_type" required>
                                        <?php foreach ($request_types as $type): ?>
                                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $selected_request_type === $type ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($type); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="field-label">Short Title</label>
                                    <input type="text" class="form-control" name="request_title" id="request_title" maxlength="255" placeholder="Example: Service support request">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="field-label">Requested Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="amount" id="amount" min="1" step="0.01" placeholder="Enter amount" required>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="field-label">Service From Date</label>
                                    <input type="date" class="form-control" name="service_from_date" id="service_from_date" value="<?php echo !empty($selected_visit->start_date) ? htmlspecialchars($selected_visit->start_date) : ''; ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="field-label">Service To Date</label>
                                    <input type="date" class="form-control" name="service_to_date" id="service_to_date" value="<?php echo !empty($selected_visit->end_date) ? htmlspecialchars($selected_visit->end_date) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="field-label">Link to Existing Request</label>
                                <select class="form-control select2" name="parent_request_id" id="parent_request_id">
                                    <option value="">Fresh request</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="field-label">Purpose / Requirement <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="purpose" id="purpose" placeholder="Mention where the payment will be used." required></textarea>
                            </div>

                            <div class="form-group hidden" id="linkedReasonWrap">
                                <label class="field-label">Linked Request Note</label>
                                <textarea class="form-control" name="extension_reason" id="extension_reason" placeholder="Add context if this request is linked to an earlier request."></textarea>
                            </div>

                            <div class="form-group">
                                <label class="field-label">Attachment</label>
                                <input type="file" class="form-control" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                            </div>

                            <div class="submit-row">
                                <div class="submit-note">
                                    Saves as <strong>Pending HOD Approval</strong>. Link the previous request when this request depends on an earlier one.
                                </div>
                                <button type="submit" class="btn btn-primary btn-submit" <?php echo !$can_submit_request ? 'disabled' : ''; ?>>
                                    <i class="fa fa-paper-plane-o"></i> Submit Service Payment Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="side-panel">
                        <h5>Quick Info</h5>
                        <ul class="meta-list">
                            <li>
                                <span class="meta-key">Approver</span>
                                <span class="meta-value"><?php echo htmlspecialchars($service_hod_name); ?></span>
                            </li>
                            <li>
                                <span class="meta-key">Email</span>
                                <span class="meta-value"><?php echo $service_hod_email !== '' ? htmlspecialchars($service_hod_email) : 'Not available'; ?></span>
                            </li>
                        </ul>

                        <div id="orderSummary" style="margin-top:12px;">
                            <?php if (!empty($selected_order)): ?>
                                <ul class="meta-list">
                                    <li><span class="meta-key">Order Ref</span><span class="meta-value"><?php echo htmlspecialchars($selected_order->op_no); ?></span></li>
                                    <li><span class="meta-key">Customer</span><span class="meta-value"><?php echo htmlspecialchars(!empty($selected_order->customer_name) ? $selected_order->customer_name : $selected_order->company_name); ?></span></li>
                                    <li><span class="meta-key">PO Number</span><span class="meta-value"><?php echo htmlspecialchars($selected_order->po_number); ?></span></li>
                                    <li><span class="meta-key">PO Amount</span><span class="meta-value">₹ <?php echo number_format((float) $selected_order->po_amount, 2); ?></span></li>
                                </ul>
                            <?php else: ?>
                                <div class="empty-state">Select a won order to load details.</div>
                            <?php endif; ?>
                        </div>

                        <div class="summary-strip" id="contextTotals">
                            <div class="summary-box">
                                <small>Approved</small>
                                <strong>₹ <?php echo number_format((float) ($context_totals['approved_total'] ?? 0), 2); ?></strong>
                            </div>
                            <div class="summary-box">
                                <small>Pending</small>
                                <strong>₹ <?php echo number_format((float) ($context_totals['pending_total'] ?? 0), 2); ?></strong>
                            </div>
                            <div class="summary-box">
                                <small>Requests</small>
                                <strong><?php echo (int) ($context_totals['request_count'] ?? 0); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="side-panel">
                        <h5>Previous Requests</h5>
                        <div class="request-chain" id="relatedRequestChain">
                            <?php if (empty($related_requests)): ?>
                                <div class="empty-state">No previous requests for this selection.</div>
                            <?php else: ?>
                                <?php foreach ($related_requests as $request): ?>
                                    <div class="chain-row">
                                        <div class="chain-head">
                                            <div class="chain-code"><?php echo htmlspecialchars($request->request_code); ?></div>
                                            <div class="chain-status status-<?php echo htmlspecialchars($request->status_slug); ?>"><?php echo htmlspecialchars($request->status); ?></div>
                                        </div>
                                        <div class="chain-meta">
                                            <?php echo htmlspecialchars($request->request_type); ?> | ₹ <?php echo number_format((float) $request->amount, 2); ?><br>
                                            <?php echo htmlspecialchars($request->created_by_name); ?> | <?php echo date('d M Y', strtotime($request->request_date)); ?>
                                            <?php if (!empty($request->parent_request_code)): ?>
                                                <br>Linked to <?php echo htmlspecialchars($request->parent_request_code); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

    <script>
        var visitOptions = <?php echo json_encode($visit_options); ?>;
        var selectedVisitId = <?php echo (int) ($selected_visit_id ?? 0); ?>;
        var selectedParentRequestId = <?php echo (int) ($selected_parent_request_id ?? 0); ?>;

        function currencyFormat(amount) {
            return '₹ ' + Number(amount || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function buildVisitLabel(visit) {
            return visit.op_no + ' | Visit #' + visit.visit_id + ' | ' + visit.engineer_name + ' | ' + visit.start_date + ' to ' + visit.end_date;
        }

        function isOrderWonType() {
            return $('#request_type').val() === 'Order Won';
        }

        function isFocType() {
            return $('#request_type').val() === 'FOC';
        }

        function fillVisitOptions() {
            var opportunityId = $('#opportunity_id').val();
            var visitSelector = $('#visit_selector');
            var currentVisit = $('#visit_id').val();

            visitSelector.empty().append('<option value="">Select visit</option>');

            $.each(visitOptions, function(_, visit) {
                if (opportunityId && String(visit.opportunity_id) !== String(opportunityId)) {
                    return;
                }

                var option = new Option(buildVisitLabel(visit), visit.visit_id, false, String(currentVisit) === String(visit.visit_id));
                $(option).attr('data-engineer-id', visit.engineer_id);
                $(option).attr('data-engineer-name', visit.engineer_name);
                $(option).attr('data-start-date', visit.start_date);
                $(option).attr('data-end-date', visit.end_date);
                visitSelector.append(option);
            });

            visitSelector.trigger('change.select2');
        }

        function toggleBasisSections() {
            var basis = $('input[name="request_basis"]:checked').val();
            var orderWonType = isOrderWonType();
            var focType = isFocType();
            var orderWrap = $('#orderSelectWrap');
            var visitWrap = $('#visitSelectWrap');
            var engineerWrap = $('#engineerSelectWrap');

            if (orderWonType || focType) {
                orderWrap.removeClass('hidden');
                $('#opportunity_id').prop('required', true);
            } else {
                orderWrap.addClass('hidden');
                $('#opportunity_id').prop('required', false);
                $('#opportunity_id').val('').trigger('change.select2');
            }

            if (focType) {
                visitWrap.addClass('hidden').removeClass('col-md-12').addClass('col-md-6');
                $('#visit_selector').prop('required', false).val('').trigger('change.select2');
                $('#visit_id').val('');
                engineerWrap.removeClass('hidden');
                $('#engineer_selector').prop('required', true);
                $('#requested_for_user_id').val($('#engineer_selector').val() || '');
            } else if (basis === 'VISIT' || !orderWonType) {
                visitWrap.removeClass('hidden col-md-6').addClass(orderWonType ? 'col-md-6' : 'col-md-12');
                $('#visit_selector').prop('required', true);
                engineerWrap.addClass('hidden');
                $('#engineer_selector').prop('required', false);
            } else {
                visitWrap.addClass('hidden');
                visitWrap.removeClass('col-md-12').addClass('col-md-6');
                $('#visit_selector').prop('required', false);
                $('#visit_selector').val('').trigger('change.select2');
                $('#visit_id').val('');
                $('#requested_for_user_id').val('');
                engineerWrap.addClass('hidden');
                $('#engineer_selector').prop('required', false);
            }

            fillVisitOptions();
        }

        function syncRequestTypeLayout() {
            var orderWonType = isOrderWonType();
            var focType = isFocType();
            var orderBasisOption = $('#orderBasisOption');
            var orderBasisInput = orderBasisOption.find('input[name="request_basis"]');
            var visitBasisInput = $('#visitBasisOption').find('input[name="request_basis"]');

            if (focType) {
                $('#requestBasisWrap').addClass('hidden');
                orderBasisOption.removeClass('hidden');
                orderBasisInput.prop('disabled', false).prop('checked', true);
                visitBasisInput.prop('checked', false);
            } else if (orderWonType) {
                $('#requestBasisWrap').removeClass('hidden');
                orderBasisOption.removeClass('hidden');
                orderBasisInput.prop('disabled', false);
            } else {
                $('#requestBasisWrap').removeClass('hidden');
                orderBasisOption.addClass('hidden');
                orderBasisInput.prop('disabled', true).prop('checked', false);
                visitBasisInput.prop('checked', true);
            }

            toggleBasisSections();
        }

        function toggleLinkedReason() {
            var parentRequestId = $('#parent_request_id').val();
            if (parentRequestId) {
                $('#linkedReasonWrap').removeClass('hidden');
            } else {
                $('#linkedReasonWrap').addClass('hidden');
            }
        }

        function renderOrderSummary(order) {
            if (!order) {
                $('#orderSummary').html('<div class="empty-state">Select a won order to load details.</div>');
                return;
            }

            var html = '<ul class="meta-list">' +
                '<li><span class="meta-key">Order Ref</span><span class="meta-value">' + order.op_no + '</span></li>' +
                '<li><span class="meta-key">Customer</span><span class="meta-value">' + order.customer_name + '</span></li>' +
                '<li><span class="meta-key">PO Number</span><span class="meta-value">' + order.po_number + '</span></li>' +
                '<li><span class="meta-key">PO Amount</span><span class="meta-value">' + currencyFormat(order.po_amount) + '</span></li>' +
                '</ul>';

            $('#orderSummary').html(html);
        }

        function renderTotals(totals) {
            totals = totals || {};
            var html = '' +
                '<div class="summary-box"><small>Approved</small><strong>' + currencyFormat(totals.approved_total) + '</strong></div>' +
                '<div class="summary-box"><small>Pending</small><strong>' + currencyFormat(totals.pending_total) + '</strong></div>' +
                '<div class="summary-box"><small>Requests</small><strong>' + (totals.request_count || 0) + '</strong></div>';

            $('#contextTotals').html(html);
        }

        function renderRelatedRequests(requests) {
            var parentSelect = $('#parent_request_id');
            parentSelect.empty().append('<option value="">Fresh request</option>');

            if (!requests || !requests.length) {
                $('#relatedRequestChain').html('<div class="empty-state">No previous requests for this selection.</div>');
                toggleLinkedReason();
                return;
            }

            var html = '';
            $.each(requests, function(_, request) {
                parentSelect.append(
                    $('<option>', {
                        value: request.request_id,
                        text: request.request_code + ' | ' + request.request_type + ' | ' + currencyFormat(request.amount),
                        selected: String(selectedParentRequestId) === String(request.request_id)
                    })
                );

                html += '<div class="chain-row">' +
                    '<div class="chain-head">' +
                    '<div class="chain-code">' + request.request_code + '</div>' +
                    '<div class="chain-status status-' + request.status.toLowerCase().replace(/[^a-z0-9]+/g, '-') + '">' + request.status + '</div>' +
                    '</div>' +
                    '<div class="chain-meta">' +
                    request.request_type + ' | ' + currencyFormat(request.amount) + '<br>' +
                    request.created_by_name + ' | ' + request.request_date +
                    (request.parent_request_code ? '<br>Linked to ' + request.parent_request_code : '') +
                    '</div>' +
                    '</div>';
            });

            $('#relatedRequestChain').html(html);
            parentSelect.trigger('change.select2');
            toggleLinkedReason();
        }

        function syncVisitFields(visit) {
            if (!visit) {
                $('#visit_id').val('');
                $('#requested_for_user_id').val('');
                return;
            }

            $('#visit_id').val(visit.visit_id);
            $('#requested_for_user_id').val(visit.engineer_id);
            $('#service_from_date').val(visit.start_date);
            $('#service_to_date').val(visit.end_date);
        }

        function refreshContext() {
            var opportunityId = $('#opportunity_id').val();
            var visitId = $('#visit_id').val();

            if (!opportunityId && !visitId) {
                renderOrderSummary(null);
                renderTotals({approved_total: 0, pending_total: 0, request_count: 0});
                renderRelatedRequests([]);
                return;
            }

            $.getJSON('<?php echo page_url; ?>ServiceLeads/get_service_payment_request_context', {
                opportunity_id: opportunityId,
                visit_id: visitId
            }).done(function(res) {
                if (!res.status) {
                    renderOrderSummary(null);
                    renderTotals({approved_total: 0, pending_total: 0, request_count: 0});
                    renderRelatedRequests([]);
                    return;
                }

                renderOrderSummary(res.order);
                renderTotals(res.totals);
                renderRelatedRequests(res.related_requests || []);

                if (res.visit) {
                    syncVisitFields(res.visit);
                }
            });
        }

        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            fillVisitOptions();
            syncRequestTypeLayout();
            toggleLinkedReason();
            refreshContext();

            $('input[name="request_basis"]').on('change', function() {
                toggleBasisSections();
                refreshContext();
            });

            $('#request_type').on('change', function() {
                syncRequestTypeLayout();
                refreshContext();
            });

            $('#parent_request_id').on('change', function() {
                toggleLinkedReason();
            });

            $('#opportunity_id').on('change', function() {
                $('#visit_id').val('');
                selectedParentRequestId = 0;
                fillVisitOptions();
                refreshContext();
            });

            $('#visit_selector').on('change', function() {
                var visitId = $(this).val();
                $('#visit_id').val(visitId);

                if (!visitId) {
                    $('#requested_for_user_id').val('');
                    refreshContext();
                    return;
                }

                var selectedOption = $(this).find(':selected');
                $('#requested_for_user_id').val(selectedOption.data('engineer-id') || '');
                refreshContext();
            });

            $('#engineer_selector').on('change', function() {
                if (isFocType()) {
                    $('#requested_for_user_id').val($(this).val() || '');
                }
            });
        });
    </script>
</body>
</html>
