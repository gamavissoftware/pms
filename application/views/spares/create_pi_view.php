<?php
defined('BASEPATH') or exit('No direct script access allowed');

$currency = strtoupper(trim((string) ($pi->currency ?? 'INR')));
$currency_symbol = $currency === 'USD' ? '$' : '₹';
$company_color = $company_profile['colorcode'] ?? '#003366';
$pi_items = !empty($pi_items) ? $pi_items : [];
$unit_options = [
    'NOS' => 'NOS',
    'PCS' => 'PCS',
    'SET' => 'SET',
    'KG' => 'KG',
    'MTR' => 'MTR',
    'LTR' => 'LTR',
    'ROLL' => 'ROLL',
];

if (empty($pi_items)) {
    $pi_items[] = (object) [
        'product_id' => 0,
        'product_code' => '',
        'hsn_code' => '',
        'description' => '',
        'quantity' => 1,
        'unit' => 'NOS',
        'unit_price' => 0,
        'discount_percent' => 0,
        'discount_amount' => 0,
        'total_price' => 0,
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
        .summary-strip { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 18px; }
        .summary-chip { min-width: 180px; padding: 12px 14px; border-radius: 12px; background: #fff; border: 1px solid #e6ebf2; }
        .summary-chip small { display: block; color: #7b8794; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
        .summary-chip strong { display: block; margin-top: 4px; color: #17212b; font-size: 14px; }
        .section-title { font-size: 16px; font-weight: 700; color: #17212b; margin-bottom: 18px; }
        .section-title i { color: <?php echo $company_color; ?>; margin-right: 8px; }
        .compact-label { font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
        .item-table th { background: #f2f6fb; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; border-color: #dfe7f1 !important; }
        .item-table td { vertical-align: top !important; border-color: #e7edf5 !important; }
        .item-table textarea { min-height: 66px; resize: vertical; }
        .row-total-badge { display: inline-block; min-width: 120px; text-align: right; padding: 8px 10px; border-radius: 8px; background: #f7f9fc; font-weight: 600; }
        .totals-box { border: 1px solid #e6ebf2; border-radius: 12px; background: #fafcff; padding: 16px; }
        .totals-row { display: flex; justify-content: space-between; padding: 7px 0; font-size: 14px; }
        .totals-row.total { border-top: 1px solid #dce4ee; margin-top: 8px; padding-top: 12px; font-weight: 700; font-size: 16px; }
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
                                <a href="<?php echo page_url; ?>Spares/view_pi_pdf/<?php echo (int) $pi->id; ?>" target="_blank" class="btn btn-danger waves-effect waves-light">
                                    <i class="fa fa-file-pdf-o"></i> View PDF
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo page_url; ?>Spares/opportunity_detail/<?php echo (int) $opportunity->opportunity_id; ?>" class="btn btn-default waves-effect waves-light">
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
                    <small>Current Stage</small>
                    <strong><?php echo htmlspecialchars($opportunity->current_stage_name ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="summary-chip">
                    <small>Currency</small>
                    <strong><?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>

            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
            <?php endif; ?>

            <form action="<?php echo page_url; ?>Spares/save_pi_details" method="post" id="sparePiForm">
                <input type="hidden" name="pi_id" value="<?php echo (int) ($pi->id ?? 0); ?>">
                <input type="hidden" name="opportunity_id" value="<?php echo (int) $opportunity->opportunity_id; ?>">
                <input type="hidden" name="quote_id" value="<?php echo (int) (($pi->quote_id ?? 0) ?: ($latest_quote->quotation_id ?? 0)); ?>">
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
                        <div class="col-md-3 form-group">
                            <label class="compact-label">Attention</label>
                            <input type="text" name="attention" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->attention ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-3 form-group">
                            <label class="compact-label">Reference Quotation No.</label>
                            <input type="text" name="reference_quote_no" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->reference_quote_no ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="compact-label">Quote Date</label>
                            <input type="date" name="reference_quote_date" class="form-control" value="<?php echo !empty($pi->reference_quote_date) && $pi->reference_quote_date !== '0000-00-00' ? htmlspecialchars((string) $pi->reference_quote_date, ENT_QUOTES, 'UTF-8') : ''; ?>">
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
                                    <label class="compact-label">Buyer Contact</label>
                                    <input type="text" name="buyer_contact" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->buyer_contact ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Buyer Email</label>
                                    <input type="email" name="buyer_email" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->buyer_email ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Buyer GSTIN</label>
                                    <input type="text" name="buyer_gstin" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->buyer_gstin ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="compact-label">Buyer Address</label>
                                    <textarea name="buyer_address" class="form-control" rows="4"><?php echo htmlspecialchars((string) ($pi->buyer_address ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
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
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Consignee Phone</label>
                                    <input type="text" name="consignee_phone" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->consignee_phone ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="compact-label">Consignee GSTIN</label>
                                    <input type="text" name="consignee_gstin" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->consignee_gstin ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label class="compact-label">Consignee Address</label>
                                    <textarea name="consignee_address" class="form-control" rows="4"><?php echo htmlspecialchars((string) ($pi->consignee_address ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
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
                                    <th style="width: 10%;">Item Code</th>
                                    <th style="width: 10%;">HSN</th>
                                    <th style="width: 24%;">Description</th>
                                    <th style="width: 8%;">Qty</th>
                                    <th style="width: 8%;">Unit</th>
                                    <th style="width: 12%;">Unit Price</th>
                                    <th style="width: 9%;">Discount %</th>
                                    <th style="width: 12%;">Line Total</th>
                                    <th style="width: 4%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pi_items as $item) : ?>
                                    <tr class="pi-item-row">
                                        <td>
                                            <input type="hidden" name="product_id[]" value="<?php echo (int) ($item->product_id ?? 0); ?>">
                                            <input type="text" name="product_code[]" class="form-control" value="<?php echo htmlspecialchars((string) ($item->product_code ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                        </td>
                                        <td><input type="text" name="hsn_code[]" class="form-control" value="<?php echo htmlspecialchars((string) ($item->hsn_code ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        <td><textarea name="description[]" class="form-control"><?php echo htmlspecialchars((string) ($item->description ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea></td>
                                        <td><input type="number" step="0.01" min="0" name="quantity[]" class="form-control qty-input" value="<?php echo htmlspecialchars((string) ((float) ($item->quantity ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        <td>
                                            <?php
                                            $selected_unit = strtoupper(trim((string) ($item->unit ?? 'NOS')));
                                            $row_unit_options = $unit_options;
                                            if ($selected_unit !== '' && !isset($row_unit_options[$selected_unit])) {
                                                $row_unit_options[$selected_unit] = $selected_unit;
                                            }
                                            ?>
                                            <select name="unit[]" class="form-control">
                                                <?php foreach ($row_unit_options as $unit_value => $unit_label) : ?>
                                                    <option value="<?php echo htmlspecialchars($unit_value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected_unit === $unit_value ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($unit_label, ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" min="0" name="unit_price[]" class="form-control price-input" value="<?php echo htmlspecialchars((string) ((float) ($item->unit_price ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        <td><input type="number" step="0.01" min="0" name="discount_percent[]" class="form-control discount-input" value="<?php echo htmlspecialchars((string) ((float) ($item->discount_percent ?? 0)), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        <td class="text-right"><span class="row-total-badge"><?php echo $currency_symbol . ' ' . number_format((float) ($item->total_price ?? 0), 2); ?></span></td>
                                        <td class="text-center"><button type="button" class="btn btn-link text-danger remove-row"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-box">
                    <div class="section-title"><i class="fa fa-calculator"></i> Commercial Summary</div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="compact-label">Packing %</label>
                                    <input type="number" step="0.01" min="0" name="packing_percent" id="packing_percent" class="form-control" value="<?php echo htmlspecialchars((string) ((float) ($pi->packing_percent ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="compact-label">Freight Charge</label>
                                    <input type="number" step="0.01" min="0" name="freight_charge" id="freight_charge" class="form-control" value="<?php echo htmlspecialchars((string) ((float) ($pi->freight_charge ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="compact-label">Ex-Work Charge</label>
                                    <input type="number" step="0.01" min="0" name="ex_work_charge" id="ex_work_charge" class="form-control" value="<?php echo htmlspecialchars((string) ((float) ($pi->ex_work_charge ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="compact-label">Insurance %</label>
                                    <input type="number" step="0.01" min="0" name="insurance_percent" id="insurance_percent" class="form-control" value="<?php echo htmlspecialchars((string) ((float) ($pi->insurance_percent ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="compact-label">Overall Discount %</label>
                                    <input type="number" step="0.01" min="0" name="overall_discount_percent" id="overall_discount_percent" class="form-control" value="<?php echo htmlspecialchars((string) ((float) ($pi->overall_discount_percent ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="compact-label">GST %</label>
                                    <input type="number" step="0.01" min="0" name="gst_percent" id="gst_percent" class="form-control" value="<?php echo htmlspecialchars((string) ((float) ($pi->gst_percent ?? 0)), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="totals-box">
                                <div class="totals-row"><span>Basic Value</span><strong id="basicValueText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->basic_value ?? 0), 2); ?></strong></div>
                                <div class="totals-row"><span>Packing</span><strong id="packingChargeText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->packing_charge ?? 0), 2); ?></strong></div>
                                <div class="totals-row"><span>Insurance</span><strong id="insuranceChargeText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->insurance_charge ?? 0), 2); ?></strong></div>
                                <div class="totals-row"><span>Discount</span><strong id="discountAmountText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->overall_discount_amount ?? 0), 2); ?></strong></div>
                                <div class="totals-row"><span>Taxable Value</span><strong id="totalValueText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->total_value ?? 0), 2); ?></strong></div>
                                <div class="totals-row"><span>GST</span><strong id="gstAmountText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->gst_amount ?? 0), 2); ?></strong></div>
                                <div class="totals-row total"><span>Grand Total</span><strong id="grandTotalText"><?php echo $currency_symbol . ' ' . number_format((float) ($pi->grand_total ?? 0), 2); ?></strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <div class="section-title"><i class="fa fa-university"></i> Commercial Terms</div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Payment Terms</label>
                            <input type="text" name="payment_terms" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->payment_terms ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Validity</label>
                            <input type="text" name="validity" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->validity ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="compact-label">Delivery Terms</label>
                            <input type="text" name="delivery_terms" class="form-control" value="<?php echo htmlspecialchars((string) ($pi->delivery_terms ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="compact-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars((string) ($pi->notes ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="compact-label">Declaration</label>
                            <textarea name="declaration_text" class="form-control" rows="3"><?php echo htmlspecialchars((string) ($pi->declaration_text ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
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
            var unitOptions = <?php echo json_encode($unit_options); ?>;

            function toNumber(value) {
                var num = parseFloat(value);
                return isNaN(num) ? 0 : num;
            }

            function formatMoney(value) {
                return currencySymbol + ' ' + value.toFixed(2);
            }

            function buildUnitOptions(selectedValue) {
                var html = '';

                Object.keys(unitOptions).forEach(function(unitValue) {
                    var selected = unitValue === selectedValue ? ' selected' : '';
                    html += '<option value="' + unitValue + '"' + selected + '>' + unitOptions[unitValue] + '</option>';
                });

                return html;
            }

            function updateRow($row) {
                var qty = toNumber($row.find('.qty-input').val());
                var price = toNumber($row.find('.price-input').val());
                var discountPercent = toNumber($row.find('.discount-input').val());
                var lineBeforeDiscount = qty * price;
                var discountAmount = (lineBeforeDiscount * discountPercent) / 100;
                var total = lineBeforeDiscount - discountAmount;

                $row.find('.row-total-badge').text(formatMoney(total));
                return total;
            }

            function updateTotals() {
                var basicValue = 0;

                $('#piItemsTable tbody tr').each(function() {
                    basicValue += updateRow($(this));
                });

                var packingPercent = toNumber($('#packing_percent').val());
                var freightCharge = toNumber($('#freight_charge').val());
                var exWorkCharge = toNumber($('#ex_work_charge').val());
                var insurancePercent = toNumber($('#insurance_percent').val());
                var overallDiscountPercent = toNumber($('#overall_discount_percent').val());
                var gstPercent = toNumber($('#gst_percent').val());

                var packingCharge = (basicValue * packingPercent) / 100;
                var insuranceCharge = (basicValue * insurancePercent) / 100;
                var subTotal = basicValue + packingCharge + freightCharge + exWorkCharge + insuranceCharge;
                var discountAmount = (subTotal * overallDiscountPercent) / 100;
                var totalValue = subTotal - discountAmount;
                var gstAmount = (totalValue * gstPercent) / 100;
                var grandTotal = totalValue + gstAmount;

                $('#basicValueText').text(formatMoney(basicValue));
                $('#packingChargeText').text(formatMoney(packingCharge));
                $('#insuranceChargeText').text(formatMoney(insuranceCharge));
                $('#discountAmountText').text(formatMoney(discountAmount));
                $('#totalValueText').text(formatMoney(totalValue));
                $('#gstAmountText').text(formatMoney(gstAmount));
                $('#grandTotalText').text(formatMoney(grandTotal));
            }

            function buildRow() {
                return '' +
                    '<tr class="pi-item-row">' +
                        '<td><input type="hidden" name="product_id[]" value="0"><input type="text" name="product_code[]" class="form-control"></td>' +
                        '<td><input type="text" name="hsn_code[]" class="form-control"></td>' +
                        '<td><textarea name="description[]" class="form-control"></textarea></td>' +
                        '<td><input type="number" step="0.01" min="0" name="quantity[]" class="form-control qty-input" value="1"></td>' +
                        '<td><select name="unit[]" class="form-control">' + buildUnitOptions('NOS') + '</select></td>' +
                        '<td><input type="number" step="0.01" min="0" name="unit_price[]" class="form-control price-input" value="0"></td>' +
                        '<td><input type="number" step="0.01" min="0" name="discount_percent[]" class="form-control discount-input" value="0"></td>' +
                        '<td class="text-right"><span class="row-total-badge">' + formatMoney(0) + '</span></td>' +
                        '<td class="text-center"><button type="button" class="btn btn-link text-danger remove-row"><i class="fa fa-trash"></i></button></td>' +
                    '</tr>';
            }

            $('#addPiRow').on('click', function() {
                $('#piItemsTable tbody').append(buildRow());
            });

            $(document).on('input change', '.qty-input, .price-input, .discount-input, #packing_percent, #freight_charge, #ex_work_charge, #insurance_percent, #overall_discount_percent, #gst_percent', updateTotals);

            $(document).on('click', '.remove-row', function() {
                var $rows = $('#piItemsTable tbody tr');
                if ($rows.length > 1) {
                    $(this).closest('tr').remove();
                    updateTotals();
                }
            });

            $('#sparePiForm button[type="submit"]').on('click', function() {
                $('#action_type').val($(this).data('action'));
            });

            updateTotals();
        })();
    </script>
</body>
</html>
