<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_import_model
 *
 * Reads a released DF spreadsheet and turns it into a BUILD: one
 * abom_variant, one abom_variant_rule, and one abom_item per line.
 *
 * WHY THIS IS THE MOST DANGEROUS WRITE PATH IN THE MODULE
 * A saved BOM is one document and a mistake in it affects one machine.
 * Master data is the catalogue every FUTURE BOM is generated from, so a
 * wrong quantity formula here is silently wrong on every machine of that
 * type from now on — and nobody finds out until something is built
 * short. That is why this model does not write anything on its own: it
 * parses, it analyses, and the controller shows the result for approval
 * before a single row is inserted.
 *
 * THE HARD PART IS NOT PARSING, IT IS THE QUANTITY FORMULAS
 * A spreadsheet cell holds a NUMBER — "7". The module needs to know
 * whether that 7 is a fixed 7, or one per axis on a 7-axis machine, or
 * the 12-track temperature-card rule that happens to come to 7. Get it
 * wrong and the sheet is right for the machine it was imported from and
 * wrong for every other size. So each row's number is tested against
 * what every formula WOULD have produced for the stated reference
 * machine, the candidates are ranked by what the part actually is, and
 * anything ambiguous is put in front of a person rather than guessed.
 *
 * NOTHING IS EVER OVERWRITTEN. An import only ever creates a new build.
 * A clashing build code is a hard error, not a merge.
 *
 * PHP 7.4 compatible.
 */
class Abom_import_model extends CI_Model
{
    /** Accepted spellings for each column we need. Compared normalised. */
    private $aliases = array(
        'serial'       => array('s.no.', 's.no', 'sno', 'sr.no.', 'sr no', 'sl no',
                                'serial', 'sr', '#', 'no'),
        // Plurals matter. A real DF headed "ERP CODES" matched nothing
        // here, so the column was never read and EVERY imported item came
        // out with no ERP code and an ERP PENDING badge -- on a sheet that
        // had codes for most of its lines. Silent, because a missing
        // OPTIONAL column is not an error.
        'erp_code'     => array('erp code', 'erp codes', 'erp', 'erp no', 'erp no.',
                                'erp nos', 'erp code no', 'erp codes no',
                                'erp_code', 'erp code(s)', 'item code', 'item codes',
                                'material code', 'material codes', 'part code'),
        'description'  => array('description', 'item description', 'particulars',
                                'item', 'material description', 'desc'),
        'part_no'      => array('model no. / part no.', 'model no / part no',
                                'model no./part no.', 'model no', 'model no.',
                                'part no', 'part no.', 'part number', 'model',
                                'model/part no', 'catalogue no', 'cat no'),
        'manufacturer' => array('manufacturer', 'manufacturers', 'make', 'brand',
                                'mfr', 'mfg', 'maker'),
        'qty'          => array('qty.', 'qty', 'quantity', 'nos', 'no of', 'count'),
        'uom'          => array('uom', 'unit', 'units', 'u.o.m.', 'u.o.m'),
        'remarks'      => array('remarks', 'remark', 'note', 'notes', 'comment',
                                'comments', 'description of work'),
        'status'       => array('status', 'flag', 'issue'),
    );

    /** Columns without which a row cannot become an item. */
    private $required = array('description', 'part_no', 'qty');

    // -----------------------------------------------------------------
    // 1. PARSE
    // -----------------------------------------------------------------

