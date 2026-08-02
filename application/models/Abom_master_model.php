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

    /** Read only by the user-guide feeds below, for usage counts. */
    private $item_table    = 'abom_item';

    /** Simple per-request caches — generate() is called on every keystroke. */
    private $family_cache  = null;
    private $rule_cache    = null;
    private $section_cache = array();

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
