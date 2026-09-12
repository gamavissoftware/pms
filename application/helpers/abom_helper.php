<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * abom_helper
 *
 * Presentation helpers for the Automation BOM module: row class, status
 * badges, the nine-column cell mapping and escaping.
 *
 * These functions FORMAT. They never decide a quantity and they never
 * decide a row's precedence — Abom_engine owns both (spec section 10).
 * abom_row_class() reads the class the engine already put on the line.
 *
 * PHP 7.4 compatible.
 */

if (!function_exists('abom_e')) {
    /**
     * HTML-escape. utf8mb4 in, utf8 out — the data carries ° Ω ⚠ − and
     * the usage_remark strings must survive verbatim.
     */
    function abom_e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('abom_uom')) {
    /**
     * UOM for HUMAN-READABLE output: screen, print, PDF, XLSX.
     *
     * Maps the canonical stored code to what the released DF prints —
     * 'NOS' becomes 'NO(S)'. Anything not in the map passes through
     * unchanged.
     *
     * Machine-readable and ERP-bound exports MUST use the raw stored
     * value and must not call this.
     *
     * @param  string $uom
     * @return string
     */
    function abom_uom($uom)
    {
        $uom = (string) $uom;
        $map = get_instance()->config->item('abom_uom_display', 'abom');

        if (is_array($map) && isset($map[$uom])) {
            return $map[$uom];
        }

        return $uom;
    }
}

if (!function_exists('abom_qty_editable')) {
    /**
     * May line quantities be edited on this BOM?
     *
     * Driven by workflow state, never by a default. An engineering- or
     * procurement-approved BOM is the document someone is about to print
     * and send: its quantities must not be typeable over.
     *
     *   draft / submitted / checked / rejected -> editable
     *   eng_approved / approved / superseded   -> locked
     *
     * An unsaved BOM (id 0, generator screen) is editable: its
     * quantities are form state that will be posted on save.
     *
     * @param  object $bom
     * @return bool
     */
    function abom_qty_editable($bom)
    {
        $status = isset($bom->status) ? (string) $bom->status : '';

        if ($status === '') {
            return true;                      // unsaved / generator
        }

        $allowed = get_instance()->config->item('abom_qty_editable_status', 'abom');
        if (!is_array($allowed)) {
            $allowed = array('draft', 'submitted', 'checked', 'rejected');
        }

        return in_array($status, $allowed, true);
    }
}

if (!function_exists('abom_who')) {
    /**
     * A person's display name, falling back to the id rather than to
     * nothing.
     *
     * "user #7" is poor, but it is auditable — a blank cell in an
     * approval trail invites the reading that nobody did it.
     *
     * @param  array $user_names  id => name
     * @param  mixed $id
     * @return string
     */
    function abom_who($user_names, $id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return '—';
        }

        return isset($user_names[$id]) ? $user_names[$id] : ('user #' . $id);
    }
}

if (!function_exists('abom_when')) {
    /**
     * A timestamp as people here write dates. Empty renders as an em
     * dash, never as 01-01-1970.
     *
     * @param  string $ts
     * @return string
     */
    function abom_when($ts)
    {
        return !empty($ts) ? date('d-m-Y H:i', strtotime($ts)) : '—';
    }
}

if (!function_exists('abom_line_label')) {
    /**
     * One BOM line described the way it reads on the sheet, for use in
     * a changelog where there is no table to give the columns meaning.
     *
     * ERP code first: it is what procurement orders by.
     *
     * @param  array|object $line
     * @return string
     */
    function abom_line_label($line)
    {
        $line = (array) $line;
        $bits = array();

        if (!empty($line['erp_code']))    { $bits[] = $line['erp_code']; }
        if (!empty($line['description'])) { $bits[] = $line['description']; }
        if (!empty($line['part_no']))     { $bits[] = '(' . $line['part_no'] . ')'; }

        return $bits ? implode(' · ', $bits) : 'unnamed line';
    }
}

