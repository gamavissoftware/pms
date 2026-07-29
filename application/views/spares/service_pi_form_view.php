<?php
defined('BASEPATH') or exit('No direct script access allowed');

$currency = strtoupper(trim((string) ($pi->currency ?? 'INR')));
$currency_symbol = $currency === 'USD' ? '$' : '₹';
$company_color = $company_profile['colorcode'] ?? '#003366';
$pi_items = !empty($pi_items) ? $pi_items : [];

if (empty($pi_items)) {
    $pi_items[] = (object) [
        'sac_code' => '998719',
        'description' => '',
        'unit_rate' => 0,
        'no_of_days' => 1,
        'no_of_engineers' => 1,
        'uom' => 'Day',
        'is_customer_scope' => 0,
        'scope_note' => '',
        'row_total' => 0,
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Create PI</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f7fb; }
        .card-box { background: #fff; border-radius: 14px; border: 1px solid #e6ebf2; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06); padding: 24px; margin-bottom: 24px; }
        .page-title-box h4 { font-weight: 700; }
        .summary-strip { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
        .summary-chip { min-width: 180px; padding: 12px 14px; border-radius: 12px; background: #fff; border: 1px solid #e6ebf2; }
        .summary-chip small { display: block; color: #7b8794; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
        .summary-chip strong { display: block; margin-top: 4px; color: #17212b; font-size: 14px; }
        .section-title { font-size: 16px; font-weight: 700; color: #17212b; margin-bottom: 18px; }
        .section-title i { color: <?php echo $company_color; ?>; margin-right: 8px; }
        .compact-label { font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
        .totals-box { border: 1px solid #e6ebf2; border-radius: 12px; background: #fafcff; padding: 16px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .totals-row.total { border-top: 1px solid #dce4ee; margin-top: 8px; padding-top: 12px; font-weight: 700; font-size: 16px; }
        .item-table th { background: #f2f6fb; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; border-color: #dfe7f1 !important; }
        .item-table td { vertical-align: top !important; border-color: #e7edf5 !important; }
        .item-table textarea { min-height: 74px; resize: vertical; }
        .row-total-badge { display: inline-block; min-width: 130px; text-align: right; padding: 9px 10px; border-radius: 8px; background: #f7f9fc; font-weight: 600; }
        .scope-note-wrap.is-hidden { display: none; }
        .btn-outline-brand { border: 1px solid <?php echo $company_color; ?>; color: <?php echo $company_color; ?>; background: #fff; }
        .btn-brand { background: <?php echo $company_color; ?>; border-color: <?php echo $company_color; ?>; color: #fff; }
        .btn-brand:hover, .btn-brand:focus { color: #fff; }
        .required { color: #dc3545; }
        @media (max-width: 767px) {
            .summary-strip { display: block; }
            .summary-chip { margin-bottom: 10px; }
            .card-box { padding: 16px; }
        }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="btn-group pull-right">
                            <?php if (!empty($pi->id)) : ?>
                                <a href="<?php echo page_url; ?>ServiceLeads/view_pi_pdf/<?php echo (int) $pi->id; ?>" target="_blank" class="btn btn-danger waves-effect waves-light">
                                    <i class="fa fa-file-pdf-o"></i> View PDF
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo page_url; ?>ServiceLeads/opportunity_detail/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-default waves-effect waves-light">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                        <h4 class="page-title"><?php echo !empty($pi->id) ? 'Edit PI' : 'Create PI'; ?> - <?php echo htmlspecialchars($opportunity->op_no, ENT_QUOTES, 'UTF-8'); ?></h4>
                    </div>
                </div>
            </div>

            <div class="summary-strip">
                <div class="summary-chip">
                    <small>Customer</small>
                    <strong><?php echo htmlspecialchars($customer->company_name ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="summary-chip">
                    <small>Latest Quote</small>
                    <strong><?php echo htmlspecialchars($latest_quote->quotation_no ?? 'Not Available', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="summary-chip">
                    <small>PO No.</small>
                    <strong><?php echo htmlspecialchars($latest_po->po_number ?? 'Not Available', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="summary-chip">
                    <small>Currency</small>
                    <strong><?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>

            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
            <?php endif; ?>

            <form action="<?php echo page_url; ?>ServiceLeads/save_pi_details" method="post" id="servicePiForm">
                <input type="hidden" name="pi_id" value="<?php echo (int) ($pi->id ?? 0); ?>">
                <input type="hidden" name="opportunity_id" value="<?php echo (int) $opportunity->opportunity_id; ?>">
                <input type="hidden" name="quote_id" value="<?php echo (int) ($latest_quote->id ?? 0); ?>">
                <input type="hidden" name="po_id" value="<?php echo (int) ($latest_po->id ?? 0); ?>">
                <input type="hidden" name="currency" value="<?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action_type" id="action_type" value="save">

                <div class="card-box">
                    <div class="section-title"><i class="fa fa-file-text-o"></i> PI Details</div>
                    <div class="row">
                        <div class="col-md-2 form-group">
                            <label class="compact-label">PI No. <span class="required">*</span></label>
                            <input type="text" name="pi_no" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->pi_no ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="compact-label">PI Date <span class="required">*</span></label>
                            <input type="date" name="pi_date" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->pi_date ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="compact-label">GST %</label>
                            <input type="number" name="gst_percent" id="gst_percent" class="form-control" min="0" step="0.01" value="<?php echo htmlspecialchars((string) ($pi->gst_percent ?? 0), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="compact-label">Buyer PO Number</label>
                            <input type="text" name="buyer_order_no" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->buyer_order_no ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="compact-label">Buyer Order Date</label>
                            <input type="date" name="buyer_order_date" class="form-control" value="<?php echo !empty($pi->buyer_order_date) && $pi->buyer_order_date !== '0000-00-00' ? htmlspecialchars($pi->buyer_order_date, ENT_QUOTES, 'UTF-8') : ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <div class="section-title"><i class="fa fa-building-o"></i> Buyer & Consignee</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Buyer Name <span class="required">*</span></label>
                                    <input type="text" name="buyer_name" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->buyer_name ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Contact Person</label>
                                    <input type="text" name="buyer_contact" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->buyer_contact ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="compact-label">Buyer Address</label>
                                    <textarea name="buyer_address" class="form-control" rows="4"><?php echo htmlspecialchars((string) ($pi->buyer_address ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Buyer GSTIN</label>
                                    <input type="text" name="buyer_gstin" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->buyer_gstin ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Consignee Name</label>
                                    <input type="text" name="consignee_name" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->consignee_name ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Consignee Contact</label>
                                    <input type="text" name="consignee_contact" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->consignee_contact ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="compact-label">Consignee Address</label>
                                    <textarea name="consignee_address" class="form-control" rows="4"><?php echo htmlspecialchars((string) ($pi->consignee_address ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Consignee ECC No.</label>
                                    <input type="text" name="consignee_ecc_no" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->consignee_ecc_no ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Consignee Phone</label>
                                    <input type="text" name="consignee_phone" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->consignee_phone ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <div class="clearfix m-b-15">
                        <div class="section-title pull-left m-b-0"><i class="fa fa-list-alt"></i> PI Items</div>
                        <button type="button" id="addPiRow" class="btn btn-outline-brand btn-sm pull-right"><i class="fa fa-plus"></i> Add Row</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered item-table" id="piItemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 9%;">SAC</th>
                                    <th style="width: 28%;">Description</th>
                                    <th style="width: 10%;">Rate</th>
                                    <th style="width: 9%;">Days/Times</th>
                                    <th style="width: 9%;">Engg.</th>
                                    <th style="width: 9%;">UOM</th>
                                    <th style="width: 12%;">Scope</th>
                                    <th style="width: 10%;">Total</th>
                                    <th style="width: 4%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pi_items as $item) : ?>
                                    <tr class="pi-item-row">
                                        <td><input type="text" name="sac_code[]" class="form-control" value="<?php echo htmlspecialchars((string) ($item->sac_code ?? '998719'), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        <td>
                                            <textarea name="description[]" class="form-control"><?php echo htmlspecialchars((string) ($item->description ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                            <div class="scope-note-wrap <?php echo !empty($item->is_customer_scope) ? '' : 'is-hidden'; ?> m-t-10">
                                                <input type="text" name="scope_note[]" class="form-control input-sm scope-note" value="<?php echo htmlspecialchars((string) ($item->scope_note ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Customer scope note">
                                            </div>
                                        </td>
                                        <td><input type="number" step="0.01" min="0" name="unit_rate[]" class="form-control unit-rate" value="<?php echo htmlspecialchars((string) ((float) ($item->unit_rate ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        <td><input type="number" step="0.01" min="0" name="no_of_days[]" class="form-control days-count" value="<?php echo htmlspecialchars((string) ((float) ($item->no_of_days ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        <td><input type="number" step="0.01" min="0" name="no_of_engineers[]" class="form-control eng-count" value="<?php echo htmlspecialchars((string) ((float) ($item->no_of_engineers ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        <td>
                                            <select name="uom[]" class="form-control uom-select">
                                                <?php foreach (['Day', 'Days', 'Time', 'Times', 'Visit'] as $uom) : ?>
                                                    <option value="<?php echo $uom; ?>" <?php echo (($item->uom ?? 'Day') === $uom) ? 'selected' : ''; ?>><?php echo $uom; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="scope_type[]" class="form-control scope-type">
                                                <option value="0" <?php echo empty($item->is_customer_scope) ? 'selected' : ''; ?>>SPM Scope</option>
                                                <option value="1" <?php echo !empty($item->is_customer_scope) ? 'selected' : ''; ?>>Customer Scope</option>
                                            </select>
                                        </td>
                                        <td class="text-right"><span class="row-total-badge"><?php echo !empty($item->is_customer_scope) ? 'Customer Scope' : $currency_symbol . ' ' . number_format((float) ($item->row_total ?? 0), 2); ?></span></td>
                                        <td class="text-center"><button type="button" class="btn btn-link text-danger remove-row"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-md-offset-8">
                            <div class="totals-box">
                                <div class="totals-row"><span>Basic</span><strong id="basicTotalText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->basic_amount ?? 0), 2); ?></strong></div>
                                <div class="totals-row"><span>GST</span><strong id="gstTotalText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->gst_amount ?? 0), 2); ?></strong></div>
                                <div class="totals-row total"><span>Grand Total</span><strong id="grandTotalText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->grand_total ?? 0), 2); ?></strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <div class="section-title"><i class="fa fa-university"></i> Terms & Bank</div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Payment Terms</label>
                            <input type="text" name="payment_terms" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->payment_terms ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Country of Origin</label>
                            <input type="text" name="country_of_origin" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->country_of_origin ?? 'India'), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->bank_name ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Account No.</label>
                            <input type="text" name="account_no" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->account_no ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="compact-label">IFSC</label>
                            <input type="text" name="ifsc_code" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->ifsc_code ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Swift</label>
                            <input type="text" name="swift_code" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->swift_code ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Account Type</label>
                            <input type="text" name="account_type" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->account_type ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-8 form-group">
                            <label class="compact-label">Account Holder</label>
                            <input type="text" name="account_holder" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->account_holder ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="compact-label">Bank Address</label>
                            <textarea name="bank_address" class="form-control" rows="3"><?php echo htmlspecialchars((string) ($pi->bank_address ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="compact-label">Declaration</label>
                            <textarea name="declaration_text" class="form-control" rows="3"><?php echo htmlspecialchars((string) ($pi->declaration_text ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="compact-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars((string) ($pi->notes ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="text-right m-b-30">
                    <button type="submit" class="btn btn-default waves-effect waves-light" data-action="save">Save</button>
                    <button type="submit" class="btn btn-brand waves-effect waves-light" data-action="pdf">
                        <i class="fa fa-file-pdf-o"></i> Save & Open PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script>
        (function() {
            var currencySymbol = <?php echo json_encode($currency_symbol); ?>;

            function toNumber(value) {
                var num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            }

            function formatMoney(value) {
                return currencySymbol + ' ' + value.toFixed(2);
            }

            function updateRow($row) {
                var scope = $row.find('.scope-type').val();
                var rate = toNumber($row.find('.unit-rate').val());
                var days = toNumber($row.find('.days-count').val());
                var engineers = toNumber($row.find('.eng-count').val());
                var total = scope === '1' ? 0 : (rate * days * engineers);

                $row.find('.row-total-badge').text(scope === '1' ? 'Customer Scope' : formatMoney(total));
                $row.find('.scope-note-wrap').toggleClass('is-hidden', scope !== '1');
            }

            function updateTotals() {
                var basic = 0;

                $('#piItemsTable tbody tr').each(function() {
                    var $row = $(this);
                    updateRow($row);

                    if ($row.find('.scope-type').val() !== '1') {
                        basic += toNumber($row.find('.unit-rate').val()) * toNumber($row.find('.days-count').val()) * toNumber($row.find('.eng-count').val());
                    }
                });

                var gstPercent = toNumber($('#gst_percent').val());
                var gstAmount = (basic * gstPercent) / 100;
                var grandTotal = basic + gstAmount;

                $('#basicTotalText').text(formatMoney(basic));
                $('#gstTotalText').text(formatMoney(gstAmount));
                $('#grandTotalText').text(formatMoney(grandTotal));
            }

            function buildRow() {
                return '' +
                    '<tr class="pi-item-row">' +
                        '<td><input type="text" name="sac_code[]" class="form-control" value="998719"></td>' +
                        '<td>' +
                            '<textarea name="description[]" class="form-control"></textarea>' +
                            '<div class="scope-note-wrap is-hidden m-t-10">' +
                                '<input type="text" name="scope_note[]" class="form-control input-sm scope-note" placeholder="Customer scope note">' +
                            '</div>' +
                        '</td>' +
                        '<td><input type="number" step="0.01" min="0" name="unit_rate[]" class="form-control unit-rate" value="0"></td>' +
                        '<td><input type="number" step="0.01" min="0" name="no_of_days[]" class="form-control days-count" value="1"></td>' +
                        '<td><input type="number" step="0.01" min="0" name="no_of_engineers[]" class="form-control eng-count" value="1"></td>' +
                        '<td>' +
                            '<select name="uom[]" class="form-control uom-select">' +
                                '<option value="Day">Day</option>' +
                                '<option value="Days">Days</option>' +
                                '<option value="Time">Time</option>' +
                                '<option value="Times">Times</option>' +
                                '<option value="Visit">Visit</option>' +
                            '</select>' +
                        '</td>' +
                        '<td>' +
                            '<select name="scope_type[]" class="form-control scope-type">' +
                                '<option value="0">SPM Scope</option>' +
                                '<option value="1">Customer Scope</option>' +
                            '</select>' +
                        '</td>' +
                        '<td class="text-right"><span class="row-total-badge">' + formatMoney(0) + '</span></td>' +
                        '<td class="text-center"><button type="button" class="btn btn-link text-danger remove-row"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
            }

            $('#addPiRow').on('click', function() {
                $('#piItemsTable tbody').append(buildRow());
            });

            $(document).on('input change', '.unit-rate, .days-count, .eng-count, .scope-type, #gst_percent', updateTotals);

            $(document).on('click', '.remove-row', function() {
                var $rows = $('#piItemsTable tbody tr');
                if ($rows.length > 1) {
                    $(this).closest('tr').remove();
                    updateTotals();
                }
            });

            $('#servicePiForm button[type="submit"]').on('click', function() {
                $('#action_type').val($(this).data('action'));
            });

            updateTotals();
        })();
    </script>
</body>
</html>
