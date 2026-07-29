<?php
defined('BASEPATH') or exit('No direct script access allowed');

$request_basis = strtoupper(trim((string) ($request->request_basis ?? 'ORDER')));
$request_basis_label = $request_basis === 'VISIT' ? 'Engineer Visit Request' : 'Order-wise Request';
$service_window = '-';
$arial_regular_path = realpath(FCPATH . 'application/views/formats/rfq/fonts/ARIAL.TTF');
$arial_bold_path = realpath(FCPATH . 'application/views/formats/rfq/fonts/ARIALBD.TTF');
$arial_italic_path = realpath(FCPATH . 'application/views/formats/rfq/fonts/ARIALI.TTF');
$arial_bold_italic_path = realpath(FCPATH . 'application/views/formats/rfq/fonts/ARIALBI.TTF');
$font_face_css = '';

if (!empty($arial_regular_path)) {
    $font_face_css .= "@font-face {\n"
        . "font-family: 'ArialPdf';\n"
        . "font-style: normal;\n"
        . "font-weight: normal;\n"
        . "src: url('file://" . str_replace('\\', '/', $arial_regular_path) . "') format('truetype');\n"
        . "}\n";
}

if (!empty($arial_bold_path)) {
    $font_face_css .= "@font-face {\n"
        . "font-family: 'ArialPdf';\n"
        . "font-style: normal;\n"
        . "font-weight: bold;\n"
        . "src: url('file://" . str_replace('\\', '/', $arial_bold_path) . "') format('truetype');\n"
        . "}\n";
}

if (!empty($arial_italic_path)) {
    $font_face_css .= "@font-face {\n"
        . "font-family: 'ArialPdf';\n"
        . "font-style: italic;\n"
        . "font-weight: normal;\n"
        . "src: url('file://" . str_replace('\\', '/', $arial_italic_path) . "') format('truetype');\n"
        . "}\n";
}

if (!empty($arial_bold_italic_path)) {
    $font_face_css .= "@font-face {\n"
        . "font-family: 'ArialPdf';\n"
        . "font-style: italic;\n"
        . "font-weight: bold;\n"
        . "src: url('file://" . str_replace('\\', '/', $arial_bold_italic_path) . "') format('truetype');\n"
        . "}\n";
}

$format_person_name = static function ($name, $fallback = '-') {
    $name = trim((string) $name);
    if ($name === '') {
        return $fallback;
    }

    if (preg_match('/^[A-Z][A-Z\\s]+$/', $name)) {
        return ucwords(strtolower($name));
    }

    return $name;
};

$format_action_label = static function ($action) {
    $action = trim((string) $action);
    $map = [
        'REQUEST_CREATED' => 'Request created',
        'HOD_APPROVED' => 'Approved by HOD',
        'HOD_REJECTED' => 'Rejected by HOD',
    ];

    if (isset($map[$action])) {
        return $map[$action];
    }

    if ($action === '') {
        return '-';
    }

    return ucwords(strtolower(str_replace('_', ' ', $action)));
};

if (!empty($request->service_from_date) || !empty($request->service_to_date)) {
    $service_window = (!empty($request->service_from_date) ? date('d M Y', strtotime($request->service_from_date)) : '-')
        . ' to '
        . (!empty($request->service_to_date) ? date('d M Y', strtotime($request->service_to_date)) : '-');
}

