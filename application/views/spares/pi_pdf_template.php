<?php
defined('BASEPATH') or exit('No direct script access allowed');

$currency = strtoupper(trim((string) ($pi->currency ?? 'INR')));
$currency_symbols = ['INR' => '₹', 'USD' => '$', 'EUR' => '€'];
$currency_symbol = $currency_symbols[$currency] ?? $currency;
$company_color = $company_profile['colorcode'] ?? '#003366';
$notes_lines = preg_split('/\r\n|\r|\n/', (string) ($pi->notes ?? ''), -1, PREG_SPLIT_NO_EMPTY);
$has_line_item_discount = false;
$has_overall_discount = (float) ($pi->overall_discount_amount ?? 0) > 0;

foreach ((array) $items as $discount_item) {
    if ((float) ($discount_item->discount_percent ?? 0) > 0 || (float) ($discount_item->discount_amount ?? 0) > 0) {
        $has_line_item_discount = true;
        break;
    }
}

if (empty($notes_lines)) {
    $notes_lines = [
        'This PI is based on the latest approved commercial discussion.',
        'All disputes are subject to Faridabad Jurisdiction.',
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PI - <?php echo htmlspecialchars((string) ($pi->pi_no ?? ''), ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        @page { margin: 84px 28px 64px 28px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 8.8pt; color: #2d3748; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        header {
            position: fixed;
            top: -66px;
            left: 0;
            right: 0;
            height: 52px;
            border-bottom: 1px solid #edf3f8;
            padding-bottom: 8px;
        }
        footer {
            position: fixed;
            bottom: -48px;
            left: 0;
            right: 0;
            height: 36px;
            background: #f7fafc;
            border-top: 1px solid #d9e2ec;
            padding-top: 4px;
        }
        .header-table,
        .quote-meta-table,
        .summary-layout,
        .signature-box { table-layout: fixed; }
        .header-table td,
        .footer-table td,
        .address-table td { vertical-align: top; }
        .header-logo { width: 32%; }
        .header-logo img {
            width: 122px;
            max-width: 122px;
            max-height: 46px;
            height: auto;
        }
        .header-title { width: 68%; text-align: right; }
        .brand-table { width: 100%; table-layout: fixed; }
        .brand-table td { vertical-align: middle; }
        .brand-logo-cell { width: 132px !important; }
        .brand-copy { padding-left: 2px; }
        .brand-name {
            font-size: 11.3pt;
            font-weight: 700;
            line-height: 1.15;
            text-transform: uppercase;
            color: #243b53;
            margin-bottom: 3px;
        }
        .brand-copy p,
        .brand-legal {
            margin: 0;
            font-size: 7.4pt;
            line-height: 1.25;
            color: #7b8794;
        }
        .doc-kicker {
            font-size: 6.9pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7b8794;
            margin-bottom: 2px;
        }
        .header-title h1 {
            margin: 0;
            font-size: 17.8pt;
            font-weight: 700;
            color: <?php echo $company_color; ?>;
            letter-spacing: 0.3px;
        }
        .header-title p {
            margin: 2px 0 0;
            line-height: 1.35;
            font-size: 7.8pt;
            color: #52606d;
        }
        .footer-info { font-size: 7.1pt; color: #5f6c7b; width: 80%; line-height: 1.3; }
        .footer-image { text-align: right; width: 22%; }
        .footer-image img { max-height: 24px; width: auto; }
        .watermark {
            position: fixed;
            top: 47%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: -1000;
            width: 100%;
            text-align: center;
            white-space: nowrap;
            font-size: 58pt;
            font-weight: 700;
            color: rgba(0, 0, 0, 0.02);
        }
        .quote-meta-table { margin: 0 0 9px; }
        .quote-meta-table td { width: 50%; vertical-align: top; }
        .quote-meta-table td:first-child { padding-right: 6px; }
        .quote-meta-table td:last-child { padding-left: 6px; }
        .quote-meta-box {
            border: 1px solid #d9e3ef;
            background: #f8fbff;
            padding: 7px 10px;
            border-radius: 4px;
        }
        .quote-meta-label {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #6c7a89;
            margin-bottom: 3px;
        }
        .quote-meta-value {
            font-size: 10.1pt;
            font-weight: 700;
            color: <?php echo $company_color; ?>;
            line-height: 1.25;
        }
        .meta-note {
            margin-top: 3px;
            font-size: 7.7pt;
            line-height: 1.35;
            color: #52606d;
        }
        .address-table { margin-bottom: 9px; table-layout: fixed; }
        .address-table td {
            width: 50%;
            padding: 8px 10px;
            border: 1px solid #d9e3ef;
            vertical-align: top;
            line-height: 1.35;
        }
        .address-table td + td { border-left: none; }
        .section-heading {
            margin: 0 0 6px;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #344054;
            padding-bottom: 4px;
            border-bottom: 1px solid #e6edf5;
        }
        .address-strong { font-size: 9.6pt; color: #243b53; }
        .address-line { margin-bottom: 1px; }
        .main-table { margin-top: 8px; table-layout: fixed; }
        .main-table thead { display: table-header-group; }
        .main-table tr { page-break-inside: avoid; }
        .main-table th,
        .main-table td {
            padding: 4px 5px;
            border: 1px solid #d8dee9;
            vertical-align: top;
            word-break: break-word;
        }
        .main-table th {
            color: #2d3748;
            font-size: 7.4pt;
            font-weight: 700;
            text-transform: uppercase;
            background: #eef4fa;
        }
        .main-table .col-index { width: 5%; text-align: center; }
        .main-table .col-code { width: 11%; text-align: center; }
        .main-table .col-hsn { width: 9%; text-align: center; }
        .main-table .col-description { width: 33%; line-height: 1.4; }
        .main-table .col-qty { width: 7%; text-align: center; white-space: nowrap; }
        .main-table .col-uom { width: 7%; text-align: center; white-space: nowrap; }
        .main-table .col-unit { width: 11%; text-align: right; white-space: nowrap; }
        .main-table .col-disc { width: 7%; text-align: right; white-space: nowrap; }
        .main-table .col-total { width: 10%; text-align: right; white-space: nowrap; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-layout,
        .declaration-box,
        .signature-box,
        .notes-card,
        .totals-card,
        .signature-panel { page-break-inside: avoid; }
        .summary-layout { margin-top: 10px; }
        .summary-layout td { vertical-align: top; }
        .notes-card, .totals-card {
            border: 1px solid #d9e3ef;
            background: #fff;
            border-radius: 6px;
            padding: 8px 10px;
        }
        .summary-title {
            margin: 0 0 6px;
            font-size: 8.1pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #344054;
        }
        .totals-table { table-layout: fixed; }
        .totals-table td {
            padding: 3px 0;
            border: none;
            font-size: 8.3pt;
        }
        .totals-table td:first-child {
            color: #52606d;
            padding-right: 10px;
        }
        .totals-table td:last-child { white-space: nowrap; }
        .totals-table .grand td {
            font-size: 9.8pt;
            font-weight: 700;
            border-top: 1px solid #d9e3ef;
            padding-top: 6px;
        }
        .declaration-box {
            margin-top: 9px;
            border: 1px solid #d9e3ef;
            padding: 8px 10px;
            line-height: 1.4;
            font-size: 8.3pt;
        }
        .signature-box {
            margin-top: 9px;
            width: 100%;
            table-layout: fixed;
        }
        .signature-box td { vertical-align: top; }
        .signature-panel {
            border: 1px solid #d9e3ef;
            padding: 10px 12px 12px;
            height: 92px;
            font-size: 8.2pt;
            line-height: 1.45;
        }
        .signature-title {
            margin-bottom: 6px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #344054;
        }
        .bank-line {
            margin-bottom: 3px;
            word-break: break-word;
        }
        .bank-line:last-child { margin-bottom: 0; }
        .signature-label { font-weight: 700; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="watermark">Shubham Pack</div>

    <header>
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <table class="brand-table">
                        <tr>
                            <td class="brand-logo-cell">
                                <img src="<?php echo htmlspecialchars($company_profile['logo'], ENT_QUOTES, 'UTF-8'); ?>" alt="Logo">
                            </td>
                           <!--  <td class="brand-copy">
                                <div class="brand-name"><?php echo htmlspecialchars($company_profile['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <p class="brand-legal">GSTIN: <?php echo htmlspecialchars($company_profile['gst_no'], ENT_QUOTES, 'UTF-8'); ?> | CIN: <?php echo htmlspecialchars($company_profile['cin_no'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </td> -->
                        </tr>
                    </table>
                </td>
                <td class="header-title">
                    <div class="doc-kicker">Spares Division</div>
                    <h1>Proforma Invoice</h1>
                    <p>Commercial offer ready for customer PO release.</p>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td class="footer-info">
                    <?php echo htmlspecialchars($company_profile['corporate_office'], ENT_QUOTES, 'UTF-8'); ?><br>
                    Phone: <?php echo htmlspecialchars($company_profile['phone'], ENT_QUOTES, 'UTF-8'); ?> | Email: <?php echo htmlspecialchars($company_profile['email'], ENT_QUOTES, 'UTF-8'); ?> | Website: <?php echo htmlspecialchars($company_profile['website'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td class="footer-image">
                    <?php if (!empty($company_profile['footer_image'])) : ?>
                        <img src="<?php echo htmlspecialchars($company_profile['footer_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Footer">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </footer>

    <table class="quote-meta-table">
        <tr>
            <td>
                <div class="quote-meta-box">
                    <div class="quote-meta-label">PI Number</div>
                    <div class="quote-meta-value"><?php echo htmlspecialchars((string) ($pi->pi_no ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </td>
            <td>
                <div class="quote-meta-box">
                    <div class="quote-meta-label">PI Date</div>
                    <div class="quote-meta-value"><?php echo !empty($pi->pi_date) ? date('d M Y', strtotime($pi->pi_date)) : '-'; ?></div>
                    <div class="meta-note">Currency: <?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </td>
        </tr>
    </table>

    <table class="address-table">
        <tr>
            <td>
                <h3 class="section-heading">Buyer</h3>
                <div class="address-strong"><?php echo htmlspecialchars($pi->buyer_name ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="address-line">Attention: <?php echo htmlspecialchars($pi->attention ?: ($pi->buyer_contact ?: '-'), ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="address-line">Email: <?php echo htmlspecialchars($pi->buyer_email ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="address-line">GSTIN: <?php echo htmlspecialchars($pi->buyer_gstin ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                <?php echo nl2br(htmlspecialchars($pi->buyer_address ?: '-', ENT_QUOTES, 'UTF-8')); ?>
            </td>
            <td>
                <h3 class="section-heading">Consignee</h3>
                <div class="address-strong"><?php echo htmlspecialchars($pi->consignee_name ?: ($pi->buyer_name ?: '-'), ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="address-line">Contact: <?php echo htmlspecialchars($pi->consignee_contact ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="address-line">Phone: <?php echo htmlspecialchars($pi->consignee_phone ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="address-line">GSTIN: <?php echo htmlspecialchars($pi->consignee_gstin ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                <?php echo nl2br(htmlspecialchars($pi->consignee_address ?: '-', ENT_QUOTES, 'UTF-8')); ?>
            </td>
        </tr>
    </table>

    <table class="quote-meta-table">
        <tr>
            <td>
                <div class="quote-meta-box">
                    <div class="quote-meta-label">Reference Quotation</div>
                    <div class="quote-meta-value"><?php echo htmlspecialchars($pi->reference_quote_no ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="meta-note"><?php echo !empty($pi->reference_quote_date) ? date('d M Y', strtotime($pi->reference_quote_date)) : '-'; ?></div>
                </div>
            </td>
            <td>
                <div class="quote-meta-box">
                    <div class="quote-meta-label">Commercial Terms</div>
                    <div class="address-line">Payment: <?php echo htmlspecialchars($pi->payment_terms ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="address-line">Validity: <?php echo htmlspecialchars($pi->validity ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="address-line">Delivery: <?php echo htmlspecialchars($pi->delivery_terms ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th class="col-index">#</th>
                <th class="col-code">Item Code</th>
                <th class="col-hsn">HSN</th>
                <th class="col-description">Description</th>
                <th class="col-qty">Qty</th>
                <th class="col-uom">Unit</th>
                <th class="col-unit">Unit Price</th>
                <?php if ($has_line_item_discount) : ?>
                    <th class="col-disc">Disc %</th>
                <?php endif; ?>
                <th class="col-total">Line Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $index => $item) : ?>
            <tr>
                <td class="col-index"><?php echo $index + 1; ?></td>
                <td class="col-code"><?php echo htmlspecialchars($item->product_code ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="col-hsn"><?php echo htmlspecialchars($item->hsn_code ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="col-description"><?php echo nl2br(htmlspecialchars($item->description ?: '-', ENT_QUOTES, 'UTF-8')); ?></td>
                <td class="col-qty"><?php echo number_format((float) $item->quantity, 2); ?></td>
                <td class="col-uom"><?php echo htmlspecialchars((string) (($item->unit ?? '') !== '' ? $item->unit : 'NOS'), ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="col-unit"><?php echo $currency_symbol . ' ' . number_format((float) $item->unit_price, 2); ?></td>
                <?php if ($has_line_item_discount) : ?>
                    <td class="col-disc"><?php echo number_format((float) $item->discount_percent, 2); ?></td>
                <?php endif; ?>
                <td class="col-total"><?php echo $currency_symbol . ' ' . number_format((float) $item->total_price, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="summary-layout">
        <tr>
            <td style="width: 57%; padding-right: 14px;">
                <div class="notes-card">
                    <p class="summary-title">Notes</p>
                    <?php foreach ($notes_lines as $line) : ?>
                        <div class="bank-line"><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endforeach; ?>
                </div>
            </td>
            <td style="width: 43%;">
                <div class="totals-card">
                    <p class="summary-title">Value Summary</p>
                    <table class="totals-table">
                        <tr><td>Basic Value</td><td class="text-right"><?php echo $currency_symbol . ' ' . number_format((float) $pi->basic_value, 2); ?></td></tr>
                        <tr><td>Packing (<?php echo number_format((float) $pi->packing_percent, 2); ?>%)</td><td class="text-right"><?php echo $currency_symbol . ' ' . number_format((float) $pi->packing_charge, 2); ?></td></tr>
                        <tr><td>Freight</td><td class="text-right"><?php echo $currency_symbol . ' ' . number_format((float) $pi->freight_charge, 2); ?></td></tr>
                        <tr><td>Ex-Work</td><td class="text-right"><?php echo $currency_symbol . ' ' . number_format((float) $pi->ex_work_charge, 2); ?></td></tr>
                        <tr><td>Insurance (<?php echo number_format((float) $pi->insurance_percent, 2); ?>%)</td><td class="text-right"><?php echo $currency_symbol . ' ' . number_format((float) $pi->insurance_charge, 2); ?></td></tr>
                        <?php if ($has_overall_discount) : ?>
                            <tr><td>Overall Discount (<?php echo number_format((float) $pi->overall_discount_percent, 2); ?>%)</td><td class="text-right">-<?php echo $currency_symbol . ' ' . number_format((float) $pi->overall_discount_amount, 2); ?></td></tr>
                        <?php endif; ?>
                        <tr><td>Taxable Value</td><td class="text-right"><?php echo $currency_symbol . ' ' . number_format((float) $pi->total_value, 2); ?></td></tr>
                        <tr><td>GST (<?php echo number_format((float) $pi->gst_percent, 2); ?>%)</td><td class="text-right"><?php echo $currency_symbol . ' ' . number_format((float) $pi->gst_amount, 2); ?></td></tr>
                        <tr class="grand"><td>Grand Total</td><td class="text-right"><?php echo $currency_symbol . ' ' . number_format((float) $pi->grand_total, 2); ?></td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="declaration-box">
        <strong>Amount In Words:</strong> <?php echo htmlspecialchars($amount_in_words, ENT_QUOTES, 'UTF-8'); ?><br><br>
        <strong>Declaration:</strong> <?php echo nl2br(htmlspecialchars($pi->declaration_text ?: '-', ENT_QUOTES, 'UTF-8')); ?>
    </div>

    <table class="signature-box">
        <tr>
            <td style="width: 56%; padding-right: 14px;">
                <div class="signature-panel">
                    <div class="signature-title">Banking Details</div>
                    <div class="bank-line">Bank: <?php echo htmlspecialchars($company_profile['bank_name'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="bank-line">Account No.: <?php echo htmlspecialchars($company_profile['account_no'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="bank-line">IFSC: <?php echo htmlspecialchars($company_profile['ifsc_code'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="bank-line">Swift: <?php echo htmlspecialchars($company_profile['swift_code'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="bank-line">Address: <?php echo htmlspecialchars($company_profile['bank_address'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </td>
            <td style="width: 44%;">
                <div class="signature-panel">
                    <div style="height: 56px;"></div>
                    <div class="signature-label">For <?php echo htmlspecialchars($company_profile['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="muted">Authorised Signatory</div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
