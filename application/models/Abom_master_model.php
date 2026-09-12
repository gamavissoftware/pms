<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_master_model
 *
 * Read access to the Automation BOM reference data: PLC families,
 * sections, quantity formulas, optional features and the data-driven
 * PLC selection rules.
 *
 * Read paths only at this stage (spec section 9, build order step 2).
 * Master-item WRITE paths and audit logging arrive with the admin
 * screens in build order step 8.
 *
 * PHP 7.4 compatible.
 */
class Abom_master_model extends CI_Model
{
    private $family_table  = 'abom_plc_family';
    private $section_table = 'abom_section';
    private $formula_table = 'abom_formula';
    private $feature_table = 'abom_feature';
    private $rule_table    = 'abom_plc_rule';
    private $variant_table = 'abom_variant';
    private $vrule_table   = 'abom_variant_rule';

    /** Read only by the user-guide feeds below, for usage counts. */
    private $item_table    = 'abom_item';

    /** Simple per-request caches — generate() is called on every keystroke. */
    private $family_cache  = null;
    private $rule_cache    = null;
    private $section_cache = array();
    private $variant_cache = null;
    private $vrule_cache   = null;

    // -----------------------------------------------------------------
    // GUARD
    // -----------------------------------------------------------------

    /**
     * Matches the tables_ready() pattern used by Df_dispatch_plan_model.
     * The controller fails gracefully with a clear message when the
     * abom_* tables have not been installed yet.
     *
     * @return bool
     */
    public function tables_ready()
    {
        $tables = $this->config->item('abom_tables');
        if (empty($tables)) {
            return false;
        }

        foreach ($tables as $table) {
            if (!$this->db->table_exists($table)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Names the first missing table, for the operator-facing message.
     *
     * @return string  '' when every table is present
     */
    public function first_missing_table()
    {
        $tables = $this->config->item('abom_tables');
        if (empty($tables)) {
            return 'abom_plc_family';
        }

        foreach ($tables as $table) {
            if (!$this->db->table_exists($table)) {
                return $table;
            }
        }

        return '';
    }

    // -----------------------------------------------------------------
    // PLC FAMILIES
    // -----------------------------------------------------------------

    /**
     * @return array  keyed by family id
     */
    public function get_families()
    {
        if ($this->family_cache !== null) {
            return $this->family_cache;
        }

        $rows = $this->db->from($this->family_table)
            ->order_by('sort_order', 'ASC')
            ->get()
            ->result();

        $this->family_cache = array();
        foreach ($rows as $row) {
            $this->family_cache[(int) $row->id] = $row;
        }

        return $this->family_cache;
    }

    /**
     * @param  int $family_id
     * @return object|null
     */
    public function get_family($family_id)
    {
        $families = $this->get_families();
        $family_id = (int) $family_id;

        return isset($families[$family_id]) ? $families[$family_id] : null;
    }

    /**
     * @param  int $family_id
     * @return string  '' when unknown
     */
    public function family_code($family_id)
    {
        $family = $this->get_family($family_id);

        return $family ? (string) $family->code : '';
    }

    /**
     * Defaults applied when the detected family changes (spec 2.3).
     * FX5 => 7 / 7, iQ-R => 11 / 12.
     *
     * @param  int $family_id
     * @return array
     */
    public function family_defaults($family_id)
    {
        $family = $this->get_family($family_id);

        if (!$family) {
            return array('j4_units' => 0, 'battery_qty' => 0, 'panel_location' => '');
        }

        return array(
            'j4_units'       => (int) $family->default_j4_units,
            'battery_qty'    => (int) $family->default_battery,
            'panel_location' => (string) $family->default_panel_location,
        );
    }

    // -----------------------------------------------------------------
    // BUILD VARIANTS
    //
    // A variant is one buildable configuration of a family — which
    // amplifier range, which HMI, which panel. Items hang off a variant,
    // so these reads sit on the generate() hot path and are cached for
    // the request like the family and rule reads above.
    // -----------------------------------------------------------------

    /**
     * @return array  keyed by variant id, ordered by sort_order
     */
    public function get_variants()
    {
        if ($this->variant_cache !== null) {
            return $this->variant_cache;
        }

        $rows = $this->db->from($this->variant_table)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get()
            ->result();

        $this->variant_cache = array();
        foreach ($rows as $row) {
            $this->variant_cache[(int) $row->id] = $row;
        }

        return $this->variant_cache;
    }

    /**
     * @param  int $variant_id
     * @return object|null
     */
    public function get_variant($variant_id)
    {
        $variants   = $this->get_variants();
        $variant_id = (int) $variant_id;

        return isset($variants[$variant_id]) ? $variants[$variant_id] : null;
    }

    /**
     * A generator configuration that produces this BUILD.
     *
     * What "Generate BOM" on the Reference BOMs register lands on. The
     * operator picks the build they already know they want, instead of
     * reverse-engineering which axis and speed combination happens to
     * route to it — which is exactly the job the selection rules exist
     * to do and no human should be doing by hand.
     *
     * Reads abom_variant.ref_* first: the reference machine recorded
     * with the build, taken from its released DF. Where a build has none
     * — one added later and not yet filled in — it falls back to the
     * build's own SELECTION RULE, which is the next best evidence
     * available:
     *
     *   axes   min_axes, else max_axes, else the module default
     *   speed  min_speed, else max_speed, else the module default
     *   motion the rule's motion_type when it names one
     *   model  the rule's, else the variant's own
     *
     * TRACKS have no fallback beyond the module default, and that is not
     * an oversight: tracks do not appear in any selection rule because
     * they do not select anything. They drive quantities only. A build
     * with no recorded reference genuinely does not know its own track
     * count, and the caller is told so rather than shown a guess dressed
     * up as a fact.
     *
     * @param  int   $variant_id
     * @param  array $defaults  module defaults to fall back on
     * @return array|null  ['cfg' => array, 'exact' => bool, 'missing' => array]
     */
    public function config_for_variant($variant_id, array $defaults = array())
    {
        $variant = $this->get_variant($variant_id);

        if (!$variant) {
            return null;
        }

        $cfg = $defaults;
        $missing = array();

        $cfg['machine_model'] = !empty($variant->machine_model)
            ? $variant->machine_model
            : (isset($defaults['machine_model']) ? $defaults['machine_model'] : '');

        // The recorded reference machine, where there is one.
        $have = array();
        foreach (array('axes' => 'ref_axes', 'tracks' => 'ref_tracks',
                       'speed_ppm' => 'ref_speed_ppm') as $key => $col) {
            if (isset($variant->$col) && $variant->$col !== null && (int) $variant->$col > 0) {
                $cfg[$key] = (int) $variant->$col;
                $have[$key] = true;
            }
        }

        if (isset($variant->ref_motion_type) && $variant->ref_motion_type !== null
            && $variant->ref_motion_type !== '') {
            $cfg['motion_type'] = $variant->ref_motion_type;
            $have['motion_type'] = true;
        }

        if (isset($variant->ref_machine_side) && $variant->ref_machine_side !== null
            && $variant->ref_machine_side !== '') {
            $cfg['machine_side'] = $variant->ref_machine_side;
        }

        // Anything still unrecorded: fall back to the selection rule.
        $rule = $this->rule_for_variant($variant_id);

        if (empty($have['axes'])) {
            $missing[] = 'axes';
            if ($rule) {
                if ($rule->min_axes !== null)      { $cfg['axes'] = (int) $rule->min_axes; }
                elseif ($rule->max_axes !== null)  { $cfg['axes'] = (int) $rule->max_axes; }
            }
        }

        if (empty($have['speed_ppm'])) {
            $missing[] = 'speed';
            if ($rule) {
                if ($rule->min_speed !== null)     { $cfg['speed_ppm'] = (int) $rule->min_speed; }
                elseif ($rule->max_speed !== null) { $cfg['speed_ppm'] = (int) $rule->max_speed; }
            }
        }

        if (empty($have['motion_type'])) {
            $missing[] = 'motion type';
            if ($rule && $rule->motion_type !== 'ANY') {
                $cfg['motion_type'] = $rule->motion_type;
            }
        }

        // No rule carries tracks. Say so rather than imply the default
        // came from the build.
        if (empty($have['tracks'])) {
            $missing[] = 'tracks';
        }

        if ($rule && !empty($rule->machine_model)) {
            $cfg['machine_model'] = $rule->machine_model;
        }

        // The J4 and battery counts the build already carries.
        $cfg['j4_units']    = (int) $variant->default_j4_units;
        $cfg['battery_qty'] = (int) $variant->default_battery;

        // Auto-detection is left ON. If the recorded machine really is a
        // machine of this build, the rules will pick it; pinning an
        // override would hide a data fault instead of exposing it, and
        // would stamp "Manual override" on an ordinary new BOM.
        $cfg['variant_id']    = null;
        $cfg['plc_family_id'] = null;

        return array(
            'cfg'     => $cfg,
            'variant' => $variant,
            'exact'   => empty($missing),
            'missing' => $missing,
        );
    }

    /**
     * The highest-priority active rule that selects this build.
     *
     * Priority ASC, first match wins — the same order Abom_engine uses,
     * so the rule read here is the one that would actually fire.
     *
     * @param  int $variant_id
     * @return object|null
     */
    public function rule_for_variant($variant_id)
    {
        foreach ($this->get_active_variant_rules() as $rule) {
            if ((int) $rule->result_variant_id === (int) $variant_id) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Active variants only, for the override picker. An operator must not
     * be offered a build that engineering has retired.
     *
     * @return array
     */
    public function get_active_variants()
    {
        $out = array();
        foreach ($this->get_variants() as $id => $variant) {
            if (!empty($variant->is_active)) {
                $out[$id] = $variant;
            }
        }

        return $out;
    }

    /**
     * @param  int $variant_id
     * @return string  '' when unknown
     */
    public function variant_code($variant_id)
    {
        $variant = $this->get_variant($variant_id);

        return $variant ? (string) $variant->code : '';
    }

    /**
     * MR-J4 unit / battery / panel defaults for a variant, falling back
     * to the family when the BOM has no variant.
     *
     * These live on the variant because they differ between builds of one
     * family: DF-1770 (iQ-R) is 9 / 9 in a panel with the machine,
     * DF-1826 (iQ-R) is 11 / 12 standalone. Reading them off the family
     * would put DF-1770's BOM on DF-1826's numbers.
     *
     * @param  int      $variant_id
     * @param  int|null $family_id  fallback when the variant is unknown
     * @return array
     */
    public function variant_defaults($variant_id, $family_id = null)
    {
        $variant = $this->get_variant($variant_id);

        if (!$variant) {
            return $this->family_defaults($family_id);
        }

        return array(
            'j4_units'       => (int) $variant->default_j4_units,
            'battery_qty'    => (int) $variant->default_battery,
            'panel_location' => (string) $variant->default_panel_location,
        );
    }

    /**
     * Variant selection rules, priority ASC — Abom_engine evaluates in
     * this order and the first match wins. Same contract as
     * get_active_rules() above.
     *
     * @return array
     */
    public function get_active_variant_rules()
    {
        if ($this->vrule_cache !== null) {
            return $this->vrule_cache;
        }

        $this->vrule_cache = $this->db->from($this->vrule_table)
            ->where('is_active', 1)
            ->order_by('priority', 'ASC')
            ->order_by('id', 'ASC')
            ->get()
            ->result();

        return $this->vrule_cache;
    }

    public function count_variants()
    {
        return (int) $this->db->where('is_active', 1)
            ->count_all_results($this->variant_table);
    }

    // -----------------------------------------------------------------
    // SECTIONS
    // -----------------------------------------------------------------

    /**
     * @param  int $family_id
     * @return array  ordered by sort_order
     */
    public function get_sections($family_id)
    {
        $family_id = (int) $family_id;

        if (isset($this->section_cache[$family_id])) {
            return $this->section_cache[$family_id];
        }

        $this->section_cache[$family_id] = $this->db->from($this->section_table)
            ->where('plc_family_id', $family_id)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get()
            ->result();

        return $this->section_cache[$family_id];
    }

    public function count_sections()
    {
        return (int) $this->db->count_all_results($this->section_table);
    }

    // -----------------------------------------------------------------
    // FORMULAS
    // -----------------------------------------------------------------

    /**
     * @return array  keyed by formula code
     */
    public function get_formulas()
    {
        $rows = $this->db->from($this->formula_table)
            ->order_by('sort_order', 'ASC')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[$row->code] = $row;
        }

        return $out;
    }

    public function count_formulas()
    {
        return (int) $this->db->count_all_results($this->formula_table);
    }

    // -----------------------------------------------------------------
    // FEATURES
    // -----------------------------------------------------------------

    /**
     * @return array  keyed by feature code, ordered by sort_order
     */
    public function get_features()
    {
        $rows = $this->db->from($this->feature_table)
            ->order_by('sort_order', 'ASC')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[$row->code] = $row;
        }

        return $out;
    }

    /**
     * All four features default to ON (spec 2.4).
     *
     * @return array  ['feat_perf' => 1, ...]
     */
    public function default_features()
    {
        $out = array();
        foreach ($this->get_features() as $code => $feature) {
            $out[$code] = (int) $feature->default_on;
        }

        return $out;
    }

    public function count_features()
    {
        return (int) $this->db->count_all_results($this->feature_table);
    }

    // -----------------------------------------------------------------
    // PLC SELECTION RULES
    // -----------------------------------------------------------------

    /**
     * Data-driven PLC selection (spec 2.1). Priority ASC — Abom_engine
     * evaluates in this order and the first match wins. Engineering
     * retunes thresholds by editing rows, not by a code deploy.
     *
     * @return array
     */
    public function get_active_rules()
    {
        if ($this->rule_cache !== null) {
            return $this->rule_cache;
        }

        $this->rule_cache = $this->db->from($this->rule_table)
            ->where('is_active', 1)
            ->order_by('priority', 'ASC')
            ->order_by('id', 'ASC')
            ->get()
            ->result();

        return $this->rule_cache;
    }

    // -----------------------------------------------------------------
    // DELETING A BUILD
    //
    // Only ever for a build nothing has been generated from -- the
    // duplicate left behind when the same DF is imported twice. A build
    // that a BOM was generated from is part of that document's
    // provenance and must outlive the register.
    // -----------------------------------------------------------------

    /**
     * How many generated BOMs were built from this variant.
     *
     * Counts SOFT-DELETED BOMs too, on purpose. A soft-deleted BOM still
     * exists, is still restorable and still has to render; deleting the
     * build out from under it would leave a document that cannot explain
     * where its lines came from.
     *
     * abom_bom.variant_id carries an INDEX but no foreign key, so the
     * database will not refuse this on its own. This count is the only
     * thing standing between a stray click and an orphaned BOM.
     *
     * @param  int $variant_id
     * @return int
     */
    public function variant_bom_usage($variant_id)
    {
        return (int) $this->db->from('abom_bom')
            ->where('variant_id', (int) $variant_id)
            ->count_all_results();
    }

    /**
     * variant_id => how many BOMs were generated from it, for the whole
     * register in ONE query. The list renders a delete control per row and
     * asking per row would be a query per build.
     *
     * A build absent from the map has never been used.
     *
     * @return array
     */
    public function variant_bom_usage_map()
    {
        $rows = $this->db->select('variant_id, COUNT(*) AS n', false)
            ->from('abom_bom')
            ->where('variant_id IS NOT NULL', null, false)
            ->group_by('variant_id')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[(int) $row->variant_id] = (int) $row->n;
        }

        return $out;
    }

    /**
     * The BOM numbers using this variant, for the refusal message.
     * Naming them beats "it is in use" -- the operator can go and look.
     *
     * @param  int $variant_id
     * @param  int $limit
     * @return array
     */
    public function variant_bom_numbers($variant_id, $limit = 5)
    {
        $rows = $this->db->select('bom_no')
            ->from('abom_bom')
            ->where('variant_id', (int) $variant_id)
            ->order_by('id', 'ASC')
            ->limit((int) $limit)
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[] = $row->bom_no;
        }

        return $out;
    }

    /**
     * How many master items the build owns -- what would be removed.
     *
     * @param  int $variant_id
     * @return int
     */
    public function variant_item_count($variant_id)
    {
        return (int) $this->db->from($this->item_table)
            ->where('variant_id', (int) $variant_id)
            ->count_all_results();
    }

    /**
     * Delete a build and everything that belongs only to it.
     *
     * Refuses outright if any BOM was generated from it -- the caller is
     * expected to have checked and said so in a better message, but this
     * re-checks INSIDE the transaction because the caller's check and
     * this write are not atomic on their own.
     *
     * abom_variant_rule has ON DELETE CASCADE, so its rules go with it.
     * abom_item.variant_id has NO foreign key, so its items are removed
     * here explicitly -- without this they would survive as rows nothing
     * can reach, and reappear in item searches.
     *
     * @param  int $variant_id
     * @param  int $user_id
     * @return array ['ok'=>bool,'error'=>string,'items'=>int,'rules'=>int]
     */
    public function delete_variant($variant_id, $user_id = 0)
    {
        $variant_id = (int) $variant_id;

        $variant = $this->get_variant($variant_id);

        if (!$variant) {
            return array('ok' => false, 'error' => 'That build no longer exists.',
                         'items' => 0, 'rules' => 0);
        }

        $this->db->trans_begin();

        // Re-checked inside the transaction, not merely in the caller.
        $used = (int) $this->db->from('abom_bom')
            ->where('variant_id', $variant_id)
            ->count_all_results();

        if ($used > 0) {
            $this->db->trans_rollback();

            return array('ok' => false, 'items' => 0, 'rules' => 0,
                'error' => 'A BOM has been generated from this build since the page was '
                         . 'loaded. Nothing was deleted.');
        }

        $items = (int) $this->db->from($this->item_table)
            ->where('variant_id', $variant_id)
            ->count_all_results();

        $rules = (int) $this->db->from($this->vrule_table)
            ->where('result_variant_id', $variant_id)
            ->count_all_results();

        $this->db->where('variant_id', $variant_id)->delete($this->item_table);
        $this->db->where('id', $variant_id)->delete($this->variant_table);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return array('ok' => false, 'items' => 0, 'rules' => 0,
                'error' => 'The build could not be deleted. Nothing was changed.');
        }

        // Written before the commit so the record cannot outlive its
        // transaction, or be lost by one.
        $this->load->model('Abom_model');
        $this->Abom_model->log_audit('abom_variant', $variant_id, 'delete', array(
            'code'          => $variant->code,
            'name'          => $variant->name,
            'machine_model' => $variant->machine_model,
            'source_df'     => $variant->source_df,
            'items'         => $items,
            'rules'         => $rules,
        ), null, $user_id);

        $this->db->trans_commit();

        // The caches were built before the delete.
        $this->variant_cache = null;
        $this->vrule_cache   = null;

        return array('ok' => true, 'error' => '', 'items' => $items, 'rules' => $rules);
    }

