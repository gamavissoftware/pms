<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotation - <?php echo htmlspecialchars($quotation_no); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap');

        @page {
            margin: 92px 36px 74px 36px;
        }

        body {
            font-family: "IBM Plex Sans", "Helvetica", sans-serif !important;
            font-size: 9pt;
            color: #444;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 58px;
            border-bottom: 2px solid <?php echo $company_info['colorcode'] ?? '#003366'; ?>;
            padding-bottom: 8px;
        }

        footer {
            position: fixed;
            bottom: -56px;
            left: 0;
            right: 0;
            height: 44px;
            background-color: #f5f7fa;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }

        .header-table {
            table-layout: fixed;
        }

        .header-table td,
        .footer-table td,
        .address-table td {
            vertical-align: top;
        }

        .header-table .logo {
            width: 30%;
            vertical-align: middle;
        }

        .header-table .logo img {
            max-width: 170px;
            max-height: 44px;
        }

        .header-table .title-block {
            width: 70%;
            text-align: right;
            vertical-align: middle;
        }

        .title-block h1 {
            margin: 0;
            font-size: 18.5pt;
            font-weight: 700;
            color: <?php echo $company_info['colorcode'] ?? '#003366'; ?>;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }

        .title-block .revision-tag {
            display: inline;
            font-size: 12pt;
            font-weight: 700;
            white-space: nowrap;
        }

        .title-block p {
            margin: 5px 0 0;
            font-size: 9.5pt;
            line-height: 1.45;
        }

        .quote-meta-table {
            margin: 0 0 12px;
            table-layout: fixed;
        }

        .quote-meta-table td {
            width: 50%;
            vertical-align: top;
        }

        .quote-meta-box {
            border: 1px solid #d9e3ef;
            background: #f8fbff;
            padding: 8px 10px;
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
            font-size: 10pt;
            font-weight: 700;
            color: <?php echo $company_info['colorcode'] ?? '#003366'; ?>;
        }

        .footer-table .info {
            font-size: 7.7pt;
            color: #888;
            text-align: left;
            width: 78%;
        }

        .footer-table .footer-image {
            text-align: right;
            vertical-align: middle;
            width: 22%;
        }

        .footer-table .footer-image img {
            max-height: 34px;
            width: auto;
        }

        .page-number:after {
            content: "Page " counter(page);
        }

        .watermark {
            position: fixed;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: -1000;
            width: 100%;
            text-align: center;
            white-space: nowrap;
            font-size: 78pt;
            font-weight: 700;
            color: rgba(0, 0, 0, 0.02);
            pointer-events: none;
        }

        .address-table {
            margin-bottom: 12px;
        }

        .address-table td {
            width: 50%;
            padding-right: 16px;
            font-size: 8.8pt;
            line-height: 1.35;
        }

        .address-table td:last-child {
            padding-right: 0;
            padding-left: 16px;
        }

        .address-table h3 {
            margin: 0 0 6px;
            font-size: 9pt;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .intro-text {
            margin: 0 0 4px;
            line-height: 1.45;
        }

        .main-table {
            margin-top: 10px;
            table-layout: fixed;
        }

        .main-table thead {
            display: table-header-group;
        }

        .main-table tr {
            page-break-inside: avoid;
        }

        .main-table th,
        .main-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
            word-break: break-word;
        }

        .main-table th {
            color: #333;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #ddd;
        }

        .main-table .col-sno,
        .main-table .col-hsn,
        .main-table .col-qty {
            text-align: center;
        }

        .main-table .col-sno {
            width: 5%;
        }

        .main-table .col-description {
            width: 42%;
            text-align: left;
            line-height: 1.45;
        }

        .main-table .col-hsn {
            width: 9%;
        }

        .main-table .col-qty {
            width: 6%;
        }

        .main-table .col-unit {
            width: 12%;
            text-align: right;
        }

        .main-table .col-discount {
            width: 8%;
            text-align: right;
        }

        .main-table .col-gst {
            width: 8%;
            text-align: right;
        }

        .main-table .col-total {
            width: 15%;
            text-align: right;
        }

        .summary-layout {
            margin-top: 14px;
            table-layout: fixed;
            page-break-inside: avoid;
        }

        .summary-layout td {
            vertical-align: top;
        }

        .summary-terms-cell {
            width: 52%;
            padding-right: 14px;
        }

        .summary-totals-cell {
            width: 48%;
        }

        .terms-section {
            margin-top: 0;
        }

        .terms-section h4,
        .totals-section h4 {
            margin: 0 0 8px;
            font-size: 10pt;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        .terms-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .terms-table td:first-child {
            width: 120px;
            font-weight: 700;
        }

        .totals-section {
            margin-top: 0;
            width: 100%;
            margin-left: 0;
            page-break-inside: avoid;
        }

        .totals-table td {
            padding: 5px 7px;
            border-bottom: 1px solid #e7edf4;
        }

        .totals-table td:last-child {
            text-align: right;
        }

        .totals-table tr.grand-total td {
            font-weight: 700;
            font-size: 13pt;
            background-color: <?php echo $company_info['colorcode'] ?? '#003366'; ?>;
            color: #fff;
            border-top: 2px solid <?php echo $company_info['colorcode'] ?? '#003366'; ?>;
        }

        .amount-in-words {
            margin-top: 8px;
            font-size: 8.7pt;
            font-weight: 700;
            line-height: 1.4;
            page-break-inside: avoid;
        }

        .signature-area {
            margin-top: 14px;
            line-height: 1.5;
            page-break-inside: avoid;
        }

        .important-note {
            margin-top: 12px;
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 8.2pt;
            line-height: 1.45;
            page-break-inside: avoid;
        }

        .important-note h4 {
            margin: 0 0 6px;
            font-size: 9pt;
            color: #c0392b;
        }

        .important-note p {
            margin: 0 0 6px;
        }
    </style>
</head>
<body>
    <?php
        $currency_type = (isset($currency) && $currency == 'USD') ? 'USD' : 'INR';
        $curr_symbol = ($currency_type == 'USD') ? '$' : '₹';
        $packing_charge_mode = strtolower(trim((string) ($packing_charge_mode ?? 'included')));
        $freight_charge_mode = strtolower(trim((string) ($freight_charge_mode ?? 'included')));
        $ex_work_charge_mode = strtolower(trim((string) ($ex_work_charge_mode ?? 'included')));
        $insurance_charge_mode = strtolower(trim((string) ($insurance_charge_mode ?? 'included')));

        $billing_email = !empty($customer->email) ? $customer->email : '';
        $billing_contact = !empty($customer->contact_person_no) ? $customer->contact_person_no : '';
        $shipping_email = !empty($shipping_customer->email) ? $shipping_customer->email : $billing_email;
        $shipping_contact = !empty($shipping_customer->contact_person_no) ? $shipping_customer->contact_person_no : $billing_contact;
    ?>

    <div class="watermark">Shubham Pack</div>

    <header>
        <table class="header-table">
            <tr>
                <td class="logo">
                    <img src="<?php echo $company_info['logo']; ?>" alt="Logo">
                </td>
                <td class="title-block">
                    <h1>QUOTATION - SPARES<?php if (isset($revision_no) && $revision_no > 0): ?> <span class="revision-tag">(Rev. <?php echo $revision_no; ?>)</span><?php endif; ?></h1>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td class="info">
                    <b><?php echo htmlspecialchars($company_info['name']); ?></b><br>
                    <?php echo htmlspecialchars($company_info['address']); ?><br>
                    <?php echo htmlspecialchars($company_info['contact']); ?> | E-mail: <?php echo htmlspecialchars($company_info['email']); ?> | Website: <?php echo htmlspecialchars($company_info['website']); ?> | <span class="page-number"></span>
                </td>
                <td class="footer-image">
                    <?php if (!empty($company_info['footer_image'])): ?>
                        <img src="<?php echo $company_info['footer_image']; ?>" alt="Accreditation">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </footer>

    <main>
        <table class="quote-meta-table">
            <tr>
                <td style="padding-right: 8px;">
                    <div class="quote-meta-box">
                        <div class="quote-meta-label">Reference No.</div>
                        <div class="quote-meta-value"><?php echo htmlspecialchars($quotation_no); ?></div>
                    </div>
                </td>
                <td style="padding-left: 8px;">
                    <div class="quote-meta-box">
                        <div class="quote-meta-label">Date</div>
                        <div class="quote-meta-value"><?php echo date('F j, Y', strtotime($quotation_date)); ?></div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="address-table">
            <tr>
                <td>
                    <h3>Bill To</h3>
                    <b><?php echo htmlspecialchars($customer->company_name); ?></b><br>
                    <?php echo nl2br(htmlspecialchars($customer->address)); ?><br>
                    <?php if ($billing_email !== ''): ?>
                        <?php echo htmlspecialchars($billing_email); ?><br>
                    <?php endif; ?>
                    <?php if ($billing_contact !== ''): ?>
                        <?php echo htmlspecialchars($billing_contact); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($attention)): ?>
                        <b>Kind Attention:</b> <?php echo htmlspecialchars($attention); ?>
                    <?php endif; ?>
                </td>
                <td>
                    <h3>Ship To</h3>
                    <?php if (!empty($shipping_customer)): ?>
                        <b><?php echo htmlspecialchars($shipping_customer->company_name); ?></b><br>
                        <?php echo nl2br(htmlspecialchars($shipping_customer->address)); ?><br>
                        <?php if ($shipping_email !== ''): ?>
                            <?php echo htmlspecialchars($shipping_email); ?><br>
                        <?php endif; ?>
                        <?php if ($shipping_contact !== ''): ?>
                            <?php echo htmlspecialchars($shipping_contact); ?><br>
                        <?php endif; ?>
                    <?php else: ?>
                        <b><?php echo htmlspecialchars($customer->company_name); ?></b><br>
                        <?php echo nl2br(htmlspecialchars($customer->address)); ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <p class="intro-text">Dear Sir/Madam,</p>
        <p class="intro-text">Further to your inquiry, we are pleased to submit our most competitive offer for the following items:</p>

        <table class="main-table">
            <thead>
                <tr>
                    <th class="col-sno">#</th>
                    <th class="col-description">Item Description</th>
                    <th class="col-hsn">HSN</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-unit">Unit Price (<?php echo $curr_symbol; ?>)</th>
                    <th class="col-discount">Disc (%)</th>
                    <th class="col-gst">GST (%)</th>
                    <th class="col-total">Line Total (<?php echo $curr_symbol; ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($products as $item): ?>
                <tr>
                    <td class="col-sno"><?php echo $i++; ?></td>
                    <td class="col-description"><?php echo nl2br(htmlspecialchars($item['description'])); ?></td>
                    <td class="col-hsn"><?php echo htmlspecialchars($item['hsn_code']); ?></td>
                    <td class="col-qty"><?php echo $item['quantity']; ?></td>
                    <td class="col-unit"><?php echo number_format($item['unit_price'], 2); ?></td>
                    <td class="col-discount"><?php echo number_format((float) ($item['discount_percent'] ?? 0), 2); ?></td>
                    <td class="col-gst"><?php echo number_format((float) ($item['gst_percent'] ?? 0), 2); ?></td>
                    <td class="col-total"><?php echo number_format($item['total_price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="summary-layout">
            <tr>
                <td class="summary-terms-cell">
                    <div class="terms-section">
                        <h4>Terms & Conditions</h4>
                        <table class="terms-table">
                            <tr><td>Payment Terms</td><td>: <?php echo htmlspecialchars($payment_terms); ?></td></tr>
                            <tr><td>Validity</td><td>: <?php echo htmlspecialchars($validity); ?></td></tr>
                            <tr><td>Delivery</td><td>: <?php echo htmlspecialchars($delivery_terms); ?></td></tr>
                        </table>
                    </div>
                </td>
                <td class="summary-totals-cell">
                    <div class="totals-section">
                        <table class="totals-table">
                            <tr><td>Basic Value (<?php echo $currency_type; ?>)</td><td><?php echo number_format($basic_value, 2); ?></td></tr>
                            <?php if ($packing_charge_mode === 'extra'): ?>
                            <tr><td>Packing Charges</td><td>Extra</td></tr>
                            <?php else: ?>
                            <tr><td>Packing (<?php echo $packing_percent; ?>%)</td><td><?php echo number_format($packing_charge, 2); ?></td></tr>
                            <?php endif; ?>

                            <?php if ($freight_charge_mode === 'extra'): ?>
                            <tr><td>Freight Charges</td><td>Extra</td></tr>
                            <?php elseif ($freight_charge > 0): ?>
                            <tr><td>Freight</td><td><?php echo number_format($freight_charge, 2); ?></td></tr>
                            <?php endif; ?>

                            <?php if ($ex_work_charge_mode === 'extra'): ?>
                            <tr><td>Ex-Work Charges</td><td>Extra</td></tr>
                            <?php elseif (isset($ex_work_charge) && $ex_work_charge > 0): ?>
                            <tr><td>Ex-Work Charges</td><td><?php echo number_format($ex_work_charge, 2); ?></td></tr>
                            <?php endif; ?>

                            <?php if ($insurance_charge_mode === 'extra'): ?>
                            <tr><td>Insurance Charges</td><td>Extra</td></tr>
                            <?php else: ?>
                            <tr><td>Insurance (<?php echo $insurance_percent; ?>%)</td><td><?php echo number_format($insurance_charge, 2); ?></td></tr>
                            <?php endif; ?>

                            <?php if ($overall_discount_amount > 0): ?>
                            <tr><td>Sub Total</td><td><?php echo number_format($sub_total, 2); ?></td></tr>
                            <tr><td>Overall Discount (<?php echo $overall_discount_percent; ?>%)</td><td style="color: #e74c3c;">- <?php echo number_format($overall_discount_amount, 2); ?></td></tr>
                            <?php endif; ?>

                            <tr><td><b>Total Value (<?php echo $currency_type; ?>)</b></td><td><b><?php echo number_format($total_value, 2); ?></b></td></tr>

                            <?php if ($gst_amount > 0): ?>
                            <tr><td><?php echo htmlspecialchars($gst_summary_label ?? 'GST', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo number_format($gst_amount, 2); ?></td></tr>
                            <?php endif; ?>

                            <tr class="grand-total">
                                <td>Grand Total (<?php echo $currency_type; ?>)</td>
                                <td><?php echo $curr_symbol; ?> <?php echo number_format($grand_total, 2); ?></td>
                            </tr>
                        </table>
                        <div class="amount-in-words">
                            <b>Amount in Words:</b><br>
                            <?php echo htmlspecialchars($amount_in_words); ?>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="signature-area">
            <p style="margin: 0 0 18px;">Thank you for the opportunity to quote. We look forward to working with you.</p>
            <p style="margin: 0;"><b>For <?php echo htmlspecialchars($company_info['name']); ?></b><br><br><br><b>Authorized Signatory</b></p>
        </div>

        <div class="important-note">
            <h4>Important Note:</h4>
            <p>Please follow all terms and conditions mentioned in the quotation and release the Purchase order accordingly. Installation and commissioning will be extra as applicable.</p>
            <p>Kindly mention our quotation Reference Number in your PO.</p>
        </div>
    </main>
</body>
</html>