if (!function_exists('abom_audit_action_label')) {
    /**
     * An audit action in words.
     *
     * The stored values are terse verbs meant for a WHERE clause.
     * A version report is read by people deciding whether to approve a
     * document, and "edit_lines" is not a sentence.
     *
     * @param  string $action
     * @return string
     */
    function abom_audit_action_label($action)
    {
        $map = array(
            'create'      => 'BOM created',
            'edit_lines'  => 'Rows edited',
            'reconfigure' => 'Machine reconfigured',
            'duplicate'   => 'Copied from another BOM',
            'delete'      => 'Deleted',
            'qty'         => 'Quantity overridden',
            'update'      => 'Updated',
            'confirm'     => 'Line confirmed',
            'acknowledge' => 'Conflict acknowledged',
        );

        $action = (string) $action;

        return isset($map[$action]) ? $map[$action] : ucfirst(str_replace('_', ' ', $action));
    }
}

if (!function_exists('abom_audit_detail')) {
    /**
     * The one-line summary of an audit row.
     *
     * abom_audit_log stores old_values and new_values as JSON. Printing
     * raw JSON into a report aimed at engineers and buyers is not
     * showing them anything — this states the fields that actually
     * moved, and stops at three so one noisy row cannot push the rest
     * of the history off the screen.
     *
     * @param  object $row
     * @return string
     */
    function abom_audit_detail($row)
    {
        $old = json_decode(isset($row->old_values) ? (string) $row->old_values : '', true);
        $new = json_decode(isset($row->new_values) ? (string) $row->new_values : '', true);

        if (!is_array($new)) {
            return '';
        }

        $bits = array();

        foreach ($new as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $was = (is_array($old) && array_key_exists($key, $old) && !is_array($old[$key]))
                ? (string) $old[$key] : null;
            $now = (string) $value;

            if ($was !== null && $was === $now) {
                continue;
            }

            $label = str_replace('_', ' ', $key);

            $bits[] = ($was === null)
                ? $label . ' ' . $now
                : $label . ' ' . ($was !== '' ? $was : '—') . ' → ' . ($now !== '' ? $now : '—');

            if (count($bits) === 3) {
                $remaining = count($new) - 3;
                if ($remaining > 0) {
                    $bits[] = 'and ' . $remaining . ' more';
                }
                break;
            }
        }

        return implode(', ', $bits);
    }
}

if (!function_exists('abom_sel')) {
    /**
     * ` selected` when a filter value matches an option.
     *
     * Lived inside master_list.php as a bare function declaration.
     * Moved here once a second register needed it — two unguarded
     * declarations of one name is a fatal error waiting for the day
     * somebody loads both views in one request.
     *
     * String comparison on purpose: filter values arrive from the query
     * string as strings, and 0 == '' would select the wrong option.
     *
     * @param  mixed $a  current filter value
     * @param  mixed $b  option value
     * @return string
     */
    function abom_sel($a, $b)
    {
        return ((string) $a === (string) $b) ? ' selected' : '';
    }
}

if (!function_exists('abom_config_editable')) {
    /**
     * May the MACHINE CONFIGURATION of this saved BOM be changed?
     *
     * A narrower gate than abom_qty_editable(), on purpose. Editing a
     * quantity marks up a document; editing the axis or track count
     * changes what machine the document is FOR, and re-derives every
     * computed line under it. That is a reasonable thing to do to your
     * own draft, and an unreasonable thing to do to a sheet somebody
     * else is part-way through checking.
     *
     *   draft / rejected                       -> editable
     *   submitted / checked                    -> locked (under review)
     *   eng_approved / approved / superseded   -> locked (released)
     *
     * The same set delete uses, and for the same reason: these are the
     * two states in which a BOM is still the author's to change.
     *
     * This is what makes DUPLICATE useful. A copy arrives as a draft, so
     * a clone of an approved BOM can be re-specified for the next
     * machine without the original being touched.
     *
     * @param  object $bom
     * @return bool
     */
    function abom_config_editable($bom)
    {
        if (empty($bom->id)) {
            return false;                     // generator; $editable governs
        }

        $status = isset($bom->status) ? (string) $bom->status : '';

        $allowed = get_instance()->config->item('abom_config_editable_status', 'abom');
        if (!is_array($allowed)) {
            $allowed = array('draft', 'rejected');
        }

        return in_array($status, $allowed, true);
    }
}