    // -----------------------------------------------------------------
    // USER GUIDE FEEDS
    //
    // The guide renders from these rather than from prose, so editing
    // abom_plc_rule or abom_formula updates the training material too.
    // -----------------------------------------------------------------

    /**
     * Every formula with how many master items actually use it.
     *
     * @return array
     */
    public function formulas_with_usage()
    {
        $counts = array();
        $rows = $this->db->select('formula_code, COUNT(*) AS n')
            ->from($this->item_table)
            ->group_by('formula_code')
            ->get()
            ->result();
        foreach ($rows as $r) {
            $counts[$r->formula_code] = (int) $r->n;
        }

        $out = array();
        foreach ($this->get_formulas() as $code => $f) {
            $f->item_count = isset($counts[$code]) ? $counts[$code] : 0;
            $out[$code] = $f;
        }

        return $out;
    }

    /**
     * Active PLC rules with the family they select and their condition
     * rendered as a sentence.
     *
     * @return array
     */
    public function plc_rules_readable()
    {
        $families = array();
        foreach ($this->get_families() as $fam) {
            $families[(int) $fam->id] = $fam;
        }

        $out = array();
        foreach ($this->get_active_rules() as $rule) {
            $bits = array();

            if ($rule->min_axes !== null && $rule->max_axes !== null) {
                $bits[] = 'axes between ' . (int) $rule->min_axes . ' and ' . (int) $rule->max_axes;
            } elseif ($rule->min_axes !== null) {
                $bits[] = 'axes is ' . (int) $rule->min_axes . ' or more';
            } elseif ($rule->max_axes !== null) {
                $bits[] = 'axes is ' . (int) $rule->max_axes . ' or fewer';
            }

            if ($rule->min_speed !== null && $rule->max_speed !== null) {
                $bits[] = 'speed between ' . (int) $rule->min_speed . ' and ' . (int) $rule->max_speed . ' PPM';
            } elseif ($rule->min_speed !== null) {
                $bits[] = 'speed is ' . (int) $rule->min_speed . ' PPM or more';
            } elseif ($rule->max_speed !== null) {
                $bits[] = 'speed is ' . (int) $rule->max_speed . ' PPM or less';
            }

            if (!empty($rule->motion_type) && strtoupper($rule->motion_type) !== 'ANY') {
                $bits[] = 'motion type is ' . $rule->motion_type;
            }

            $fid = (int) $rule->result_family_id;

            $out[] = array(
                'priority'    => (int) $rule->priority,
                'condition'   => empty($bits) ? '' : implode(' AND ', $bits),
                'is_catch_all'=> empty($bits),
                'family'      => isset($families[$fid]) ? $families[$fid]->name : ('family ' . $fid),
                'family_code' => isset($families[$fid]) ? $families[$fid]->code : '',
                'explanation' => (string) $rule->explanation,
            );
        }

        return $out;
    }

