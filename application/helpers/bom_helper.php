<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * bom_helper
 *
 * Presentation helpers for the Automation BOM module: row class, status
 * badges, the nine-column cell mapping and escaping.
 *
 * These functions FORMAT. They never decide a quantity and they never
 * decide a row's precedence — Bom_engine owns both (spec section 10).
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
     * REMARKS column: usage_remark, then panel_location, then OPTIONAL.
     *
     * usage_remark is reproduced VERBATIM — never normalised, re-cased
     * or abbreviated. The reviewing engineers match these strings by
     * hand against the mechanical layout drawings (spec section 10).
     *
     * @param  object $line
     * @return string  HTML
     */
    function abom_remarks_cell($line)
    {
        $parts = array();

        if (!empty($line->usage_remark)) {
            $parts[] = abom_e($line->usage_remark);
        }

        if (!empty($line->panel_location)) {
            $parts[] = '<span class="rc-loc">' . abom_e($line->panel_location) . '</span>';
        }

        if (!empty($line->is_optional)) {
            $parts[] = '<span class="rc-opt">OPTIONAL</span>';
        }

        return implode(' &middot; ', $parts);
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
        );

        $badges = isset($line->status_badges) && is_array($line->status_badges)
            ? $line->status_badges
            : array();

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
            return '<input type="number" class="qty-input' . $cls . '"'
                . ' value="' . $qty . '" min="0"'
                . ' data-line="' . (int) $line->line_no . '"'
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
