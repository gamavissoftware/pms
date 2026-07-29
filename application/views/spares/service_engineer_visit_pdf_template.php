<?php
defined('BASEPATH') or exit('No direct script access allowed');

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

$format_label = static function ($value, $fallback = '-') {
    $value = trim((string) $value);
    if ($value === '') {
        return $fallback;
    }

    $normalized = str_replace(['_', '-'], ' ', $value);
    if ($normalized === strtoupper($normalized)) {
        return ucwords(strtolower($normalized));
    }

    return $normalized;
};

$format_person_name = static function ($value, $fallback = '-') {
    $value = trim((string) $value);
    if ($value === '') {
        return $fallback;
    }

    if (preg_match('/^[A-Z][A-Z\\s]+$/', $value)) {
        return ucwords(strtolower($value));
    }

    return $value;
};

$visit_window = (!empty($visit->start_date) ? date('d M Y', strtotime($visit->start_date)) : '-')
    . ' to '
    . (!empty($visit->end_date) ? date('d M Y', strtotime($visit->end_date)) : '-');

$customer_contact_parts = array_filter(
    [
        trim((string) ($visit->customer_contact_name ?? '')),
        trim((string) ($visit->customer_contact_no ?? '')),
        trim((string) ($visit->customer_email ?? '')),
    ],
    static function ($value) {
        return $value !== '';
    }
);

