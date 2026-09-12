<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_config_model
 *
 * Read/write over the module's six CONFIGURATION tables — the ones that
 * decide which items exist, which machines they land on, and how their
 * quantities are worked out:
 *
 *   abom_variant        build variants
 *   abom_variant_rule   which build a configuration selects
 *   abom_plc_rule       which PLC family a configuration selects
 *   abom_section        printed groupings, per family
 *   abom_feature        optional feature gates
 *   abom_formula        quantity formulas (metadata only — see below)
 *
 * WHY ONE MODEL AND NOT SIX
 * -------------------------
 * These tables are small, structurally similar, and each needs exactly
 * the same treatment: whitelist the columns, validate, check references
 * before deleting, log the diff. Six hand-written CRUD models would be
 * six places for that treatment to be applied inconsistently, and the
 * one that got it wrong would be the one nobody looked at. The DESCRIPTOR
 * below is the single source of truth; the generic read/write paths are
 * driven entirely from it.
 *
 * THIS IS THE MOST CONSEQUENTIAL DATA IN THE MODULE
 * -------------------------------------------------
 * abom_item decides what a build contains. These tables decide which
 * build a machine gets at all. A wrong selection rule does not produce a
 * visibly broken BOM — it produces a plausible one for the wrong
 * machine. So every write is audited, and every destructive action is
 * refused while anything still points at the row.
 *
 * NOTHING HERE TOUCHES A SAVED BOM. abom_bom_line is a frozen snapshot
 * and is never read or written by this model.
 *
 * PHP 7.4 compatible.
 */