if (!function_exists('abom_row_class')) {
    /**
     * Modifier class for a data-row. Comes straight from the engine.
     *
     * @param  object $line
     * @return string
     */
    function abom_row_class($line)
    {
        return isset($line->row_class) ? (string) $line->row_class : '';
    }
}

if (!function_exists('abom_erp_cell')) {
    /**
     * ERP CODE column. A missing code renders the NEW / PENDING badge in
     * the design's .erp-new style rather than an empty cell.
     *
     * @param  object $line
     * @return string  HTML
     */
    function abom_erp_cell($line)
    {
        $code = isset($line->erp_code) ? trim((string) $line->erp_code) : '';

        if ($code === '') {
            // The design distinguishes NEW from PENDING. A part the
            // source BOM marked NEW carries that word in its data_issue
            // note; the rest are simply awaiting a code.
            $note  = isset($line->remarks) ? (string) $line->remarks : '';
            $label = (stripos($note, 'NEW PART') !== false) ? 'NEW' : 'PENDING';

            return '<span class="erp-new" title="ERP code not yet assigned">' . $label . '</span>';
        }

        return '<span class="erp-cell">' . abom_e($code) . '</span>';
    }
}

if (!function_exists('abom_remarks_cell')) {
    /**
     * REMARKS column — the ENGINEER'S remark for this BOM.
     *
     * Seeded from the master item's usage note at generation time and
     * editable from there on. It shows, prints and exports as an
     * ordinary value; clearing it clears it for this document only.
     *
     * The seeded text is reproduced VERBATIM — never normalised,
     * re-cased or abbreviated. The reviewing engineers match these
     * strings by hand against the mechanical layout drawings (spec
     * section 10).
     *
     * OPTIONAL moved to the STATUS column and panel_location was dropped
     * when this became a typeable field; see the note in the body.
     *
     * @param  object $line
     * @param  bool   $editable
     * @return string  HTML
     */
    function abom_remarks_cell($line, $editable = false)
    {
        $value = isset($line->user_remark) ? (string) $line->user_remark : '';

        if (!$editable) {
            // Printed / released document: the engineer's text and
            // nothing else. An empty remark prints as an empty cell,
            // which is the honest rendering — it says nobody wrote one.
            return $value !== '' ? abom_e($value) : '';
        }

        // The master item's usage note arrives as the VALUE, seeded by
        // Abom_engine::generate() — see the note there. It shows in
        // black, submits, prints and exports, and the engineer edits or
        // clears it for this BOM alone.
        //
        // It was briefly a grey placeholder instead. That was the wrong
        // call for this document: the released DFs carry that text in
        // this column, and making somebody retype "HORZ. + VERT." on
        // every line to get it onto the print is friction, not a
        // safeguard.
        //
        // panel_location is still deliberately absent. It was repeated
        // on every single row while already being a header chip, which
        // is how a 220px column ended up carrying no information at all.
        $line_id = isset($line->id) ? ' data-line-id="' . (int) $line->id . '"' : '';

        return '<input type="text" class="remark-input"'
            . ' value="' . abom_e($value) . '"'
            . ' data-line="' . (int) $line->line_no . '"'
            . ' data-saved="' . abom_e($value) . '"'
            . $line_id
            . ' maxlength="255"'
            . ' placeholder="Add a remark…"'
            . ' aria-label="Remark for ' . abom_e($line->description) . '">';
    }
}