$customer_contact_line = !empty($customer_contact_parts) ? implode(' | ', $customer_contact_parts) : 'No customer contact details available.';
$planner_remarks = trim((string) ($visit->remarks ?? ''));
$completion_notes = trim((string) ($visit->completion_notes ?? ''));
$update_count = is_array($updates) ? count($updates) : 0;
$document_count = is_array($documents) ? count($documents) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Engineer Visit MOM Report</title>
    <style>
        <?php echo $font_face_css; ?>

        @page { margin: 18px 20px 20px 20px; }

        body {
            font-family: Calibri, ArialPdf, Arial, "DejaVu Sans", sans-serif;
            font-size: 10.8px;
            line-height: 1.4;
            color: #243447;
            margin: 0;
            padding-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo {
            width: 86px;
            max-height: 86px;
        }

        .company-name {
            font-size: 19px;
            font-weight: bold;
            color: #173f6d;
            margin-bottom: 5px;
        }

        .company-subline {
            color: #54657d;
            line-height: 1.45;
            font-size: 9.9px;
        }

        .report-title {
            margin-top: 14px;
            margin-bottom: 12px;
            padding: 10px 13px;
            background: #f4f8fc;
            border: 1px solid #d8e3ef;
            border-left: 4px solid #1d4f82;
        }

        .report-title h1 {
            margin: 0 0 5px;
            font-size: 16px;
            color: #1b2940;
        }

        .report-subline {
            font-size: 10.1px;
            color: #5f6f84;
            line-height: 1.42;
        }

        .section-title {
            margin-top: 12px;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: bold;
            color: #1b2940;
        }

        .meta-table,
        .updates-table,
        .documents-table {
            border: 1px solid #d7e1ec;
        }

        .meta-table td,
        .updates-table th,
        .updates-table td,
        .documents-table th,
        .documents-table td {
            border-right: 1px solid #d7e1ec;
            border-bottom: 1px solid #d7e1ec;
            padding: 8px 10px;
            vertical-align: top;
        }

        .meta-table td:last-child,
        .updates-table th:last-child,
        .updates-table td:last-child,
        .documents-table th:last-child,
        .documents-table td:last-child {
            border-right: none;
        }

        .meta-table tr:last-child td,
        .updates-table tr:last-child td,
        .documents-table tr:last-child td {
            border-bottom: none;
        }

        .meta-label {
            display: block;
            margin-bottom: 4px;
            color: #6d7d93;
            font-size: 9.4px;
            font-weight: bold;
        }

        .meta-value {
            color: #1f2937;
            font-size: 10.9px;
            line-height: 1.48;
        }

        .status-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #d7e1ec;
            background: #f8fafc;
            color: #1f2937;
            font-size: 9px;
            font-weight: bold;
        }

        .box {
            border: 1px solid #d7e1ec;
            background: #fbfcfe;
            padding: 9px 11px;
            line-height: 1.54;
        }

        .box.tight {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .muted {
            color: #6d7d93;
        }

        .updates-table th,
        .documents-table th {
            background: #f4f7fb;
            color: #5e6d80;
            font-size: 9.4px;
            font-weight: bold;
        }

        .updates-table td,
        .documents-table td {
            line-height: 1.48;
        }

        .stacked-value {
            white-space: pre-line;
        }

        .footer-note {
            position: fixed;
            right: 0;
            bottom: -2px;
            font-size: 9px;
            color: #7c8798;
        }

        .section-block,
        .meta-table,
        .updates-table,
        .documents-table {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 94px;">
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
        <h1>Engineer Visit MOM Report</h1>
        <div class="report-subline">
            Opportunity no.: <strong><?php echo htmlspecialchars((string) ($visit->op_no ?? '-'), ENT_QUOTES, 'UTF-8'); ?></strong>
            &nbsp;|&nbsp;
            Visit id: <strong>#<?php echo (int) $visit->visit_id; ?></strong>
            &nbsp;|&nbsp;
            Visit window: <strong><?php echo htmlspecialchars($visit_window, ENT_QUOTES, 'UTF-8'); ?></strong>
        </div>
    </div>

    <div class="section-title">Visit snapshot</div>
    <table class="meta-table">
        <tr>
            <td>
                <span class="meta-label">Customer</span>
                <div class="meta-value stacked-value"><?php echo htmlspecialchars((string) ($visit->customer_name ?? '-'), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">Engineer</span>
                <div class="meta-value"><?php echo htmlspecialchars($format_person_name($visit->engineer_full_name ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">Visit type</span>
                <div class="meta-value"><?php echo htmlspecialchars($format_label($visit->visit_type ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
            </td>
            <td>
                <span class="meta-label">Visit status</span>
                <div class="meta-value"><span class="status-pill"><?php echo htmlspecialchars($format_label($visit->visit_status ?? '', 'Open'), ENT_QUOTES, 'UTF-8'); ?></span></div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">Visit dates</span>
                <div class="meta-value">
                    <?php echo htmlspecialchars($visit_window, ENT_QUOTES, 'UTF-8'); ?><br>
                    <span class="muted"><?php echo htmlspecialchars((string) ($visit->duration_label ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </td>
            <td>
                <span class="meta-label">Completed on</span>
                <div class="meta-value"><?php echo !empty($visit->completed_on) ? date('d M Y h:i A', strtotime($visit->completed_on)) : 'Open'; ?></div>
            </td>
            <td>
                <span class="meta-label">Daily MOM entries</span>
                <div class="meta-value"><?php echo (int) $update_count; ?></div>
            </td>
            <td>
                <span class="meta-label">Signed documents</span>
                <div class="meta-value"><?php echo (int) $document_count; ?></div>
            </td>
        </tr>
    </table>

    <div class="section-block">
        <div class="section-title">Customer contact</div>
        <div class="box tight"><?php echo htmlspecialchars($customer_contact_line, ENT_QUOTES, 'UTF-8'); ?></div>
    </div>

    <div class="section-block">
        <div class="section-title">Planner remarks</div>
        <div class="box">
            <?php echo $planner_remarks !== '' ? nl2br(htmlspecialchars($planner_remarks, ENT_QUOTES, 'UTF-8')) : 'No planning remarks available.'; ?>
        </div>
    </div>

    <?php if ($completion_notes !== ''): ?>
        <div class="section-block">
            <div class="section-title">Completion notes</div>
            <div class="box"><?php echo nl2br(htmlspecialchars($completion_notes, ENT_QUOTES, 'UTF-8')); ?></div>
        </div>
    <?php endif; ?>

    <div class="section-title">Daily MOM updates</div>
    <table class="updates-table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 44%;">Work done / MOM</th>
                <th style="width: 25%;">Next plan</th>
                <th style="width: 16%;">Updated by</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($updates)): ?>
                <tr>
                    <td colspan="4">No daily MOM entries have been recorded for this visit.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($updates as $update): ?>
                    <tr>
                        <td><?php echo !empty($update->work_date) ? date('d M Y', strtotime($update->work_date)) : '-'; ?></td>
                        <td><?php echo !empty($update->mom_points) ? nl2br(htmlspecialchars((string) $update->mom_points, ENT_QUOTES, 'UTF-8')) : '-'; ?></td>
                        <td><?php echo !empty($update->next_plan) ? nl2br(htmlspecialchars((string) $update->next_plan, ENT_QUOTES, 'UTF-8')) : '-'; ?></td>
                        <td>
                            <?php
                            $updated_by_name = trim((string) ($update->updated_by_name ?? ''));
                            $added_by_name = trim((string) ($update->added_by_name ?? ''));
                            $editor_name = $updated_by_name !== '' ? $updated_by_name : $added_by_name;
                            echo htmlspecialchars($format_person_name($editor_name, 'System'), ENT_QUOTES, 'UTF-8');
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="section-title">Signed documents</div>
    <table class="documents-table">
        <thead>
            <tr>
                <th style="width: 52%;">File name</th>
                <th style="width: 22%;">Uploaded on</th>
                <th style="width: 26%;">Uploaded by</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($documents)): ?>
                <tr>
                    <td colspan="3">No signed documents uploaded for this visit.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($documents as $document): ?>
                    <tr>
                        <td><?php echo htmlspecialchars((string) ($document->original_name ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo !empty($document->uploaded_at) ? date('d M Y h:i A', strtotime($document->uploaded_at)) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($format_person_name($document->uploaded_by_name ?? '', 'System'), ENT_QUOTES, 'UTF-8'); ?></td>
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