class Abom_config_model extends CI_Model
{
    /**
     * The six tables, their editable columns, and how each column
     * behaves. Field types:
     *
     *   text     trimmed string, 'max' enforced
     *   textarea same, rendered larger
     *   int      integer, 'min'/'max' enforced
     *   nullint  integer or NULL — an empty box means "unbounded", which
     *            is what a NULL bound means to the rule evaluator
     *   bool     0/1 checkbox
     *   enum     one of 'options'
     *   ref      foreign key; 'ref' names the descriptor to draw from
     *
     * @return array
     */
    public function descriptors()
    {
        return array(

            // ---------------------------------------------------------
            'variant' => array(
                'table'    => 'abom_variant',
                'label'    => 'Build variant',
                'plural'   => 'Build variants',
                'blurb'    => 'One buildable configuration of a PLC family — which amplifier '
                            . 'range, which HMI, which panel. Master items belong to a build, '
                            . 'and a generated BOM contains exactly one build\'s items.',
                'order'    => 'sort_order ASC, id ASC',
                'display'  => 'code',
                'columns'  => array('code', 'name', 'machine_model', 'source_df', 'is_active'),
                'active'   => 'is_active',
                'deletable'=> true,
                'fields'   => array(
                    'code' => array('type' => 'text', 'label' => 'Code', 'required' => true, 'max' => 24,
                        'help' => 'Short, stable, unique. Shown on the sheet and in the header chips — e.g. FX5-JE.'),
                    'name' => array('type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 96),
                    'description' => array('type' => 'textarea', 'label' => 'Description', 'max' => 255,
                        'help' => 'Shown to the engineer under the build badge in the generator.'),
                    'plc_family_id' => array('type' => 'ref', 'ref' => 'family', 'label' => 'PLC family',
                        'required' => true,
                        'help' => 'Which CPU platform. Items and sections must belong to the same family.'),
                    'machine_model' => array('type' => 'text', 'label' => 'Machine model', 'max' => 32,
                        'help' => 'Blank = any model. Recorded here for reference; what actually SELECTS '
                                . 'the build is the rules below.'),
                    'source_df' => array('type' => 'text', 'label' => 'Source DFs', 'max' => 96,
                        'help' => 'Which reference BOMs this build was extracted from. Provenance.'),
                    'default_j4_units' => array('type' => 'int', 'label' => 'Default MR-J4 units', 'min' => 0, 'max' => 99),
                    'default_battery' => array('type' => 'int', 'label' => 'Default battery qty', 'min' => 0, 'max' => 99),
                    'default_panel_location' => array('type' => 'text', 'label' => 'Panel location', 'max' => 64),
                    'sort_order' => array('type' => 'int', 'label' => 'Sort order', 'min' => 0, 'max' => 255),
                    'is_active' => array('type' => 'bool', 'label' => 'Active',
                        'help' => 'Inactive builds are never selected and cannot be chosen as an override.'),
                ),
            ),

            // ---------------------------------------------------------
            'variant_rule' => array(
                'table'    => 'abom_variant_rule',
                'label'    => 'Build selection rule',
                'plural'   => 'Build selection rules',
                'blurb'    => 'Evaluated AFTER the PLC family, in priority order, first match wins. '
                            . 'A blank bound means unbounded. A rule needs more than one row when its '
                            . 'condition is an OR — "15 axes OR 180 PPM" is two rules pointing at the '
                            . 'same build.',
                'order'    => 'priority ASC, id ASC',
                'display'  => 'explanation',
                'columns'  => array('priority', 'plc_family_id', 'machine_model', 'result_variant_id', 'is_active'),
                'active'   => 'is_active',
                'deletable'=> true,
                'fields'   => array(
                    'priority' => array('type' => 'int', 'label' => 'Priority', 'required' => true,
                        'min' => 1, 'max' => 9999,
                        'help' => 'Lowest number wins. Leave gaps (10, 20, 30) so a rule can be inserted between two later.'),
                    'plc_family_id' => array('type' => 'ref', 'ref' => 'family', 'label' => 'PLC family',
                        'nullable' => true, 'help' => 'Blank = any family.'),
                    'machine_model' => array('type' => 'text', 'label' => 'Machine model', 'max' => 32,
                        'help' => 'Blank = ANY model. Leaving this blank on a catch-all rule is how a '
                                . 'machine the reference data does not describe quietly inherits '
                                . 'another model\'s build.'),
                    'min_axes' => array('type' => 'nullint', 'label' => 'Min axes', 'min' => 0, 'max' => 99),
                    'max_axes' => array('type' => 'nullint', 'label' => 'Max axes', 'min' => 0, 'max' => 99),
                    'min_speed' => array('type' => 'nullint', 'label' => 'Min speed (PPM)', 'min' => 0, 'max' => 999),
                    'max_speed' => array('type' => 'nullint', 'label' => 'Max speed (PPM)', 'min' => 0, 'max' => 999),
                    'motion_type' => array('type' => 'enum', 'label' => 'Motion type',
                        'options' => array('ANY' => 'Any', 'Intermittent' => 'Intermittent', 'Continuous' => 'Continuous')),
                    'result_variant_id' => array('type' => 'ref', 'ref' => 'variant', 'label' => 'Selects build',
                        'required' => true),
                    'explanation' => array('type' => 'textarea', 'label' => 'Explanation', 'required' => true, 'max' => 255,
                        'help' => 'Printed on the sheet and shown in the generator as the reason this build '
                                . 'was chosen. Write it for whoever has to justify the BOM later.'),
                    'is_active' => array('type' => 'bool', 'label' => 'Active'),
                ),
            ),

            // ---------------------------------------------------------
            'plc_rule' => array(
                'table'    => 'abom_plc_rule',
                'label'    => 'PLC family rule',
                'plural'   => 'PLC family rules',
                'blurb'    => 'Chooses the CPU platform, before the build. Same contract: priority '
                            . 'order, first match wins, blank bound = unbounded. Keep the catch-all '
                            . 'last — with no rule matching, generation throws.',
                'order'    => 'priority ASC, id ASC',
                'display'  => 'explanation',
                'columns'  => array('priority', 'machine_model', 'min_axes', 'min_speed', 'result_family_id', 'is_active'),
                'active'   => 'is_active',
                'deletable'=> true,
                'fields'   => array(
                    'priority' => array('type' => 'int', 'label' => 'Priority', 'required' => true, 'min' => 1, 'max' => 9999),
                    'machine_model' => array('type' => 'text', 'label' => 'Machine model', 'max' => 32,
                        'help' => 'Blank = ANY model, which is what the four original rules mean. Name a '
                                . 'model only to carve out an exception for it — DF-1883 runs 11 axes on '
                                . 'FX5 with a second motion card, which no general rule should describe.'),
                    'min_axes' => array('type' => 'nullint', 'label' => 'Min axes', 'min' => 0, 'max' => 99),
                    'max_axes' => array('type' => 'nullint', 'label' => 'Max axes', 'min' => 0, 'max' => 99),
                    'min_speed' => array('type' => 'nullint', 'label' => 'Min speed (PPM)', 'min' => 0, 'max' => 999),
                    'max_speed' => array('type' => 'nullint', 'label' => 'Max speed (PPM)', 'min' => 0, 'max' => 999),
                    'motion_type' => array('type' => 'enum', 'label' => 'Motion type',
                        'options' => array('ANY' => 'Any', 'Intermittent' => 'Intermittent', 'Continuous' => 'Continuous')),
                    'result_family_id' => array('type' => 'ref', 'ref' => 'family', 'label' => 'Selects family',
                        'required' => true),
                    'explanation' => array('type' => 'textarea', 'label' => 'Explanation', 'required' => true, 'max' => 255),
                    'is_active' => array('type' => 'bool', 'label' => 'Active'),
                ),
            ),

            // ---------------------------------------------------------
            'section' => array(
                'table'    => 'abom_section',
                'label'    => 'Section',
                'plural'   => 'Sections',
                'blurb'    => 'The grouping headings on the printed sheet, and the order items appear in. '
                            . 'Scoped to a PLC family — an item\'s section must belong to the same family '
                            . 'as its build.',
                'order'    => 'plc_family_id ASC, sort_order ASC',
                'display'  => 'name',
                'columns'  => array('code', 'name', 'plc_family_id', 'sort_order'),
                'active'   => null,
                'deletable'=> true,
                'fields'   => array(
                    'plc_family_id' => array('type' => 'ref', 'ref' => 'family', 'label' => 'PLC family', 'required' => true),
                    'code' => array('type' => 'text', 'label' => 'Code', 'required' => true, 'max' => 8,
                        'help' => 'Unique within the family — e.g. F1, R3.'),
                    'name' => array('type' => 'text', 'label' => 'Heading', 'required' => true, 'max' => 128,
                        'help' => 'Printed verbatim as the section header.'),
                    'sort_order' => array('type' => 'int', 'label' => 'Sort order', 'required' => true, 'min' => 0, 'max' => 255,
                        'help' => 'Decides the printed order of the sections, and therefore of the line numbers.'),
                ),
            ),

            // ---------------------------------------------------------
            'feature' => array(
                'table'    => 'abom_feature',
                'label'    => 'Feature gate',
                'plural'   => 'Feature gates',
                'blurb'    => 'Optional machine features. An item marked optional and gated by a feature '
                            . 'is left off the BOM ENTIRELY when the feature is switched off — not shown '
                            . 'with quantity zero, because procurement may order anything visible.',
                'order'    => 'sort_order ASC, code ASC',
                'display'  => 'label',
                'key'      => 'code',
                'columns'  => array('code', 'label', 'default_on', 'sort_order'),
                'active'   => null,
                'deletable'=> true,
                'fields'   => array(
                    'code' => array('type' => 'text', 'label' => 'Code', 'required' => true, 'max' => 32,
                        'key' => true,
                        'help' => 'The key items are gated on — e.g. feat_perf. Cannot be changed once items use it.'),
                    'label' => array('type' => 'text', 'label' => 'Label', 'required' => true, 'max' => 128,
                        'help' => 'The checkbox caption in the generator, and the header chip when it is on.'),
                    'help_text' => array('type' => 'textarea', 'label' => 'Help text', 'max' => 255),
                    'default_on' => array('type' => 'bool', 'label' => 'On by default'),
                    'sort_order' => array('type' => 'int', 'label' => 'Sort order', 'min' => 0, 'max' => 255),
                ),
            ),

            // ---------------------------------------------------------
            // METADATA ONLY. A formula CODE is dispatched on by
            // Abom_engine::calc_qty(): a code the engine has no case for
            // falls through to `default` and silently behaves as FIXED.
            // So rows cannot be added or removed here — a new formula is
            // an engine change, and offering a button that appears to
            // create one would be offering a quantity rule that quietly
            // does nothing.
            'formula' => array(
                'table'     => 'abom_formula',
                'label'     => 'Quantity formula',
                'plural'    => 'Quantity formulas',
                'blurb'     => 'How a line quantity is worked out. The wording is editable; the codes are '
                             . 'not. Each code is dispatched on in Abom_engine — a code the engine does '
                             . 'not know silently behaves as a fixed quantity, so ADDING one is a code '
                             . 'change, not a data change.',
                'order'     => 'sort_order ASC, code ASC',
                'display'   => 'name',
                'key'       => 'code',
                'columns'   => array('code', 'name', 'needs_review', 'sort_order'),
                'active'    => null,
                'deletable' => false,
                'addable'   => false,
                'fields'    => array(
                    'name' => array('type' => 'text', 'label' => 'Name', 'required' => true, 'max' => 64),
                    'description' => array('type' => 'textarea', 'label' => 'Description', 'required' => true, 'max' => 255,
                        'help' => 'Shown on the guide and under the formula picker on the master-item form.'),
                    'needs_review' => array('type' => 'bool', 'label' => 'Needs engineer confirmation',
                        'help' => 'Lines using this formula must be confirmed before the BOM can be submitted.'),
                    'sort_order' => array('type' => 'int', 'label' => 'Sort order', 'min' => 0, 'max' => 255),
                ),
            ),
        );
    }

    /**
     * @param  string $entity
     * @return array|null
     */
    public function descriptor($entity)
    {
        $all = $this->descriptors();

        return isset($all[$entity]) ? $all[$entity] : null;
    }

    /** The primary key column. Every table uses `id` except the two keyed by code. */
    public function key_column($entity)
    {
        $d = $this->descriptor($entity);

        return ($d && isset($d['key'])) ? $d['key'] : 'id';
    }

    // -----------------------------------------------------------------
    // READ
    // -----------------------------------------------------------------

    public function rows($entity)
    {
        $d = $this->descriptor($entity);

        if (!$d) {
            return array();
        }

        return $this->db->from($d['table'])->order_by($d['order'])->get()->result();
    }

    public function row($entity, $id)
    {
        $d = $this->descriptor($entity);

        if (!$d) {
            return null;
        }

        $row = $this->db->from($d['table'])
            ->where($this->key_column($entity), $id)
            ->limit(1)->get()->row();

        return $row ? $row : null;
    }

    /**
     * Options for a 'ref' field: id => label.
     *
     * 'family' is not a descriptor of its own — abom_plc_family is two
     * fixed rows that the module does not create or delete — so it is
     * resolved here rather than given a CRUD screen that would only ever
     * be used to break something.
     *
     * @return array
     */
    public function ref_options($ref)
    {
        if ($ref === 'family') {
            $out = array();
            foreach ($this->db->from('abom_plc_family')->order_by('sort_order')->get()->result() as $r) {
                $out[(int) $r->id] = $r->name;
            }
            return $out;
        }

        $d = $this->descriptor($ref);

        if (!$d) {
            return array();
        }

        $out = array();
        foreach ($this->rows($ref) as $r) {
            $key   = $this->key_column($ref);
            $label = $r->{$d['display']};

            if ($ref === 'variant') {
                $label = $r->code . ' — ' . $r->name;
                if (isset($r->is_active) && !$r->is_active) {
                    $label .= '  (inactive)';
                }
            }

            $out[$r->$key] = $label;
        }

        return $out;
    }

    // -----------------------------------------------------------------
    // WRITE
    // -----------------------------------------------------------------

    /**
     * Normalises a posted payload to the descriptor's fields, and
     * nothing else. A stray field in the request never reaches the
     * table.
     *
     * @return array
     */
    public function payload($entity, $post, $is_new)
    {
        $d    = $this->descriptor($entity);
        $data = array();

        foreach ($d['fields'] as $field => $spec) {
            // A code-keyed table's key is settable on create only —
            // changing it would orphan every row referencing it.
            if (!empty($spec['key']) && !$is_new) {
                continue;
            }

            $raw = isset($post[$field]) ? $post[$field] : null;

            switch ($spec['type']) {
                case 'bool':
                    $data[$field] = !empty($raw) ? 1 : 0;
                    break;

                case 'int':
                    $data[$field] = (int) $raw;
                    break;

                case 'nullint':
                    // '' means UNBOUNDED, and that is a different thing
                    // from 0. A rule with min_axes 0 matches everything
                    // with a non-negative axis count; a rule with NULL
                    // does not test axes at all.
                    $data[$field] = ($raw === null || trim((string) $raw) === '')
                        ? null : (int) $raw;
                    break;

                case 'ref':
                    $v = (int) $raw;
                    $data[$field] = ($v > 0) ? $v : (!empty($spec['nullable']) ? null : 0);
                    break;

                default:
                    $v = trim((string) $raw);
                    if (isset($spec['max'])) {
                        $v = mb_substr($v, 0, (int) $spec['max']);
                    }
                    // A blank optional string is stored NULL, not '', so
                    // "not set" reads the same everywhere.
                    $data[$field] = ($v === '' && empty($spec['required'])) ? null : $v;
            }
        }

        return $data;
    }

    /**
     * @return array  field => message
     */
    public function validate($entity, array $data, $id = null)
    {
        $d      = $this->descriptor($entity);
        $errors = array();

        foreach ($d['fields'] as $field => $spec) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $value = $data[$field];

            if (!empty($spec['required'])
                && ($value === null || $value === '' || ($spec['type'] === 'ref' && (int) $value <= 0))) {
                $errors[$field] = $spec['label'] . ' is required.';
                continue;
            }

            if (in_array($spec['type'], array('int', 'nullint'), true) && $value !== null) {
                if (isset($spec['min']) && (int) $value < (int) $spec['min']) {
                    $errors[$field] = $spec['label'] . ' must be ' . (int) $spec['min'] . ' or more.';
                }
                if (isset($spec['max']) && (int) $value > (int) $spec['max']) {
                    $errors[$field] = $spec['label'] . ' must be ' . (int) $spec['max'] . ' or less.';
                }
            }

            if ($spec['type'] === 'enum' && $value !== null
                && !array_key_exists((string) $value, $spec['options'])) {
                $errors[$field] = 'Unknown ' . strtolower($spec['label']) . '.';
            }

            if ($spec['type'] === 'ref' && !empty($value)) {
                if (!$this->ref_exists($spec['ref'], $value)) {
                    $errors[$field] = $spec['label'] . ' does not exist.';
                }
            }
        }

        // A min above its max matches nothing, and does so silently —
        // the rule simply never fires and the machine falls through to
        // whatever is next.
        foreach (array(array('min_axes', 'max_axes', 'axis'), array('min_speed', 'max_speed', 'speed')) as $pair) {
            list($lo, $hi, $what) = $pair;

            if (isset($data[$lo], $data[$hi]) && $data[$lo] !== null && $data[$hi] !== null
                && (int) $data[$lo] > (int) $data[$hi]) {
                $errors[$hi] = 'Maximum ' . $what . ' is below the minimum, so this rule can never match.';
            }
        }

        // Unique key columns.
        foreach (array('code') as $unique) {
            if (!isset($data[$unique]) || $data[$unique] === null) {
                continue;
            }

            $this->db->from($d['table'])->where($unique, $data[$unique]);

            if ($id !== null) {
                $this->db->where($this->key_column($entity) . ' !=', $id);
            }

            // abom_section's code is unique WITHIN a family, not globally.
            if ($entity === 'section' && isset($data['plc_family_id'])) {
                $this->db->where('plc_family_id', (int) $data['plc_family_id']);
            }

            if ($this->db->count_all_results() > 0) {
                $errors[$unique] = 'That code is already in use.';
            }
        }

        return $errors;
    }

