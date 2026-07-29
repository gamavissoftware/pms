<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_exporter
 *
 * CSV, XLSX and PDF from a saved BOM.
 *
 * ISOLATION
 * ---------
 * Every PHPExcel and TCPDF call in the module lives in this one file, so
 * swapping to PhpSpreadsheet later is a one-file change. It goes through
 * the project's existing wrappers — application/libraries/Excel.php and
 * application/libraries/Pdf.php — and adds no dependency.
 *
 * UNICODE
 * -------
 * TCPDF core fonts are not Unicode. The seed carries Ω (U+03A9), the
 * design depends on ✎ (U+270E), and — (U+2014) is used for an empty
 * field. Rendered with helvetica all three come out as '?'; with
 * dejavusans all render correctly. Verified by
 * BOMMODULEDEVELOPMENT/tests/pdf_glyph_probe.php. The font comes from
 * $config['abom_pdf_font'] and must stay a Unicode face.
 *
 * The UOM display map (NOS -> NO(S)) applies to all three of these —
 * they are read by humans. A future machine-readable or ERP-bound export
 * must use the raw stored value and must not call abom_uom().
 *
 * PHP 7.4 compatible.
 */
class Abom_exporter
{
    protected $CI;

    /** Nine columns, matching the approved design document. */
    private $columns = array(
        'S.NO.', 'ERP CODE', 'DESCRIPTION', 'MODEL NO. / PART NO.',
        'MANUFACTURER', 'QTY.', 'UOM', 'REMARKS', 'STATUS',
    );

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('abom_helper');
    }

    // -----------------------------------------------------------------
    // SHARED
    // -----------------------------------------------------------------

    /**
     * One export row per BOM line, in the design's nine columns.
     *
     * @param  array $lines
     * @return array
     */
    private function rows(array $lines)
    {
        $out = array();

        foreach ($lines as $line) {
            $remarks = array();
            if (!empty($line->usage_remark))   { $remarks[] = $line->usage_remark; }
            if (!empty($line->panel_location)) { $remarks[] = $line->panel_location; }
            if (!empty($line->is_optional))    { $remarks[] = 'OPTIONAL'; }

            $badges = isset($line->status_badges) && is_array($line->status_badges)
                ? $line->status_badges : array();
            if (!empty($line->is_overridden) && !in_array('QTY EDITED', $badges, true)) {
                $badges[] = 'QTY EDITED';
            }

            $erp = ($line->erp_code === null || $line->erp_code === '')
                ? 'PENDING' : $line->erp_code;

            $out[] = array(
                'section' => $line->section_name,
                'cells'   => array(
                    (int) $line->line_no,
                    $erp,
                    $line->description,
                    $line->part_no !== '' ? $line->part_no : '-',
                    $line->manufacturer !== '' ? $line->manufacturer : '-',
                    (int) $line->qty,
                    abom_uom($line->uom),          // human-readable: NO(S)
                    implode(' | ', $remarks),
                    implode(', ', $badges),
                ),
                'class'   => isset($line->row_class) ? $line->row_class : '',
            );
        }

        return $out;
    }

    /**
     * Document title strip, identical wording to the on-screen header.
     *
     * @param  object $bom
     * @return string
     */
    private function title($bom)
    {
        $bits = array();
        $bits[] = $bom->bom_no . ($bom->revision !== '' ? ' (REV.' . $bom->revision . ')' : '');
        if (!empty($bom->df_ref)) { $bits[] = 'REF ' . $bom->df_ref; }
        $bits[] = $bom->machine_model;
        $bits[] = strtoupper($bom->motion_type);
        $bits[] = $bom->axes . ' AXIS';
        $bits[] = $bom->tracks . ' TRACKS';
        $bits[] = 'SPEED ' . $bom->speed_ppm;
        if (!empty($bom->machine_side) && $bom->machine_side !== 'N/A') { $bits[] = $bom->machine_side; }
        $bits[] = 'MITSUBISHI';

        return implode(', ', $bits);
    }

    /**
     * Every export embeds number, revision, status and generation time.
     * An exported file circulating without its revision is how the wrong
     * BOM gets built (spec section 8).
     *
     * @param  object $bom
     * @return string
     */
    private function provenance($bom)
    {
        return sprintf(
            '%s  Rev %s  |  Status: %s  |  Generated %s',
            $bom->bom_no,
            $bom->revision,
            abom_status_label($bom->status),
            date('d-m-Y H:i')
        );
    }

    /**
     * Sign-off roles, honouring $config['abom_signoff_mode'].
     *
     * @return array
     */
    private function signoff_roles()
    {
        $mode = $this->CI->config->item('abom_signoff_mode', 'abom');

        if ($mode === 'internal') {
            return array('Prepared By — Engineering', 'Checked By',
                         'Approved By — Engineering', 'Approved By — Procurement');
        }

        return array('Prepared By — Engineering', 'Checked By', 'Approved By — Customer');
    }

    private function filename($bom, $ext)
    {
        $rev = $bom->revision !== '' ? '_REV' . $bom->revision : '';

        return preg_replace('/[^A-Za-z0-9_.-]/', '', $bom->bom_no . $rev . '_Automation_BOM') . '.' . $ext;
    }

    // -----------------------------------------------------------------
    // CSV
    // -----------------------------------------------------------------

    /**
     * UTF-8 with a BOM so Excel opens Ω and ✎ correctly. Section name
     * repeated in a column rather than as a spanning header row.
     *
     * @param  object $bom
     * @param  array  $lines
     * @return array  ['filename'=>string, 'body'=>string]
     */
    public function csv($bom, array $lines)
    {
        $q = function ($v) { return '"' . str_replace('"', '""', (string) $v) . '"'; };

        $out = array();
        $out[] = $q($this->title($bom));
        $out[] = $q($this->provenance($bom));
        $out[] = '';

        $header = $this->columns;
        $header[] = 'SECTION';
        $out[] = implode(',', array_map($q, $header));

        foreach ($this->rows($lines) as $row) {
            $cells = $row['cells'];
            $line  = array();
            foreach ($cells as $i => $cell) {
                // Numeric columns unquoted, everything else quoted.
                $line[] = ($i === 0 || $i === 5) ? (int) $cell : $q($cell);
            }
            $line[] = $q($row['section']);
            $out[] = implode(',', $line);
        }

        $out[] = '';
        foreach ($this->signoff_roles() as $role) {
            $out[] = $q($role) . ',' . $q('Name / Sign: ______________') . ',' . $q('Date: __________');
        }

        return array(
            'filename' => $this->filename($bom, 'csv'),
            'body'     => "\xEF\xBB\xBF" . implode("\r\n", $out) . "\r\n",
        );
    }

    // -----------------------------------------------------------------
    // XLSX
    // -----------------------------------------------------------------

    /**
     * Reproduces the on-screen formatting: section header rows in the
     * family colour, item rows in their precedence colour, header row
     * #1F3864 with white bold text, freeze panes, autofilter, sensible
     * widths, document header above and sign-off below.
     *
     * @param  object $bom
     * @param  array  $lines
     * @return array  ['filename'=>string, 'path'=>string]
     */
    public function xlsx($bom, array $lines)
    {
        // PHPExcel's Excel2007 writer needs ext-zip. It is present on
        // most cPanel PHP builds but not all, and without this check the
        // failure is a 500 from deep inside the writer rather than
        // something an operator can act on.
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException(
                'Excel export needs the PHP zip extension (ZipArchive), which is not enabled '
                . 'on this server. Enable ext-zip in cPanel > Select PHP Version > Extensions, '
                . 'or use the CSV or PDF export instead.'
            );
        }

        $this->CI->load->library('Excel');

        $xl = $this->CI->excel;
        $xl->setActiveSheetIndex(0);
        $sheet = $xl->getActiveSheet();
        $sheet->setTitle('Automation BOM');

        $last = 'I';
        $r = 1;

        // Document header
        $sheet->setCellValue('A' . $r, $this->title($bom));
        $sheet->mergeCells('A' . $r . ':' . $last . $r);
        $sheet->getStyle('A' . $r)->getFont()->setBold(true)->setSize(12);
        $r++;

        $sheet->setCellValue('A' . $r, $this->provenance($bom));
        $sheet->mergeCells('A' . $r . ':' . $last . $r);
        $r++; $r++;

        // Column header
        $header_row = $r;
        foreach ($this->columns as $i => $label) {
            $sheet->setCellValueByColumnAndRow($i, $r, $label);
        }
        $sheet->getStyle('A' . $r . ':' . $last . $r)->applyFromArray(array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array('rgb' => '1F3864')),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        ));
        $r++;

        $fills = array(
            'is-conflict' => 'F7D9D9',
            'is-noerp'    => 'FFE0B2',
            'is-optional' => 'E2EFDA',
            'is-manual'   => 'DEEAF1',
        );

        $section = null;
        foreach ($this->rows($lines) as $row) {
            if ($row['section'] !== $section) {
                $section = $row['section'];
                $sheet->setCellValue('A' . $r, strtoupper($section));
                $sheet->mergeCells('A' . $r . ':' . $last . $r);
                $sheet->getStyle('A' . $r)->applyFromArray(array(
                    'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
                    'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'startcolor' => array('rgb' => '2E75B6')),
                ));
                $r++;
            }

            foreach ($row['cells'] as $i => $cell) {
                // Any value starting with '=' would be written as a
                // formula and evaluate to #VALUE!. Several remark strings
                // in this dataset start with '=' (spec section 8).
                if (is_string($cell) && substr($cell, 0, 1) === '=') {
                    $sheet->setCellValueExplicitByColumnAndRow(
                        $i, $r, $cell, PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValueByColumnAndRow($i, $r, $cell);
                }
            }

            if (!empty($row['class']) && isset($fills[$row['class']])) {
                $sheet->getStyle('A' . $r . ':' . $last . $r)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($fills[$row['class']]);
            }
            $r++;
        }

        // Sign-off block
        $r++;
        foreach ($this->signoff_roles() as $role) {
            $sheet->setCellValue('A' . $r, $role);
            $sheet->setCellValue('C' . $r, 'Name / Sign: ______________________');
            $sheet->setCellValue('F' . $r, 'Date: ______________');
            $sheet->getStyle('A' . $r)->getFont()->setBold(true);
            $r += 2;
        }

        $widths = array('A' => 7, 'B' => 12, 'C' => 46, 'D' => 24,
                        'E' => 14, 'F' => 7, 'G' => 8, 'H' => 40, 'I' => 18);
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->freezePane('A' . ($header_row + 1));
        $sheet->setAutoFilter('A' . $header_row . ':' . $last . ($r - 1));

        $path   = sys_get_temp_dir() . '/' . $this->filename($bom, 'xlsx');
        $writer = PHPExcel_IOFactory::createWriter($xl, 'Excel2007');
        $writer->save($path);

        return array('filename' => $this->filename($bom, 'xlsx'), 'path' => $path);
    }

    // -----------------------------------------------------------------
    // PDF
    // -----------------------------------------------------------------

    /**
     * Landscape A4, repeating header, sign-off on the final page, footer
     * carrying BOM number, revision, page x of y and timestamp.
     *
     * @param  object $bom
     * @param  array  $lines
     * @return array  ['filename'=>string, 'body'=>string]
     */
    public function pdf($bom, array $lines)
    {
        $this->CI->load->library('Pdf');

        $font  = $this->CI->config->item('abom_pdf_font', 'abom');
        $size  = (int) $this->CI->config->item('abom_pdf_font_size', 'abom');
        $orient = $this->CI->config->item('abom_pdf_orientation', 'abom');
        $format = $this->CI->config->item('abom_pdf_format', 'abom');

        if (empty($font)) { $font = 'dejavusans'; }
        if ($size <= 0)   { $size = 7; }

        $pdf = new Pdf();
        $pdf->SetCreator('Shubham Packaging PMS');
        $pdf->SetTitle($bom->bom_no . ' Automation BOM');
        $pdf->SetMargins(6, 14, 6);
        $pdf->SetAutoPageBreak(true, 14);

        $pdf->setPrintHeader(true);
        $pdf->SetHeaderData('', 0, $this->title($bom), $this->provenance($bom));
        $pdf->setHeaderFont(array($font, '', $size));
        $pdf->setFooterFont(array($font, '', $size - 1));
        $pdf->setHeaderMargin(4);
        $pdf->setFooterMargin(8);

        $pdf->AddPage($orient, $format);
        $pdf->SetFont($font, '', $size);

        // Widths total 285mm, the printable width of landscape A4 at 6mm margins.
        $w = array(10, 20, 74, 38, 26, 12, 14, 60, 31);

        $html  = '<style>';
        $html .= 'table{border-collapse:collapse;}';
        $html .= 'th{background-color:#1F3864;color:#FFFFFF;font-weight:bold;}';
        $html .= 'td,th{border:0.4px solid #D0D7E3;padding:2px;}';
        $html .= '.sec{background-color:#2E75B6;color:#FFFFFF;font-weight:bold;}';
        $html .= '.is-conflict{background-color:#F7D9D9;}';
        $html .= '.is-noerp{background-color:#FFE0B2;}';
        $html .= '.is-optional{background-color:#E2EFDA;}';
        $html .= '.is-manual{background-color:#DEEAF1;}';
        $html .= '</style>';

        $html .= '<table cellpadding="2"><thead><tr>';
        foreach ($this->columns as $i => $label) {
            $html .= '<th width="' . $w[$i] . 'mm">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        $section = null;
        foreach ($this->rows($lines) as $row) {
            if ($row['section'] !== $section) {
                $section = $row['section'];
                $html .= '<tr><td class="sec" colspan="9" width="285mm">'
                       . htmlspecialchars(strtoupper($section), ENT_QUOTES, 'UTF-8') . '</td></tr>';
            }

            $cls = !empty($row['class']) ? ' class="' . $row['class'] . '"' : '';
            $html .= '<tr>';
            foreach ($row['cells'] as $i => $cell) {
                $html .= '<td' . $cls . ' width="' . $w[$i] . 'mm">'
                       . htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Sign-off block, kept whole on the final page.
        $roles = $this->signoff_roles();
        $bw    = (int) floor(285 / count($roles));

        $sign  = '<br><br><table cellpadding="4"><tr>';
        foreach ($roles as $role) {
            $sign .= '<td width="' . $bw . 'mm" style="border:0.4px solid #D0D7E3;">'
                   . '<b>' . htmlspecialchars($role, ENT_QUOTES, 'UTF-8') . '</b>'
                   . '<br><br><br>_______________________<br>'
                   . '<span style="color:#888888;">Name / Sign &nbsp;&nbsp;&nbsp; Date</span></td>';
        }
        $sign .= '</tr></table>';

        $pdf->writeHTML($sign, true, false, true, false, '');

        return array(
            'filename' => $this->filename($bom, 'pdf'),
            'body'     => $pdf->Output($this->filename($bom, 'pdf'), 'S'),
        );
    }
}