    /**
     * Every active variant with its family, its item count and the
     * conditions that select it, rendered as sentences. Feeds the guide
     * and the generator's variant panel, so retuning abom_variant_rule
     * updates the training material too.
     *
     * @return array
     */
    public function variants_readable()
    {
        $counts = array();
        $rows = $this->db->select('variant_id, COUNT(*) AS n')
            ->from($this->item_table)
            ->where('is_active', 1)
            ->where('variant_id IS NOT NULL', null, false)
            ->group_by('variant_id')
            ->get()
            ->result();
        foreach ($rows as $r) {
            $counts[(int) $r->variant_id] = (int) $r->n;
        }

        $conditions = array();
        foreach ($this->get_active_variant_rules() as $rule) {
            $bits = array();

            if ($rule->machine_model !== null && $rule->machine_model !== '') {
                $bits[] = 'model is ' . $rule->machine_model;
            }

            if ($rule->min_axes !== null && $rule->max_axes !== null) {
                $bits[] = 'axes between ' . (int) $rule->min_axes . ' and ' . (int) $rule->max_axes;
            } elseif ($rule->min_axes !== null) {
                $bits[] = 'axes is ' . (int) $rule->min_axes . ' or more';
            } elseif ($rule->max_axes !== null) {
                $bits[] = 'axes is ' . (int) $rule->max_axes . ' or fewer';
            }

            if ($rule->min_speed !== null && $rule->max_speed !== null) {
                $bits[] = 'speed between ' . (int) $rule->min_speed . ' and ' . (int) $rule->max_speed . ' PPM';
            } elseif ($rule->min_speed !== null) {
                $bits[] = 'speed is ' . (int) $rule->min_speed . ' PPM or more';
            } elseif ($rule->max_speed !== null) {
                $bits[] = 'speed is ' . (int) $rule->max_speed . ' PPM or less';
            }

            if (!empty($rule->motion_type) && strtoupper($rule->motion_type) !== 'ANY') {
                $bits[] = 'motion type is ' . $rule->motion_type;
            }

            $vid = (int) $rule->result_variant_id;
            if (!isset($conditions[$vid])) {
                $conditions[$vid] = array();
            }

            $conditions[$vid][] = array(
                'priority'     => (int) $rule->priority,
                'condition'    => empty($bits) ? '' : implode(' AND ', $bits),
                'is_catch_all' => empty($bits),
                'explanation'  => (string) $rule->explanation,
            );
        }

        $families = $this->get_families();

        $out = array();
        foreach ($this->get_active_variants() as $id => $variant) {
            $fid = (int) $variant->plc_family_id;

            $out[] = array(
                'id'          => $id,
                'code'        => (string) $variant->code,
                'name'        => (string) $variant->name,
                'description' => (string) $variant->description,
                'model'       => (string) $variant->machine_model,
                'source_df'   => (string) $variant->source_df,
                'panel'       => (string) $variant->default_panel_location,
                'j4_units'    => (int) $variant->default_j4_units,
                'battery_qty' => (int) $variant->default_battery,
                'family'      => isset($families[$fid]) ? $families[$fid]->name : ('family ' . $fid),
                'family_code' => isset($families[$fid]) ? $families[$fid]->code : '',
                'item_count'  => isset($counts[$id]) ? $counts[$id] : 0,
                'rules'       => isset($conditions[$id]) ? $conditions[$id] : array(),
            );
        }

        return $out;
    }

    /**
     * Feature switches with how many master items each one gates.
     *
     * @return array
     */
    public function features_with_usage()
    {
        $counts = array();
        $rows = $this->db->select('feature_code, COUNT(*) AS n')
            ->from($this->item_table)
            ->where('feature_code IS NOT NULL', null, false)
            ->group_by('feature_code')
            ->get()
            ->result();
        foreach ($rows as $r) {
            $counts[$r->feature_code] = (int) $r->n;
        }

        $out = array();
        foreach ($this->get_features() as $f) {
            $f->item_count = isset($counts[$f->code]) ? $counts[$f->code] : 0;
            $out[] = $f;
        }

        return $out;
    }

    /** @return array plain list of families, for the guide */
    public function families()
    {
        return $this->get_families();
    }

    public function count_rules()
    {
        return (int) $this->db->where('is_active', 1)
            ->count_all_results($this->rule_table);
    }
}