    private function ref_exists($ref, $id)
    {
        if ($ref === 'family') {
            return $this->db->from('abom_plc_family')->where('id', (int) $id)->count_all_results() > 0;
        }

        $d = $this->descriptor($ref);

        return $d
            ? $this->db->from($d['table'])->where($this->key_column($ref), $id)->count_all_results() > 0
            : false;
    }

    /**
     * @return mixed  new key, or 0/'' on failure
     */
    public function create($entity, array $data, $user_id = 0)
    {
        $d = $this->descriptor($entity);

        $this->db->insert($d['table'], $data);

        $key = ($this->key_column($entity) === 'id')
            ? (int) $this->db->insert_id()
            : $data[$this->key_column($entity)];

        $this->load->model('Abom_model');
        $this->Abom_model->log_audit($d['table'], is_numeric($key) ? (int) $key : 0,
            'create', null, $data, $user_id);

        return $key;
    }

    /**
     * Writes an update and logs only what changed.
     *
     * @return bool
     */
    public function update($entity, $id, array $data, $user_id = 0)
    {
        $d      = $this->descriptor($entity);
        $before = $this->row($entity, $id);

        if (!$before) {
            return false;
        }

        $old = array();
        $new = array();

        foreach ($data as $field => $value) {
            $was = isset($before->$field) ? $before->$field : null;

            if ((string) $was !== (string) $value) {
                $old[$field] = $was;
                $new[$field] = $value;
            }
        }

        if (empty($new)) {
            return true;
        }

        $this->db->where($this->key_column($entity), $id)->update($d['table'], $data);

        $this->load->model('Abom_model');
        $this->Abom_model->log_audit($d['table'], is_numeric($id) ? (int) $id : 0,
            'update', $old, $new, $user_id);

        return true;
    }

