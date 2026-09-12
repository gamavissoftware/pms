<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_revision_model
 *
 * The version history of a BOM: which revisions exist, what each one
 * contained, and what changed between them.
 *
 * WHY THIS EXISTS
 * abom_bom_revision has been written to since the module shipped — a
 * full JSON snapshot of header and lines is stored when a BOM reaches
 * procurement approval, and again when it is superseded by a new
 * revision. Nothing ever read those rows. The history was being kept
 * and could not be looked at.
 *
 * HOW A VERSION CHAIN IS IDENTIFIED
 * create_revision() does not edit a BOM in place. It writes a NEW
 * abom_bom row carrying the same bom_no with the revision incremented,
 * and marks the old row superseded. So one document is several rows,
 * and `bom_no` is what ties them together. There is no parent_id
 * column and this model does not invent one: bom_no is already unique
 * per document by construction (Abom_model::next_bom_no()).
 *
 * WHAT IS AND IS NOT SNAPSHOTTED
 *   revision boundaries  -> a full snapshot in abom_bom_revision
 *   edits within a draft -> abom_audit_log only
 * That split is deliberate and this model presents both: the snapshots
 * give "what did REV.01 contain", the audit rows give "who changed what
 * while REV.01 was being prepared".
 *
 * The newest revision has no snapshot of itself until it is approved or
 * superseded, because it is still being worked on. live_snapshot()
 * builds the same shape from the current rows so it can still be
 * compared and shown.
 *
 * PHP 7.4 compatible.
 */
class Abom_revision_model extends CI_Model
{
    private $bom_table      = 'abom_bom';
    private $line_table     = 'abom_bom_line';
    private $revision_table = 'abom_bom_revision';
    private $audit_table    = 'abom_audit_log';

    /**
     * Header fields whose change is worth reporting, and what to call
     * them. Anything not listed here is either derived, internal, or a
     * timestamp that changes on every write and would bury the rest.
     *
     * @var array
     */
    private $header_fields = array(
        'df_ref'        => 'DF reference',
        'machine_model' => 'Machine model',
        'machine_side'  => 'Side',
        'axes'          => 'Axes',
        'tracks'        => 'Tracks',
        'speed_ppm'     => 'Speed (PPM)',
        'motion_type'   => 'Motion type',
        'plc_family_id' => 'PLC family',
        'variant_id'    => 'Build variant',
        'j4_units'      => 'MR-J4 units',
        'battery_qty'   => 'Battery quantity',
        'features_json' => 'Machine features',
        'notes'         => 'Notes',
    );

    // -----------------------------------------------------------------
    // THE CHAIN
    // -----------------------------------------------------------------

    /**
     * Every revision of one document, oldest first.
     *
     * Keyed on bom_no rather than on the id passed in, so opening the
     * history from ANY revision shows the whole chain — which is what
     * somebody asking "what changed" wants, whichever row they happened
     * to be looking at.
     *
     * @param  string $bom_no
     * @return array
     */
    public function chain($bom_no)
    {
        $bom_no = trim((string) $bom_no);

        if ($bom_no === '') {
            return array();
        }

        return $this->db->select('b.*, f.code AS family_code, v.code AS variant_code', false)
            ->from($this->bom_table . ' b')
            ->join('abom_plc_family f', 'f.id = b.plc_family_id', 'left')
            ->join('abom_variant v', 'v.id = b.variant_id', 'left')
            ->where('b.bom_no', $bom_no)
            ->where('b.deleted_at IS NULL', null, false)
            ->order_by('CAST(b.revision AS UNSIGNED)', 'ASC', false)
            ->order_by('b.id', 'ASC')
            ->get()
            ->result();
    }