    /**
     * Spreadsheet -> raw rows, with the header row and any section
     * headings resolved.
     *
     * Tolerant on purpose about WHERE the table starts. A released DF
     * carries a title block, sometimes a logo, sometimes blank rows
     * above the headings, and refusing to read one because the headings
     * are on row 6 instead of row 1 would make the feature useless for
     * the files it exists to read.
     *
     * @param  string $path
     * @return array  ['ok'=>bool,'error'=>string,'rows'=>[],'header_row'=>int,'columns'=>[],'sheet'=>string]
     */
    public function parse($path)
    {
        if (!is_readable($path)) {
            return $this->fail('The uploaded file could not be read from disk.');
        }

        $this->load->library('Excel');

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // The reader is chosen by EXTENSION, not by probing.
        // PHPExcel's createReaderForFile() tries each reader in turn,
        // which means loading all of them — and Reader/Excel5.php uses
        // curly-brace string offsets, removed in PHP 8. On a PHP 8 host
        // that is a fatal error while merely *looking* at a .xlsx.
        // Naming the reader keeps an .xlsx import working regardless,
        // and lets the one genuinely unsupported case say so plainly.
        $readers = array('xlsx' => 'Excel2007', 'xls' => 'Excel5', 'csv' => 'CSV');

        if (!isset($readers[$ext])) {
            return $this->fail('Only .xlsx, .xls and .csv files can be read.');
        }

        if ($ext === 'xls' && PHP_VERSION_ID >= 80000) {
            return $this->fail('The old .xls format cannot be read on this server '
                . '(PHP ' . PHP_MAJOR_VERSION . '). Open the file in Excel and '
                . '"Save As" .xlsx, then import that.');
        }

        try {
            $reader = PHPExcel_IOFactory::createReader($readers[$ext]);
            $reader->setReadDataOnly(true);
            $book = $reader->load($path);
        } catch (Exception $e) {
            return $this->fail('That file could not be opened as a spreadsheet. '
                . 'If it was renamed to .' . $ext . ' from something else, save it '
                . 'properly as .xlsx or .csv and try again. (' . $e->getMessage() . ')');
        }

        $sheet = $book->getSheet(0);
        $grid  = $sheet->toArray(null, true, false, false);

        if (empty($grid)) {
            return $this->fail('The first sheet of that file is empty.');
        }

        $found = $this->find_header($grid);

        if ($found === null) {
            return $this->fail('No heading row was found. The sheet needs a row with '
                . 'column headings including DESCRIPTION, MODEL NO. / PART NO. and QTY. '
                . 'Download the template if you are unsure of the layout.');
        }

        list($header_row, $columns) = $found;

        $missing = array();
        foreach ($this->required as $need) {
            if (!isset($columns[$need])) {
                $missing[] = strtoupper(str_replace('_', ' ', $need));
            }
        }

        if (!empty($missing)) {
            return $this->fail('The heading row is missing: ' . implode(', ', $missing)
                . '. Found: ' . implode(', ', array_map('strtoupper', array_keys($columns))) . '.');
        }

        $rows    = array();
        $section = '';

        for ($i = $header_row + 1; $i < count($grid); $i++) {
            $raw = $grid[$i];
            $cell = function ($key) use ($raw, $columns) {
                if (!isset($columns[$key])) { return ''; }
                $idx = $columns[$key];

                return isset($raw[$idx]) ? trim((string) $raw[$idx]) : '';
            };

            $desc = $cell('description');
            $part = $cell('part_no');
            $qty  = $cell('qty');
            $erp  = $cell('erp_code');

            // A SECTION HEADING, in WHICHEVER COLUMN the draughtsman put
            // it. This is how a released DF groups its lines, and losing
            // the grouping puts every part in one undifferentiated list.
            //
            // Originally this only looked at the DESCRIPTION column. On
            // DF-1822 all three headings sit in the S.No. column instead,
            // so every one of them fell through to the "entirely blank"
            // test below and was skipped in silence -- the whole sheet
            // imported under a single fallback section.
            //
            // A heading row is: nothing purchasable on it (no part, no
            // qty, no ERP) and exactly ONE cell with text in it. That is
            // narrow enough not to swallow a stray note in a margin, and
            // it is checked BEFORE the blank test so a heading outside
            // the mapped columns is still seen.
            if ($part === '' && $qty === '' && $erp === '') {
                $filled = array();
                foreach ($raw as $cell_value) {
                    $cell_value = trim((string) $cell_value);
                    if ($cell_value !== '') { $filled[] = $cell_value; }
                }

                if (count($filled) === 1) {
                    $candidate = $filled[0];

                    // Not a heading: a bare number (a stray serial), or
                    // something too long to be one.
                    if (!is_numeric($candidate) && mb_strlen($candidate) <= 128) {
                        $section = $candidate;
                        continue;
                    }
                }

                // Entirely blank — a spacer, not a fault.
                if ($desc === '') {
                    continue;
                }
            }

            // Text in the description column with nothing purchasable
            // beside it is still a heading, even if other columns on the
            // row carry machine metadata (axis / track / speed).
            if ($desc !== '' && $part === '' && $qty === '') {
                $section = $desc;
                continue;
            }

            $rows[] = array(
                'excel_row'    => $i + 1,          // 1-based, as the operator sees it
                'section_text' => $section,
                'erp_code'     => $erp,
                'description'  => $desc,
                'part_no'      => $part,
                'manufacturer' => $cell('manufacturer'),
                'qty_raw'      => $qty,
                'uom'          => $cell('uom'),
                'remarks'      => $cell('remarks'),
                'status'       => $cell('status'),
            );
        }

        if (empty($rows)) {
            return $this->fail('The heading row was found on row ' . ($header_row + 1)
                . ' but there are no data rows beneath it.');
        }

        return array(
            'ok'         => true,
            'error'      => '',
            'rows'       => $rows,
            'header_row' => $header_row + 1,
            'columns'    => $columns,
            'sheet'      => $sheet->getTitle(),
        );
    }

