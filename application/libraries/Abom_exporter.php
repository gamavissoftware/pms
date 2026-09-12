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
            // REMARKS is the ENGINEER'S column, and the export says
            // what the screen and the print say — the text somebody
            // typed, or nothing.
            //
            // usage_remark and panel_location used to be concatenated in
            // here. They are reference material, not this machine's
            // remark: usage_remark appears on screen as the input's
            // placeholder to help whoever is filling the row in, and
            // panel_location is a header chip. Emitting them as remarks
            // put "Panel With Machine" on every line of a procurement
            // document and told the reader nothing.
            $remarks = array();
            if (isset($line->user_remark) && $line->user_remark !== '' && $line->user_remark !== null) {
                $remarks[] = $line->user_remark;
            }

            $badges = isset($line->status_badges) && is_array($line->status_badges)
                ? $line->status_badges : array();
            if (!empty($line->is_manual_add) && !in_array('MANUAL ROW', $badges, true)) {
                array_unshift($badges, 'MANUAL ROW');
            }
            if (!empty($line->is_optional) && !in_array('OPTIONAL', $badges, true)) {
                $badges[] = 'OPTIONAL';
            }
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
                // A hand-added row that carries no other flag still gets a
                // tint, matching the screen. A more severe class (a
                // missing ERP code, say) keeps precedence — severity
                // outranks provenance, exactly as it does on screen.
                'class'   => (empty($line->row_class) && !empty($line->is_manual_add))
                                ? 'is-manual-row'
                                : (isset($line->row_class) ? $line->row_class : ''),
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
    private function title($bom, array $lines = array())
    {
        $bits = array();
        $bits[] = $bom->bom_no . ($bom->revision !== '' ? ' (REV.' . $bom->revision . ')' : '');
        if (!empty($bom->df_ref)) { $bits[] = 'REF ' . $bom->df_ref; }
        $bits[] = $bom->machine_model;
        $bits[] = strtoupper($bom->motion_type);
        $bits[] = $bom->axes . ' AXIS';
        $bits[] = $bom->tracks . ' TRACKS';
        $bits[] = 'SPEED ' . $bom->speed_ppm;
        $brand = $this->brand($lines);
        if (!empty($bom->machine_side) && $bom->machine_side !== 'N/A') { $bits[] = $bom->machine_side; }
        $bits[] = $brand;
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
    private function provenance($bom, array $lines = array())
    {
        $build = $this->build_code($bom, $lines);

        return sprintf(
            '%s  Rev %s  |  %s  |  Status: %s  |  Generated %s',
            $bom->bom_no,
            $bom->revision,
            $build !== '' ? 'Build ' . $build : 'Build not recorded',
            abom_status_label($bom->status),
            date('d-m-Y H:i')
        );
    }

    /**
     * The build variant this document was cut from.
     *
     * Read off the LINES first, because abom_bom_line.variant_code is
     * frozen at generation time — it is what this sheet was actually
     * built from, and stays true even if the variant is later renamed or
     * retired. abom_bom.variant_id is the fallback.
     *
     * Two BOMs can share model, axes, tracks and speed and still be
     * different machines: the FX5 family covers both the MR-JE and the
     * MR-J4 servo ranges. A printed BOM that does not say which one it
     * is cannot be checked against the machine it was built for.
     *
     * @param  object $bom
     * @param  array  $lines
     * @return string  '' when the BOM predates build variants
     */
    private function build_code($bom, array $lines = array())
    {
        foreach ($lines as $line) {
            if (!empty($line->variant_code)) {
                return (string) $line->variant_code;
            }
        }

        if (!empty($bom->variant_code)) {
            return (string) $bom->variant_code;
        }

        if (!empty($bom->variant_id) && isset($this->CI->Abom_master_model)) {
            return (string) $this->CI->Abom_master_model->variant_code((int) $bom->variant_id);
        }

        return '';
    }

    /**
     * The open issues on this BOM, as the on-screen review banner states
     * them.
     *
     * A released document that hides its own unresolved ERP codes is the
     * failure this whole module exists to prevent: procurement may order
     * anything visible on the sheet, and "PENDING" in a column is easy to
     * read past. Stated once, in full, above the sign-off block.
     *
     * @param  array $lines
     * @return array  list of sentences, empty when the BOM is clean
     */
    private function open_issues(array $lines)
    {
        $notes    = array();
        $pending  = array();
        $conflict = array();

        foreach ($lines as $line) {
            if ($line->erp_code === null || $line->erp_code === '') {
                $pending[] = 'S.No ' . (int) $line->line_no . ' ' . $line->description
                    . ' (' . $line->part_no . ')';
            }
            if ($line->issue_severity === 'conflict') {
                $conflict[$line->erp_code][] = 'S.No ' . (int) $line->line_no;
            }
            if (!empty($line->is_manual_add)) {
                $notes['manual'] = true;
            }
        }

        $out = array();

        if (!empty($pending)) {
            $out[] = count($pending) . ' item' . (count($pending) === 1 ? '' : 's')
                . ' pending an ERP code: ' . implode('; ', $pending) . '.';
        }

        foreach ($conflict as $erp => $where) {
            $out[] = 'ERP ' . $erp . ' is recorded against more than one part ('
                . implode(', ', $where) . ') - verify before ordering.';
        }

        if (!empty($notes['manual'])) {
            $out[] = 'This BOM contains hand-added rows, marked MANUAL ROW. '
                . 'They come from no reference build and nothing validated them.';
        }

        return $out;
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

    /**
     * Row tints, shared by XLSX and PDF so the three renderings of one
     * BOM cannot drift apart.
     *
     * is-manual-row is the HAND-ADDED row, and is distinct from
     * is-manual, which is a master item whose quantity needs a human.
     * The screen tints the first #FFFDF0; without it here, a row nobody
     * validated printed as though it were an ordinary catalogue line.
     *
     * @return array
     */
    private function row_fills()
    {
        return array(
            'is-conflict'   => 'F7D9D9',
            'is-noerp'      => 'FFE0B2',
            'is-optional'   => 'E2EFDA',
            'is-manual'     => 'DEEAF1',
            'is-manual-row' => 'FFF6D5',
        );
    }

    /**
     * The downloaded file's name, in the form engineering asked for:
     *
     *   DF-1826 REV.02 - SPM1200L, 15A, 12T, 180PPM - CONTINUOUS - MITSUBISHI.pdf
     *
     * It reads like the source workbooks do, so a downloaded BOM sorts
     * and searches alongside the DF sheets it came from. `ABOM-5_REV00_
     * Automation_BOM.pdf` told the recipient nothing about the machine —
     * and a folder of them was indistinguishable.
     *
     * The DF reference leads because that is what the recipient looks
     * for. A BOM with no DF reference falls back to its own number
     * rather than starting with a stray separator.
     *
     * @param  object $bom
     * @param  string $ext
     * @param  array  $lines  used only to name the manufacturer
     * @return string
     */
    private function filename($bom, $ext, array $lines = array())
    {
        $lead = trim((string) $bom->df_ref) !== '' ? trim((string) $bom->df_ref) : $bom->bom_no;

        if ((string) $bom->revision !== '' && (string) $bom->revision !== '00') {
            $lead .= ' REV.' . $bom->revision;
        }

        $spec = array();
        $spec[] = $bom->machine_model;
        $spec[] = (int) $bom->axes . 'A';
        $spec[] = (int) $bom->tracks . 'T';
        $spec[] = (int) $bom->speed_ppm . 'PPM';

        $parts = array(
            $lead,
            implode(', ', $spec),
            strtoupper((string) $bom->motion_type),
            $this->brand($lines),
        );

        if (!empty($bom->machine_side) && $bom->machine_side !== 'N/A') {
            // Side belongs with the machine spec, not on the end.
            $parts[1] .= ', ' . $bom->machine_side;
        }

        return $this->safe_filename(implode(' - ', array_filter($parts))) . '.' . $ext;
    }

    /**
     * The dominant manufacturer on the sheet.
     *
     * Derived rather than hard-coded: the exporter used to append
     * "MITSUBISHI" to every title on the assumption that every BOM is a
     * Mitsubishi one. That is true of all eleven reference builds and
     * would stop being true the day it is not, silently, on a document
     * going to a customer.
     *
     * @param  array $lines
     * @return string
     */
    private function brand(array $lines)
    {
        $count = array();

        foreach ($lines as $line) {
            $m = trim((string) $line->manufacturer);

            // RECKON supplies one resistor on several builds; a single
            // bought-in part does not make it the machine's brand.
            if ($m === '' || $m === '-') {
                continue;
            }

            $count[$m] = isset($count[$m]) ? $count[$m] + 1 : 1;
        }

        if (empty($count)) {
            return 'MITSUBISHI';
        }

        arsort($count);

        return strtoupper((string) key($count));
    }

    /**
     * Filesystem- and header-safe, while staying readable.
     *
     * The old rule stripped everything but letters, digits, dot, dash and
     * underscore, which would have reduced the name above to an
     * unreadable run. Spaces, commas and parentheses are safe on every
     * platform this ships to; what is not safe is the reserved set
     * \ / : * ? " < > | and a newline, and those are what this removes.
     *
     * Also strips characters that would break the Content-Disposition
     * header — a quote or a semicolon there truncates the filename or
     * lets a crafted BOM number inject a header directive.
     *
     * @param  string $name
     * @return string
     */
    private function safe_filename($name)
    {
        // ~ delimiter, not /. With a / delimiter the escaped backslash
        // at the head of the class is followed by a literal slash, which
        // PCRE reads as the closing delimiter — the pattern silently
        // becomes "[\\" plus a run of unknown modifiers, preg_replace
        // returns NULL, and every download is named ".pdf".
        $name = preg_replace('~[\\\\/:*?"<>|;\r\n]+~', '', (string) $name);
        $name = preg_replace('~\s+~', ' ', (string) $name);

        // Windows caps a path component at 255; leave room for the
        // extension and for a browser appending " (1)".
        return trim(mb_substr($name, 0, 180), ' .-');
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
        $out[] = $q($this->title($bom, $lines));
        $out[] = $q($this->provenance($bom, $lines));
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
            'filename' => $this->filename($bom, 'csv', $lines),
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
        $sheet->setCellValue('A' . $r, $this->title($bom, $lines));
        $sheet->mergeCells('A' . $r . ':' . $last . $r);
        $sheet->getStyle('A' . $r)->getFont()->setBold(true)->setSize(12);
        $r++;

        $sheet->setCellValue('A' . $r, $this->provenance($bom, $lines));
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

        $fills = $this->row_fills();

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

        $last_data_row = $r - 1;

        // Open issues, before the signatures — see open_issues().
        $issues = $this->open_issues($lines);
        if (!empty($issues)) {
            $r++;
            $sheet->setCellValue('A' . $r, 'POINTS TO VERIFY BEFORE APPROVAL');
            $sheet->mergeCells('A' . $r . ':' . $last . $r);
            $sheet->getStyle('A' . $r)->applyFromArray(array(
                'font' => array('bold' => true, 'color' => array('rgb' => 'BF8F00')),
            ));
            $r++;
            foreach ($issues as $issue) {
                $sheet->setCellValue('A' . $r, $issue);
                $sheet->mergeCells('A' . $r . ':' . $last . $r);
                $r++;
            }
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

        // REMARKS is populated on most rows — it is seeded from the
        // master item's usage note — so it needs the room back. Taken
        // from MANUFACTURER, whose longest value is "MITSUBISHI".
        $widths = array('A' => 7, 'B' => 12, 'C' => 54, 'D' => 26,
                        'E' => 13, 'F' => 7, 'G' => 8, 'H' => 42, 'I' => 26);
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->getStyle('A' . $header_row . ':' . $last . $last_data_row)
              ->getAlignment()->setWrapText(true)->setVertical(
                  PHPExcel_Style_Alignment::VERTICAL_TOP);

        $sheet->freezePane('A' . ($header_row + 1));

        // AUTOFILTER RANGE ENDS AT THE LAST DATA ROW.
        //
        // It used to be built from $r AFTER the sign-off block had been
        // written, so the filter covered the blank rows and the
        // signature lines as though they were BOM items. Filtering the
        // sheet in Excel then hid or reordered the sign-off block.
        $sheet->setAutoFilter('A' . $header_row . ':' . $last . $last_data_row);

        $path   = sys_get_temp_dir() . '/' . $this->filename($bom, 'xlsx', $lines);
        $writer = PHPExcel_IOFactory::createWriter($xl, 'Excel2007');

        // SUPPRESS THE VENDOR WARNING WHILE SAVING.
        //
        // On PHP 7.4 the count() bug at Worksheet.php:771 is an
        // E_WARNING, not a Throwable, so the try/catch below never sees
        // it. CodeIgniter's error handler renders that warning as an
        // HTML block and ECHOES it — straight into the download stream,
        // ahead of the ZIP. Excel then refuses the file, and the user is
        // told "cannot open" with no clue why. That is exactly what
        // happened to ABOM-5 on 2026-08-11: the .xlsx was a CI warning
        // page with a perfectly good spreadsheet stapled to the end.
        //
        // Scoped to errors raised INSIDE the vendored PHPExcel tree.
        // Anything from the module's own code still reaches the normal
        // handler — this hides a known third-party defect, not our bugs.
        set_error_handler(function ($no, $str, $file = '', $line = 0) {
            return (strpos(str_replace('\\', '/', $file), '/third_party/PHPExcel/') !== false);
        });

        try {
            $writer->save($path);
        } catch (Throwable $e) {
            // PHPExcel's Excel2007 autofilter writer contains
            //
            //     if (count($columns > 0)) {          // Worksheet.php:771
            //
            // with the comparison INSIDE count(). On PHP 7.4 that is a
            // warning and returns 1, so the export works. On PHP 8 it is
            // a TypeError and the whole export dies.
            //
            // The bug is in the vendored library, which is shared with
            // the rest of the application — patching it here would be
            // lost on any update and would change behaviour for other
            // modules. So the export drops the filter and re-saves
            // instead. Losing the filter is a far smaller harm than
            // losing the file, and this keeps the module working across
            // a PHP upgrade rather than breaking on the day of it.
            $sheet->setAutoFilter('');

            try {
                $writer->save($path);      // second chance, without the filter
            } catch (Throwable $inner) {
                restore_error_handler();
                throw $e;                  // report the ORIGINAL failure
            }
        }

        restore_error_handler();

        return array('filename' => $this->filename($bom, 'xlsx', $lines), 'path' => $path);
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
        $pdf->SetHeaderData('', 0, $this->title($bom, $lines), $this->provenance($bom, $lines));
        $pdf->setHeaderFont(array($font, '', $size));
        $pdf->setFooterFont(array($font, '', $size - 1));
        $pdf->setHeaderMargin(4);
        $pdf->setFooterMargin(8);

        $pdf->AddPage($orient, $format);
        $pdf->SetFont($font, '', $size);

        // Widths total 285mm, the printable width of landscape A4 at 6mm
        // margins.
        //
        // REMARKS has been 60mm, then 42mm, and is now 56mm. The 42 was
        // set while the column was blank on most rows — the engineer's
        // note was opt-in and few were typed. Since it is seeded from
        // the master item's usage note it is populated on most lines
        // again, and 42mm wrapped two thirds of them onto a second line
        // and cost a whole page.
        //
        // The room came off columns whose content is bounded and short:
        // MANUFACTURER holds "MITSUBISHI" at longest, UOM holds "NO(S)",
        // ERP CODE holds seven digits. None of them was ever near its
        // width; REMARKS is free text and always will be.
        //
        // MANUFACTURER is 24mm rather than the 20 the VALUES need,
        // because the HEADER word is longer than anything under it and
        // wrapped "MANUFACTUR / ER" at 20. The printed column names come
        // from the approved design document and are not abbreviated to
        // save space.
        $w = array(10, 18, 80, 33, 24, 12, 12, 55, 41);

        if (array_sum($w) !== 285) {          // guard: A4 landscape at 6mm margins
            throw new RuntimeException(
                'Abom_exporter: PDF column widths total ' . array_sum($w) . 'mm, expected 285mm.');
        }

        $html  = '<style>';
        $html .= 'table{border-collapse:collapse;}';
        $html .= 'th{background-color:#1F3864;color:#FFFFFF;font-weight:bold;}';
        $html .= 'td,th{border:0.4px solid #D0D7E3;padding:2px;}';
        $html .= '.sec{background-color:#2E75B6;color:#FFFFFF;font-weight:bold;}';
        foreach ($this->row_fills() as $cls => $rgb) {
            $html .= '.' . $cls . '{background-color:#' . $rgb . ';}';
        }
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

        // COLOUR KEY. The tints carry meaning and the recipient of a PDF
        // has no legend panel to refer to, so it travels with the
        // document. Built from the same map the rows are painted from.
        $legend_labels = array(
            'is-conflict'   => 'ERP code on more than one part',
            'is-noerp'      => 'No ERP code yet',
            'is-optional'   => 'Optional / feature-gated item',
            'is-manual'     => 'Quantity needs engineering review',
            'is-manual-row' => 'Hand-added row',
        );

        $legend = '<br><table cellpadding="3"><tr>';
        foreach ($this->row_fills() as $cls => $rgb) {
            $legend .= '<td width="10mm" style="background-color:#' . $rgb
                     . ';border:0.4px solid #D0D7E3;"> </td>'
                     . '<td width="47mm" style="font-size:6pt;">'
                     . htmlspecialchars($legend_labels[$cls], ENT_QUOTES, 'UTF-8') . '</td>';
        }
        $legend .= '</tr></table>';
        $pdf->writeHTML($legend, true, false, true, false, '');

        // OPEN ISSUES, stated in full rather than left as a word in a
        // column. Procurement may order anything visible on this sheet.
        $issues = $this->open_issues($lines);
        if (!empty($issues)) {
            $note = '<br><table cellpadding="4"><tr><td width="285mm" '
                  . 'style="border:0.4px solid #BF8F00;background-color:#FFF9E6;">'
                  . '<b>POINTS TO VERIFY BEFORE APPROVAL</b><br>';
            foreach ($issues as $issue) {
                $note .= '&bull; ' . htmlspecialchars($issue, ENT_QUOTES, 'UTF-8') . '<br>';
            }
            $note .= '</td></tr></table>';
            $pdf->writeHTML($note, true, false, true, false, '');
        }

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
            'filename' => $this->filename($bom, 'pdf', $lines),
            'body'     => $pdf->Output($this->filename($bom, 'pdf', $lines), 'S'),
        );
    }
}
