<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_item_model
 *
 * Read access to the 71 master line items. Returns rows joined to their
 * section so Abom_engine can emit section_name / section_sort without a
 * second query.
 *
 * Read paths only at this stage (spec section 9, build order step 2).
 * CRUD and audit logging arrive with the master admin screens in build
 * order step 8.
 *
 * This model NEVER computes a quantity. Quantity logic lives only in
 * Abom_engine (spec section 10).
 *
 * PHP 7.4 compatible.
 */
class Abom_item_model extends CI_Model
{
    private $item_table    = 'abom_item';
    private $section_table = 'abom_section';
    private $variant_table = 'abom_variant';

    /**
     * Every active master item for one PLC family, ordered by section
     * sort_order then item id — this ordering IS the BOM line order.
     *
     * @param  int $family_id
     * @return array
     */
    public function get_by_family($family_id)
    {
        return $this->base_select()
            ->where('i.plc_family_id', (int) $family_id)
            ->where('i.is_active', 1)
            ->order_by('s.sort_order', 'ASC')
            ->order_by('i.id', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Every active master item for one BUILD VARIANT, in printed order.
     *
     * This — not get_by_family() — is what Abom_engine generates from.
     * A family holds more than one buildable machine: the FX5 family
     * covers both the MR-JE range (5-7 axes) and the MR-J4 range (8
     * axes), and emitting both onto one purchasable document would have
     * procurement order two sets of drives.
     *
     * An item with a NULL variant_id is NEVER returned. That is the safe
     * failure for a master item that has not been assigned to a build:
     * left off every BOM and visible as unassigned in the admin list,
     * rather than silently ordered on every machine.
     *
     * @param  int $variant_id
     * @return array
     */
    public function get_by_variant($variant_id)
    {
        return $this->base_select()
            ->where('i.variant_id', (int) $variant_id)
            ->where('i.is_active', 1)
            ->order_by('s.sort_order', 'ASC')
            ->order_by('i.id', 'ASC')
            ->get()
            ->result();
    }

    /**
     * @return array  [variant_id => count]  (NULL variant keyed as 0)
     */
    public function count_by_variant()
    {
        $rows = $this->db->select('variant_id, COUNT(*) AS total')
            ->from($this->item_table)
            ->group_by('variant_id')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[(int) $row->variant_id] = (int) $row->total;
        }

        return $out;
    }

    /**
     * @param  int $item_id
     * @return object|null
     */
    public function get_item($item_id)
    {
        $row = $this->base_select()
            ->where('i.id', (int) $item_id)
            ->limit(1)
            ->get()
            ->row();

        return $row ? $row : null;
    }

    /**
     * Master-item admin listing. Filters are all optional.
     *
     * @param  array $filters  family_id, section_id, severity, formula_code, search
     * @return array
     */
    public function get_all($filters = array())
    {
        $this->base_select();

        if (!empty($filters['family_id'])) {
            $this->db->where('i.plc_family_id', (int) $filters['family_id']);
        }

        if (!empty($filters['variant_id'])) {
            $this->db->where('i.variant_id', (int) $filters['variant_id']);
        }

        if (!empty($filters['section_id'])) {
            $this->db->where('i.section_id', (int) $filters['section_id']);
        }

        if (!empty($filters['severity'])) {
            $this->db->where('i.issue_severity', $filters['severity']);
        }

        if (!empty($filters['formula_code'])) {
            $this->db->where('i.formula_code', $filters['formula_code']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('i.is_active', (int) $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $this->db->group_start()
                ->like('i.description', $search)
                ->or_like('i.part_no', $search)
                ->or_like('i.erp_code', $search)
                ->group_end();
        }

        return $this->db
            ->order_by('i.plc_family_id', 'ASC')
            ->order_by('s.sort_order', 'ASC')
            ->order_by('i.id', 'ASC')
            ->get()
            ->result();
    }

    // -----------------------------------------------------------------
    // WRITE PATHS  (master-item admin)
    //
    // THE MOST DANGEROUS SURFACE IN THE MODULE. abom_item is the seed
    // every BOM is generated from: an edit here changes what every
    // FUTURE BOM contains. It does NOT change any existing one —
    // abom_bom_line is a frozen snapshot and nothing below writes to it
    // — but a wrong quantity formula or a wrong variant assignment
    // propagates silently into every sheet generated afterwards.
    //
    // So: every write is validated against the lookup tables before it
    // lands, and every write logs its before-and-after to abom_audit_log.
    // "Who changed this ERP code" is the question this table exists to
    // answer (abom_001.sql section 11).
    // -----------------------------------------------------------------

    /**
     * The editable columns, and nothing else.
     *
     * A whitelist rather than "whatever was posted": id, created_at and
     * anything added to the table later must never be settable from a
     * form, and a stray field in the request must not reach the insert.
     *
     * @return array
     */
    public function editable_fields()
    {
        return array(
            'erp_code', 'description', 'part_no', 'manufacturer',
            'base_qty', 'formula_code', 'variant_id', 'section_id',
            'is_optional', 'feature_code', 'usage_remark', 'data_issue',
            'issue_severity', 'panel_location', 'uom', 'source_df', 'is_active',
        );
    }

    /**
     * Validates one item payload against the lookup tables.
     *
     * Referential checks are done here rather than left to the foreign
     * keys because a caught FK is a 500 with a stack trace, and the
     * person filling in this form needs a sentence naming the field.
     *
     * @param  array $data
     * @return array  field => message; empty when valid
     */
    public function validate($data)
    {
        $errors = array();

        if (trim((string) $data['description']) === '') {
            $errors['description'] = 'Description is required.';
        }

        if ((int) $data['base_qty'] < 0 || (int) $data['base_qty'] > 65535) {
            $errors['base_qty'] = 'Base quantity must be between 0 and 65535.';
        }

        if (trim((string) $data['uom']) === '') {
            $errors['uom'] = 'UOM is required.';
        }

        $formula = $this->db->from('abom_formula')
            ->where('code', (string) $data['formula_code'])->count_all_results();
        if ($formula === 0) {
            $errors['formula_code'] = 'Unknown quantity formula.';
        }

        $variant = $this->db->select('id, plc_family_id')->from('abom_variant')
            ->where('id', (int) $data['variant_id'])->limit(1)->get()->row();
        if (!$variant) {
            $errors['variant_id'] = 'Choose the build this item belongs to.';
        }

        $section = $this->db->select('id, plc_family_id')->from('abom_section')
            ->where('id', (int) $data['section_id'])->limit(1)->get()->row();
        if (!$section) {
            $errors['section_id'] = 'Choose a section.';
        }

        // A section belongs to a PLC family, and so does a variant. An
        // item in a section from the other family would print under a
        // heading that does not exist in its own build, and would sort
        // into the wrong place on every sheet.
        if ($variant && $section && (int) $variant->plc_family_id !== (int) $section->plc_family_id) {
            $errors['section_id'] = 'That section belongs to the other PLC family. '
                . 'Pick a section from the same family as the build.';
        }

        if (!empty($data['feature_code'])) {
            $feature = $this->db->from('abom_feature')
                ->where('code', (string) $data['feature_code'])->count_all_results();
            if ($feature === 0) {
                $errors['feature_code'] = 'Unknown feature gate.';
            }
        }

        // A feature gate on a non-optional item does nothing: the engine
        // only consults feature_code when is_optional is set. Silently
        // ignoring it would leave someone convinced they had gated a
        // line that in fact appears on every BOM.
        if (!empty($data['feature_code']) && empty($data['is_optional'])) {
            $errors['is_optional'] = 'A feature gate only applies to an optional item. '
                . 'Tick Optional, or clear the feature gate.';
        }

        $severities = array('none', 'review', 'no_erp', 'conflict');
        if (!in_array((string) $data['issue_severity'], $severities, true)) {
            $errors['issue_severity'] = 'Unknown severity.';
        }

        return $errors;
    }

    /**
     * Normalises a posted payload into the columns, deriving what the
     * caller must not set by hand.
     *
     * @param  array $post
     * @return array
     */
    public function payload($post)
    {
        $str = function ($v, $limit) {
            $v = trim((string) $v);
            return $v === '' ? null : mb_substr($v, 0, $limit);
        };

        $variant_id = (int) (isset($post['variant_id']) ? $post['variant_id'] : 0);

        // plc_family_id is DERIVED from the variant, never posted. The
        // two disagreeing is what put an iQ-R build under an FX5 heading
        // on ABOM-5.
        $family_id = 0;
        $variant   = $this->db->select('plc_family_id')->from('abom_variant')
            ->where('id', $variant_id)->limit(1)->get()->row();
        if ($variant) {
            $family_id = (int) $variant->plc_family_id;
        }

        return array(
            'erp_code'       => $str(isset($post['erp_code']) ? $post['erp_code'] : '', 32),
            'description'    => (string) mb_substr(trim((string) $post['description']), 0, 255),
            'part_no'        => (string) mb_substr(trim((string) (isset($post['part_no']) ? $post['part_no'] : '')), 0, 96),
            'manufacturer'   => (string) mb_substr(trim((string) (isset($post['manufacturer']) ? $post['manufacturer'] : 'MITSUBISHI')), 0, 64),
            'base_qty'       => (int) (isset($post['base_qty']) ? $post['base_qty'] : 1),
            'formula_code'   => (string) (isset($post['formula_code']) ? $post['formula_code'] : 'FIXED'),
            'plc_family_id'  => $family_id,
            'variant_id'     => $variant_id,
            'section_id'     => (int) (isset($post['section_id']) ? $post['section_id'] : 0),
            'is_optional'    => !empty($post['is_optional']) ? 1 : 0,
            'feature_code'   => $str(isset($post['feature_code']) ? $post['feature_code'] : '', 32),
            'usage_remark'   => $str(isset($post['usage_remark']) ? $post['usage_remark'] : '', 255),
            'data_issue'     => $str(isset($post['data_issue']) ? $post['data_issue'] : '', 255),
            'issue_severity' => (string) (isset($post['issue_severity']) ? $post['issue_severity'] : 'none'),
            'panel_location' => $str(isset($post['panel_location']) ? $post['panel_location'] : '', 64),
            'uom'            => (string) mb_substr(trim((string) (isset($post['uom']) ? $post['uom'] : 'NOS')), 0, 16),
            'source_df'      => $str(isset($post['source_df']) ? $post['source_df'] : '', 32),
            'is_active'      => !empty($post['is_active']) ? 1 : 0,
        );
    }

    /**
     * @return int  new item id, 0 on failure
     */
    public function create(array $data, $user_id = 0)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = $data['created_at'];

        $this->db->insert($this->item_table, $data);
        $id = (int) $this->db->insert_id();

        if ($id > 0) {
            $this->load->model('Abom_model');
            $this->Abom_model->log_audit('abom_item', $id, 'create', null, $data, $user_id);
        }

        return $id;
    }

    /**
     * Writes an update and logs ONLY the fields that actually changed.
     *
     * A diff rather than a full before/after: an audit row listing
     * eighteen unchanged columns buries the one that moved, which is the
     * only thing anybody reads it for.
     *
     * @return bool
     */
    public function update($item_id, array $data, $user_id = 0)
    {
        $before = $this->get_item($item_id);

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
            return true;                     // nothing changed; no audit noise
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', (int) $item_id)->update($this->item_table, $data);

        $this->load->model('Abom_model');
        $this->Abom_model->log_audit('abom_item', (int) $item_id, 'update', $old, $new, $user_id);

        return true;
    }

    /**
     * Activates or deactivates an item.
     *
     * DEACTIVATION, NOT DELETION. abom_bom_line.item_id points at these
     * rows for provenance, and a deleted row would leave every BOM ever
     * generated from it pointing at nothing. An inactive item is simply
     * never generated again — get_by_variant() filters on is_active —
     * while every BOM that already contains it still renders and prints
     * exactly as it did.
     *
     * @return bool
     */
    public function set_active($item_id, $active, $user_id = 0)
    {
        $before = $this->get_item($item_id);

        if (!$before) {
            return false;
        }

        $active = $active ? 1 : 0;

        $this->db->where('id', (int) $item_id)->update($this->item_table, array(
            'is_active'  => $active,
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        $this->load->model('Abom_model');
        $this->Abom_model->log_audit('abom_item', (int) $item_id,
            $active ? 'activate' : 'deactivate',
            array('is_active' => (int) $before->is_active),
            array('is_active' => $active),
            $user_id);

        return true;
    }

    /**
     * Bulk-saves the item sheet of ONE BUILD.
     *
     * The counterpart of Abom_model::save_lines(), which does the same
     * job for one saved BOM. The difference matters:
     *
     *   save_lines()  edits a FROZEN DOCUMENT. Nothing else sees it.
     *   save_build()  edits MASTER DATA. Every BOM generated from this
     *                 build from now on inherits the change, and no BOM
     *                 already saved does — abom_bom_line holds its own
     *                 copy of every line and is never touched here.
     *
     * Rows are matched by item id. A row with id 0 is a new item and is
     * created against this build. A row that is not sent is NOT deleted:
     * it is retired (is_active = 0), because abom_bom_line.item_id points
     * at these rows for provenance and a delete would leave every BOM
     * ever generated from it pointing at nothing.
     *
     * Validation is per row and the whole save is refused if any row
     * fails — a half-applied edit to the seed every BOM is built from is
     * worse than none.
     *
     * @param  int   $variant_id
     * @param  array $rows
     * @param  int   $user_id
     * @return array ['ok'=>bool,'errors'=>[id=>[field=>msg]],'updated'=>int,
     *                'created'=>int,'retired'=>int,'error'=>string]
     */
    public function save_build($variant_id, array $rows, $user_id = 0)
    {
        $variant_id = (int) $variant_id;

        $variant = $this->db->select('id, plc_family_id')->from('abom_variant')
            ->where('id', $variant_id)->limit(1)->get()->row();

        if (!$variant) {
            return array('ok' => false, 'errors' => array(), 'updated' => 0,
                         'created' => 0, 'retired' => 0,
                         'error' => 'That build no longer exists.');
        }

        $existing = array();
        foreach ($this->get_all(array('variant_id' => $variant_id)) as $item) {
            $existing[(int) $item->id] = $item;
        }

        // ---- validate everything BEFORE writing anything ------------
        $errors  = array();
        $payload = array();
        $seen    = array();

        foreach ($rows as $n => $row) {
            if (!is_array($row)) {
                continue;
            }

            $id  = isset($row['id']) ? (int) $row['id'] : 0;
            $ref = $id > 0 ? $id : ('new' . $n);

            // Structural fields are NOT settable from this sheet. The
            // build is implied by the screen, the family follows the
            // build, and moving an item to another section is what the
            // full item form is for. Anything else here could quietly
            // reassign an item to a different machine.
            $base = ($id > 0 && isset($existing[$id])) ? $existing[$id] : null;

            $data = $this->payload(array_merge($row, array(
                'variant_id' => $variant_id,
                'section_id' => isset($row['section_id']) && (int) $row['section_id'] > 0
                                    ? (int) $row['section_id']
                                    : ($base ? (int) $base->section_id : 0),
                'is_optional' => isset($row['is_optional']) ? $row['is_optional']
                                    : ($base ? $base->is_optional : 0),
                'feature_code' => array_key_exists('feature_code', $row) ? $row['feature_code']
                                    : ($base ? $base->feature_code : null),
                'data_issue' => array_key_exists('data_issue', $row) ? $row['data_issue']
                                    : ($base ? $base->data_issue : null),
                'panel_location' => array_key_exists('panel_location', $row) ? $row['panel_location']
                                    : ($base ? $base->panel_location : null),
                'source_df' => array_key_exists('source_df', $row) ? $row['source_df']
                                    : ($base ? $base->source_df : null),
                'is_active' => isset($row['is_active']) ? $row['is_active'] : 1,
            )));

            // A wholly blank new row is the engineer clicking Add and
            // changing their mind. Discarded, not saved and not an error.
            if ($id === 0 && trim((string) $data['description']) === ''
                && trim((string) $data['part_no']) === ''
                && ($data['erp_code'] === null || trim((string) $data['erp_code']) === '')) {
                continue;
            }

            $row_errors = $this->validate($data);

            if (!empty($row_errors)) {
                $errors[$ref] = $row_errors;
                continue;
            }

            $payload[] = array('id' => $id, 'data' => $data);

            if ($id > 0) {
                $seen[$id] = true;
            }
        }

        if (!empty($errors)) {
            return array('ok' => false, 'errors' => $errors, 'updated' => 0,
                         'created' => 0, 'retired' => 0,
                         'error' => count($errors) . ' row'
                                  . (count($errors) === 1 ? '' : 's')
                                  . ' need correcting. Nothing was saved.');
        }

        if (empty($payload)) {
            return array('ok' => false, 'errors' => array(), 'updated' => 0,
                         'created' => 0, 'retired' => 0,
                         'error' => 'No rows were received, so nothing was changed.');
        }

        // ---- write ---------------------------------------------------
        $updated = 0;
        $created = 0;
        $retired = 0;

        $this->db->trans_begin();

        foreach ($payload as $p) {
            if ($p['id'] > 0 && isset($existing[$p['id']])) {
                $this->update($p['id'], $p['data'], $user_id);
                $updated++;
            } else {
                if ($this->create($p['data'], $user_id) > 0) {
                    $created++;
                }
            }
        }

        // Rows removed from the sheet are RETIRED, never deleted.
        foreach ($existing as $id => $item) {
            if (!isset($seen[$id]) && !empty($item->is_active)) {
                $this->set_active($id, 0, $user_id);
                $retired++;
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return array('ok' => false, 'errors' => array(), 'updated' => 0,
                         'created' => 0, 'retired' => 0,
                         'error' => 'The changes could not be saved. Nothing was written.');
        }

        $this->db->trans_commit();

        return array('ok' => true, 'errors' => array(), 'error' => '',
                     'updated' => $updated, 'created' => $created, 'retired' => $retired);
    }

    /**
     * Per-build totals for the reference-BOM register: how many items,
     * and how many carry each kind of flag.
     *
     * @return array  variant_id => ['items'=>,'active'=>,'conflict'=>,
     *                               'no_erp'=>,'review'=>]
     */
    public function build_summary()
    {
        $rows = $this->db->select("
                variant_id,
                COUNT(*) AS items,
                SUM(is_active = 1) AS active,
                SUM(issue_severity = 'conflict') AS conflict,
                SUM(erp_code IS NULL OR erp_code = '') AS no_erp,
                SUM(issue_severity = 'review') AS review
            ", false)
            ->from($this->item_table)
            ->group_by('variant_id')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $r) {
            $out[(int) $r->variant_id] = array(
                'items'    => (int) $r->items,
                'active'   => (int) $r->active,
                'conflict' => (int) $r->conflict,
                'no_erp'   => (int) $r->no_erp,
                'review'   => (int) $r->review,
            );
        }

        return $out;
    }

    /**
     * How many SAVED BOM LINES were generated from this item.
     *
     * Shown before deactivating, so the operator knows whether they are
     * retiring something unused or something on ninety documents. Those
     * documents are not affected either way — the count is context, not
     * a blocker.
     *
     * @return int
     */
    public function usage_count($item_id)
    {
        return (int) $this->db->from('abom_bom_line')
            ->where('item_id', (int) $item_id)
            ->count_all_results();
    }

    // -----------------------------------------------------------------
    // COUNTS — used by the post-install verification queries
    // -----------------------------------------------------------------

    public function count_items()
    {
        return (int) $this->db->count_all_results($this->item_table);
    }

    /**
     * @return array  [family_id => count]
     */
    public function count_by_family()
    {
        $rows = $this->db->select('plc_family_id, COUNT(*) AS total')
            ->from($this->item_table)
            ->group_by('plc_family_id')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[(int) $row->plc_family_id] = (int) $row->total;
        }

        return $out;
    }

    /**
     * @return array  [severity => count]
     */
    public function count_by_severity()
    {
        $rows = $this->db->select('issue_severity, COUNT(*) AS total')
            ->from($this->item_table)
            ->group_by('issue_severity')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[$row->issue_severity] = (int) $row->total;
        }

        return $out;
    }

    /**
     * @return array  [formula_code => count]
     */
    public function count_by_formula()
    {
        $rows = $this->db->select('formula_code, COUNT(*) AS total')
            ->from($this->item_table)
            ->group_by('formula_code')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[$row->formula_code] = (int) $row->total;
        }

        return $out;
    }

    /**
     * ERP codes assigned to more than one DISTINCT PART — surfaces the
     * 4060431 and 2110163 conflicts, and any new one engineering
     * introduces later.
     *
     * The count is over DISTINCT part_no, not over rows. Since the master
     * data was re-scoped to build variants (Database/abom_006_seed.sql)
     * the same part legitimately appears once per variant that fits it —
     * VFD 2040140 is on all five. Counting rows would report every one of
     * those as a conflict and bury the two real defects in noise.
     *
     * @return array
     */
    public function find_erp_conflicts()
    {
        return $this->db->select('erp_code, COUNT(DISTINCT part_no) AS total', false)
            ->from($this->item_table)
            ->where('erp_code IS NOT NULL', null, false)
            ->where('erp_code !=', '')
            ->group_by('erp_code')
            ->having('COUNT(DISTINCT part_no) > 1', null, false)
            ->get()
            ->result();
    }

    // -----------------------------------------------------------------

    /**
     * Shared SELECT + JOIN. Returns the query builder so callers chain
     * their own WHERE / ORDER BY onto it.
     *
     * @return CI_DB_query_builder
     */
    private function base_select()
    {
        return $this->db->select('
                i.*,
                s.name       AS section_name,
                s.code       AS section_code,
                s.sort_order AS section_sort,
                v.code       AS variant_code,
                v.name       AS variant_name
            ', false)
            ->from($this->item_table . ' i')
            ->join($this->section_table . ' s', 's.id = i.section_id', 'left')
            ->join($this->variant_table . ' v', 'v.id = i.variant_id', 'left');
    }
}