if (!function_exists('abom_row_actions_cell')) {
    /**
     * The insert / delete controls for one row.
     *
     * Only ever rendered when the caller says the sheet is editable, and
     * NEVER on a manual row's own delete guard — a hand-added row is
     * deleted the same way as any other.
     *
     * These act on THIS BOM ONLY. Nothing here writes to abom_item: the
     * generator holds them as form state until Save, and a saved BOM
     * writes them to abom_bom_line, which is a per-BOM snapshot. Adding
     * a row to one BOM cannot change what the next one generates.
     *
     * @param  object $line
     * @return string  HTML
     */
    function abom_row_actions_cell($line)
    {
        return '<button type="button" class="row-act row-add" title="Insert a blank row below this one"'
             . ' data-line="' . (int) $line->line_no . '"'
             . ' aria-label="Insert a row below line ' . (int) $line->line_no . '">+</button>'
             . '<button type="button" class="row-act row-del" title="Remove this row from this BOM"'
             . ' data-line="' . (int) $line->line_no . '"'
             . ' aria-label="Remove line ' . (int) $line->line_no . '">&times;</button>';
    }
}

if (!function_exists('abom_manual_cell')) {
    /**
     * A typeable cell on a hand-added row.
     *
     * Manual rows carry no master item, so description, part number,
     * ERP code and manufacturer are all free text. They are marked
     * is_manual_add and badged MANUAL ROW, because a line that nobody
     * can trace to a reference BOM is exactly the line a checker needs
     * to look at hardest.
     *
     * @param  string $field
     * @param  string $value
     * @param  int    $line_no
     * @param  string $placeholder
     * @param  int    $maxlength
     * @return string  HTML
     */
    function abom_manual_cell($field, $value, $line_no, $placeholder = '', $maxlength = 255)
    {
        return '<input type="text" class="manual-input manual-' . abom_e($field) . '"'
            . ' value="' . abom_e((string) $value) . '"'
            . ' data-line="' . (int) $line_no . '"'
            . ' data-field="' . abom_e($field) . '"'
            . ' maxlength="' . (int) $maxlength . '"'
            . ' placeholder="' . abom_e($placeholder) . '">';
    }
}

if (!function_exists('abom_asset')) {
    /**
     * URL for one of the module's own assets, with a cache-busting
     * version derived from the file's modification time.
     *
     * WHY THIS EXISTS
     * ---------------
     * The module's CSS and JS were referenced as plain
     * `assets_url . 'abom/abom-generate.js'`, with nothing to tell a
     * browser the file had changed. Deploying a JS change therefore did
     * nothing at all for anybody whose browser already held the old copy
     * — and the failure is worse than "no new feature", because the PHP
     * ships the new MARKUP regardless. On 2026-08-11 that put insert and
     * remove buttons on screen with no handlers behind them: they
     * rendered, they were clickable, and they did nothing.
     *
     * filemtime() rather than a hand-maintained version constant,
     * because a constant only works if somebody remembers to bump it,
     * and the one deployment where they forget is indistinguishable from
     * this bug.
     *
     * Falls back to the bare URL if the file cannot be stat'ed, so a
     * path this helper does not understand still renders a working link
     * rather than a broken one.
     *
     * @param  string $path  relative to assets/, e.g. 'abom/abom.js'
     * @return string
     */
    function abom_asset($path)
    {
        $url = assets_url . $path;

        if (!defined('FCPATH')) {
            return $url;
        }

        $file = FCPATH . 'assets/' . $path;

        if (!is_file($file)) {
            return $url;
        }

        $stamp = @filemtime($file);

        return $stamp ? ($url . '?v=' . $stamp) : $url;
    }
}