    /**
     * Finds the heading row and maps our field names onto its columns.
     *
     * Scans the first 30 rows rather than assuming row 1, and accepts
     * the best match rather than the first: a title block can easily
     * contain the word "DESCRIPTION" on its own.
     *
     * @param  array $grid
     * @return array|null  [row index, ['field' => column index]]
     */
    private function find_header(array $grid)
    {
        $best   = null;
        $best_n = 0;
        $limit  = min(30, count($grid));

        for ($i = 0; $i < $limit; $i++) {
            $columns = array();

            foreach ($grid[$i] as $idx => $value) {
                $key = $this->match_column($value);

                // First column wins a tie: a sheet with two DESCRIPTION
                // columns is malformed either way, and taking the first
                // is at least predictable.
                if ($key !== null && !isset($columns[$key])) {
                    $columns[$key] = $idx;
                }
            }

            $hits = 0;
            foreach ($this->required as $need) {
                if (isset($columns[$need])) { $hits++; }
            }

            // All three required columns, and more matched columns than
            // any earlier candidate row.
            if ($hits === count($this->required) && count($columns) > $best_n) {
                $best   = array($i, $columns);
                $best_n = count($columns);
            }
        }

        return $best;
    }

    /**
     * Which of our fields, if any, this heading cell names.
     *
     * @param  mixed $value
     * @return string|null
     */
    private function match_column($value)
    {
        $norm = $this->normalise((string) $value);

        if ($norm === '') {
            return null;
        }

        foreach ($this->aliases as $field => $names) {
            foreach ($names as $name) {
                if ($norm === $this->normalise($name)) {
                    return $field;
                }
            }
        }

        return null;
    }

