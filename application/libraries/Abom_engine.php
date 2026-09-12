<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_engine
 *
 * Pure business logic — no DB writes, no output. Takes a configuration
 * and a set of master items, returns computed BOM lines.
 *
 * This library is the ONLY place quantity or PLC-family logic may live.
 * Controllers, models and views MUST NOT compute quantities (spec
 * section 10). Keeping it in one class is what makes the rules testable
 * — see BOMMODULEDEVELOPMENT/tests/ for the section 5.1 suite, which
 * runs this class with no CodeIgniter bootstrap at all.
 *
 * PHP 7.4 compatible (production runs ea-php74).
 */
class Abom_engine
{
    protected $CI;
    protected $rules = null;

    protected $variant_rules = null;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Abom_master_model');
        $this->CI->load->model('Abom_item_model');
    }

    // -----------------------------------------------------------------
    // PLC FAMILY DETECTION
    // -----------------------------------------------------------------

    /**
     * Data-driven evaluation over abom_plc_rule, priority ascending,
     * first match wins (spec 2.1). Deliberately NOT a hard-coded if
     * chain — engineering retunes thresholds by editing rows.
     *
     * @param  array $cfg ['axes'=>int, 'speed_ppm'=>int, 'motion_type'=>string,
     *                      'machine_model'=>string]
     * @return array ['family_id'=>int, 'code'=>string, 'rule_id'=>int,
     *                'priority'=>int, 'explanation'=>string]
     */
    public function detect_plc_family(array $cfg)
    {
        if ($this->rules === null) {
            $this->rules = $this->CI->Abom_master_model->get_active_rules();
        }

        $axes   = isset($cfg['axes']) ? (int) $cfg['axes'] : 0;
        $speed  = isset($cfg['speed_ppm']) ? (int) $cfg['speed_ppm'] : 0;
        $motion = isset($cfg['motion_type']) ? $cfg['motion_type'] : 'ANY';
        $model  = isset($cfg['machine_model']) ? $cfg['machine_model'] : '';

        foreach ($this->rules as $r) {
            // machine_model arrived with abom_008. A rule that predates
            // it has NULL here and still applies to every model, so
            // detection is unchanged for all four original rules.
            //
            // It exists because DF-1883 runs 11 axes on FX5 by fitting a
            // second simple-motion card — the 8-axis limit is a property
            // of one card, not of the family — and that exception has to
            // be scoped to the machine it describes rather than widening
            // the rule for everything.
            if (isset($r->machine_model) && $r->machine_model !== null
                && $r->machine_model !== '' && $r->machine_model !== $model) continue;

            if ($r->min_axes  !== null && $axes  <  (int) $r->min_axes)  continue;
            if ($r->max_axes  !== null && $axes  >  (int) $r->max_axes)  continue;
            if ($r->min_speed !== null && $speed <  (int) $r->min_speed) continue;
            if ($r->max_speed !== null && $speed >  (int) $r->max_speed) continue;
            if ($r->motion_type !== 'ANY' && $r->motion_type !== $motion) continue;

            return array(
                'family_id'   => (int) $r->result_family_id,
                'code'        => $this->CI->Abom_master_model->family_code((int) $r->result_family_id),
                'rule_id'     => (int) $r->id,
                'priority'    => (int) $r->priority,
                'explanation' => $r->explanation,
            );
        }

        // Unreachable while the catch-all rule (priority 99) is active.
        throw new RuntimeException('No PLC selection rule matched. Check abom_plc_rule seed data.');
    }

    // -----------------------------------------------------------------
    // BUILD VARIANT DETECTION
    //
    // A family is a CPU platform; a VARIANT is a buildable machine. The
    // nine reference BOMs put two incompatible servo ranges inside the
    // FX5 family alone (MR-JE/HG-SN below 8 axes, MR-J4/HG-JR at 8), so
    // "every item of the detected family" would generate both amplifier
    // ranges and procurement would order twice the drives.
    //
    // Same contract as detect_plc_family(): data-driven over
    // abom_variant_rule, priority ascending, first match wins, NULL bound
    // = unbounded. Evaluated AFTER the family is known, so a rule row
    // with plc_family_id filters within that family rather than
    // re-detecting it.
    // -----------------------------------------------------------------

    /**
     * @param  array $cfg        axes, tracks, speed_ppm, motion_type,
     *                           machine_model
     * @param  int   $family_id  the already-detected (or overridden) family
     * @return array|null  ['variant_id'=>int, 'code'=>string,
     *                      'name'=>string, 'rule_id'=>int,
     *                      'priority'=>int, 'explanation'=>string]
     *                     NULL when no rule matches — see generate()
     */
    public function detect_variant(array $cfg, $family_id)
    {
        if ($this->variant_rules === null) {
            $this->variant_rules = $this->CI->Abom_master_model->get_active_variant_rules();
        }

        $family_id = (int) $family_id;
        $axes      = isset($cfg['axes']) ? (int) $cfg['axes'] : 0;
        $speed     = isset($cfg['speed_ppm']) ? (int) $cfg['speed_ppm'] : 0;
        $motion    = isset($cfg['motion_type']) ? $cfg['motion_type'] : 'ANY';
        $model     = isset($cfg['machine_model']) ? $cfg['machine_model'] : '';

        foreach ($this->variant_rules as $r) {
            if ($r->plc_family_id !== null && (int) $r->plc_family_id !== $family_id) continue;
            if ($r->machine_model !== null && $r->machine_model !== '' && $r->machine_model !== $model) continue;
            if ($r->min_axes  !== null && $axes  <  (int) $r->min_axes)  continue;
            if ($r->max_axes  !== null && $axes  >  (int) $r->max_axes)  continue;
            if ($r->min_speed !== null && $speed <  (int) $r->min_speed) continue;
            if ($r->max_speed !== null && $speed >  (int) $r->max_speed) continue;
            if ($r->motion_type !== 'ANY' && $r->motion_type !== $motion) continue;

            $variant = $this->CI->Abom_master_model->get_variant((int) $r->result_variant_id);
            if (!$variant || empty($variant->is_active)) {
                // A rule pointing at a deactivated variant must not
                // silently fall through to the next one — that would
                // generate a different machine's BOM without saying so.
                continue;
            }

            return array(
                'variant_id'  => (int) $variant->id,
                'code'        => (string) $variant->code,
                'name'        => (string) $variant->name,
                'rule_id'     => (int) $r->id,
                'priority'    => (int) $r->priority,
                'explanation' => (string) $r->explanation,
            );
        }

        return null;
    }

    // -----------------------------------------------------------------
    // QUANTITY CALCULATION
    // -----------------------------------------------------------------

    /**
     * Quantities are COMPUTED, not stored (spec section 1). Two machines
     * of the same model with different axis counts must produce
     * different SSCNET cable quantities without anybody editing master
     * data.
     *
     * J4_STO and BATTERY read two SEPARATE inputs and must never be
     * driven from one number — DF-1826 shows 11 MR-J4 units but 12
     * battery sets (spec 2.2).
     *
     * @param  object $item  row from abom_item
     * @param  array  $cfg   ['axes'=>int, 'tracks'=>int, 'j4_units'=>int,
     *                        'battery_qty'=>int]
     * @return int
     */
    public function calc_qty($item, array $cfg)
    {
        $base = (int) $item->base_qty;

        switch ($item->formula_code) {
            case 'AXES':
                return max(0, isset($cfg['axes']) ? (int) $cfg['axes'] : 0);

            case 'AXES_MINUS_1':
                // Floor of 1: an 8-axis machine daisy-chains 7 links, a
                // 1-axis machine still needs 1 — never 0.
                return max(1, (isset($cfg['axes']) ? (int) $cfg['axes'] : 0) - 1);

            case 'TRACKS':
                // One unit per track — the SPM1250P cut-off drives.
                return max(0, isset($cfg['tracks']) ? (int) $cfg['tracks'] : 0);

            case 'TRACK_TEMP':
                // 4-channel temperature / RTD card count, per engineering:
                //
                //     (((tracks + 1) * 2) + 2) / 4
                //
                // Written out in the form engineering specified rather
                // than the algebraically identical (tracks + 2) / 2, so
                // this line can be read straight against their note.
                //
                // Rounded UP: 9 tracks gives 5.5 and half a card cannot
                // be bought. Verified against every source BOM carrying a
                // 4-channel card — 6T=4, 8T=5, 9T=6, 12T=7.
                $tracks = isset($cfg['tracks']) ? (int) $cfg['tracks'] : 0;
                if ($tracks <= 0) {
                    return 0;
                }

                return (int) ceil(((($tracks + 1) * 2) + 2) / 4);

            case 'J4_STO':
                return max(0, isset($cfg['j4_units']) ? (int) $cfg['j4_units'] : 0);

            case 'BATTERY':
                return max(0, isset($cfg['battery_qty']) ? (int) $cfg['battery_qty'] : 0);

            case 'FIXED':
            case 'MANUAL':
            default:
                return $base;
        }
    }

    // -----------------------------------------------------------------
    // FEATURE GATING
    // -----------------------------------------------------------------

    /**
     * @param  string $feature_code
     * @param  array  $features  ['feat_perf' => 1, ...]
     * @return bool
     */
    public function feature_enabled($feature_code, array $features)
    {
        if (empty($feature_code)) {
            return true;                      // ungated item — always included
        }

        return !empty($features[$feature_code]);
    }

    // -----------------------------------------------------------------
    // GENERATION
    // -----------------------------------------------------------------

    /**
     * @param  array $cfg  axes, tracks, speed_ppm, motion_type,
     *                     machine_model, machine_side, j4_units,
     *                     battery_qty, features[],
     *                     plc_family_id (optional manual override),
     *                     variant_id    (optional manual override)
     * @return array ['family'=>..., 'family_id'=>int, 'overridden'=>bool,
     *                'variant'=>..., 'variant_id'=>int|null,
     *                'variant_overridden'=>bool, 'variant_missing'=>bool,
     *                'sections'=>[...], 'lines'=>[...], 'stats'=>[...]]
     */
    public function generate(array $cfg)
    {
        $detected  = $this->detect_plc_family($cfg);
        $family_id = !empty($cfg['plc_family_id'])
            ? (int) $cfg['plc_family_id']
            : $detected['family_id'];

        // An override that is never surfaced is how wrong BOMs get
        // approved (spec 2.1) — the caller must render this.
        $overridden = ($family_id !== $detected['family_id']);

        // --- build variant ------------------------------------------
        $detected_variant = $this->detect_variant($cfg, $family_id);

        $variant_id = !empty($cfg['variant_id'])
            ? (int) $cfg['variant_id']
            : ($detected_variant ? (int) $detected_variant['variant_id'] : null);

        $variant_overridden = ($variant_id !== null && $detected_variant !== null
            && $variant_id !== (int) $detected_variant['variant_id']);

        // An overridden variant must still describe itself in the panel,
        // so resolve whatever id we ended up with rather than reporting
        // the detected one.
        $variant = ($variant_id !== null)
            ? $this->CI->Abom_master_model->get_variant($variant_id)
            : null;

        if ($variant === null) {
            $variant_id = null;
        }

        // THE FAMILY FOLLOWS THE VARIANT.
        //
        // A variant belongs to exactly one PLC family, and it is the
        // variant that decides which items are on the sheet. So if an
        // engineer overrides the build to one from another family, the
        // family label has to move with it — otherwise the document
        // says "FX5 Series" in the header while every line on it is an
        // iQ-R part, which is precisely what happened on ABOM-5
        // (2026-08-11): a 6-axis SPM1200L detected FX5, was overridden
        // to IQR-STD, and printed the two side by side.
        //
        // The family override flag is raised as well, because this IS a
        // family override — the operator just expressed it by choosing a
        // build rather than by choosing a family.
        if ($variant !== null && (int) $variant->plc_family_id !== $family_id) {
            $family_id  = (int) $variant->plc_family_id;
            $overridden = true;
        }

        // No variant means no items. That is the SAFE failure and it is
        // deliberate: generating the whole family catalogue instead would
        // put two incompatible servo ranges on one purchasable document.
        // The caller renders variant_missing as a blocking message.
        $items = ($variant_id === null)
            ? array()
            : $this->CI->Abom_item_model->get_by_variant($variant_id);

        $features = isset($cfg['features']) ? $cfg['features'] : array();

        $lines   = array();
        $line_no = 0;

        foreach ($items as $item) {
            if ($item->is_optional && !$this->feature_enabled($item->feature_code, $features)) {
                // Gated out — excluded ENTIRELY, not greyed out and not
                // shown with quantity zero. Procurement may order
                // anything visible on this document (spec 2.4).
                continue;
            }

            $qty = $this->calc_qty($item, $cfg);
            $line_no++;

            $lines[] = (object) array(
                'line_no'        => $line_no,
                'item_id'        => (int) $item->id,
                'section_name'   => $item->section_name,
                'section_order'  => (int) $item->section_sort,
                'erp_code'       => $item->erp_code,
                'description'    => $item->description,
                'part_no'        => $item->part_no,
                'manufacturer'   => $item->manufacturer,
                'qty'            => $qty,
                'computed_qty'   => $qty,
                'is_overridden'  => 0,
                'uom'            => $item->uom,
                'formula_code'   => $item->formula_code,
                'variant_code'   => $variant ? $variant->code : null,
                'usage_remark'   => $item->usage_remark,
                'remarks'        => $item->data_issue,
                // SEEDED FROM THE MASTER ITEM'S USAGE NOTE.
                //
                // This started out empty, with usage_remark shown only
                // as the input's grey placeholder — the reasoning being
                // that a remark on a signed document should only ever be
                // there because a person typed it.
                //
                // Engineering overruled that on 2026-08-11, and they are
                // right about their own document: "HORZ. + VERT." tells
                // the reader which axis the part is for, it is the same
                // text the released DFs carry in that column, and making
                // someone retype it on every line to get it onto the
                // print is not a safeguard, it is friction.
                //
                // So it is a real, editable VALUE now: it shows, it
                // prints, it exports, and the engineer can change or
                // clear it per BOM. Clearing it is still a deliberate
                // act, and it only ever affects the one document —
                // abom_item is not written from any BOM screen.
                'user_remark'    => (string) $item->usage_remark,
                'issue_severity' => $item->issue_severity,
                'is_optional'    => (int) $item->is_optional,
                'feature_code'   => $item->feature_code,
                'panel_location' => $item->panel_location,
                'is_manual_add'  => 0,
                'row_class'      => $this->row_class($item),
                'status_badges'  => $this->status_badges($item),
            );
        }

        return array(
            'family'             => $detected,
            'family_id'          => $family_id,
            'overridden'         => $overridden,
            'variant'            => $variant,
            'variant_detected'   => $detected_variant,
            'variant_id'         => $variant_id,
            'variant_overridden' => $variant_overridden,
            'variant_missing'    => ($variant_id === null),
            'sections'           => $this->sections_present($lines),
            'lines'              => $lines,
            'stats'              => $this->stats($lines),
        );
    }

    // -----------------------------------------------------------------
    // ROW CLASS
    //
    // The approved design document governs appearance, so these are
    // MODIFIER classes appended to the design's own `data-row`, coloured
    // from the design's existing palette variables. No new CSS variable
    // is introduced and none is renamed.
    //
    //   is-conflict  --red      ERP code assigned to two different parts
    //   is-noerp     --orange   no ERP code yet
    //   is-optional  --lgreen   feature-gated item
    //   is-manual    --lblue    MANUAL formula, quantity needs a human
    //
    // Precedence, first match wins — severity outranks category. A
    // conflict on a manual optional line shows as a conflict.
    //
    // 'review' severity deliberately produces NO row tint. It applies to
    // 29 of 71 master items; tinting them would put 41% of the sheet in
    // a warning colour on a document that carries a customer approval
    // signature, and would reduce is-manual to the 3 rows that happen to
    // carry no severity. Review surfaces as a STATUS badge instead —
    // nothing is lost, because the STATUS column carries every
    // applicable badge regardless of which class wins the row.
    //
    // --lyellow is therefore reserved for the job the design's own
    // legend gives it: "Qty edited during review (marked ✎)". That is a
    // qty-edited class on the QTY CELL only, never the whole row — see
    // qty_cell_class() below.
    //
    // PLC family colour coding is deliberately absent. A generated BOM
    // is always one family and the family is shown in the configuration
    // panel.
    // -----------------------------------------------------------------

    /**
     * @param  object $item
     * @return string  '' for a clean line
     */
    public function row_class($item)
    {
        if ($item->issue_severity === 'conflict') {
            return 'is-conflict';
        }

        if (empty($item->erp_code) || $item->issue_severity === 'no_erp') {
            return 'is-noerp';
        }

        if (!empty($item->is_optional)) {
            return 'is-optional';
        }

        if ($item->formula_code === 'MANUAL') {
            return 'is-manual';
        }

        return '';
    }

    /**
     * Class for the QTY cell alone. --lyellow + the ✎ marker, exactly as
     * the reference document does it. Never applied to the row.
     *
     * @param  object $line  a generated line (or a persisted abom_bom_line)
     * @return string  '' when the quantity is the computed one
     */
    public function qty_cell_class($line)
    {
        return !empty($line->is_overridden) ? 'qty-edited' : '';
    }

    // -----------------------------------------------------------------
    // STATUS BADGES
    //
    // The design has nine columns, one of which is STATUS. Every flag
    // the engine produces surfaces there as a .formula-tag badge, so
    // collapsing twelve fields into nine columns loses no information.
    // A clean line returns an empty array and renders nothing.
    // -----------------------------------------------------------------

    /**
     * @param  object $item
     * @return array  list of badge labels, most severe first
     */
    public function status_badges($item)
    {
        $badges = array();

        if ($item->issue_severity === 'conflict') {
            $badges[] = 'CONFLICT';
        }

        if (empty($item->erp_code) || $item->issue_severity === 'no_erp') {
            $badges[] = 'ERP PENDING';
        }

        if ($item->issue_severity === 'review') {
            $badges[] = 'REVIEW';
        }

        if ($item->formula_code === 'MANUAL') {
            $badges[] = 'MANUAL QTY';
        }

        return $badges;
    }

    // -----------------------------------------------------------------

    /**
     * Distinct sections present in the generated lines, in line order.
     * Drives the section filter pills and the sec-row grouping headers.
     *
     * @param  array $lines
     * @return array
     */
    public function sections_present(array $lines)
    {
        $sections = array();

        foreach ($lines as $line) {
            $name = $line->section_name;
            if (!isset($sections[$name])) {
                $sections[$name] = array(
                    'name'  => $name,
                    'order' => (int) $line->section_order,
                    'count' => 0,
                );
            }
            $sections[$name]['count']++;
        }

        return array_values($sections);
    }

    /**
     * @param  array $lines
     * @return array
     */
    public function stats(array $lines)
    {
        $s = array(
            'lines'     => 0,
            'total_qty' => 0,
            'manual'    => 0,
            'review'    => 0,
            'no_erp'    => 0,
            'conflict'  => 0,
            'optional'  => 0,
        );

        foreach ($lines as $l) {
            $s['lines']++;
            $s['total_qty'] += (int) $l->qty;

            if ($l->formula_code === 'MANUAL')     $s['manual']++;
            if ($l->issue_severity === 'review')   $s['review']++;
            if (empty($l->erp_code))               $s['no_erp']++;
            if ($l->issue_severity === 'conflict') $s['conflict']++;
            if ($l->is_optional)                   $s['optional']++;
        }

        $s['open_issues'] = $s['no_erp'] + $s['conflict'];

        return $s;
    }
}