if (!function_exists('abom_status_badges')) {
    /**
     * STATUS column. Every flag the engine raised, as .formula-tag
     * badges. This is what makes the nine-column layout lossless: the
     * row can only carry one colour, STATUS carries them all.
     *
     * @param  object $line
     * @return string  HTML
     */
    function abom_status_badges($line)
    {
        $classes = array(
            'CONFLICT'    => 'tag-conflict',
            'ERP PENDING' => 'tag-pending',
            'REVIEW'      => 'tag-review',
            'MANUAL QTY'  => 'tag-manual',
            'QTY EDITED'  => 'tag-edited',
            'MANUAL ROW'  => 'tag-manual',
            'OPTIONAL'    => 'tag-ok',
        );

        $badges = isset($line->status_badges) && is_array($line->status_badges)
            ? $line->status_badges
            : array();

        // A hand-added row is the line a checker must look at hardest —
        // it has no master item behind it, so nothing validated its ERP
        // code, its part number or its quantity. It is badged first.
        if (!empty($line->is_manual_add) && !in_array('MANUAL ROW', $badges, true)) {
            array_unshift($badges, 'MANUAL ROW');
        }

        // OPTIONAL moved here from the REMARKS column when that column
        // became the engineer's own free-text field. It belongs with the
        // other flags anyway: it states why a line is present, which is
        // status, not a remark.
        if (!empty($line->is_optional) && !in_array('OPTIONAL', $badges, true)) {
            $badges[] = 'OPTIONAL';
        }

        // A persisted line carries its edit state; a freshly generated
        // one does not.
        if (!empty($line->is_overridden) && !in_array('QTY EDITED', $badges, true)) {
            $badges[] = 'QTY EDITED';
        }

        if (empty($badges)) {
            return '<span class="tag-ok">&mdash;</span>';
        }

        $out = array();
        foreach ($badges as $badge) {
            $class = isset($classes[$badge]) ? $classes[$badge] : 'tag-review';
            $label = ($badge === 'QTY EDITED') ? 'QTY EDITED &#9998;' : abom_e($badge);
            $out[] = '<span class="formula-tag ' . $class . '">' . $label . '</span>';
        }

        return implode('<br>', $out);
    }
}

if (!function_exists('abom_qty_cell')) {
    /**
     * QTY column. Read-only on the view/print page, an input on the
     * generate page. --lyellow plus the design's ✎ marker when the
     * engineer has overridden the computed quantity — cell only, never
     * the row.
     *
     * @param  object $line
     * @param  bool   $editable
     * @return string  HTML
     */
    function abom_qty_cell($line, $editable = false)
    {
        $qty      = (int) $line->qty;
        $computed = isset($line->computed_qty) ? (int) $line->computed_qty : $qty;
        $edited   = !empty($line->is_overridden);
        $cls      = $edited ? ' qty-edited' : '';
        $title    = 'Computed quantity: ' . $computed;

        if ($editable) {
            $line_id = isset($line->id) ? ' data-line-id="' . (int) $line->id . '"' : '';

            return '<input type="number" class="qty-input' . $cls . '"'
                . ' value="' . $qty . '" min="0"'
                . ' data-line="' . (int) $line->line_no . '"'
                . ' data-saved="' . $qty . '"'
                . $line_id
                . ' data-computed="' . $computed . '"'
                . ' aria-label="Quantity for ' . abom_e($line->description) . '"'
                . ' title="' . abom_e($title) . '">';
        }

        $mark = $edited ? '<span class="qty-mark" title="Quantity edited during review">&#9998;</span>' : '';

        return '<span class="qty-static' . $cls . '" title="' . abom_e($title) . '">'
            . $qty . '</span>' . $mark;
    }
}

if (!function_exists('abom_plc_badge_class')) {
    /**
     * @param  string $family_code  'FX5' | 'iQ-R'
     * @return string
     */
    function abom_plc_badge_class($family_code)
    {
        return ($family_code === 'FX5') ? 'plc-fx5' : 'plc-iqr';
    }
}

if (!function_exists('abom_chip_class')) {
    /**
     * @param  string $family_code
     * @return string
     */
    function abom_chip_class($family_code)
    {
        return ($family_code === 'FX5') ? 'fx5' : 'iqr';
    }
}

if (!function_exists('abom_status_label')) {
    /**
     * Human label for abom_bom.status — the internal four-stage
     * workflow, which is separate from the printed sign-off block.
     *
     * @param  string $status
     * @return string
     */
    function abom_status_label($status)
    {
        $map = array(
            'draft'        => 'Draft',
            'submitted'    => 'Submitted',
            'checked'      => 'Checked',
            'eng_approved' => 'Approved — Engineering',
            'approved'     => 'Approved — Procurement',
            'rejected'     => 'Rejected',
            'superseded'   => 'Superseded',
        );

        return isset($map[$status]) ? $map[$status] : ucfirst((string) $status);
    }
}
