<?php
defined('BASEPATH') or exit('No direct script access allowed');

$currency = strtoupper(trim((string) ($pi->currency ?? 'INR')));
$currency_symbol = ($currency === 'USD') ? '$' : '₹';
$company_color = $company_profile['colorcode'] ?? '#003366';
$notes_lines = preg_split('/\r\n|\r|\n/', (string) ($pi->notes ?? ''), -1, PREG_SPLIT_NO_EMPTY);

if (empty($notes_lines)) {
    $notes_lines = [
        'All disputes are subject to Faridabad Jurisdiction.',
        'Bills not paid on presentation, interest will be charged @ 18% p.a.',
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PI - <?php echo htmlspecialchars((string) ($pi->pi_no ?? ''), ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        @page { margin: 20px 24px 22px 24px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 9px; color: #1f2933; margin: 0; }
        .page-wrap { position: relative; }
        .watermark {
            position: fixed;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: -1000;
            font-size: 68pt;
            color: rgba(0, 0, 0, 0.03);
            font-weight: bold;
            letter-spacing: 1px;
            white-space: nowrap;
        }
        .header { width: 100%; margin-bottom: 18px; }
        .header td { vertical-align: top; }
        .logo-cell { width: 88px; }
        .logo-cell img { max-width: 78px; max-height: 78px; }
        .company-name {
            color: <?php echo $company_color; ?>;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .header-sub { font-size: 9px; color: #4b5563; line-height: 1.35; }
        .iso-label { font-size: 8px; color: #6b7280; margin-top: 4px; }
        .header-tagline { text-align: right; font-size: 10px; font-style: italic; color: #7b8794; font-weight: bold; }
        .boxed-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .boxed-table td, .boxed-table th { border: 1px solid #5b6673; padding: 4px 5px; vertical-align: top; }
        .title-row { font-size: 11px; font-weight: bold; text-align: center; background: #f7f9fc; }
        .mini-label { font-size: 8px; font-weight: bold; color: #374151; }
        .value { font-size: 9px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .item-table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: -1px; }
        .item-table td, .item-table th { border: 1px solid #5b6673; padding: 4px 5px; vertical-align: top; }
        .item-table thead th { background: #f2f6fb; font-size: 8.5px; font-weight: bold; text-transform: uppercase; }
        .totals-cell { font-weight: bold; }
        .signature-spacer { height: 90px; }
        .signature-cell {
            width: 32%;
            text-align: right;
            vertical-align: bottom;
            padding: 10px 12px 8px 12px !important;
        }
        .signature-company {
            font-size: 9.5px;
            font-weight: bold;
            line-height: 1.35;
        }
        .signature-gap { height: 46px; }
        .signature-label {
            font-size: 9.5px;
            font-weight: bold;
            line-height: 1.2;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 9px;
            line-height: 1.55;
        }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <div class="page-wrap">
        <div class="watermark">Shubham Pack</div>

        <table class="header">
            <tr>
                <td class="logo-cell">
                    <img src="<?php echo htmlspecialchars($company_profile['logo'], ENT_QUOTES, 'UTF-8'); ?>" alt="Logo">
                </td>
                <td>
                    <div class="company-name"><?php echo htmlspecialchars($company_profile['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="header-sub">
                        GST No. : <?php echo htmlspecialchars($company_profile['gst_no'], ENT_QUOTES, 'UTF-8'); ?><br>
                        CIN No. : <?php echo htmlspecialchars($company_profile['cin_no'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div class="iso-label"><?php echo htmlspecialchars($company_profile['iso_label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                </td>
                <td class="header-tagline">
                    <?php echo htmlspecialchars($company_profile['unit'], ENT_QUOTES, 'UTF-8'); ?><br><br>
                    <?php echo htmlspecialchars($company_profile['tagline'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
            </tr>
        </table>

        <table class="boxed-table">
            <tr>
                <td colspan="6" class="title-row">
                    <?php echo htmlspecialchars($pi->pi_title ?: 'Proforma Invoice', ENT_QUOTES, 'UTF-8'); ?>
                </td>
            </tr>
            <tr>
                <td width="16%"><span class="mini-label">ECC No.</span></td>
                <td width="40%" class="value"><?php echo htmlspecialchars($company_profile['ecc_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%" class="center"><span class="mini-label">PI No.</span></td>
                <td width="14%" class="center"><span class="mini-label">Date</span></td>
            </tr>
            <tr>
                <td><span class="mini-label">Service Tax Regn. No.</span></td>
                <td class="value"><?php echo htmlspecialchars($company_profile['service_tax_regn_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td></td>
                <td></td>
                <td rowspan="4" class="center value" style="font-size: 13px; font-weight: bold;"><?php echo htmlspecialchars((string) $pi->pi_no, ENT_QUOTES, 'UTF-8'); ?></td>
                <td rowspan="4" class="center value" style="font-size: 10px; font-weight: bold;"><?php echo !empty($pi->pi_date) ? date('d-M-Y', strtotime($pi->pi_date)) : '-'; ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">GST Regn. No.</span></td>
                <td class="value"><?php echo htmlspecialchars($company_profile['gst_regn_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><span class="mini-label">IEC Code</span></td>
                <td class="value"><?php echo htmlspecialchars($company_profile['iec_code'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><span class="mini-label">Tariff / SAC</span></td>
                <td class="value"><?php echo htmlspecialchars($company_profile['tariff_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><span class="mini-label">Buyer</span></td>
                <td class="value"><strong><?php echo htmlspecialchars($pi->buyer_name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                <td colspan="2"></td>
                <td colspan="2"><span class="mini-label">Consignee (if other than buyer)</span></td>
            </tr>
            <tr>
                <td><span class="mini-label">Name</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->buyer_contact ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td colspan="2" class="value"><?php echo htmlspecialchars($pi->consignee_name ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">Address</span></td>
                <td class="value"><?php echo nl2br(htmlspecialchars($pi->buyer_address ?: '-', ENT_QUOTES, 'UTF-8')); ?></td>
                <td colspan="2"></td>
                <td colspan="2" class="value"><?php echo nl2br(htmlspecialchars($pi->consignee_address ?: '-', ENT_QUOTES, 'UTF-8')); ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">GSTIN No.</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->buyer_gstin ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td><span class="mini-label">ECC No.</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->consignee_ecc_no ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">Terms of Payment</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->payment_terms ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td><span class="mini-label">Phone No.</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->consignee_phone ?: ($pi->consignee_contact ?: '-'), ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">Bank Name</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->bank_name ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td><span class="mini-label">Buyer PO Number</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->buyer_order_no ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">A/c No.</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->account_no ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td><span class="mini-label">Buyer Order Date</span></td>
                <td class="value"><?php echo !empty($pi->buyer_order_date) ? date('d-M-Y', strtotime($pi->buyer_order_date)) : '-'; ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">Bank Address</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->bank_address ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><span class="mini-label">IFSC Code</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->ifsc_code ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><span class="mini-label">A/c Type</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->account_type ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><span class="mini-label">A/c Holder</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->account_holder ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="2"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><span class="mini-label">Swift Code</span></td>
                <td class="value"><?php echo htmlspecialchars($pi->swift_code ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                <td colspan="4"></td>
            </tr>
        </table>

        <table class="item-table">
            <thead>
                <tr>
                    <th width="6%">S.No.</th>
                    <th width="10%">SAC</th>
                    <th width="40%">Description</th>
                    <th width="12%">Unit Rate (<?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>)</th>
                    <th width="10%">Days / Times</th>
                    <th width="9%">No. of Engg.</th>
                    <th width="13%">Total Amount (<?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item) : ?>
                    <tr>
                        <td class="center"><?php echo $index + 1; ?></td>
                        <td class="center"><?php echo htmlspecialchars($item->sac_code ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($item->description ?: '-', ENT_QUOTES, 'UTF-8')); ?></td>
                        <td class="right">
                            <?php echo !empty($item->is_customer_scope) ? 'Customer Scope' : number_format((float) $item->unit_rate, 2); ?>
                        </td>
                        <td class="center">
                            <?php echo number_format((float) $item->no_of_days, 2); ?><br>
                            <span class="muted"><?php echo htmlspecialchars($item->uom ?: 'Day', ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                        <td class="center"><?php echo number_format((float) $item->no_of_engineers, 2); ?></td>
                        <td class="right">
                            <?php echo !empty($item->is_customer_scope) ? htmlspecialchars($item->scope_note ?: 'Customer Scope', ENT_QUOTES, 'UTF-8') : number_format((float) $item->row_total, 2); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6" class="right totals-cell">Basic Value (<?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>)</td>
                    <td class="right totals-cell"><?php echo number_format((float) $pi->basic_amount, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="6" class="right totals-cell">GST (<?php echo number_format((float) $pi->gst_percent, 2); ?>%)</td>
                    <td class="right totals-cell"><?php echo number_format((float) $pi->gst_amount, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="6" class="right totals-cell">TOTAL VALUE (<?php echo htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?>)</td>
                    <td class="right totals-cell"><?php echo number_format((float) $pi->grand_total, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <table class="boxed-table" style="margin-top:-1px;">
            <tr>
                <td width="16%"><span class="mini-label">Country of Origin</span></td>
                <td width="84%" class="value"><?php echo htmlspecialchars($pi->country_of_origin ?: 'India', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">In Words</span></td>
                <td class="value"><?php echo htmlspecialchars($amount_in_words, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">Declaration</span></td>
                <td class="value"><?php echo nl2br(htmlspecialchars($pi->declaration_text ?: '-', ENT_QUOTES, 'UTF-8')); ?></td>
            </tr>
            <tr>
                <td><span class="mini-label">Notes</span></td>
                <td class="value">
                    <?php foreach ($notes_lines as $line) : ?>
                        <?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?><br>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <td class="signature-spacer"></td>
                <td class="signature-cell">
                    <div class="signature-company">For <?php echo htmlspecialchars($company_profile['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="signature-gap"></div>
                    <div class="signature-label">Authorised Signatory</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Corporate Office : <?php echo htmlspecialchars($company_profile['corporate_office'], ENT_QUOTES, 'UTF-8'); ?><br>
            Registered Office : <?php echo htmlspecialchars($company_profile['registered_office'], ENT_QUOTES, 'UTF-8'); ?><br>
            Phone: <?php echo htmlspecialchars($company_profile['phone'], ENT_QUOTES, 'UTF-8'); ?> Fax: <?php echo htmlspecialchars($company_profile['fax'], ENT_QUOTES, 'UTF-8'); ?><br>
            Email: <?php echo htmlspecialchars($company_profile['emails'], ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($company_profile['website'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    </div>
</body>
</html>