    /**
     * Archived snapshots for a set of BOM rows, oldest first.
     *
     * The JSON itself is NOT decoded here — a chain of ten revisions
     * each holding forty lines is a lot of text to parse when the
     * history screen only needs the row's date, author and note.
     * snapshot_data() decodes one when it is actually opened.
     *
     * @param  array $bom_ids
     * @return array
     */
    public function snapshots(array $bom_ids)
    {
        $ids = array_filter(array_map('intval', $bom_ids));

        if (empty($ids)) {
            return array();
        }

        return $this->db->select('id, bom_id, revision, change_note, created_by, created_at,
                                  LENGTH(snapshot) AS snapshot_bytes', false)
            ->from($this->revision_table)
            ->where_in('bom_id', $ids)
            ->order_by('id', 'ASC')
            ->get()
            ->result();
    }

    /**
     * One archived snapshot, decoded.
     *
     * @param  int $revision_id
     * @return array|null  ['row' => object, 'header' => array, 'lines' => array]
     */
    public function snapshot_data($revision_id)
    {
        $row = $this->db->from($this->revision_table)
            ->where('id', (int) $revision_id)
            ->limit(1)
            ->get()
            ->row();

        if (!$row) {
            return null;
        }

        $decoded = json_decode((string) $row->snapshot, true);

        // A snapshot that will not decode is a fault worth surfacing
        // rather than rendering as an empty BOM — an empty parts list
        // reads as "nothing was required", which is the one wrong
        // answer this module must never give.
        if (!is_array($decoded) || !isset($decoded['header'])) {
            return null;
        }

        return array(
            'row'    => $row,
            'header' => isset($decoded['header']) && is_array($decoded['header'])
                            ? $decoded['header'] : array(),
            'lines'  => isset($decoded['lines']) && is_array($decoded['lines'])
                            ? $decoded['lines'] : array(),
        );
    }

    /**
     * The CURRENT state of a BOM in snapshot shape.
     *
     * The newest revision has no archived snapshot until it is approved
     * or superseded — it is still being prepared. Without this it would
     * be the one version nobody could compare, which is the version
     * people ask about most.
     *
     * @param  int $bom_id
     * @return array|null
     */
    public function live_snapshot($bom_id)
    {
        $header = $this->db->from($this->bom_table)
            ->where('id', (int) $bom_id)
            ->get()
            ->row_array();

        if (!$header) {
            return null;
        }

        $lines = $this->db->from($this->line_table)
            ->where('bom_id', (int) $bom_id)
            ->order_by('section_order', 'ASC')
            ->order_by('line_no', 'ASC')
            ->get()
            ->result_array();

        return array('row' => null, 'header' => $header, 'lines' => $lines);
    }

    /**
     * The audit trail for a set of BOM rows AND their lines.
     *
     * This is the within-revision half of the story: who typed a
     * remark, who overrode a quantity, who re-specified the machine,
     * while a revision was being prepared.
     *
     * Line-level rows are found by current line id, so a reconfiguration
     * — which replaces the whole line set — ends that lineage. The
     * abom_bom-level 'reconfigure' row records the change itself and is
     * what explains the gap. See Abom_model::replace_lines().
     *
     * @param  array $bom_ids
     * @return array  bom_id => rows, newest first
     */
    public function audit_for(array $bom_ids)
    {
        $ids = array_filter(array_map('intval', $bom_ids));

        if (empty($ids)) {
            return array();
        }

        $line_owner = array();
        foreach ($this->db->select('id, bom_id')->from($this->line_table)
                     ->where_in('bom_id', $ids)->get()->result() as $row) {
            $line_owner[(int) $row->id] = (int) $row->bom_id;
        }

        $this->db->from($this->audit_table)->group_start()
            ->group_start()
                ->where('entity', 'abom_bom')->where_in('entity_id', $ids)
            ->group_end();

        if (!empty($line_owner)) {
            $this->db->or_group_start()
                ->where('entity', 'abom_bom_line')
                ->where_in('entity_id', array_keys($line_owner))
                ->group_end();
        }

        $rows = $this->db->group_end()->order_by('id', 'DESC')->get()->result();

        $out = array();
        foreach ($rows as $row) {
            $owner = ($row->entity === 'abom_bom')
                ? (int) $row->entity_id
                : (isset($line_owner[(int) $row->entity_id]) ? $line_owner[(int) $row->entity_id] : 0);

            if ($owner > 0) {
                $out[$owner][] = $row;
            }
        }

        return $out;
    }

    // -----------------------------------------------------------------
    // THE DIFF
    // -----------------------------------------------------------------

    /**
     * What changed between two versions.
     *
     * Lines are matched by MASTER ITEM, not by line number. Line 7 of
     * one revision and line 7 of the next are unrelated parts as soon as
     * anything is inserted or the build changes, so comparing by
     * position would report an entire sheet as rewritten because one row
     * was added near the top.
     *
     * Hand-added rows have no item_id, so they are matched on part
     * number and description together — the only identity they have.
     *
     * @param  array|null $old  ['header' => …, 'lines' => …], or null for "first version"
     * @param  array      $new
     * @return array
     */
    public function diff($old, array $new)
    {
        $out = array(
            'first'   => ($old === null),
            'header'  => array(),
            'added'   => array(),
            'removed' => array(),
            'qty'     => array(),
            'remark'  => array(),
            'detail'  => array(),
            'counts'  => array('added' => 0, 'removed' => 0, 'qty' => 0,
                               'remark' => 0, 'detail' => 0, 'header' => 0),
        );

        if ($old === null) {
            $out['counts']['added'] = count($new['lines']);

            return $out;
        }

        // --- header ---------------------------------------------------
        foreach ($this->header_fields as $field => $label) {
            $was = isset($old['header'][$field]) ? (string) $old['header'][$field] : '';
            $now = isset($new['header'][$field]) ? (string) $new['header'][$field] : '';

            if ($was === $now) {
                continue;
            }

            $out['header'][] = array(
                'field' => $field,
                'label' => $label,
                'was'   => $this->readable($field, $was),
                'now'   => $this->readable($field, $now),
            );
        }

        // --- lines ----------------------------------------------------
        $a = $this->key_lines($old['lines']);
        $b = $this->key_lines($new['lines']);

        foreach ($b as $key => $line) {
            if (!isset($a[$key])) {
                $out['added'][] = $line;
                continue;
            }

            $before = $a[$key];

            if ((int) $before['qty'] !== (int) $line['qty']) {
                $out['qty'][] = array(
                    'line' => $line,
                    'was'  => (int) $before['qty'],
                    'now'  => (int) $line['qty'],
                );
            }

            $r_was = trim((string) (isset($before['user_remark']) ? $before['user_remark'] : ''));
            $r_now = trim((string) (isset($line['user_remark']) ? $line['user_remark'] : ''));

            if ($r_was !== $r_now) {
                $out['remark'][] = array('line' => $line, 'was' => $r_was, 'now' => $r_now);
            }

            // A frozen line duplicates its description, part number and
            // ERP code on purpose. If one of those moved between
            // revisions the master item was corrected and the new
            // revision picked the correction up — which is exactly the
            // kind of change a reviewer must be shown, not left to spot.
            foreach (array('erp_code' => 'ERP code', 'description' => 'Description',
                           'part_no' => 'Part number', 'manufacturer' => 'Manufacturer') as $f => $label) {
                $fw = trim((string) (isset($before[$f]) ? $before[$f] : ''));
                $fn = trim((string) (isset($line[$f]) ? $line[$f] : ''));

                if ($fw !== $fn) {
                    $out['detail'][] = array('line' => $line, 'label' => $label,
                                             'was' => $fw, 'now' => $fn);
                }
            }
        }

        foreach ($a as $key => $line) {
            if (!isset($b[$key])) {
                $out['removed'][] = $line;
            }
        }

        foreach (array('added', 'removed', 'qty', 'remark', 'detail', 'header') as $k) {
            $out['counts'][$k] = count($out[$k]);
        }

        return $out;
    }

    /**
     * Is there anything to report?
     *
     * @param  array $diff
     * @return bool
     */
    public function diff_is_empty(array $diff)
    {
        foreach ($diff['counts'] as $n) {
            if ($n > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Lines keyed by the only stable identity they have.
     *
     * @param  array $lines
     * @return array
     */
    private function key_lines(array $lines)
    {
        $out = array();

        foreach ($lines as $line) {
            $line = (array) $line;

            $key = !empty($line['item_id'])
                ? 'i' . (int) $line['item_id']
                // A hand-added row has no master item. Part number and
                // description together are what identifies it; either
                // alone collides too readily on a parts list.
                : 'm' . strtolower(trim((string) (isset($line['part_no']) ? $line['part_no'] : ''))
                    . '|' . trim((string) (isset($line['description']) ? $line['description'] : '')));

            // A duplicate key means the same master item appears twice
            // on one sheet. Keeping the FIRST is arbitrary but stable,
            // so the diff does not flicker between runs.
            if (!isset($out[$key])) {
                $out[$key] = $line;
            }
        }

        return $out;
    }

    /**
     * Turns a stored value into something a person can read.
     *
     * ids and JSON are how the row is stored, not what changed as far
     * as anyone reviewing the document is concerned.
     *
     * @param  string $field
     * @param  string $value
     * @return string
     */
    private function readable($field, $value)
    {
        if ($value === '' || $value === null) {
            return '—';
        }

        if ($field === 'plc_family_id') {
            $this->load->model('Abom_master_model');
            $code = $this->Abom_master_model->family_code((int) $value);

            return $code !== '' ? $code : ('family #' . (int) $value);
        }

        if ($field === 'variant_id') {
            $this->load->model('Abom_master_model');
            $variant = $this->Abom_master_model->get_variant((int) $value);

            return $variant ? $variant->code : ('build #' . (int) $value);
        }

        if ($field === 'features_json') {
            $decoded = json_decode($value, true);

            if (!is_array($decoded)) {
                return $value;
            }

            $this->load->model('Abom_master_model');
            $labels = $this->Abom_master_model->get_features();

            $on = array();
            foreach ($decoded as $code => $enabled) {
                if (!empty($enabled)) {
                    $on[] = isset($labels[$code]) ? $labels[$code]->label : $code;
                }
            }

            return empty($on) ? 'none' : implode(', ', $on);
        }

        return $value;
    }
}
