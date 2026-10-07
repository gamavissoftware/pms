<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();
// Currency Detection Logic
$is_export = ($opportunity->op_type != 1); 
$curr_symbol = $is_export ? '$' : '₹';
$curr_text = $is_export ? 'USD' : 'INR';
$spare_pi = isset($spare_pi) ? $spare_pi : null;
$has_quotation = !empty($quotations);
$is_cancelled_quotation = !empty($is_cancelled_quotation);
$can_manage_pi = !$is_cancelled_quotation && (!empty($spare_pi) || ($has_quotation && ((int) ($opportunity->current_stage_id ?? 0) !== 11) && (int) ($opportunity->current_stage_order ?? 0) >= 9));
$pi_currency = strtoupper(trim((string) ($spare_pi->currency ?? $curr_text)));
$pi_symbol = $pi_currency === 'USD' ? '$' : '₹';
$quotation_type_labels = array(
    'CONSUMABLE' => 'Consumable',
    'CRITICAL' => 'Critical',
    'CONS_CRITICAL' => 'Consumable + Critical',
    'CUSTOM_ENGG' => 'Custom Engg.',
);
$custom_engg_type_labels = array(
    'CHANGEOVER' => 'Changeover',
    'SPEED_UPGRADATION' => 'Speed Upgradation',
);
$dispatch_mode_labels = array(
    'SELF_PICKUP' => 'Self Pickup',
    'COURIER' => 'Courier',
);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Opportunity Details</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { background-color: #ffffff; border-radius: 12px; border: 1px solid #e9ecef; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); padding: 25px; margin-bottom: 25px; }
        .page-header h2 { font-weight: 600; color: #343a40; margin: 0; display: inline-block; vertical-align: middle; }
        .page-header .op-no { font-size: 1rem; color: #6c757d; font-weight: 400; margin-left: 10px; }
        .nav-tabs > li > a .badge { background-color: #777; color: white; }
        .nav-tabs > li.active > a .badge { background-color: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; }
        .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus { color: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; border-top: 3px solid <?php echo $company_info->colorcode ?? '#4a90e2'; ?> !important; }
        .info-list .info-item { display: flex; align-items: center; margin-bottom: 15px; font-size: 14px; }
        .info-list .info-item .info-icon { color: <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; font-size: 16px; width: 30px; text-align: center; margin-right: 10px; }
        .info-list .info-item .info-label { font-weight: 600; color: #495057; width: 150px; }
        .info-list .info-item .info-value { color: #6c757d; }
        .history-timeline { list-style: none; padding-left: 0; }
        .history-timeline li { position: relative; padding: 10px 0 20px 25px; border-left: 2px solid #e9ecef; }
        .history-timeline li::before { content: ''; position: absolute; left: -8px; top: 12px; width: 14px; height: 14px; background: #fff; border: 3px solid <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; border-radius: 50%; }
        .is-invalid { border-color: #dc3545 !important; }
        #growl-container { position: fixed; top: 80px; right: 20px; z-index: 9999; width: 320px; }
        .growl-notification { padding: 15px; margin-bottom: 10px; border-radius: 8px; color: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.2); opacity: 0; transform: translateX(100%); transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); font-size: 14px; }
        .growl-notification.show { opacity: 1; transform: translateX(0); }
        .growl-notification.success { background-color: #28a745; }
        .growl-notification.error { background-color: #dc3545; }
        .follow-up-label .text-danger { display: none; }
        .pi-card { border-left: 4px solid <?php echo $company_info->colorcode ?? '#4a90e2'; ?>; }
        .pi-meta { color: #6c757d; margin-bottom: 8px; }
        .cancelled-alert { border-left: 5px solid #e11d48; background-color: #fff7f8; }
        .quote-flow-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; background: #eef6ff; color: #1f4e79; font-size: 11px; font-weight: 600; }
        .dispatch-badge { display: inline-block; margin-top: 4px; color: #667085; font-size: 11px; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div id="growl-container"></div>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">
                             <a href="<?php echo page_url; ?>Spares/opportunity_list" class="btn btn-default waves-effect waves-light"><i class="fa fa-arrow-left"></i> Back to List</a>
                        </div>
                        <h4 class="page-title">Opportunity Details & Tracking</h4>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-5">
                    <div class="page-header" style="margin-bottom: 25px;">
                        <h2><?php echo htmlspecialchars($opportunity->company_name); ?></h2>
                        <span class="op-no">(<?php echo htmlspecialchars($opportunity->op_no); ?>)</span>
                    </div>

                    <?php if ($is_cancelled_quotation): ?>
                    <div class="card-box cancelled-alert">
                        <h4 class="m-t-0 text-danger"><b><i class="fa fa-calendar-times-o"></i> Quotation Automatically Cancelled</b></h4>
                        <p>This quotation became inactive after one month without progressing to PI, PO, or order stages.</p>
                        <p class="small text-muted">
                            Previous stage: <b><?php echo htmlspecialchars($quotation_cancellation->previous_stage_name ?? 'Quotation Follow-up'); ?></b>
                            <?php if (!empty($quotation_cancellation->quotation_date)): ?>
                                | Quotation date: <b><?php echo date('d M Y', strtotime($quotation_cancellation->quotation_date)); ?></b>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($can_reopen_quotation)): ?>
                            <form method="post" action="<?php echo page_url; ?>Spares/reopen_cancelled_quotation/<?php echo (int) $opportunity->opportunity_id; ?>" onsubmit="return confirm('Reopen this quotation at <?php echo htmlspecialchars($quotation_cancellation->previous_stage_name ?? 'its previous stage', ENT_QUOTES, 'UTF-8'); ?>?');">
                                <input type="hidden" name="return_url" value="<?php echo page_url; ?>Spares/opportunity_detail/<?php echo (int) $opportunity->opportunity_id; ?>">
                                <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-undo"></i> Reopen at Previous Stage</button>
                            </form>
                        <?php else: ?>
                            <p class="small text-muted m-b-0">The account owner or Spares manager can reopen this quotation when the customer responds.</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!$is_cancelled_quotation): ?>
                    <div class="card-box" id="progressCard">
                        <h4 class="m-t-0 m-b-20 header-title"><b>Update Progress</b></h4>
                        
                        <form id="progressForm" method="post" action="<?php echo page_url; ?>Spares/update_opportunity_progress/<?php echo $opportunity->opportunity_id; ?>">
                            <div class="form-group">
                                <label>Current Stage</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($opportunity->current_stage_name); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>New Stage <span class="text-danger">*</span></label>
                                <select class="form-control required-field" name="new_stage_id" id="new_stage_select">
                                    <option value="">Select New Stage</option>
                                    <?php foreach ($next_stages as $stage): ?>
                                    <option 
                                        value="<?php echo $stage['lead_id']; ?>" 
                                        data-followup-required="<?php echo $stage['followup_date_required'] ? 'true' : 'false'; ?>">
                                        <?php echo htmlspecialchars($stage['lead_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remarks <span class="text-danger">*</span></label>
                                <textarea rows="4" name="remarks" class="form-control required-field" placeholder="Enter status update details..."></textarea>
                            </div>
                            <div class="form-group">
                                <label class="follow-up-label">Next Follow-up Date <span class="text-danger">*</span></label>
                                <input class="form-control" name="followup_date" id="followup_date" type="text" placeholder="Select Date">
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary waves-effect waves-light" style="background-color:<?php echo $company_info->colorcode ?? '#4a90e2'; ?>; border-color:<?php echo $company_info->colorcode ?? '#4a90e2'; ?>;">Update Status</button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                    <?php if ($can_manage_pi): ?>
                    <div class="card-box pi-card">
                        <div class="clearfix m-b-15">
                            <h4 class="m-t-0 header-title pull-left"><b>Proforma Invoice</b></h4>
                            <span class="label <?php echo !empty($spare_pi) ? 'label-success' : 'label-warning'; ?> pull-right">
                                <?php echo !empty($spare_pi) ? 'Created' : 'Ready to Create'; ?>
                            </span>
                        </div>

                        <?php if (!empty($spare_pi)): ?>
                            <p class="pi-meta"><b>PI No:</b> <?php echo htmlspecialchars($spare_pi->pi_no, ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="pi-meta"><b>Date:</b> <?php echo !empty($spare_pi->pi_date) ? date('d M, Y', strtotime($spare_pi->pi_date)) : '-'; ?></p>
                            <p class="pi-meta"><b>Quote Ref:</b> <?php echo htmlspecialchars($spare_pi->reference_quote_no ?: '-', ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="pi-meta m-b-0"><b>Value:</b> <?php echo $pi_symbol . ' ' . number_format((float) $spare_pi->grand_total, 2); ?></p>

                            <div class="m-t-15">
                                <a href="<?php echo page_url; ?>Spares/create_pi/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-pencil"></i> Edit PI
                                </a>
                                <a href="<?php echo page_url; ?>Spares/view_pi_pdf/<?php echo (int) $spare_pi->id; ?>" target="_blank" class="btn btn-danger btn-sm">
                                    <i class="fa fa-file-pdf-o"></i> View PDF
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="text-muted m-b-15">PI can now be created from the latest shared quotation. It will pull quoted items, charges, taxes, and customer details into a strong letterhead PDF.</p>
                            <a href="<?php echo page_url; ?>Spares/create_pi/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Create PI
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-7">
                    <div class="card-box">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
                            <li><a href="#products" data-toggle="tab">Products</a></li>
                            <li><a href="#quotations" data-toggle="tab">Quotations <span class="badge"><?php echo count($quotations); ?></span></a></li>
                            <li><a href="#proforma_invoices" data-toggle="tab">Proforma Invoices <span class="badge"><?php echo !empty($spare_pi) ? 1 : 0; ?></span></a></li>
                            <li><a href="#purchase_orders" data-toggle="tab">Purchase Orders <span class="badge"><?php echo count($purchase_orders); ?></span></a></li>
                            <li><a href="#sales_orders" data-toggle="tab">Sales Orders <span class="badge"><?php echo count($sales_orders); ?></span></a></li>
                            <li><a href="#history" data-toggle="tab">History</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="overview">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Key Metrics</b></h4>
                                <div class="info-list">
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-sitemap"></i></div><div class="info-label">Opportunity No</div><div class="info-value"><?php echo htmlspecialchars($opportunity->op_no); ?></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-calendar"></i></div><div class="info-label">Created Date</div><div class="info-value"><?php echo date('d M, Y', strtotime($opportunity->op_date)); ?></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-user"></i></div><div class="info-label">Marketing</div><div class="info-value"><?php echo htmlspecialchars($opportunity->marketing_person_name); ?></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-globe"></i></div><div class="info-label">Type</div><div class="info-value"><span class="label label-inverse"><?php echo ($opportunity->op_type == 1) ? 'Domestic' : 'Export'; ?></span></div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-line-chart"></i></div><div class="info-label">Probability</div><div class="info-value"><?php echo $opportunity->probability; ?>%</div></div>
                                    <div class="info-item"><div class="info-icon"><i class="fa fa-info-circle"></i></div><div class="info-label">Current Status</div><div class="info-value"><span class="<?php echo $is_cancelled_quotation ? 'text-danger' : 'text-primary'; ?>"><b><?php echo $is_cancelled_quotation ? 'Cancelled' : htmlspecialchars($opportunity->status); ?></b></span></div></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="products">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Enquired Items</b></h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-light">
                                                <th>#</th><th>Product Code & Description</th><th class="text-center">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($products)): $i = 1; foreach ($products as $product): ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><b><?php echo htmlspecialchars($product->code); ?></b> - <?php echo htmlspecialchars($product->description); ?></td>
                                                <td class="text-center"><?php echo $product->quantity; ?></td>
                                            </tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="3" class="text-center">No products enquired.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="history">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Timeline</b></h4>
                                <ul class="history-timeline">
                                    <?php if (!empty($history)): foreach ($history as $item): ?>
                                    <li>
                                        <p class="m-b-5">Stage: <b><?php echo htmlspecialchars($item->stage_name); ?></b></p>
                                        <p class="text-muted small">"<?php echo nl2br(htmlspecialchars($item->remarks)); ?>"</p>
                                        <small class="text-muted">
                                            <i class="fa fa-user"></i> <?php echo htmlspecialchars($item->added_by_name); ?> | 
                                            <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($item->added_on)); ?>
                                            <?php if($item->next_follow_date && $item->next_follow_date != '0000-00-00'): ?>
                                            | Follow-up: <b><?php echo date('d M Y', strtotime($item->next_follow_date)); ?></b>
                                            <?php endif; ?>
                                        </small>
                                    </li>
                                    <?php endforeach; else: ?>
                                    <li><p>No activity yet.</p></li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                            <div class="tab-pane" id="quotations">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Proposals</b></h4>
                                <?php if (!empty($quotations)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr><th>Quote No</th><th>Date</th><th>SF Flow</th><th class="text-right">Value (<?php echo $curr_text; ?>)</th><th class="text-center">Action</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($quotations as $quote): ?>
                                                <?php
                                                    $quote_type = strtoupper(trim((string) ($quote->quotation_type ?? 'CONSUMABLE')));
                                                    $custom_type = strtoupper(trim((string) ($quote->custom_engg_type ?? '')));
                                                    $dispatch_mode = strtoupper(trim((string) ($quote->dispatch_mode ?? 'COURIER')));
                                                    $quote_type_label = isset($quotation_type_labels[$quote_type]) ? $quotation_type_labels[$quote_type] : 'Consumable';
                                                    if ($quote_type === 'CUSTOM_ENGG' && isset($custom_engg_type_labels[$custom_type])) {
                                                        $quote_type_label .= ' - ' . $custom_engg_type_labels[$custom_type];
                                                    }
                                                    $dispatch_label = isset($dispatch_mode_labels[$dispatch_mode]) ? $dispatch_mode_labels[$dispatch_mode] : 'Courier';
                                                ?>
                                                <tr>
                                                    <td><b><?php echo htmlspecialchars($quote->quotation_no); ?></b></td>
                                                    <td><?php echo date('d M, Y', strtotime($quote->quotation_date)); ?></td>
                                                    <td>
                                                        <span class="quote-flow-badge"><?php echo htmlspecialchars($quote_type_label); ?></span><br>
                                                        <span class="dispatch-badge"><i class="fa fa-truck"></i> <?php echo htmlspecialchars($dispatch_label); ?></span>
                                                    </td>
                                                    <td class="text-right"><?php echo $curr_symbol; ?> <?php echo number_format($quote->total_value, 2); ?></td>
                                                    <td class="text-center">
                                                        <a href="<?php echo page_url; ?>Spares/view_quotation_pdf/<?php echo $quote->quotation_id; ?>" target="_blank" class="btn btn-primary btn-xs waves-effect"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted p-t-30">No quotations generated.</div>
                                <?php endif; ?>
                            </div>

                            <div class="tab-pane" id="proforma_invoices">
                                <h4 class="m-t-20 m-b-20 header-title"><b>PI Workspace</b></h4>
                                <?php if (!empty($spare_pi)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>PI No</th>
                                                    <th>Date</th>
                                                    <th>Quote Ref</th>
                                                    <th class="text-right">Grand Total</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><b><?php echo htmlspecialchars($spare_pi->pi_no, ENT_QUOTES, 'UTF-8'); ?></b></td>
                                                    <td><?php echo !empty($spare_pi->pi_date) ? date('d M, Y', strtotime($spare_pi->pi_date)) : '-'; ?></td>
                                                    <td><?php echo htmlspecialchars($spare_pi->reference_quote_no ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="text-right"><?php echo $pi_symbol; ?> <?php echo number_format((float) $spare_pi->grand_total, 2); ?></td>
                                                    <td class="text-center">
                                                        <a href="<?php echo page_url; ?>Spares/create_pi/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-primary btn-xs waves-effect m-r-5"><i class="fa fa-pencil"></i> Edit</a>
                                                        <a href="<?php echo page_url; ?>Spares/view_pi_pdf/<?php echo (int) $spare_pi->id; ?>" target="_blank" class="btn btn-danger btn-xs waves-effect"><i class="fa fa-file-pdf-o"></i> PDF</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php elseif ($can_manage_pi): ?>
                                    <div class="text-center text-muted p-t-30">
                                        PI has not been created yet for this opportunity.
                                        <br>
                                        <a href="<?php echo page_url; ?>Spares/create_pi/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-success btn-sm m-t-15">
                                            <i class="fa fa-plus"></i> Create PI
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted p-t-30">PI will be available once the opportunity reaches "Quotation Shared & Following Up".</div>
                                <?php endif; ?>
                            </div>

                            <div class="tab-pane" id="purchase_orders">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Received Documents</b></h4>
                                <?php if (!empty($purchase_orders)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr><th>Customer PO No</th><th>Date</th><th class="text-right">Value</th><th class="text-center">Downloads</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($purchase_orders as $po): ?>
                                                <tr>
                                                    <td><b><?php echo htmlspecialchars($po->po_no); ?></b></td>
                                                    <td><?php echo date('d M, Y', strtotime($po->po_date)); ?></td>
                                                    <td class="text-right"><?php echo $curr_symbol; ?> <?php echo number_format($po->grand_total, 2); ?></td>
                                                    <td class="text-center">
                                                        <a href="<?php echo page_url; ?>Spares/view_po_pdf/<?php echo $po->po_id; ?>" target="_blank" class="btn btn-inverse btn-xs m-r-5" title="View Generated SO PDF"><i class="fa fa-file-text-o"></i> View SO</a>
                                                        <?php if(!empty($po->po_copy)): ?>
                                                            <a href="<?php echo uploadsurl.'po_copies/'.$po->po_copy; ?>" target="_blank" class="btn btn-success btn-xs" title="Download Uploaded PO File"><i class="fa fa-download"></i> Orig. PO</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted p-t-30">No Purchase Orders recorded.</div>
                                <?php endif; ?>
                            </div>

                            <div class="tab-pane" id="sales_orders">
                                <h4 class="m-t-20 m-b-20 header-title"><b>Finalized Sales Orders</b></h4>
                                <?php if (!empty($sales_orders)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr class="bg-success text-white">
                                                    <th>SO ID</th><th>Date Confirmed</th><th class="text-right">Final Value (<?php echo $curr_text; ?>)</th><th class="text-center">Ref</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($sales_orders as $so): ?>
                                                <tr>
                                                    <td><b>SO-<?php echo $so->po_id; ?></b></td>
                                                    <td><?php echo date('d M, Y', strtotime($so->order_date)); ?></td>
                                                    <td class="text-right"><b><?php echo $curr_symbol; ?> <?php echo number_format($so->order_value, 2); ?></b></td>
                                                    <td class="text-center"><span class="badge badge-success">Closed</span></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted p-t-30">This opportunity has not been marked as "Won" yet.</div>
                                <?php endif; ?>
                            </div>

                        </div>
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
    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#new_stage_select').on('change', function() {
                var selectedStage = $(this).find('option:selected');
                var stageId = selectedStage.val();
                var followupRequired = selectedStage.data('followup-required');
                
                // Redirect logic based on stage
                var opportunityId = <?php echo $opportunity->opportunity_id; ?>;
                if (stageId == '7') { window.location.href = "<?php echo page_url; ?>Spares/create_quotation/" + opportunityId; return; }
                else if (stageId == '9') { window.location.href = "<?php echo page_url; ?>Spares/revise_quotation/" + opportunityId; return; }
                else if (stageId == '13') { window.location.href = "<?php echo page_url; ?>Spares/create_pi/" + opportunityId; return; }
                else if (stageId == '14') { window.location.href = "<?php echo page_url; ?>Spares/create_po/" + opportunityId; return; }
                else if (stageId == '10') { window.location.href = "<?php echo page_url; ?>Spares/orderwon/" + opportunityId; return; }

                if (followupRequired == true) {
                    $('#followup_date').addClass('required-field');
                    $('.follow-up-label .text-danger').show();
                } else {
                    $('#followup_date').removeClass('required-field is-invalid');
                    $('.follow-up-label .text-danger').hide();
                }
            });

            jQuery('#followup_date').datepicker({ autoclose: true, todayHighlight: true, format: 'dd-mm-yyyy', startDate: new Date() });

            $('#progressForm').on('submit', function(e) {
                e.preventDefault(); 
                let isValid = true;
                $('#progressForm .required-field').each(function() { if (!$(this).val()) { $(this).addClass('is-invalid'); isValid = false; } else { $(this).removeClass('is-invalid'); } });
                if (!isValid) { showGrowl('Please fill all required fields.', 'error'); return false; }
                
                $('#progressCard').block({ message: 'Saving...' });
                var form = $(this);
                $.ajax({
                    type: 'POST', url: form.attr('action'), data: form.serialize(), dataType: 'json',
                    success: function(response) { 
                        if (response.status === 'success') { showGrowl(response.message, 'success'); setTimeout(() => location.reload(), 1500); } 
                        else { $('#progressCard').unblock(); showGrowl(response.message, 'error'); } 
                    },
                    error: function(xhr) {
                        $('#progressCard').unblock();

                        var message = 'Server Connection Error.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            var plainText = $('<div>').html(xhr.responseText).text().trim();
                            if (plainText) {
                                message = plainText.substring(0, 200);
                            }
                        }

                        showGrowl(message, 'error');
                    }
                });
            });
        });
        function showGrowl(message, type) {
            var $growl = $('<div class="growl-notification ' + type + '">' + message + '</div>');
            $('#growl-container').append($growl);
            setTimeout(() => { $growl.addClass('show'); }, 10);
            setTimeout(() => { $growl.removeClass('show'); setTimeout(() => { $growl.remove(); }, 500); }, 4000);
        }
    </script>
</body>
</html>