    /**
     * What still points at this row.
     *
     * Deletion is refused while anything does — not because the database
     * would necessarily stop it (abom_item.variant_id carries no foreign
     * key, by design) but because the consequences are silent: items
     * orphaned from their build stop appearing on every BOM, and a rule
     * pointing at a deleted variant makes generation fall through to the
     * next rule and quote a different machine.
     *
     * @return array  human-readable reasons; empty means safe to delete
     */
    public function blockers($entity, $id)
    {
        $out = array();

        $count = function ($table, $column, $value) {
            return (int) $this->db->from($table)->where($column, $value)->count_all_results();
        };

        if ($entity === 'variant') {
            $n = $count('abom_item', 'variant_id', (int) $id);
            if ($n) { $out[] = $n . ' master item' . ($n === 1 ? '' : 's') . ' belong to this build'; }

            $n = $count('abom_variant_rule', 'result_variant_id', (int) $id);
            if ($n) { $out[] = $n . ' selection rule' . ($n === 1 ? '' : 's') . ' point at it'; }

            $n = $count('abom_bom', 'variant_id', (int) $id);
            if ($n) { $out[] = $n . ' saved BOM' . ($n === 1 ? '' : 's') . ' record it'; }
        }

        if ($entity === 'section') {
            $n = $count('abom_item', 'section_id', (int) $id);
            if ($n) { $out[] = $n . ' master item' . ($n === 1 ? '' : 's') . ' use this section'; }
        }

        if ($entity === 'feature') {
            $n = $count('abom_item', 'feature_code', $id);
            if ($n) { $out[] = $n . ' master item' . ($n === 1 ? '' : 's') . ' are gated on it'; }
        }

        if ($entity === 'plc_rule' || $entity === 'variant_rule') {
            // A rule is free to delete — nothing references one. The
            // caller still warns, because removing the catch-all is how
            // generation starts throwing.
            return array();
        }

        return $out;
    }

    /**
     * @return bool
     */
    public function delete($entity, $id, $user_id = 0)
    {
        $d      = $this->descriptor($entity);
        $before = $this->row($entity, $id);

        if (!$before || empty($d['deletable'])) {
            return false;
        }

        $this->db->where($this->key_column($entity), $id)->delete($d['table']);

        $this->load->model('Abom_model');
        $this->Abom_model->log_audit($d['table'], is_numeric($id) ? (int) $id : 0,
            'delete', (array) $before, null, $user_id);

        return true;
    }

    /**
     * Row counts per entity, for the hub.
     *
     * @return array
     */
    public function counts()
    {
        $out = array();

        foreach ($this->descriptors() as $entity => $d) {
            $total = (int) $this->db->count_all_results($d['table']);

            $active = $total;
            if (!empty($d['active'])) {
                $active = (int) $this->db->from($d['table'])->where($d['active'], 1)->count_all_results();
            }

            $out[$entity] = array('total' => $total, 'active' => $active);
        }

        return $out;
    }
}
