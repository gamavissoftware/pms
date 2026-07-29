<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sales Order - <?php echo htmlspecialchars($po_details->po_no); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap');

        @page { margin: 0; }
        body {
            font-family: "IBM Plex Sans", sans-serif !important;
            font-size: 9.5pt;
            color: #444;
            margin: 0;
        }
        .container { padding: 25px 40px; }
        table { width: 100%; border-collapse: collapse; }
        
        /* --- Header --- */
        .header-table { padding: 25px 40px 15px 40px; }
        .header-table td { vertical-align: middle; }
        .header-table .logo img { max-width: 200px; max-height: 55px; }
        .header-table .title-block { text-align: right; }
        .title-block h1 {
            margin: 0;
            font-size: 24pt;
            font-weight: bold;
            color: <?php echo $company_info['colorcode'] ?? '#003366'; ?>;
        }
        .title-block p { margin: 4px 0 0; font-size: 9.5pt; }
        .header-separator {
            border: 0;
            height: 2px;
            background-color: <?php echo $company_info['colorcode'] ?? '#003366'; ?>;
            margin: 0 40px 20px 40px;
        }

        /* --- Address Section --- */
        .address-table { width: 100%; margin-bottom: 20px; padding: 0 40px; }
        .address-table td {
            vertical-align: top;
            font-size: 9pt;
            line-height: 1.5;
            width: 50%;
            padding: 0 10px;
        }
        .address-table td:first-child { padding-left: 0; }
        .address-table h3 {
            margin: 0 0 5px 0;
            font-size: 9pt;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .main-content { padding: 0 40px; }
        .main-table { margin-top: 15px; }
        .main-table th, .main-table td {
            padding: 8px 6px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        .main-table th {
            background-color: transparent;
            color: #333;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #ddd;
        }
        .main-table .description { text-align: left; }
        .main-table td.center { text-align: center; }

        /* --- Totals --- */
        .summary-table { margin-top: 20px; page-break-inside: avoid; }
        .summary-table .terms-cell { width: 55%; vertical-align: top; padding-right: 20px; }
        .summary-table .totals-cell { width: 45%; vertical-align: top; }
        
        .totals-table td { padding: 6px 8px; }
        .totals-table td:last-child { text-align: right; }
        .totals-table tr.grand-total td {
            font-weight: bold;
            font-size: 13pt;
            background-color: <?php echo $company_info['colorcode'] ?? '#003366'; ?>;
            color: white;
        }
        .amount-in-words { margin-top: 10px; font-size: 9pt; font-weight: bold; }
        
        .terms-section h4 {
            font-size: 10pt;
            margin: 0 0 8px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
        }

        /* --- Footer --- */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; width: 100%; background-color: #f5f7fa; border-top: 1px solid #ddd; }
        .footer-table { width: 100%; padding: 10px 40px; }
        .footer-table .info { font-size: 8pt; color: #888; text-align: left; vertical-align: middle; }
        .page-number:after { content: "Page " counter(page); }
        
        .watermark {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: -1000; font-size: 80pt;
            color: rgba(0, 0, 0, 0.03); font-weight: bold;
            text-align: center; white-space: nowrap; width: 100%; pointer-events: none;
        }
    </style>
</head>
<body>

    <div class="watermark">SALES ORDER</div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="info">
                    <b><?php echo htmlspecialchars($company_info['name']); ?></b><br>
                    <?php echo htmlspecialchars($company_info['address']); ?> | <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>

    <table class="header-table">
        <tr>
            <td class="logo"><img src="<?php echo $company_info['logo']; ?>" alt="Logo"></td>
            <td class="title-block">
                <h1>SALES ORDER</h1>
                <p><b>SO No:</b> SO-<?php echo $po_details->po_id; ?><br>
                <b>Date:</b> <?php echo date('F j, Y', strtotime($po_details->created_at)); ?></p>
            </td>
        </tr>
    </table>
    
    <hr class="header-separator">

    <div class="main-content">
        <table class="address-table" style="padding: 0; margin-bottom: 25px;">
            <tr>
                <td width="50%">
                    <h3>CUSTOMER DETAILS</h3>
                    <b><?php echo htmlspecialchars($po_details->company_name); ?></b><br>
                    <?php echo nl2br(htmlspecialchars($po_details->customer_address)); ?><br>
                    <b>GSTIN:</b> <?php echo htmlspecialchars($po_details->tax_number ?? 'N/A'); ?>
                </td>
                <td width="50%" style="background: #fcfcfc; padding: 10px; border: 1px solid #eee;">
                    <h3>PO REFERENCE</h3>
                    <b>PO Number:</b> <?php echo htmlspecialchars($po_details->po_no); ?><br>
                    <b>PO Date:</b> <?php echo date('d-M-Y', strtotime($po_details->po_date)); ?><br>
                    <b>Exp. Delivery:</b> <?php echo $po_details->expected_delivery_date ? date('d-M-Y', strtotime($po_details->expected_delivery_date)) : 'TBC'; ?>
                </td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="center">#</th>
                    <th class="description">Item Description</th>
                    <th style="width: 10%;" class="center">Qty</th>
                    <th style="width: 15%;">Rate (INR)</th>
                    <th style="width: 20%;">Total (INR)</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($products as $product): ?>
                <tr>
                    <td class="center"><?php echo $i++; ?></td>
                    <td class="description">
                        <b><?php echo htmlspecialchars($product->product_code ?? ''); ?></b><br>
                        <?php echo nl2br(htmlspecialchars($product->description)); ?>
                    </td>
                    <td class="center"><?php echo $product->quantity; ?></td>
                    <td><?php echo number_format($product->price, 2); ?></td>
                    <td style="font-weight: bold;"><?php echo number_format($product->total, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td class="terms-cell">
                    <div class="terms-section">
                        <h4>Terms & Conditions</h4>
                        <p style="font-size: 8.5pt; line-height: 1.4;">
                            1. Payment: As per agreed terms.<br>
                            2. Delivery: Expected by <?php echo $po_details->expected_delivery_date ? date('d-M-Y', strtotime($po_details->expected_delivery_date)) : 'To be confirmed'; ?>.<br>
                            3. Standard warranty applies as per our catalog terms.
                        </p>
                    </div>
                    <div style="margin-top: 30px;">
                        <p><b>For <?php echo htmlspecialchars($company_info['name']); ?></b><br><br><br><br><b>Authorized Signatory</b></p>
                    </div>
                </td>
                <td class="totals-cell">
                    <table class="totals-table">
                        <tr><td>Basic Value</td><td><?php echo number_format($po_details->sub_total, 2); ?></td></tr>
                        
                        <?php if($po_details->packing_percent > 0): ?>
                        <tr><td>Packing (<?php echo $po_details->packing_percent; ?>%)</td><td><?php echo number_format(($po_details->sub_total * $po_details->packing_percent)/100, 2); ?></td></tr>
                        <?php endif; ?>

                        <?php if($po_details->freight_charge > 0): ?>
                        <tr><td>Freight Charges</td><td><?php echo number_format($po_details->freight_charge, 2); ?></td></tr>
                        <?php endif; ?>

                        <?php if($po_details->insurance_percent > 0): ?>
                        <tr><td>Insurance (<?php echo $po_details->insurance_percent; ?>%)</td><td><?php echo number_format(($po_details->sub_total * $po_details->insurance_percent)/100, 2); ?></td></tr>
                        <?php endif; ?>

                        <tr style="border-top: 1px solid #ddd;"><td>Taxable Value</td><td><b><?php echo number_format($po_details->taxable_value, 2); ?></b></td></tr>
                        
                        <tr><td>GST (18%)</td><td><?php echo number_format($po_details->gst_amount, 2); ?></td></tr>

                        <tr class="grand-total">
                            <td>GRAND TOTAL</td>
                            <td>₹ <?php echo number_format($po_details->grand_total, 2); ?></td>
                        </tr>
                    </table>
                    <div class="amount-in-words">
                        <b>Amount in Words:</b><br>
                        <?php echo ucwords(strtolower($amount_in_words)); ?>.
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>