    /**
     * Lower-case, collapse whitespace, drop trailing punctuation.
     *
     * Heading cells in a real DF carry stray spaces, non-breaking
     * spaces, newlines and full stops. Comparing them literally would
     * fail on a file that looks correct to the person who made it.
     *
     * @param  string $value
     * @return string
     */
    private function normalise($value)
    {
        $value = str_replace(array("\xC2\xA0", "\r", "\n", "\t"), ' ', (string) $value);
        $value = strtolower(trim($value));
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value, " .:;-_");
    }

    // -----------------------------------------------------------------
    // 2. ANALYSE
    // -----------------------------------------------------------------

    /**
     * Raw rows + the stated reference machine -> what would be created,
     * with every problem found.
     *
     * Writes nothing. The controller renders this for approval, and only
     * a human pressing the confirm button turns it into rows.
     *
     * @param  array $rows  from parse()
     * @param  array $meta  the build: code, name, family, model, axes,
     *                      tracks, speed, motion, side, panel, j4, battery
     * @return array
     */
    public function analyse(array $rows, array $meta)
    {
        $this->load->model('Abom_master_model');

        $sections = $this->section_map((int) $meta['plc_family_id']);
        $existing = $this->existing_index();

        $out = array(
            'items'    => array(),
            'errors'   => array(),   // block the import
            'warnings' => array(),   // shown, do not block
            'stats'    => array('rows' => count($rows), 'ok' => 0,
                                'errors' => 0, 'warnings' => 0,
                                'no_erp' => 0, 'ambiguous' => 0),
        );

        $seen_part = array();
        $seen_erp  = array();

        foreach ($rows as $row) {
            $item = $this->analyse_row($row, $meta, $sections, $existing,
                                       $seen_part, $seen_erp);

            if (!empty($item['part_key'])) {
                $seen_part[$item['part_key']][] = $row['excel_row'];
            }
            if ($item['erp_code'] !== null) {
                $seen_erp[$item['erp_code']][] = $row['excel_row'];
            }

            if (!empty($item['errors']))   { $out['stats']['errors']++; }
            elseif (!empty($item['warnings'])) { $out['stats']['warnings']++; $out['stats']['ok']++; }
            else { $out['stats']['ok']++; }

            if ($item['erp_code'] === null)      { $out['stats']['no_erp']++; }
            if (count($item['candidates']) > 1)  { $out['stats']['ambiguous']++; }

            $out['items'][] = $item;
        }

        // --- whole-sheet checks --------------------------------------
        foreach ($seen_part as $key => $where) {
            if (count($where) > 1) {
                $out['warnings'][] = 'The same part appears on rows '
                    . implode(', ', $where) . '. That is legal — a part can be listed '
                    . 'twice for different purposes — but check it is not a copy-paste error.';
            }
        }

        foreach ($seen_erp as $erp => $where) {
            if (count($where) > 1) {
                $out['warnings'][] = 'ERP code ' . $erp . ' appears on rows '
                    . implode(', ', $where) . ' against different parts. '
                    . 'Those lines will be flagged CONFLICT for engineering to resolve.';
            }
        }

        if ($out['stats']['ok'] === 0) {
            $out['errors'][] = 'Not one row could be imported. Nothing will be written.';
        }

        return $out;
    }

    /**
     * One spreadsheet row -> one prospective abom_item.
     *
     * @return array
     */
    private function analyse_row(array $row, array $meta, array $sections,
                                 array $existing, array $seen_part, array $seen_erp)
    {
        $errors   = array();
        $warnings = array();

        $description  = $this->clip($row['description'], 255);
        $part_no      = $this->clip($row['part_no'], 96);
        $manufacturer = $this->clip($row['manufacturer'], 64);
        $uom          = $this->clip($row['uom'], 16);
        $remarks      = $this->clip($row['remarks'], 255);

        if ($description === '') { $errors[] = 'No description.'; }
        if ($part_no === '')     { $errors[] = 'No model / part number.'; }

        if ($manufacturer === '') {
            $manufacturer = 'MITSUBISHI';
            $warnings[] = 'No manufacturer given; recorded as MITSUBISHI.';
        }

        if ($uom === '') { $uom = 'NOS'; }

        // --- ERP code -------------------------------------------------
        $erp = trim((string) $row['erp_code']);
        $erp = ($erp === '' || $erp === '-' || strtolower($erp) === 'na'
                || strtolower($erp) === 'n/a' || strtolower($erp) === 'pending')
            ? null
            : $this->clip($erp, 32);

        if ($erp !== null && !preg_match('/^[A-Za-z0-9\/\-_.]+$/', $erp)) {
            $warnings[] = 'ERP code "' . $erp . '" contains unusual characters. '
                . 'Check it against the ERP system.';
        }

        // --- quantity -------------------------------------------------
        $qty_raw = trim((string) $row['qty_raw']);
        $qty     = null;

        if ($qty_raw === '') {
            $errors[] = 'No quantity.';
        } elseif (!preg_match('/^\d+(\.0+)?$/', $qty_raw)) {
            // A fraction or a range is not a quantity this module can
            // compute with, and rounding one silently is how a BOM ends
            // up short.
            $errors[] = 'Quantity "' . $qty_raw . '" is not a whole number.';
        } else {
            $qty = (int) $qty_raw;

            if ($qty < 1) {
                $errors[] = 'Quantity is ' . $qty . '. A line with no quantity should '
                    . 'be removed from the sheet rather than imported as zero.';
            } elseif ($qty > 999) {
                $warnings[] = 'Quantity ' . $qty . ' is unusually large. Check it.';
            }
        }

        // --- section --------------------------------------------------
        $section_id   = 0;
        $section_text = trim((string) $row['section_text']);

        if ($section_text === '') {
            $section_id = $this->default_section($sections);
            $warnings[] = 'No section heading above this row; filed under "'
                . $this->section_name($sections, $section_id) . '".';
        } else {
            $section_id = $this->match_section($sections, $section_text);

            if ($section_id === 0) {
                $section_id = $this->default_section($sections);
                $warnings[] = 'Section "' . $section_text . '" is not one this module '
                    . 'knows, so the row is filed under "'
                    . $this->section_name($sections, $section_id)
                    . '". Add the section under Build configuration first if it should '
                    . 'have its own group.';
            }
        }

        // --- the quantity formula ------------------------------------
        $inference = ($qty === null)
            ? array('formula' => 'FIXED', 'base' => 1, 'candidates' => array(),
                    'reason' => 'No quantity to work from.', 'confident' => false)
            : $this->infer_formula($qty, $description, $part_no, $meta);

        if (!$inference['confident'] && $qty !== null) {
            $warnings[] = 'The quantity rule for this row was not certain — '
                . $inference['reason'] . ' Check it before confirming.';
        }

        // --- clashes with master data already present -----------------
        $severity = 'none';

        if ($erp === null) {
            $severity = 'no_erp';
        }

        $part_key = strtolower($part_no . '|' . $description);

        if ($erp !== null && isset($existing['erp'][$erp])) {
            $other = $existing['erp'][$erp];

            if (strcasecmp($other['part_no'], $part_no) !== 0) {
                $severity = 'conflict';
                $warnings[] = 'ERP ' . $erp . ' is already used by "' . $other['part_no']
                    . '" on build ' . $other['variant'] . '. The row will be imported '
                    . 'and flagged CONFLICT for engineering to resolve.';
            }
        }

        if (isset($existing['part'][strtolower($part_no)])) {
            $where = $existing['part'][strtolower($part_no)];
            $warnings[] = 'Part ' . $part_no . ' already exists on build ' . $where['variant']
                . '. A separate copy is created for this build — that is how builds stay '
                . 'independent, and editing one will not change the other.';
        }

        // The STATUS column of a released DF sometimes already carries a
        // marker. Honour it rather than overriding the engineer who
        // wrote it.
        $status = strtolower(trim((string) $row['status']));
        if ($severity === 'none' && (strpos($status, 'review') !== false
            || strpos($status, 'check') !== false)) {
            $severity = 'review';
        }

        return array(
            'excel_row'    => (int) $row['excel_row'],
            'erp_code'     => $erp,
            'description'  => $description,
            'part_no'      => $part_no,
            'part_key'     => $part_key,
            'manufacturer' => strtoupper($manufacturer),
            'qty'          => $qty,
            'qty_raw'      => $qty_raw,
            'uom'          => strtoupper($uom),
            'usage_remark' => $remarks !== '' ? $remarks : null,
            'section_id'   => $section_id,
            'section_name' => $this->section_name($sections, $section_id),
            'section_text' => $section_text,
            'formula'      => $inference['formula'],
            'base_qty'     => $inference['base'],
            'candidates'   => $inference['candidates'],
            'reason'       => $inference['reason'],
            'confident'    => $inference['confident'],
            'severity'     => $severity,
            'errors'       => $errors,
            'warnings'     => $warnings,
        );
    }

    // -----------------------------------------------------------------
    // 3. THE QUANTITY FORMULA — the part that matters
    // -----------------------------------------------------------------

    /**
     * Works out what a quantity of N on this row actually MEANS.
     *
     * A cell holds "7". That 7 might be a fixed seven, or one per axis
     * on a seven-axis machine, or the temperature-card rule which comes
     * to seven at twelve tracks. Import it as FIXED and the build is
     * right for the machine it came from and silently wrong for every
     * other size — a nine-track machine would get seven cards instead of
     * six, and nobody would know until the panel was wired.
     *
     * Two signals, and the order is deliberate:
     *
     *   1. WHAT THE PART IS. A temperature card is driven by tracks
     *      whatever number happens to be in the cell. This is the
     *      stronger signal and it decides the answer when it fires.
     *   2. WHAT THE NUMBER MATCHES. Which formulas would have produced
     *      exactly this quantity for the stated reference machine.
     *
     * Where the number matches several formulas and the description says
     * nothing, the row is returned NOT confident with every candidate
     * listed, so the screen can ask rather than the model guess. That is
     * the whole point: a wrong guess here is invisible.
     *
     * @param  int    $qty
     * @param  string $description
     * @param  string $part_no
     * @param  array  $meta
     * @return array
     */
    public function infer_formula($qty, $description, $part_no, array $meta)
    {
        $axes    = (int) $meta['axes'];
        $tracks  = (int) $meta['tracks'];
        $j4      = (int) $meta['j4_units'];
        $battery = (int) $meta['battery_qty'];

        $produces = array(
            'AXES'         => $axes,
            'AXES_MINUS_1' => $axes - 1,
            'TRACKS'       => $tracks,
            'TRACK_TEMP'   => (int) ceil(((($tracks + 1) * 2) + 2) / 4),
            'J4_STO'       => $j4,
            'BATTERY'      => $battery,
        );

        // Which formulas would have given exactly this number?
        $matches = array();
        foreach ($produces as $code => $value) {
            if ($value > 0 && $value === $qty) {
                $matches[] = $code;
            }
        }

        // --- signal 1: what the part IS ------------------------------
        $text  = strtoupper($description . ' ' . $part_no);
        $named = $this->formula_from_description($text);

        if ($named !== null) {
            $would = isset($produces[$named]) ? $produces[$named] : null;

            if ($would !== null && $would !== $qty) {
                // The part is unmistakably track-driven (or axis-driven)
                // but the number does not agree with the machine that was
                // declared. One of the two is wrong and neither can be
                // assumed — this is exactly the case that must reach a
                // person.
                // DEFAULT TO THE SHEET, NOT TO THE GUESS.
                //
                // This branch means: the description looks like a
                // rule-driven part, and the rule DISAGREES with the
                // number printed on the sheet. Returning $named made the
                // preview's per-row dropdown default to a formula that
                // silently rewrites the quantity on confirm.
                //
                // DF-1822 is what that costs. Thirteen OMRON servo motor
                // and cable rows read as "per axis" on the word SERVO;
                // the machine is 23 axes; every one of them was stored as
                // AXES and recomputed from the sheet's 12, 8, 6, 3, 2, 1
                // to 23. A dynamic brake resistor became J4_STO and, with
                // no MR-J4 units on an OMRON build, recomputed to ZERO --
                // the line vanished. Sheet total 132, generated total 359.
                //
                // An imported REFERENCE BOM must reproduce the sheet it
                // came from. If it does not, the reference is corrupt and
                // every BOM generated from it inherits that. So FIXED at
                // the printed quantity is the default, the guess stays
                // first in the candidate list for the dropdown, and the
                // row is still flagged for a decision.
                return array(
                    'formula'    => 'FIXED',
                    'base'       => $qty,
                    'candidates' => array_unique(array_merge(array('FIXED', $named), $matches)),
                    'confident'  => false,
                    'reason'     => 'this looks like a ' . $this->formula_english($named)
                        . ' part, but that rule gives ' . $would . ' for a '
                        . $axes . ' axis / ' . $tracks . ' track machine and the sheet says '
                        . $qty . '. Kept as the sheet quantity (' . $qty . '). Change it to '
                        . $named . ' below if the rule is right and the sheet is the odd one out.',
                );
            }

            return array(
                'formula'    => $named,
                'base'       => $qty,
                'candidates' => array_unique(array_merge(array($named), $matches, array('FIXED'))),
                'confident'  => true,
                'reason'     => 'the description identifies it as a '
                    . $this->formula_english($named) . ' part, and '
                    . $qty . ' is what that rule gives here.',
            );
        }

        // --- signal 2: what the number matches ------------------------
        if (empty($matches)) {
            return array(
                'formula'    => 'FIXED',
                'base'       => $qty,
                'candidates' => array('FIXED'),
                'confident'  => true,
                'reason'     => $qty . ' matches no per-axis or per-track rule for this '
                    . 'machine, so it is a fixed quantity.',
            );
        }

        if (count($matches) === 1) {
            return array(
                'formula'    => $matches[0],
                'base'       => $qty,
                'candidates' => array($matches[0], 'FIXED'),
                'confident'  => true,
                'reason'     => $qty . ' is exactly '
                    . $this->formula_english($matches[0]) . ' for this machine.',
            );
        }

        // Ambiguous. FIXED is offered FIRST as the safe default: it is
        // the only choice that cannot make a differently sized machine
        // wrong, because it never varies.
        return array(
            'formula'    => 'FIXED',
            'base'       => $qty,
            'candidates' => array_merge(array('FIXED'), $matches),
            'confident'  => false,
            'reason'     => $qty . ' could be any of '
                . implode(', ', array_map(array($this, 'formula_english'), $matches))
                . ' on this machine, and the description does not say which. '
                . 'Left as a fixed quantity until you choose.',
        );
    }

    /**
     * The formula a part's own description implies, regardless of the
     * number beside it.
     *
     * Ordered most specific first. A "SERVO BATTERY" must not be caught
     * by the SSCNET rule just because its cable shares a word, and the
     * temperature card must be recognised before anything else because
     * it is the one rule nobody would guess from the number.
     *
     * @param  string $text  upper-cased description + part number
     * @return string|null
     */
    private function formula_from_description($text)
    {
        // Temperature / RTD input cards: driven by thermocouple channels,
        // which come from the track count. FX5-4LC and Q64TD are the
        // part numbers this appears under.
        if (strpos($text, 'TEMPERATURE') !== false || strpos($text, 'RTD') !== false
            || strpos($text, '4LC') !== false || strpos($text, 'THERMOCOUPLE') !== false) {
            return 'TRACK_TEMP';
        }

        if (strpos($text, 'BATTERY') !== false) {
            return 'BATTERY';
        }

        // STO / safety wiring is per MR-J4 amplifier. MR-JE amplifiers
        // have no STO connector, which is why this is its own rule and
        // not simply "per amplifier".
        if (strpos($text, 'STO') !== false) {
            return 'J4_STO';
        }

        // SSCNET links daisy-chain BETWEEN amplifiers, so there is one
        // fewer than there are axes. Checked before the general servo
        // rule below, which would otherwise claim it.
        if (strpos($text, 'SSCNET') !== false) {
            return 'AXES_MINUS_1';
        }

        // One per axis: the amplifier, the motor, and the cables that
        // run to each of them.
        if (strpos($text, 'SERVO MOTOR') !== false
            || strpos($text, 'SERVO AMPLIFIER') !== false
            || strpos($text, 'ENCODER CABLE') !== false
            || strpos($text, 'POWER CABLE') !== false
            || strpos($text, 'BRAKE CABLE') !== false
            || strpos($text, 'POWER CONNECTOR') !== false) {
            return 'AXES';
        }

        return null;
    }

    /**
     * A formula code in words, for a message a person has to act on.
     *
     * @param  string $code
     * @return string
     */
    public function formula_english($code)
    {
        $map = array(
            'FIXED'        => 'a fixed quantity',
            'AXES'         => 'one per axis',
            'AXES_MINUS_1' => 'one per axis less one',
            'TRACKS'       => 'one per track',
            'TRACK_TEMP'   => 'the track temperature-card rule',
            'J4_STO'       => 'one per MR-J4 amplifier',
            'BATTERY'      => 'the servo battery count',
            'MANUAL'       => 'set by hand on every BOM',
        );

        return isset($map[$code]) ? $map[$code] : $code;
    }

    // -----------------------------------------------------------------
    // LOOKUPS
    // -----------------------------------------------------------------

    /**
     * Sections available to one PLC family, id => name.
     *
     * @param  int $family_id
     * @return array
     */
    private function section_map($family_id)
    {
        $out = array();

        foreach ($this->Abom_master_model->get_sections((int) $family_id) as $s) {
            $out[(int) $s->id] = (string) $s->name;
        }

        return $out;
    }

    /**
     * The section whose name a heading row means, or 0.
     *
     * Compared on letters and digits only. A released DF writes
     * "PLC, I/O'S, HMI, RTD, VFD" with varying punctuation and casing
     * between documents, and none of that changes which group it is.
     *
     * @param  array  $sections
     * @param  string $text
     * @return int
     */
    private function match_section(array $sections, $text)
    {
        $want = $this->section_key($text);

        if ($want === '') {
            return 0;
        }

        foreach ($sections as $id => $name) {
            if ($this->section_key($name) === $want) {
                return (int) $id;
            }
        }

        // A partial match, so "SERVO AMPLIFIERS AND MOTORS (9 ITEMS)"
        // still lands on "Servo Amplifiers & Motors".
        foreach ($sections as $id => $name) {
            $key = $this->section_key($name);
            if ($key !== '' && (strpos($want, $key) === 0 || strpos($key, $want) === 0)) {
                return (int) $id;
            }
        }

        return 0;
    }

    /**
     * @param  string $text
     * @return string
     */
    private function section_key($text)
    {
        $text = strtolower((string) $text);
        $text = str_replace(array('&', ' and '), ' ', $text);

        return preg_replace('/[^a-z0-9]/', '', $text);
    }

    /**
     * @param  array $sections
     * @return int
     */
    private function default_section(array $sections)
    {
        $ids = array_keys($sections);

        return empty($ids) ? 0 : (int) $ids[0];
    }

    /**
     * @param  array $sections
     * @param  int   $id
     * @return string
     */
    private function section_name(array $sections, $id)
    {
        return isset($sections[(int) $id]) ? $sections[(int) $id] : '(unfiled)';
    }

    /**
     * ERP codes and part numbers already in the master catalogue, so a
     * clash can be reported at preview time rather than discovered by
     * whoever generates the next BOM.
     *
     * @return array
     */
    private function existing_index()
    {
        $rows = $this->db->select('i.erp_code, i.part_no, i.description, v.code AS variant', false)
            ->from('abom_item i')
            ->join('abom_variant v', 'v.id = i.variant_id', 'left')
            ->where('i.is_active', 1)
            ->get()
            ->result();

        $out = array('erp' => array(), 'part' => array());

        foreach ($rows as $r) {
            $where = array('part_no' => (string) $r->part_no,
                           'variant' => $r->variant !== null ? (string) $r->variant : 'an older build');

            if ($r->erp_code !== null && $r->erp_code !== '' && !isset($out['erp'][$r->erp_code])) {
                $out['erp'][(string) $r->erp_code] = $where;
            }

            $key = strtolower((string) $r->part_no);
            if ($key !== '' && !isset($out['part'][$key])) {
                $out['part'][$key] = $where;
            }
        }

        return $out;
    }

    /**
     * @param  string $value
     * @param  int    $max
     * @return string
     */
    private function clip($value, $max)
    {
        $value = trim(preg_replace('/\s+/', ' ',
            str_replace(array("Â ", "
", "
", "	"), ' ', (string) $value)));

        return function_exists('mb_substr') ? mb_substr($value, 0, $max) : substr($value, 0, $max);
    }

    // -----------------------------------------------------------------
    // 4. COMMIT
    // -----------------------------------------------------------------

    /**
     * Turns an approved analysis into a build.
     *
     * ONE TRANSACTION. A half-imported BOM — a variant with some of its
     * items — is worse than no import at all: it would generate a sheet
     * that looks complete and is missing parts.
     *
     * CREATES ONLY. No existing variant, rule or item is updated or
     * deleted by this path, ever. A clashing build code is refused
     * rather than merged, because merging two catalogues on a code match
     * is a decision no importer should make on its own.
     *
     * @param  array $items    analysed rows, with any operator overrides applied
     * @param  array $meta     the build
     * @param  int   $user_id
     * @return array  ['ok'=>bool,'error'=>string,'variant_id'=>int,'items'=>int]
     */
    public function commit(array $items, array $meta, $user_id = 0)
    {
        $code = trim((string) $meta['code']);

        if ($code === '') {
            return $this->commit_fail('The build needs a code.');
        }

        // Re-checked HERE and not only at preview: minutes can pass
        // between the two, and another import could have taken the code.
        $clash = $this->db->from('abom_variant')->where('code', $code)->limit(1)->get()->row();

        if ($clash) {
            return $this->commit_fail('Build code ' . $code . ' already exists ('
                . $clash->name . '). Choose a different code — an import never '
                . 'overwrites an existing build.');
        }

        $usable = array();
        foreach ($items as $item) {
            if (empty($item['errors'])) {
                $usable[] = $item;
            }
        }

        if (empty($usable)) {
            return $this->commit_fail('No row is free of errors, so there is nothing to import.');
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_begin();

        $this->db->insert('abom_variant', array(
            'plc_family_id'          => (int) $meta['plc_family_id'],
            'code'                   => $code,
            'name'                   => $this->clip($meta['name'], 96),
            'description'            => $this->clip($meta['description'], 255),
            'machine_model'          => $this->clip($meta['machine_model'], 32),
            'source_df'              => $this->clip($meta['source_df'], 96),
            'default_j4_units'       => (int) $meta['j4_units'],
            'default_battery'        => (int) $meta['battery_qty'],
            'default_panel_location' => $this->clip($meta['panel_location'], 64),
            'ref_axes'               => (int) $meta['axes'],
            'ref_tracks'             => (int) $meta['tracks'],
            'ref_speed_ppm'          => (int) $meta['speed_ppm'],
            'ref_motion_type'        => $meta['motion_type'],
            'ref_machine_side'       => $meta['machine_side'],
            'is_active'              => 1,
            'sort_order'             => (int) $this->next_sort_order(),
        ));

        $variant_id = (int) $this->db->insert_id();

        if ($variant_id <= 0) {
            $this->db->trans_rollback();

            return $this->commit_fail('The build could not be created. Nothing was written.');
        }

        // --- the selection rule --------------------------------------
        // Without one, the build exists and NOTHING EVER REACHES IT.
        // That failure is silent: the register would list the build, the
        // items would be there, and every generated BOM would quietly
        // pick a different build. So the rule is written in the same
        // transaction, never as a follow-up step someone might skip.
        $this->db->insert('abom_variant_rule', array(
            'priority'          => (int) $this->next_rule_priority(),
            'plc_family_id'     => (int) $meta['plc_family_id'],
            'machine_model'     => $this->clip($meta['machine_model'], 32),
            'min_axes'          => $meta['rule_min_axes'] !== '' ? (int) $meta['rule_min_axes'] : null,
            'max_axes'          => $meta['rule_max_axes'] !== '' ? (int) $meta['rule_max_axes'] : null,
            'min_speed'         => $meta['rule_min_speed'] !== '' ? (int) $meta['rule_min_speed'] : null,
            'max_speed'         => $meta['rule_max_speed'] !== '' ? (int) $meta['rule_max_speed'] : null,
            'motion_type'       => $meta['rule_motion'] !== '' ? $meta['rule_motion'] : 'ANY',
            'result_variant_id' => $variant_id,
            'explanation'       => $this->clip($meta['rule_explanation'], 255),
            'is_active'         => 1,
        ));

        // --- the items ------------------------------------------------
        // Inserted one at a time, in sheet order, because abom_item has
        // no sort column: document order IS insertion order, and a batch
        // insert makes no such promise.
        $written = 0;

        foreach ($usable as $item) {
            $this->db->insert('abom_item', array(
                'erp_code'       => $item['erp_code'],
                'description'    => $item['description'],
                'part_no'        => $item['part_no'],
                'manufacturer'   => $item['manufacturer'],
                'base_qty'       => (int) $item['base_qty'],
                'formula_code'   => $item['formula'],
                'plc_family_id'  => (int) $meta['plc_family_id'],
                'variant_id'     => $variant_id,
                'section_id'     => (int) $item['section_id'],
                'is_optional'    => !empty($item['is_optional']) ? 1 : 0,
                'feature_code'   => !empty($item['feature_code']) ? $item['feature_code'] : null,
                'usage_remark'   => $item['usage_remark'],
                'data_issue'     => !empty($item['data_issue']) ? $item['data_issue'] : null,
                'issue_severity' => $item['severity'],
                'panel_location' => $this->clip($meta['panel_location'], 64),
                'uom'            => $item['uom'],
                'source_df'      => $this->clip($meta['source_df'], 32),
                'is_active'      => 1,
                'created_at'     => $now,
                'updated_at'     => $now,
            ));

            $written++;
        }

        $this->load->model('Abom_model');
        $this->Abom_model->log_audit('abom_variant', $variant_id, 'import', null, array(
            'code'       => $code,
            'source_df'  => $meta['source_df'],
            'items'      => $written,
            'machine'    => (int) $meta['axes'] . 'A/' . (int) $meta['tracks'] . 'T/'
                            . (int) $meta['speed_ppm'] . 'PPM',
        ), $user_id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return $this->commit_fail('The import failed part way through and was rolled back. '
                . 'Nothing was written.');
        }

        $this->db->trans_commit();

        return array('ok' => true, 'error' => '', 'variant_id' => $variant_id,
                     'items' => $written, 'skipped' => count($items) - $written);
    }

    /**
     * Next free sort_order, so a new build lands at the end of the
     * register rather than in the middle of the existing ones.
     *
     * @return int
     */
    private function next_sort_order()
    {
        $row = $this->db->select_max('sort_order', 'm')->from('abom_variant')->get()->row();

        return $row ? ((int) $row->m + 1) : 1;
    }

    /**
     * Next free rule priority.
     *
     * Rules are first-match-wins in priority order, so a new rule MUST
     * go last. Inserting it anywhere else would let it capture machines
     * that an existing, more specific build already claims — silently
     * changing what every one of those machines generates.
     *
     * @return int
     */
    private function next_rule_priority()
    {
        $row = $this->db->select_max('priority', 'm')->from('abom_variant_rule')->get()->row();

        return $row ? ((int) $row->m + 1) : 1;
    }

    /**
     * @param  string $message
     * @return array
     */
    private function commit_fail($message)
    {
        return array('ok' => false, 'error' => $message, 'variant_id' => 0,
                     'items' => 0, 'skipped' => 0);
    }

    /**
     * @param  string $message
     * @return array
     */
    private function fail($message)
    {
        return array('ok' => false, 'error' => $message, 'rows' => array(),
                     'header_row' => 0, 'columns' => array(), 'sheet' => '');
    }
}