$attachment_url = !empty($request->attachment)
    ? base_url('uploads/service_payment_requests/' . rawurlencode($request->attachment))
    : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Service Payment Request - <?php echo htmlspecialchars((string) $request->request_code, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        <?php echo $font_face_css; ?>
        @page { margin: 22px 24px 22px 24px; }
        body {
            font-family: Calibri, ArialPdf, Arial, "DejaVu Sans", sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #243447;
            margin: 0;
        }
        .header-table,
        .meta-table,
        .logs-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo {
            width: 88px;
            max-height: 88px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #173f6d;
            margin-bottom: 6px;
        }
        .company-subline {
            color: #54657d;
            line-height: 1.5;
            font-size: 10.2px;
        }
        .report-title {
            margin-top: 18px;
            margin-bottom: 16px;
            padding: 14px 16px;
            background: #f4f8fc;
            border: 1px solid #d8e3ef;
            border-left: 4px solid #1d4f82;
        }
        .report-title h1 {
            margin: 0 0 7px;
            font-size: 17px;
            color: #1b2940;
        }
        .report-subline {
            font-size: 10.6px;
            color: #5f6f84;
            line-height: 1.55;
        }
        .section-title {
            margin-top: 18px;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #1b2940;
        }
        .meta-table,
        .logs-table {
            border: 1px solid #d7e1ec;
        }
        .meta-table td {
            border-right: 1px solid #d7e1ec;
            border-bottom: 1px solid #d7e1ec;
            padding: 10px 14px;
            vertical-align: top;
            width: 25%;
        }
        .meta-table td:last-child,
        .logs-table th:last-child,
        .logs-table td:last-child {
            border-right: none;
        }
        .meta-table tr:last-child td,
        .logs-table tr:last-child td {
            border-bottom: none;
        }
        .meta-label {
            display: block;
            margin-bottom: 5px;
            color: #6d7d93;
            font-size: 9.4px;
            font-weight: bold;
        }
        .meta-value {
            color: #1f2937;
            font-size: 11.2px;
            line-height: 1.58;
        }
        .amount-value {
            font-size: 16px;
            font-weight: bold;
            color: #0f766e;
        }
        .box {
            border: 1px solid #d7e1ec;
            background: #fbfcfe;
            padding: 12px 14px;
            line-height: 1.68;
        }
        .pill {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: bold;
        }
        .pill-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .pill-approved {
            background: #dcfce7;
            color: #166534;
        }
        .pill-rejected {
            background: #ffe4e6;
            color: #be123c;
        }
        .logs-table th,
        .logs-table td {
            border-right: 1px solid #d7e1ec;
            border-bottom: 1px solid #d7e1ec;
            padding: 9px 10px;
            vertical-align: top;
        }
        .logs-table th {
            background: #f4f7fb;
            color: #5e6d80;
            font-size: 9.4px;
            font-weight: bold;
        }
        .muted {
            color: #6d7d93;
        }
        .footer-note {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #7c8798;
        }
        a {
            color: #1d4ed8;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 95px;">
                <?php if (!empty($company_profile['logo'])): ?>
                    <img src="<?php echo htmlspecialchars((string) $company_profile['logo'], ENT_QUOTES, 'UTF-8'); ?>" alt="Company Logo" class="logo">
                <?php endif; ?>
            </td>
            <td>
                <div class="company-name"><?php echo htmlspecialchars((string) $company_profile['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="company-subline">
                    <?php echo htmlspecialchars((string) $company_profile['unit'], ENT_QUOTES, 'UTF-8'); ?><br>
                    <?php echo htmlspecialchars((string) $company_profile['iso_label'], ENT_QUOTES, 'UTF-8'); ?><br>
                    <?php echo htmlspecialchars((string) $company_profile['corporate_office'], ENT_QUOTES, 'UTF-8'); ?><br>
                    Phone: <?php echo htmlspecialchars((string) $company_profile['phone'], ENT_QUOTES, 'UTF-8'); ?> |
                    Email: <?php echo htmlspecialchars((string) $company_profile['emails'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </td>
        </tr>
    </table>

    <div class="report-title">
        <h1>Service Payment Request</h1>
        <div class="report-subline">
            Request code: <strong><?php echo htmlspecialchars((string) $request->request_code, ENT_QUOTES, 'UTF-8'); ?></strong>
            &nbsp;|&nbsp;
            Basis: <strong><?php echo htmlspecialchars($request_basis_label, ENT_QUOTES, 'UTF-8'); ?></strong>
            &nbsp;|&nbsp;
            Type: <strong><?php echo htmlspecialchars((string) $request->request_type, ENT_QUOTES, 'UTF-8'); ?></strong>
        </div>
    </div>

    <div class="section-title">Request snapshot</div>
    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">Request date</span>
                <div class="meta-value"><?php echo !empty($request->request_date) ? date('d M Y', strtotime($request->request_date)) : '-'; ?></div>
            </td>
            <td>
                <span class="meta-label">Status</span>
                <div class="meta-value">
                    <?php
                    $status = trim((string) ($request->status ?? ''));
                    $status_class = 'pill-pending';
                    if ($status === 'Approved') {
                        $status_class = 'pill-approved';
                    } elseif ($status === 'Rejected') {
                        $status_class = 'pill-rejected';
                    }
                    ?>
                    <span class="pill <?php echo $status_class; ?>"><?php echo htmlspecialchars($status !== '' ? $status : 'Pending', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </td>
            <td>
                <span class="meta-label">Requested amount</span>
                <div class="meta-value amount-value">Rs. <?php echo number_format((float) ($request->amount ?? 0), 2); ?></div>
            </td>
            <td>
                <span class="meta-label">PO value</span>
                <div class="meta-value">Rs. <?php echo number_format((float) ($request->po_amount ?? 0), 2); ?></div>
            </td>
        </tr>
    </table>

    <div class="section-title">Customer and order details</div>
    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">Customer</span>
                <div class="meta-value">
                    <?php echo htmlspecialchars((string) ($request->customer_name ?? '-'), ENT_QUOTES, 'UTF-8'); ?>
                    <?php if (!empty($request->customer_company_name) && strcasecmp(trim((string) $request->customer_company_name), trim((string) ($request->customer_name ?? ''))) !== 0): ?>
                        <div class="muted"><?php echo htmlspecialchars((string) $request->customer_company_name, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>
            </td>
            <td>
                <span class="meta-label">Opportunity no.</span>
                <div class="meta-value"><?php echo htmlspecialchars((string) ($request->op_no ?? '-'), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">PO number</span>
                <div class="meta-value"><?php echo htmlspecialchars((string) ($request->po_number ?? '-'), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">PO date</span>
                <div class="meta-value"><?php echo !empty($request->po_date) ? date('d M Y', strtotime($request->po_date)) : '-'; ?></div>
            </td>
        </tr>
    </table>

    <div class="section-title">Assignment and service window</div>
    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">Requested by</span>
                <div class="meta-value"><?php echo htmlspecialchars($format_person_name($request->created_by_name ?? '', 'System'), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">Requested for</span>
                <div class="meta-value"><?php echo htmlspecialchars($format_person_name($request->requested_for_name ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">Engineer</span>
                <div class="meta-value"><?php echo htmlspecialchars($format_person_name($request->engineer_name ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">Service dates</span>
                <div class="meta-value"><?php echo htmlspecialchars($service_window, ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">Short title</span>
                <div class="meta-value"><?php echo htmlspecialchars((string) ($request->request_title ?: '-'), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">Visit reference</span>
                <div class="meta-value"><?php echo !empty($request->visit_id) ? '#' . (int) $request->visit_id : 'Not applicable'; ?></div>
            </td>
            <td colspan="2">
                <span class="meta-label">Attachment</span>
                <div class="meta-value">
                    <?php if ($attachment_url !== ''): ?>
                        <?php echo htmlspecialchars((string) $request->attachment, ENT_QUOTES, 'UTF-8'); ?><br>
                        <span class="muted"><?php echo htmlspecialchars($attachment_url, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php else: ?>
                        No attachment uploaded.
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Purpose or requirement</div>
    <div class="box">
        <?php echo trim((string) ($request->purpose ?? '')) !== '' ? nl2br(htmlspecialchars((string) $request->purpose, ENT_QUOTES, 'UTF-8')) : 'No purpose details added.'; ?>
    </div>

    <?php if (!empty($request->parent_request_code) || !empty($request->extension_reason)): ?>
        <div class="section-title">Linked request information</div>
        <table class="meta-table">
            <tr>
                <td style="width: 35%;">
                    <span class="meta-label">Parent request</span>
                    <div class="meta-value">
                        <?php echo !empty($request->parent_request_code) ? htmlspecialchars((string) $request->parent_request_code, ENT_QUOTES, 'UTF-8') : 'Not Linked'; ?>
                        <?php if (!empty($request->parent_request_amount)): ?>
                            <br><span class="muted">Parent amount: Rs. <?php echo number_format((float) $request->parent_request_amount, 2); ?></span>
                        <?php endif; ?>
                    </div>
                </td>
                <td style="width: 65%;">
                    <span class="meta-label">Link reason</span>
                    <div class="meta-value">
                        <?php echo trim((string) ($request->extension_reason ?? '')) !== '' ? nl2br(htmlspecialchars((string) $request->extension_reason, ENT_QUOTES, 'UTF-8')) : 'No additional note added.'; ?>
                    </div>
                </td>
            </tr>
        </table>
    <?php endif; ?>

    <div class="section-title">Approval details</div>
    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">Approval owner</span>
                <div class="meta-value"><?php echo htmlspecialchars($format_person_name($request->hod_name ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">Action taken on</span>
                <div class="meta-value"><?php echo !empty($request->hod_action_on) ? date('d M Y h:i A', strtotime($request->hod_action_on)) : 'Pending'; ?></div>
            </td>
            <td colspan="2">
                <span class="meta-label">Approval remarks</span>
                <div class="meta-value">
                    <?php echo trim((string) ($request->hod_remarks ?? '')) !== '' ? nl2br(htmlspecialchars((string) $request->hod_remarks, ENT_QUOTES, 'UTF-8')) : 'No approval remarks recorded.'; ?>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Activity log</div>
    <table class="logs-table">
        <thead>
            <tr>
                <th style="width: 18%;">Date and time</th>
                <th style="width: 17%;">Action</th>
                <th style="width: 17%;">Status</th>
                <th style="width: 16%;">Actor</th>
                <th style="width: 32%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="5">No activity log is available for this request.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo !empty($log->created_at) ? date('d M Y h:i A', strtotime($log->created_at)) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($format_action_label($log->action_type ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) ($log->status_label ?: '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($format_person_name($log->actor_name ?? '', 'System'), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo trim((string) ($log->remarks ?? '')) !== '' ? nl2br(htmlspecialchars((string) $log->remarks, ENT_QUOTES, 'UTF-8')) : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer-note">
        Generated on <?php echo date('d M Y h:i A'); ?>
    </div>
</body>
</html>
