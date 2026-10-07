<?php $default_item_gst_percent = ((int) ($opportunity->country_id ?? 101) === 101) ? '18.00' : '0.00'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Quotation</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table thead th { background-color: #f1f5f9; white-space: nowrap; }
        .remove-row { margin-top: 5px; }
        .is-invalid { border-color: #dc3545 !important; }
        #growl-container { position: fixed; top: 80px; right: 20px; z-index: 9999; width: 320px; }
        .growl-notification { padding: 15px; margin-bottom: 10px; border-radius: 8px; color: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.2); opacity: 0; transform: translateX(100%); transition: all 0.5s; font-size: 14px; }
        .growl-notification.show { opacity: 1; transform: translateX(0); }
        .growl-notification.success { background-color: #28a745; }
        .growl-notification.error { background-color: #dc3545; }
        .select2-container { width: 100% !important; }
        #product-table { table-layout: fixed; }
        #product-table th:first-child { width: 35%; }
        /* Export Intelligence Styles */
        .export-header { border-left: 4px solid #d9534f; padding-left: 15px; }
        .converter-box { background: #fff4e5; border: 1px solid #ffd1a3; padding: 12px; border-radius: 8px; }
        .converter-box label { font-size: 11px; text-transform: uppercase; color: #a36f37; margin-bottom: 4px; display: block; }
        .highlight-input { border: 2px solid #f39c12 !important; background: #fffdfa; }
        .quote-history-box { margin-top: 8px; padding: 8px 10px; background: #f8fafc; border: 1px solid #dde7f0; border-radius: 8px; min-height: 56px; }
        .quote-history-title { font-size: 10px; font-weight: 600; color: #516072; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 6px; }
        .quote-history-empty { font-size: 11px; color: #7a8797; }
        .quote-history-item { padding-top: 7px; margin-top: 7px; border-top: 1px dashed #d7e2ec; }
        .quote-history-item:first-child { padding-top: 0; margin-top: 0; border-top: 0; }
        .quote-history-main { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .quote-history-price { font-size: 13px; font-weight: 600; color: #243447; }
        .quote-history-meta { font-size: 11px; color: #6d7a89; margin-top: 4px; line-height: 1.4; }
        .unit-price-lock .btn { min-width: 38px; }
        .unit-price.price-fixed-input { background: #fff8e8; border-color: #f0ad4e; }
        .charge-mode-row { margin-left: -5px; margin-right: -5px; }
        .charge-mode-row > div { padding-left: 5px; padding-right: 5px; }
        .charge-mode-hint { display: block; margin-top: 5px; font-size: 11px; color: #7a8797; line-height: 1.35; }
        .sf-flow-strip { margin-top: 16px; padding-top: 16px; border-top: 1px solid #edf1f7; }
        .sf-flow-strip label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; color: #667085; }
        .sf-flow-note { display: block; margin-top: 5px; font-size: 11px; color: #7a8797; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div id="growl-container"></div>
    <div class="wrapper">
        <div class="container-fluid" id="main-container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box <?php echo ($opportunity->country_id != 101) ? 'export-header' : ''; ?>">
                        <h4 class="page-title">
                            Create New Quotation 
                            <?php if($opportunity->country_id != 101): ?>
                                <span class="badge badge-danger" style="vertical-align: middle; margin-left: 10px;">EXPORT ORDER</span>
                            <?php endif; ?>
                        </h4>
                    </div>
                </div>
            </div>

            <form id="quotationForm" action="<?php echo page_url; ?>Spares/generate_quotation_pdf" method="post" target="_blank">
                <input type="hidden" name="opportunity_id" value="<?php echo $opportunity->opportunity_id; ?>">
                <input type="hidden" name="customer_id" value="<?php echo $opportunity->customer_id; ?>">

                <div class="card-box">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Quotation No</label>
                            <input type="text" name="quotation_no" class="form-control" value="<?php echo $new_quotation_no; ?>" readonly>
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Quotation Date</label>
                            <input type="date" name="quotation_date" class="form-control required-field" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Currency Type</label>
                            <select name="currency" id="currency_selector" class="form-control" style="font-weight: 600; color: #2c3e50;">
                                <option value="INR" <?php echo ($opportunity->country_id == 101) ? 'selected' : ''; ?>>Domestic (INR ₹)</option>
                                <option value="USD" <?php echo ($opportunity->country_id != 101) ? 'selected' : ''; ?>>Export (USD $)</option>
                                <option value="EUR">Export (EUR €)</option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Customer</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($opportunity->company_name); ?>" readonly>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Kind Attention</label>
                            <input type="text" name="attention" class="form-control" value="<?php echo htmlspecialchars($opportunity->contact_person ?? '');?>" placeholder="Recipient Name">
                        </div>
                    </div>
                    <div class="row sf-flow-strip">
                        <div class="col-md-4 form-group">
                            <label>Quotation Type</label>
                            <select name="quotation_type" id="quotation_type" class="form-control required-field">
                                <?php foreach ($quotation_type_options as $type_key => $type_label): ?>
                                    <option value="<?php echo htmlspecialchars($type_key); ?>" <?php echo $selected_quotation_type === $type_key ? 'selected' : ''; ?>><?php echo htmlspecialchars($type_label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="sf-flow-note">This will drive the SF execution TAT template.</small>
                        </div>
                        <div class="col-md-4 form-group custom-engg-field">
                            <label>Custom Engg. Scope</label>
                            <select name="custom_engg_type" id="custom_engg_type" class="form-control">
                                <option value="">Select Custom Engg. Scope</option>
                                <?php foreach ($custom_engg_type_options as $custom_key => $custom_label): ?>
                                    <option value="<?php echo htmlspecialchars($custom_key); ?>" <?php echo $selected_custom_engg_type === $custom_key ? 'selected' : ''; ?>><?php echo htmlspecialchars($custom_label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="sf-flow-note">Required only for Custom Engg. quotations.</small>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Dispatch Mode</label>
                            <select name="dispatch_mode" id="dispatch_mode" class="form-control required-field">
                                <?php foreach ($dispatch_mode_options as $mode_key => $mode_label): ?>
                                    <option value="<?php echo htmlspecialchars($mode_key); ?>" <?php echo $selected_dispatch_mode === $mode_key ? 'selected' : ''; ?>><?php echo htmlspecialchars($mode_label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="sf-flow-note">Used for packing and dispatch planning.</small>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <div class="row m-b-20">
                        <div class="col-md-6">
                            <h4 class="m-t-0 header-title"><b>Product Details</b></h4>
                            <?php if (!empty($opportunity_products)): ?>
                                <p class="text-muted m-b-0" style="font-size: 12px;">
                                    Opportunity has <?php echo count($opportunity_products); ?> enquiry item(s). They are loaded below and can be edited, removed, or expanded with more products.
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="converter-box pull-right">
                                <div class="form-inline">
                                    <div class="form-group m-r-10">
                                        <label>Ex. Rate (1 <span class="export-currency-code">USD</span> to INR)</label>
                                        <input type="number" id="conv_rate" class="form-control input-sm" step="0.01" value="94.00" style="width: 80px;">
                                    </div>
                                    <div class="form-group m-r-10">
                                        <label>Value in INR (₹)</label>
                                        <input type="number" id="calc_inr" class="form-control input-sm highlight-input" placeholder="0.00" style="width: 120px;">
                                    </div>
                                    <div class="form-group">
                                        <label>Result in <span class="export-currency-code">USD</span> (<span class="export-currency-symbol">$</span>)</label>
                                        <div class="input-group">
                                            <input type="text" id="calc_usd" class="form-control input-sm" placeholder="0.00" style="width: 90px; background: #eee;" readonly>
                                            <span class="input-group-btn">
                                                <button type="button" id="apply_to_prices" class="btn btn-warning btn-sm" title="Apply to Unit Price field">Apply</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <table class="table" id="product-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="width: 9%;">HSN</th>
                                <th style="width: 7%;">Qty</th>
                                <th style="width: 12%;">Unit Price (<span class="curr-symbol">₹</span>)</th>
                                <th style="width: 7%;">Disc (%)</th>
                                <th style="width: 7%;">GST (%)</th>
                                <th style="width: 11%;">Line Total</th>
                                <th style="width: 6%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach($products as $p): ?>
                                <tr>
                                    <td>
                                        <select class="form-control product-select required-field" name="product_id[]">
                                            <option value="<?php echo $p->id; ?>" selected><?php echo htmlspecialchars($p->code . ' ' . $p->description); ?></option>
                                        </select>
                                        <input type="hidden" class="description-input" name="description[]" value="<?php echo htmlspecialchars($p->code . ' ' . $p->description); ?>">
                                        <div class="quote-history-box"></div>
                                    </td>
                                    <td><input type="text" class="form-control hsn-code" name="hsn_code[]"></td>
                                    <td><input type="number" class="form-control qty required-field" name="quantity[]" value="<?php echo $p->quantity; ?>"></td>
                                    <td>
                                        <div class="input-group unit-price-lock">
                                            <input type="number" step="0.01" class="form-control unit-price required-field" name="unit_price[]" value="<?php echo $p->price ?? 0; ?>">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default toggle-price-lock" title="Fix this price"><i class="fa fa-unlock"></i></button>
                                            </span>
                                        </div>
                                        <input type="hidden" class="price-locked-input" name="price_locked[]" value="0">
                                    </td>
                                    <td><input type="number" step="0.01" class="form-control discount-percent" name="discount_percent[]" value="0"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control item-gst-percent" name="item_gst_percent[]" value="<?php echo $default_item_gst_percent; ?>"></td>
                                    <td><input type="text" class="form-control total-price" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td>
                                    <select class="form-control product-select required-field" name="product_id[]"></select>
                                    <input type="hidden" class="description-input" name="description[]">
                                    <div class="quote-history-box"></div>
                                    </td>
                                    <td><input type="text" class="form-control hsn-code" name="hsn_code[]"></td>
                                    <td><input type="number" class="form-control qty required-field" name="quantity[]" value="1"></td>
                                    <td>
                                    <div class="input-group unit-price-lock">
                                        <input type="number" step="0.01" class="form-control unit-price required-field" name="unit_price[]" value="0">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default toggle-price-lock" title="Fix this price"><i class="fa fa-unlock"></i></button>
                                        </span>
                                        </div>
                                        <input type="hidden" class="price-locked-input" name="price_locked[]" value="0">
                                    </td>
                                    <td><input type="number" step="0.01" class="form-control discount-percent" name="discount_percent[]" value="0"></td>
                                    <td><input type="number" step="0.01" min="0" class="form-control item-gst-percent" name="item_gst_percent[]" value="<?php echo $default_item_gst_percent; ?>"></td>
                                    <td><input type="text" class="form-control total-price" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <button type="button" id="add-row" class="btn btn-success waves-effect waves-light"><i class="fa fa-plus"></i> Add Product</button>
                </div>

                <div class="card-box">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="m-t-0 m-b-20 header-title"><b>Terms & Conditions</b></h4>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>GST Calculation</label>
                                    <input type="text" class="form-control" value="<?php echo ((int) ($opportunity->country_id ?? 101) === 101) ? 'Item-wise GST (18% default per row, charges at 18%)' : 'Item-wise GST (0% default for export rows)'; ?>" readonly>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Payment Terms</label>
                                    <input type="text" name="payment_terms" class="form-control" value="<?php echo ($opportunity->country_id == 101) ? 'HUL Standard' : '100% Advance against PI'; ?>">
                                </div>
                                <div class="col-md-6 form-group"><label>Validity</label><input type="text" name="validity" class="form-control" value="1 Month"></div>
                                <div class="col-md-6 form-group"><label>Delivery</label><input type="text" name="delivery_terms" class="form-control" value="5-6 Weeks after receipt of PO"></div>
                            </div>
                        </div>
                        <div class="col-md-4 totals-summary">
                             <h4 class="m-t-0 m-b-20 header-title"><b>Summary (<span class="curr-symbol">₹</span>)</b></h4>
                             <div class="form-group">
                                 <label>Packing Charges (%)</label>
                                 <div class="row charge-mode-row">
                                     <div class="col-xs-7"><input type="number" step="0.01" id="packing-percent" name="packing_percent" class="form-control" value="3.5"></div>
                                     <div class="col-xs-5">
                                         <select id="packing-charge-mode" name="packing_charge_mode" class="form-control charge-mode-select">
                                             <option value="included" selected>Included</option>
                                             <option value="extra">Extra</option>
                                         </select>
                                     </div>
                                 </div>
                                 <small class="charge-mode-hint">If set to `Extra`, this charge stays outside the quotation total and prints as `Extra` in PDF.</small>
                             </div>
                             <div class="form-group">
                                 <label>Freight Charges</label>
                                 <div class="row charge-mode-row">
                                     <div class="col-xs-7"><input type="number" step="0.01" id="freight-charge" name="freight_charge" class="form-control" value="<?php echo ($opportunity->country_id == 101) ? '1500' : '0'; ?>"></div>
                                     <div class="col-xs-5">
                                         <select id="freight-charge-mode" name="freight_charge_mode" class="form-control charge-mode-select">
                                             <option value="included" selected>Included</option>
                                             <option value="extra">Extra</option>
                                         </select>
                                     </div>
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label>Ex-Work Charges</label>
                                 <div class="row charge-mode-row">
                                     <div class="col-xs-7"><input type="number" step="0.01" id="ex-work-charge" name="ex_work_charge" class="form-control" value="0"></div>
                                     <div class="col-xs-5">
                                         <select id="ex-work-charge-mode" name="ex_work_charge_mode" class="form-control charge-mode-select">
                                             <option value="included" selected>Included</option>
                                             <option value="extra">Extra</option>
                                         </select>
                                     </div>
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label>Insurance (%)</label>
                                 <div class="row charge-mode-row">
                                     <div class="col-xs-7"><input type="number" step="0.01" id="insurance-percent" name="insurance_percent" class="form-control" value="0.5"></div>
                                     <div class="col-xs-5">
                                         <select id="insurance-charge-mode" name="insurance_charge_mode" class="form-control charge-mode-select">
                                             <option value="included" selected>Included</option>
                                             <option value="extra">Extra</option>
                                         </select>
                                     </div>
                                 </div>
                             </div>
                             <hr>
                             <div class="form-group"><label>Overall Discount (%)</label><input type="number" step="0.01" id="overall-discount-percent" name="overall_discount_percent" class="form-control" value="0"></div>
                             <hr>
                             <h5>Basic Value: <span class="pull-right"><span class="curr-symbol">₹</span> <span id="display-basic">0.00</span></span></h5>
                             <h5>Sub Total: <span class="pull-right"><span class="curr-symbol">₹</span> <span id="display-sub-total">0.00</span></span></h5>
                             <h5 class="text-danger">Total Discount: <span class="pull-right">- <span id="display-overall-discount">0.00</span></span></h5>
                             <h5>Total Value: <span class="pull-right"><span class="curr-symbol">₹</span> <span id="display-total-value">0.00</span></span></h5>
                             <h5>GST Total: <span class="pull-right"><span class="curr-symbol">₹</span> <span id="display-gst-total">0.00</span></span></h5>
                             <h4 class="font-600">Grand Total: <span class="pull-right"><span class="curr-symbol">₹</span> <span id="display-total">0.00</span></span></h4>
                        </div>
                    </div>
                </div>

                <div class="text-center m-b-30">
                    <button type="submit" class="btn btn-primary btn-lg waves-effect waves-light"><i class="fa fa-file-pdf-o"></i> Save & Generate PDF</button>
                </div>
            </form>
        </div>
    </div>

    <table style="display:none;">
        <tr id="new-row-template">
            <td>
                <select class="form-control product-select-new required-field" name="product_id[]"></select>
                <input type="hidden" class="description-input" name="description[]">
                <div class="quote-history-box"></div>
            </td>
            <td><input type="text" class="form-control hsn-code" name="hsn_code[]"></td>
            <td><input type="number" class="form-control qty required-field" name="quantity[]" value="1"></td>
            <td>
                <div class="input-group unit-price-lock">
                    <input type="number" step="0.01" class="form-control unit-price required-field" name="unit_price[]" value="0">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default toggle-price-lock" title="Fix this price"><i class="fa fa-unlock"></i></button>
                    </span>
                </div>
                <input type="hidden" class="price-locked-input" name="price_locked[]" value="0">
            </td>
            <td><input type="number" step="0.01" class="form-control discount-percent" name="discount_percent[]" value="0"></td>
            <td><input type="number" step="0.01" min="0" class="form-control item-gst-percent" name="item_gst_percent[]" value="<?php echo $default_item_gst_percent; ?>"></td>
            <td><input type="text" class="form-control total-price" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button></td>
        </tr>
    </table>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    
    <script>
    $(document).ready(function() {
        var initialQuoteHistoryMap = <?php echo json_encode($recent_price_history_map ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

        function syncCustomEnggField() {
            var isCustom = $('#quotation_type').val() === 'CUSTOM_ENGG';
            $('.custom-engg-field').toggle(isCustom);
            $('#custom_engg_type').toggleClass('required-field', isCustom).prop('required', isCustom);

            if (!isCustom) {
                $('#custom_engg_type').val('').removeClass('is-invalid');
            }
        }

        function isPriceLocked(row) {
            return row.find('.price-locked-input').val() === '1';
        }

        function setPriceLock(row, shouldLock) {
            var locked = shouldLock ? '1' : '0';
            var button = row.find('.toggle-price-lock');
            row.find('.price-locked-input').val(locked);
            row.find('.unit-price').toggleClass('price-fixed-input', shouldLock);
            button.toggleClass('btn-default', !shouldLock);
            button.toggleClass('btn-warning', shouldLock);
            button.attr('title', shouldLock ? 'Unlock fixed price' : 'Fix this price');
            button.find('i').attr('class', shouldLock ? 'fa fa-lock' : 'fa fa-unlock');
        }

        function formatQuoteDate(dateString) {
            if (!dateString || dateString === '0000-00-00') {
                return '-';
            }

            var parts = dateString.split('-');
            if (parts.length !== 3) {
                return dateString;
            }

            var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var monthIndex = parseInt(parts[1], 10) - 1;
            var monthName = monthNames[monthIndex] || parts[1];

            return parts[2] + ' ' + monthName + ' ' + parts[0];
        }

        function renderQuoteHistory(row, history) {
            var box = row.find('.quote-history-box');
            var html = '<div class="quote-history-title">Latest 2 Quotes</div>';

            if (!history || !history.length) {
                box.html(html + '<div class="quote-history-empty">No previous quoted price found for this item.</div>');
                return;
            }

            $.each(history, function(index, item) {
                var price = parseFloat(item.unit_price || 0).toFixed(2);
                var discountText = parseFloat(item.discount_percent || 0) > 0 ? ' | Disc ' + parseFloat(item.discount_percent).toFixed(2) + '%' : '';
                html += '<div class="quote-history-item">';
                html += '<div class="quote-history-main">';
                html += '<span class="quote-history-price">' + price + '</span>';
                html += '<button type="button" class="btn btn-info btn-xs apply-history-price" data-price="' + price + '" data-quotation-no="' + (item.quotation_no || '') + '">Use &amp; Fix</button>';
                html += '</div>';
                html += '<div class="quote-history-meta">' + (item.quotation_no || '-') + ' | ' + formatQuoteDate(item.quotation_date) + ' | Qty ' + (item.quantity || 0) + discountText + '</div>';
                html += '</div>';
            });

            box.html(html);
        }

        function loadQuoteHistory(row, productId) {
            productId = parseInt(productId, 10) || 0;
            if (!productId) {
                renderQuoteHistory(row, []);
                return;
            }

            var mapKey = String(productId);
            if (initialQuoteHistoryMap[mapKey]) {
                renderQuoteHistory(row, initialQuoteHistoryMap[mapKey]);
                return;
            }

            $.ajax({
                url: "<?php echo page_url; ?>Spares/product_quote_history_ajax",
                type: 'GET',
                dataType: 'json',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    var history = (response && response.history) ? response.history : [];
                    initialQuoteHistoryMap[mapKey] = history;
                    renderQuoteHistory(row, history);
                },
                error: function() {
                    renderQuoteHistory(row, []);
                }
            });
        }

        function roundCurrency(value) {
            return Math.round((parseFloat(value) || 0) * 100) / 100;
        }

        function getDefaultItemGstPercent() {
            return $('#currency_selector').val() === 'INR' ? 18 : 0;
        }

        function getDefaultChargeGstPercent() {
            return $('#currency_selector').val() === 'INR' ? 18 : 0;
        }

        function syncItemGstWithCurrency(forceReset) {
            var defaultGst = getDefaultItemGstPercent();

            $('.item-gst-percent').each(function() {
                var currentValue = parseFloat($(this).val());

                if (forceReset || isNaN(currentValue) || currentValue === 0) {
                    $(this).val(defaultGst.toFixed(2));
                }

                if ($('#currency_selector').val() !== 'INR') {
                    $(this).val('0.00');
                }
            });
        }

        function allocateAmount(totalAmount, weights) {
            var keys = Object.keys(weights || {});
            var allocations = {};

            keys.forEach(function(key) {
                allocations[key] = 0;
            });

            totalAmount = roundCurrency(totalAmount);
            if (!keys.length || Math.abs(totalAmount) < 0.01) {
                return allocations;
            }

            var absoluteAmount = Math.abs(totalAmount);
            var totalWeight = 0;
            var normalizedWeights = {};

            keys.forEach(function(key) {
                var weight = Math.max(0, parseFloat(weights[key]) || 0);
                normalizedWeights[key] = weight;
                totalWeight += weight;
            });

            if (totalWeight <= 0) {
                totalWeight = keys.length;
                keys.forEach(function(key) {
                    normalizedWeights[key] = 1;
                });
            }

            var distributed = 0;
            keys.forEach(function(key, index) {
                var share;

                if (index === keys.length - 1) {
                    share = roundCurrency(absoluteAmount - distributed);
                } else {
                    share = roundCurrency((absoluteAmount * normalizedWeights[key]) / totalWeight);
                    distributed = roundCurrency(distributed + share);
                }

                allocations[key] = totalAmount < 0 ? -share : share;
            });

            return allocations;
        }

        // --- Real-time Logic: Currency Handling ---
        $('#currency_selector').on('change', function() {
            let mode = $(this).val();
            let symbols = { INR: '₹', USD: '$', EUR: '€' };
            let symbol = symbols[mode] || mode;
            $('.curr-symbol').text(symbol);
            $('.export-currency-code').text(mode === 'INR' ? 'USD' : mode);
            $('.export-currency-symbol').text(mode === 'EUR' ? '€' : '$');

            if(mode !== 'INR') {
                $('#freight-charge').val(0);
                $('#ex-work-charge').val(0);
            } else {
                $('#freight-charge').val(1500);
            }

            syncItemGstWithCurrency(mode !== 'INR');
            calculateTotals();
        });

        // --- Real-time Logic: Currency Converter (Rate: 94.00) ---
        $('#calc_inr, #conv_rate').on('input', function() {
            let inrValue = parseFloat($('#calc_inr').val()) || 0;
            let rate = parseFloat($('#conv_rate').val()) || 94;
            let usdResult = (inrValue / rate).toFixed(2);
            $('#calc_usd').val(usdResult);
        });

        // Apply converted price to row
        $('#apply_to_prices').on('click', function() {
            let usdVal = $('#calc_usd').val();
            let lastRow = $('#product-table tbody tr:last');
            if(usdVal > 0) {
                if (isPriceLocked(lastRow)) {
                    showGrowl('The last row price is fixed. Unlock it first to overwrite.', 'error');
                    return;
                }

                lastRow.find('.unit-price').val(usdVal).trigger('input');
                let currency = $('#currency_selector').val();
                showGrowl(currency + ' price applied to the last row.', 'success');
            } else {
                showGrowl('Please enter an INR value to convert.', 'error');
            }
        });

        function getSelectedProductIds() {
            const selectedIds = [];
            $('select[name="product_id[]"]').each(function() {
                if ($(this).val()) selectedIds.push($(this).val());
            });
            return selectedIds;
        }

        function initializeAjaxSelect2(element) {
            element.select2({
                placeholder: "Search Product Code/Name...",
                allowClear: true,
                ajax: {
                    url: "<?php echo page_url; ?>Spares/search_products_ajax",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term, exclude: getSelectedProductIds() };
                    },
                    processResults: function(data) { return { results: data.results }; },
                    cache: true
                }
            });
        }

        $('.product-select').each(function() { initializeAjaxSelect2($(this)); });

        $('#add-row').on('click', function() {
            let newRow = $('#new-row-template').clone().removeAttr('id');
            $('#product-table tbody').append(newRow);
            initializeAjaxSelect2(newRow.find('.product-select-new'));
            renderQuoteHistory(newRow, []);
            setPriceLock(newRow, false);
            newRow.find('.item-gst-percent').val(getDefaultItemGstPercent().toFixed(2));
        });

        $('#product-table').on('select2:select', '.product-select, .product-select-new', function(e) {
            let data = e.params.data;
            let row = $(this).closest('tr');
            
            let finalPrice = data.price || 0;
            if($('#currency_selector').val() !== 'INR') {
                let currentRate = parseFloat($('#conv_rate').val()) || 94;
                finalPrice = (finalPrice / currentRate).toFixed(2);
            }

            row.find('.description-input').val(data.text || '');
            loadQuoteHistory(row, data.id);

            if (isPriceLocked(row)) {
                showGrowl('Price is fixed for this row. Unlock it to use the auto-filled product price.', 'error');
            } else {
                row.find('.unit-price').val(finalPrice);
            }

            calculateTotals();
        });

        $('#product-table').on('change', '.product-select, .product-select-new', function() {
            let row = $(this).closest('tr');
            if ($(this).val()) {
                return;
            }

            row.find('.description-input, .hsn-code').val('');
            row.find('.qty').val(1);
            row.find('.unit-price, .discount-percent, .total-price').val(0);
            row.find('.item-gst-percent').val(getDefaultItemGstPercent().toFixed(2));
            renderQuoteHistory(row, []);
            setPriceLock(row, false);
            calculateTotals();
        });

        $('#product-table').on('click', '.toggle-price-lock', function() {
            let row = $(this).closest('tr');
            setPriceLock(row, !isPriceLocked(row));
        });

        $('#product-table').on('click', '.apply-history-price', function() {
            let row = $(this).closest('tr');
            let price = parseFloat($(this).data('price')) || 0;
            let quotationNo = $(this).data('quotation-no') || '';

            row.find('.unit-price').val(price.toFixed(2));
            setPriceLock(row, true);
            calculateTotals();
            showGrowl('Price fixed from ' + quotationNo + '.', 'success');
        });

        function calculateTotals() {
            let basicValue = 0;
            let rowTotals = {};
            let rowGstPercents = {};

            $('#product-table tbody tr').each(function() {
                let row = $(this);
                let rowIndex = row.index();
                let qty = parseFloat(row.find('.qty').val()) || 0;
                let price = parseFloat(row.find('.unit-price').val()) || 0;
                let discountPercent = parseFloat(row.find('.discount-percent').val()) || 0;
                let lineGstPercent = parseFloat(row.find('.item-gst-percent').val()) || 0;
                
                let lineTotal = qty * price;
                let discountAmount = (lineTotal * discountPercent) / 100;
                let finalLineTotal = Math.max(0, lineTotal - discountAmount);
                
                row.find('.total-price').val(finalLineTotal.toFixed(2));
                basicValue += finalLineTotal;
                rowTotals[rowIndex] = finalLineTotal;
                rowGstPercents[rowIndex] = lineGstPercent;
            });
            
            let packingPercent = parseFloat($('#packing-percent').val()) || 0;
            let freight = parseFloat($('#freight-charge').val()) || 0;
            let exWork = parseFloat($('#ex-work-charge').val()) || 0;
            let insurancePercent = parseFloat($('#insurance-percent').val()) || 0;
            let overallDiscountPercent = parseFloat($('#overall-discount-percent').val()) || 0;
            
            let packingCharge = roundCurrency((basicValue * packingPercent) / 100);
            let insuranceCharge = roundCurrency((basicValue * insurancePercent) / 100);
            let packingForTotal = ($('#packing-charge-mode').val() === 'extra') ? 0 : packingCharge;
            let freightForTotal = ($('#freight-charge-mode').val() === 'extra') ? 0 : freight;
            let exWorkForTotal = ($('#ex-work-charge-mode').val() === 'extra') ? 0 : exWork;
            let insuranceForTotal = ($('#insurance-charge-mode').val() === 'extra') ? 0 : insuranceCharge;
            
            let subTotal = roundCurrency(basicValue + packingForTotal + freightForTotal + exWorkForTotal + insuranceForTotal);
            let overallDiscountAmount = roundCurrency((subTotal * overallDiscountPercent) / 100);
            let finalTotalValue = roundCurrency(subTotal - overallDiscountAmount);
            let totalGstAmount = 0;
            let componentWeights = {};
            let chargeGstPercent = getDefaultChargeGstPercent();

            $.each(rowTotals, function(rowIndex, rowTotal) {
                if (rowTotal > 0) {
                    componentWeights['row_' + rowIndex] = rowTotal;
                }
            });

            [
                ['charge_packing', packingForTotal],
                ['charge_freight', freightForTotal],
                ['charge_ex_work', exWorkForTotal],
                ['charge_insurance', insuranceForTotal]
            ].forEach(function(component) {
                if (component[1] > 0) {
                    componentWeights[component[0]] = component[1];
                }
            });

            let discountAllocations = allocateAmount(overallDiscountAmount, componentWeights);

            $.each(rowTotals, function(rowIndex, rowTotal) {
                let taxableValue = Math.max(0, rowTotal - (discountAllocations['row_' + rowIndex] || 0));
                let gstPercent = rowGstPercents[rowIndex] || 0;
                totalGstAmount += roundCurrency((taxableValue * gstPercent) / 100);
            });

            [
                ['charge_packing', packingForTotal],
                ['charge_freight', freightForTotal],
                ['charge_ex_work', exWorkForTotal],
                ['charge_insurance', insuranceForTotal]
            ].forEach(function(component) {
                let taxableValue;

                if (component[1] <= 0) {
                    return;
                }

                taxableValue = Math.max(0, component[1] - (discountAllocations[component[0]] || 0));
                totalGstAmount += roundCurrency((taxableValue * chargeGstPercent) / 100);
            });

            totalGstAmount = roundCurrency(totalGstAmount);
            let grandTotal = roundCurrency(finalTotalValue + totalGstAmount);
            
            $('#display-basic').text(basicValue.toFixed(2));
            $('#display-sub-total').text(subTotal.toFixed(2));
            $('#display-overall-discount').text(overallDiscountAmount.toFixed(2));
            $('#display-total-value').text(finalTotalValue.toFixed(2));
            $('#display-gst-total').text(totalGstAmount.toFixed(2));
            $('#display-total').text(grandTotal.toFixed(2));
        }

        $(document).on('input change', '.qty, .unit-price, .discount-percent, .item-gst-percent, #packing-percent, #freight-charge, #ex-work-charge, #insurance-percent, #overall-discount-percent, .charge-mode-select', calculateTotals);
        $('#product-table').on('click', '.remove-row', function() {
            if ($('#product-table tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
                return;
            }

            let row = $(this).closest('tr');
            row.find('.product-select, .product-select-new').val(null).trigger('change');
            row.find('.description-input, .hsn-code').val('');
            row.find('.qty').val(1);
            row.find('.unit-price, .discount-percent, .total-price').val(0);
            row.find('.item-gst-percent').val(getDefaultItemGstPercent().toFixed(2));
            renderQuoteHistory(row, []);
            setPriceLock(row, false);
            calculateTotals();
        });
        
        // Initial setup
        $('#product-table tbody tr').each(function() {
            let row = $(this);
            setPriceLock(row, false);
            loadQuoteHistory(row, row.find('select[name="product_id[]"]').val());
        });
        $('#currency_selector').trigger('change');
        $('#quotation_type').on('change', syncCustomEnggField);
        syncCustomEnggField();

        $('#quotationForm').on('submit', function(e) {
            e.preventDefault();
            let isValid = true;
            $('#quotationForm .required-field').each(function() {
                if (!$(this).val() || $(this).val().trim() === '') {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                showGrowl('Please fill all required fields.', 'error');
                return false;
            }

            $('#main-container').block({ message: 'Generating PDF...' });
            
            setTimeout(() => {
                this.submit();
                showGrowl('Success! Quotation generated.', 'success');
                setTimeout(() => {
                    window.location.href = "<?php echo page_url; ?>Spares/opportunity_detail/<?php echo $opportunity->opportunity_id; ?>";
                }, 1500);
            }, 1000);
        });
    });
    function showGrowl(message, type) {
        var $growl = $('<div class="growl-notification ' + type + '">' + message + '</div>');
        $('#growl-container').append($growl);
        setTimeout(() => { $growl.addClass('show'); }, 10);
        setTimeout(() => {
            $growl.removeClass('show');
            setTimeout(() => { $growl.remove(); }, 500);
        }, 4000);
    }
    </script>
</body>
</html>
