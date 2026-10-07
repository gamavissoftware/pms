<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotation - <?php 
    $q = $this->db->select('opportunity_id,kindattention,contactno,email')->from('service_quotations')->where('id',$this->uri->segment(3))->get();
    foreach($q->result() as $oppninfo);

    $q1 = $this->db->select('op_no')->from('service_opportunities')->where('opportunity_id',$oppninfo->opportunity_id)->get();
    foreach($q1->result() as $opportunitynoinfo);
    echo $opportunitynoinfo->op_no; ?></title>
    <style>
        /* 1. Page Setup for Dompdf */
        @page { 
            margin: 130px 45px 80px 45px; 
        } 
        
        body { font-family: "Helvetica", "Arial", sans-serif !important; font-size: 9.5pt; color: #333; margin: 0; padding-bottom: 50px; }

        /* FIX: Tighten Bullet Points and List Spacing */
        .terms-section ul {
            margin: 5px 0;
            padding-left: 20px; /* Reduces left indent */
            list-style-type: disc;
        }

        .terms-section li {
            margin-bottom: 3px; /* Reduces space between individual points */
            line-height: 2;
            padding-left: 0;
        }

        /* 2. Fixed Header - Repeats on every page */
        header {
            position: fixed;
            top: -110px; 
            left: 0px;
            right: 0px;
            height: 110px;
            border-bottom: 2px solid <?php echo $company_info['colorcode']; ?>;
        }

        /* 3. Fixed Footer - Repeats on every page */
        footer {
            position: fixed;
            bottom: -60px; 
            left: 0px;
            right: 0px;
            height: 50px;
            font-size: 8pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .header-table { width: 100%; border: none; }
        .header-table td { vertical-align: middle; border: none; }
        .title-block { text-align: right; }
        .title-block h1 { margin: 0; font-size: 19pt; font-weight: bold; color: <?php echo $company_info['colorcode']; ?>; letter-spacing: 1px; }
        
        .main-content { width: 100%; }
        
        .address-table { width: 100%; margin-bottom: 15px; border: none; }
        .address-table td { vertical-align: top; border: none; line-height: 1.5; }
        .client-detail-line { margin: 0 0 2px 0; word-break: break-word; }
        .client-detail-label { font-weight: bold; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        .main-table th, .main-table td { padding: 8px 6px; border: 1px solid #999; font-size: 8.5pt; word-wrap: break-word; }
        .main-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; text-transform: uppercase; }
        
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .total-row td { background-color: #fafafa; font-weight: bold; }
        .grand-total-row td { background-color: #444 !important; color: white !important; font-weight: bold; font-size: 10pt; }
        
        .watermark { 
            position: fixed; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%) rotate(-45deg); 
            z-index: -1000; 
            font-size: 70pt; 
            color: rgba(0, 0, 0, 0.02); 
            font-weight: bold; 
        }
    </style>
</head>
<body>
    <?php
    $pdf_currency = strtoupper(trim((string) ($currency ?? 'INR')));
    if (!in_array($pdf_currency, array('INR', 'USD', 'EUR'), true)) {
        $pdf_currency = 'INR';
    }
    ?>
    <div class="watermark">Shubham Pack</div>

    <!-- REPEATING HEADER -->
    <header>
        <table class="header-table">
            <tr>
                <td><img src="<?php echo $company_info['logo']; ?>" style="max-height: 60px;"></td>
                <td class="title-block">
                    <h1>QUOTATION - SERVICE</h1>
                    <p style="margin: 5px 0 0 0; font-size: 9.5pt;">
                        <b>Ref:</b> <?php 
    $q = $this->db->select('opportunity_id,kindattention,contactno,email')->from('service_quotations')->where('id',$this->uri->segment(3))->get();
    foreach($q->result() as $oppninfo);
    $q1 = $this->db->select('op_no')->from('service_opportunities')->where('opportunity_id',$oppninfo->opportunity_id)->get();
    foreach($q1->result() as $opportunitynoinfo);
    echo $opportunitynoinfo->op_no;
    if (!empty($revision_no)) {
        echo ' / R' . (int) $revision_no;
    }
    ?>
    <br>
                        <b>Date:</b> <?php echo date('d M, Y', strtotime($quotation_date)); ?>
                    </p>
                </td>
            </tr>
        </table>
    </header>

    <!-- REPEATING FOOTER -->
    <footer>
        <b><?php echo $company_info['name']; ?></b> | <?php echo $company_info['address']; ?><br>
        Email: <?php echo $company_info['email']; ?> | Web: <?php echo $company_info['website']; ?>
    </footer>

    <main class="main-content">
        <!-- Client Details -->
        <table class="address-table">
            <?php
            $sanitize_pdf_text = static function ($value) {
                $value = (string) $value;
                $value = preg_replace('/<\s*br\s*\/?>/i', "\n", $value);
                $value = html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
                $value = preg_replace("/[ \t]+/", ' ', $value);
                $value = preg_replace("/\n{3,}/", "\n\n", $value);
                return trim($value);
            };

            $escape_pdf_text = static function ($value) use ($sanitize_pdf_text) {
                return htmlspecialchars($sanitize_pdf_text($value), ENT_QUOTES, 'UTF-8');
            };

            if ($oppninfo->email == '') {
                $email = $customer->email ?? '';
            } else {
                $email = $oppninfo->email;
            }

            if (!empty($contactno)) {
                $contact_value = $contactno;
            } else {
                $contact_value = $customer->phone ?? '';
            }

            $company_name = htmlspecialchars(
                strtoupper($sanitize_pdf_text($customer->company_name ?? '')),
                ENT_QUOTES,
                'UTF-8'
            );
            $customer_address = nl2br($escape_pdf_text($customer->address ?? ''));
            $email = $escape_pdf_text($email);
            $contact_value = $escape_pdf_text($contact_value);
            $kind_attention_value = $sanitize_pdf_text($kindattention ?? '');
            if ($kind_attention_value !== '') {
                $kind_attention_value = htmlspecialchars(
                    ucwords(strtolower($kind_attention_value)),
                    ENT_QUOTES,
                    'UTF-8'
                );
            } else {
                $kind_attention_value = '________________';
            }

            ?>
            <tr>
                <td style="width: 60%;">
                    <strong>To,</strong><br>
                    <span style="font-size: 10.5pt; font-weight: bold;">M/S. <?php echo $company_name; ?></span><br>
                    <div class="client-detail-line">
                        <span class="client-detail-label">Address:</span> <?php echo $customer_address; ?>
                    </div>
                    <?php if (!empty($email)): ?>
                    <div class="client-detail-line">
                        <span class="client-detail-label">Email:</span> <?php echo $email; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($contact_value)): ?>
                    <div class="client-detail-line">
                        <span class="client-detail-label">Contact:</span> <?php echo $contact_value; ?>
                    </div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
         <table class="address-table">
            <tr>
                <td>  <div style="margin-bottom: 5px;">
            <strong>Kind Attention:</strong> <?php echo $kind_attention_value; ?><br>
            <strong>Subject:</strong> <?php echo $subject; ?>
        </div></td>
            </tr>
            <tr>
                <td><p style="margin-bottom: 5px;">
            This refers to our discussion regarding the requirement of service engineer’s deputation for the subjected work, please find our quotation for the same, as below.
        </p></td>
            </tr>
         </table>
    

        

        <!-- Items Table -->
        <table class="main-table">
            <thead>
                <tr>
                    <th width="7%">S.NO</th>
                    <th width="35%">DESCRIPTION</th>
                    <th width="13%">RATE (<?php echo $pdf_currency; ?>)</th>
                    <th width="15%">DAYS / TIMES</th>
                    <th width="10%">ENG.</th>
                    <th width="20%" class="text-right">TOTAL (<?php echo $pdf_currency; ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; foreach($items as $row): ?>
                <tr>
                    <td class="text-center"><?php echo $i++; ?></td>
                    <td class="text-center">
                        <strong style="text-transform: uppercase;"><?php echo $row->charge_name; ?></strong>
                        <?php if(!empty($row->description)): ?>
                            <br><span style="color: #555; font-size: 8pt;"><?php echo nl2br(strip_tags($row->description)); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo $pdf_currency . ' ' . number_format($row->rate, 2); ?></td>
                    <td class="text-center">
                        <?php 
                            $val = $row->no_of_days;
                            echo $val . ' ' . ( (strtolower($row->uom) == 'day' || empty($row->uom)) ? ($val > 1 ? 'Days' : 'Day') : $row->uom );
                        ?>
                    </td>
                    <td class="text-center"><?php echo $row->no_of_engineers; ?></td>
                    <td class="text-right">
                        <?php echo ($row->is_customer_scope == '1') ? '<strong>CUSTOMER SCOPE</strong>' : $pdf_currency . ' ' . number_format($row->row_total, 2); ?>
                    </td>
                </tr>
                <?php endforeach; ?>

                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL BASIC AMOUNT</td>
                    <td class="text-right"><?php echo $pdf_currency . ' ' . number_format($total_basic_amount, 2); ?></td>
                </tr>

                <?php if(isset($total_discount) && $total_discount > 0): ?>
                <tr class="total-row">
                    <td colspan="5" class="text-right" style="color: #d4380d;">DISCOUNT (-)</td>
                    <td class="text-right" style="color: #d4380d;"><?php echo $pdf_currency . ' ' . number_format($total_discount, 2); ?></td>
                </tr>
                <?php endif; ?>

                <?php if($gst_percent > 0): ?>
                <tr class="total-row">
                    <td colspan="5" class="text-right">GST (<?php echo $gst_percent; ?>%)</td>
                    <td class="text-right"><?php echo $pdf_currency . ' ' . number_format($gst_amount, 2); ?></td>
                </tr>
                <?php endif; ?>

                <?php if(isset($wht_amount) && $wht_amount > 0): ?>
                <tr class="total-row">
                    <td colspan="5" class="text-right">WHT (<?php echo $wht_percent; ?>%)</td>
                    <td class="text-right"><?php echo $pdf_currency . ' ' . number_format($wht_amount, 2); ?></td>
                </tr>
                <?php endif; ?>

                <?php if(isset($ex_works_amount) && $ex_works_amount > 0): ?>
                <tr class="total-row">
                    <td colspan="5" class="text-right">EX-WORKS</td>
                    <td class="text-right"><?php echo $pdf_currency . ' ' . number_format($ex_works_amount, 2); ?></td>
                </tr>
                <?php endif; ?>

                <?php if(isset($freight_amount) && $freight_amount > 0): ?>
                <tr class="total-row">
                    <td colspan="5" class="text-right">FREIGHT</td>
                    <td class="text-right"><?php echo $pdf_currency . ' ' . number_format($freight_amount, 2); ?></td>
                </tr>
                <?php endif; ?>

                <tr class="grand-total-row">
                    <td colspan="5" class="text-right">GRAND TOTAL (<?php echo $pdf_currency; ?>)</td>
                    <td class="text-right"><?php echo $pdf_currency . ' ' . number_format($grand_total, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 15px; font-size: 10pt;">
            <strong>Amount in Words:</strong> <?php echo $amount_in_words; ?> /-
        </div>

        <!-- Corrected Terms Section with Tight Spacing -->
        <div style="margin-top: 25px;" class="terms-section">
            <strong style="text-decoration: underline; display: block; margin-bottom: 5px;">TERMS & CONDITIONS:</strong>
            <div style="font-size: 8.5pt; color: #444; text-transform: none;">
                <?php echo $terms_conditions; ?>
            </div>
        </div>
        
        <div style="margin-top: 40px; page-break-inside: avoid;">
            <p style="margin:0;">Kind Regards,</p>
            <p style="margin:2px 0;">For <b><?php echo $company_info['name']; ?></b></p>
            <br><br><br>
            <p style="margin:0;"><b>Authorized Signatory</b></p>
        </div>
    </main>
</body>
</html>
