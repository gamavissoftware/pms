<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_model
 *
 * BOM header and line persistence.
 *
 * abom_bom_line is a frozen snapshot by design: it duplicates
 * description, part number and manufacturer instead of joining back to
 * abom_item. A master item corrected next month MUST NOT retroactively
 * change a BOM that procurement already approved (spec section 10).
 * Nothing in this model joins abom_bom_line to abom_item.
 *
 * PHP 7.4 compatible.
 */
class Abom_model extends CI_Model
{
    private $bom_table  = 'abom_bom';
    private $line_table = 'abom_bom_line';

    /**
     * @param  int $bom_id
     * @return object|null
     */
    public function get_bom($bom_id)
    {
        $row = $this->db->from($this->bom_table)
            ->where('id', (int) $bom_id)
            ->where('deleted_at IS NULL', null, false)
            ->limit(1)
            ->get()
            ->row();

        return $row ? $row : null;
    }

    /**
     * @param  string $bom_no
     * @param  string $revision
     * @return object|null
     */
    public function get_by_number($bom_no, $revision = '00')
    {
        $row = $this->db->from($this->bom_table)
            ->where('bom_no', $bom_no)
            ->where('revision', $revision)
            ->limit(1)
            ->get()
            ->row();

        return $row ? $row : null;
    }

    /**
     * Frozen snapshot rows, in document order. Deliberately not joined
     * to abom_item.
     *
     * @param  int $bom_id
     * @return array
     */
    public function get_lines($bom_id)
    {
        $lines = $this->db->from($this->line_table)
            ->where('bom_id', (int) $bom_id)
            ->order_by('section_order', 'ASC')
            ->order_by('line_no', 'ASC')
            ->get()
            ->result();

        // The views read row_class and status_badges off the line. For a
        // persisted line those are derived from the stored flags, not
        // recomputed from master data.
        $this->load->library('Abom_engine', null, 'abom_engine');

        foreach ($lines as $line) {
            $line->row_class     = $this->abom_engine->row_class($line);
            $line->status_badges = $this->abom_engine->status_badges($line);
        }

        return $lines;
    }

    /**
     * Saved-BOM listing.
     *
     * @param  array $filters  status, machine_model, df_ref, search
     * @return array
     */
    public function get_all($filters = array())
    {
        $this->db->select('b.*, f.code AS family_code, f.name AS family_name,
                           v.code AS variant_code, v.name AS variant_name', false)
            ->from($this->bom_table . ' b')
            ->join('abom_plc_family f', 'f.id = b.plc_family_id', 'left')
            ->join('abom_variant v', 'v.id = b.variant_id', 'left')
            ->where('b.deleted_at IS NULL', null, false);

        if (!empty($filters['status'])) {
            $this->db->where('b.status', $filters['status']);
        }

        if (!empty($filters['machine_model'])) {
            $this->db->where('b.machine_model', $filters['machine_model']);
        }

        if (!empty($filters['df_ref'])) {
            $this->db->where('b.df_ref', $filters['df_ref']);
        }

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $this->db->group_start()
                ->like('b.bom_no', $search)
                ->or_like('b.df_ref', $search)
                ->or_like('b.machine_model', $search)
                ->group_end();
        }

        // MANUFACTURER is a property of the LINES, not of the header —
        // a BOM is Mitsubishi automation plus, on most builds, a RECKON
        // braking resistor or an AUTONICS temperature card. So the
        // filter asks "does this BOM contain a part from X", which is
        // the question someone sourcing parts actually has.
        //
        // EXISTS rather than a join: a join would multiply the header
        // row once per matching line and the count in the page banner
        // would start lying.
        if (!empty($filters['manufacturer'])) {
            $this->db->where(
                'EXISTS (SELECT 1 FROM ' . $this->line_table . ' lm'
                . ' WHERE lm.bom_id = b.id AND lm.manufacturer = '
                . $this->db->escape(trim((string) $filters['manufacturer'])) . ')',
                null, false
            );
        }

        $boms = $this->db->order_by('b.id', 'DESC')->get()->result();

        return $this->attach_manufacturers($boms);
    }

    /**
     * Hangs a ->manufacturers array on each BOM, most-used brand first.
     *
     * A SECOND query rather than a GROUP_CONCAT join. The join would
     * have to be written as a raw derived table to survive CI's
     * identifier escaping, and getting that subtly wrong on a list
     * everyone uses is a poor trade for saving one round trip on a page
     * that shows tens of rows.
     *
     * Ordered by line count descending so the first entry is the
     * machine's actual brand — the same rule Abom_exporter::brand()
     * uses for the download filename, so the list and the file agree.
     *
     * @param  array $boms
     * @return array  the same objects, with ->manufacturers set
     */
    private function attach_manufacturers(array $boms)
    {
        $ids = array();
        foreach ($boms as $bom) {
            $bom->manufacturers = array();
            $ids[] = (int) $bom->id;
        }

        if (empty($ids)) {
            return $boms;
        }

        $rows = $this->db
            ->select('bom_id, manufacturer, COUNT(*) AS n', false)
            ->from($this->line_table)
            ->where_in('bom_id', $ids)
            ->where('manufacturer IS NOT NULL', null, false)
            // '-' is the placeholder a few lines carry where no brand
            // applies. It is not a manufacturer and must not become a
            // filter option.
            ->where_not_in('manufacturer', array('', '-'))
            ->group_by(array('bom_id', 'manufacturer'))
            ->order_by('n', 'DESC')
            ->order_by('manufacturer', 'ASC')
            ->get()
            ->result();

        $by_id = array();
        foreach ($boms as $bom) {
            $by_id[(int) $bom->id] = $bom;
        }

        foreach ($rows as $row) {
            $key = (int) $row->bom_id;
            if (isset($by_id[$key])) {
                $by_id[$key]->manufacturers[] = (string) $row->manufacturer;
            }
        }

        return $boms;
    }

    /**
     * Is this DF reference already claimed by another BOM?
     *
     * A DF reference is an ENGINEERING DRAWING NUMBER. Two live BOMs
     * carrying the same one at the same revision are not a near-miss,
     * they are two documents both claiming to be the parts list for one
     * drawing — and the person who eventually finds out is whoever
     * ordered from the wrong one.
     *
     * Keyed on df_ref AND revision, never df_ref alone: raising REV.01
     * of DF-1805A is a legitimate and common act, and blocking it would
     * break the revision workflow outright.
     *
     * Soft-deleted BOMs do not hold a reference. A deleted document is
     * not competing for the drawing number, and refusing to reuse one
     * would make delete a trap.
     *
     * Case is decided by the column's collation, which on this install
     * is case-insensitive — so "df-1805a" collides with "DF-1805A", as
     * it should.
     *
     * @param  string $df_ref
     * @param  string $revision
     * @param  int    $exclude_bom_id  the BOM being edited, if any
     * @return object|null  the conflicting BOM, or null when free
     */
    public function df_ref_conflict($df_ref, $revision = '00', $exclude_bom_id = 0)
    {
        $df_ref = trim((string) $df_ref);

        // No reference is not a conflict. A BOM may legitimately have
        // none — the register is keyed on bom_no, not on this.
        if ($df_ref === '') {
            return null;
        }

        $revision = trim((string) $revision);
        if ($revision === '') {
            $revision = '00';
        }

        $this->db->select('id, bom_no, revision, df_ref, status, machine_model')
            ->from($this->bom_table)
            ->where('df_ref', $df_ref)
            ->where('revision', $revision)
            ->where('deleted_at IS NULL', null, false);

        if ((int) $exclude_bom_id > 0) {
            $this->db->where('id !=', (int) $exclude_bom_id);
        }

        $row = $this->db->limit(1)->get()->row();

        return $row ? $row : null;
    }

    /**
     * Every manufacturer present on a saved, non-deleted BOM.
     *
     * Derived, never a hard-coded list. The register gains a brand the
     * moment a BOM containing one is saved, so the filter keeps up with
     * the master data on its own — which is the point, since more
     * manufacturers are expected.
     *
     * @return array  brand names, A-Z
     */
    public function manufacturers()
    {
        $rows = $this->db
            ->distinct()
            ->select('l.manufacturer')
            ->from($this->line_table . ' l')
            ->join($this->bom_table . ' b', 'b.id = l.bom_id')
            ->where('b.deleted_at IS NULL', null, false)
            ->where('l.manufacturer IS NOT NULL', null, false)
            ->where_not_in('l.manufacturer', array('', '-'))
            ->order_by('l.manufacturer', 'ASC')
            ->get()
            ->result();

        $out = array();
        foreach ($rows as $row) {
            $out[] = (string) $row->manufacturer;
        }

        return $out;
    }

    // -----------------------------------------------------------------
    // WRITE PATHS
    // -----------------------------------------------------------------

    /**
     * Persists a header and its lines as a FROZEN SNAPSHOT.
     *
     * Every descriptive field is copied onto abom_bom_line rather than
     * joined from abom_item, so a master item corrected next month
     * cannot retroactively change a BOM procurement already approved
     * (spec section 10).
     *
     * Wrapped in a transaction: a half-written BOM is worse than none.
     *
     * @param  array $header  abom_bom column values
     * @param  array $lines   generated lines (objects from Abom_engine)
     * @param  int   $user_id
     * @return int   new bom id, or 0 on failure
     */
    public function save_bom(array $header, array $lines, $user_id = 0)
    {
        $now = date('Y-m-d H:i:s');

        $header['total_lines'] = count($lines);
        $header['total_qty']   = 0;
        $header['open_issues'] = 0;
        foreach ($lines as $line) {
            $header['total_qty'] += (int) $line->qty;
            if (empty($line->erp_code) || $line->issue_severity === 'conflict') {
                $header['open_issues']++;
            }
        }

        $header['created_by'] = $user_id > 0 ? $user_id : null;
        $header['created_at'] = $now;
        $header['updated_at'] = $now;

        $this->db->trans_begin();

        $this->db->insert($this->bom_table, $header);
        $bom_id = (int) $this->db->insert_id();

        if ($bom_id > 0) {
            $rows = array();
            foreach ($lines as $line) {
                $rows[] = array(
                    'bom_id'          => $bom_id,
                    'line_no'         => (int) $line->line_no,
                    // NULL, not 0, on a hand-added row. item_id is
                    // provenance — "which master item produced this" —
                    // and 0 would claim a master item that does not
                    // exist. NULL says truthfully that there isn't one.
                    'item_id'         => !empty($line->item_id) ? (int) $line->item_id : null,
                    'section_name'    => $line->section_name,
                    'section_order'   => (int) $line->section_order,
                    'erp_code'        => $line->erp_code,
                    'description'     => $line->description,
                    'part_no'         => $line->part_no,
                    'manufacturer'    => $line->manufacturer,
                    'qty'             => (int) $line->qty,
                    'computed_qty'    => (int) $line->computed_qty,
                    'is_overridden'   => !empty($line->is_overridden) ? 1 : 0,
                    'override_reason' => isset($line->override_reason) ? $line->override_reason : null,
                    'uom'             => $line->uom,
                    'formula_code'    => $line->formula_code,
                    // Frozen, like every other descriptive field on the
                    // line: which build this part came from must survive
                    // a later edit to abom_variant.
                    'variant_code'    => isset($line->variant_code) ? $line->variant_code : null,
                    'usage_remark'    => $line->usage_remark,
                    'remarks'         => $line->remarks,
                    // The engineer's own text, kept apart from `remarks`
                    // so that clearing it can never erase a conflict
                    // warning copied from the master item.
                    'user_remark'     => isset($line->user_remark) && $line->user_remark !== ''
                                            ? $line->user_remark : null,
                    'issue_severity'  => $line->issue_severity,
                    'is_optional'     => (int) $line->is_optional,
                    'feature_code'    => $line->feature_code,
                    'panel_location'  => $line->panel_location,
                    'is_manual_add'   => !empty($line->is_manual_add) ? 1 : 0,
                    'inserted_after'  => isset($line->inserted_after) ? (int) $line->inserted_after : null,
                    'is_confirmed'    => 0,
                    'is_acknowledged' => 0,
                );
            }

            if (!empty($rows)) {
                $this->db->insert_batch($this->line_table, $rows);
            }

            $this->log_audit('abom_bom', $bom_id, 'create', null, array(
                'bom_no'      => $header['bom_no'],
                'revision'    => $header['revision'],
                'total_lines' => $header['total_lines'],
                'total_qty'   => $header['total_qty'],
            ), $user_id);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 0;
        }

        $this->db->trans_commit();

        return $bom_id;
    }

    /**
     * Persists ONE line-quantity override, with an audit trail.
     *
     * computed_qty is never touched — it stays as the engine produced
     * it, so the document always shows what the rule said alongside what
     * the engineer decided. Setting the quantity back to the computed
     * value clears the override rather than recording an edit to the
     * same number.
     *
     * @param  int    $bom_id
     * @param  int    $line_id
     * @param  int    $qty
     * @param  string $reason
     * @param  int    $user_id
     * @return object|null  the updated line, or null when not found
     */
    public function update_line_qty($bom_id, $line_id, $qty, $reason = '', $user_id = 0)
    {
        $line = $this->db->from($this->line_table)
            ->where('id', (int) $line_id)
            ->where('bom_id', (int) $bom_id)
            ->limit(1)
            ->get()
            ->row();

        if (!$line) {
            return null;
        }

        $qty        = max(0, (int) $qty);
        $computed   = (int) $line->computed_qty;
        $overridden = ($qty !== $computed);

        $data = array(
            'qty'             => $qty,
            'is_overridden'   => $overridden ? 1 : 0,
            'override_reason' => $overridden ? ($reason !== '' ? $reason : null) : null,
        );

        $this->db->trans_begin();

        $this->db->where('id', (int) $line_id)
            ->where('bom_id', (int) $bom_id)
            ->update($this->line_table, $data);

        $this->log_audit(
            'abom_bom_line',
            (int) $line_id,
            $overridden ? 'qty_override' : 'qty_reset',
            array('qty' => (int) $line->qty, 'is_overridden' => (int) $line->is_overridden),
            array('qty' => $qty, 'is_overridden' => $overridden ? 1 : 0, 'computed_qty' => $computed),
            $user_id
        );

        $this->refresh_totals((int) $bom_id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return null;
        }

        $this->db->trans_commit();

        $updated = $this->db->from($this->line_table)->where('id', (int) $line_id)->get()->row();

        $this->load->library('Abom_engine', null, 'abom_engine');
        $updated->row_class     = $this->abom_engine->row_class($updated);
        $updated->status_badges = $this->abom_engine->status_badges($updated);

        return $updated;
    }

    /**
     * Recomputes the header roll-ups after a line change.
     *
     * @param int $bom_id
     */
    public function refresh_totals($bom_id)
    {
        $row = $this->db->select('COUNT(*) AS lines_count, COALESCE(SUM(qty),0) AS total_qty', false)
            ->from($this->line_table)
            ->where('bom_id', (int) $bom_id)
            ->get()
            ->row();

        $this->db->where('id', (int) $bom_id)->update($this->bom_table, array(
            'total_lines' => (int) $row->lines_count,
            'total_qty'   => (int) $row->total_qty,
            'updated_at'  => date('Y-m-d H:i:s'),
        ));
    }

    /**
     * Master-data and BOM audit trail. Procurement will ask who changed
     * a quantity or an ERP code.
     *
     * @param string $entity
     * @param int    $entity_id
     * @param string $action
     * @param mixed  $old
     * @param mixed  $new
     * @param int    $user_id
     */
    /**
     * Rewrites the line set of ONE saved BOM from an ordered list of
     * edits: rows kept, rows added, rows removed, quantities and remarks
     * changed.
     *
     * NOTHING IS REGENERATED. On a saved BOM the lines ARE the document
     * — abom_bom_line is a frozen snapshot, and the master item behind a
     * line may since have changed, been re-costed, or been retired. So
     * existing rows are matched by their own id and only the fields an
     * engineer is allowed to touch are written. Description, part
     * number, ERP code and manufacturer stay exactly as they were
     * frozen, EXCEPT on a hand-added row, which has no master item
     * behind it and is free text by definition.
     *
     * A row the client did not send is a row the engineer removed. That
     * is a real DELETE from abom_bom_line rather than a flag, because a
     * line that is not on the BOM must not appear in any export, any
     * total, or any filter — and the audit row below records what went.
     *
     * abom_item IS NOT TOUCHED. Not by this method and not by anything
     * it calls. Editing one BOM cannot change what the next one
     * generates.
     *
     * @param  array $rows    ordered; each ['id'=>int, 'manual'=>bool, ...]
     * @return array ['ok'=>bool, 'error'=>string, 'kept'=>int,
     *                'added'=>int, 'removed'=>int]
     */
    public function save_lines($bom_id, array $rows, $user_id = 0)
    {
        $bom_id = (int) $bom_id;

        $existing = array();
        foreach ($this->db->from($this->line_table)->where('bom_id', $bom_id)->get()->result() as $line) {
            $existing[(int) $line->id] = $line;
        }

        if (empty($existing)) {
            return array('ok' => false, 'error' => 'That BOM has no lines to edit.',
                         'kept' => 0, 'added' => 0, 'removed' => 0);
        }

        $keep    = array();
        $insert  = array();
        $line_no = 0;
        $kept    = 0;
        $added   = 0;

        $trim = function ($v, $limit) {
            return mb_substr(trim((string) $v), 0, $limit);
        };

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $id     = isset($row['id']) ? (int) $row['id'] : 0;
            $manual = !empty($row['manual']);
            $qty    = isset($row['qty']) ? max(0, (int) $row['qty']) : 0;
            $remark = isset($row['user_remark']) ? $trim($row['user_remark'], 255) : '';

            $line_no++;

            if ($id > 0 && isset($existing[$id])) {
                $line = $existing[$id];

                $update = array(
                    'line_no'     => $line_no,
                    'qty'         => $qty,
                    'user_remark' => $remark !== '' ? $remark : null,
                );

                // An override is a quantity that differs from what the
                // engine computed when this BOM was generated. Setting
                // it back to the computed value clears the flag — the
                // line is no longer edited.
                $update['is_overridden'] = ($qty !== (int) $line->computed_qty) ? 1 : 0;

                // Free-text columns, hand-added rows only.
                if (!empty($line->is_manual_add)) {
                    $erp = isset($row['erp_code']) ? $trim($row['erp_code'], 32) : '';

                    $update['erp_code']       = $erp !== '' ? $erp : null;
                    $update['description']    = isset($row['description']) ? $trim($row['description'], 255) : $line->description;
                    $update['part_no']        = isset($row['part_no']) ? $trim($row['part_no'], 96) : $line->part_no;
                    $update['manufacturer']   = isset($row['manufacturer']) ? $trim($row['manufacturer'], 64) : $line->manufacturer;
                    $update['uom']            = isset($row['uom']) && $trim($row['uom'], 16) !== ''
                                                    ? $trim($row['uom'], 16) : $line->uom;
                    $update['computed_qty']   = $qty;      // no engine value to differ from
                    $update['is_overridden']  = 0;
                    $update['issue_severity'] = $erp === '' ? 'no_erp' : 'none';
                }

                $this->db->where('id', $id)->update($this->line_table, $update);

                $keep[$id] = true;
                $kept++;
                continue;
            }

            // A new row. Only ever a hand-added one: the client cannot
            // conjure a line from master data, because this path never
            // consults master data.
            $erp  = isset($row['erp_code']) ? $trim($row['erp_code'], 32) : '';
            $desc = isset($row['description']) ? $trim($row['description'], 255) : '';
            $part = isset($row['part_no']) ? $trim($row['part_no'], 96) : '';

            if ($desc === '' && $part === '' && $erp === '') {
                $line_no--;                 // wholly blank — discard, do not save
                continue;
            }

            $section_name = isset($row['section']) ? $trim($row['section'], 128) : '';

            // WHERE THE ROW SITS.
            //
            // Lines are read back ordered by section_order, then line_no.
            // A hand-added row that arrived with a section NAME but no
            // section ORDER used to be stored with 99 — after every real
            // section — so it was placed carefully in the sheet, saved,
            // and then reappeared at the very bottom under a second copy
            // of its own heading.
            //
            // The client now sends section_order, but it is not trusted to
            // be the only source: a cached script, or any caller that is
            // not this page, would put the row back at the bottom. So the
            // order is DERIVED from the section the row says it is in, by
            // asking the BOM's own existing lines. That is authoritative,
            // needs no lookup table, and is right even for a section this
            // module has never seen.
            //
            // 99 survives only as the genuine last resort: a named section
            // with no other line in it and no order supplied.
            $section_order = null;

            if ($section_name !== '') {
                foreach ($existing as $sibling) {
                    if ($sibling->section_name === $section_name) {
                        $section_order = (int) $sibling->section_order;
                        break;
                    }
                }
            }

            if ($section_order === null && isset($row['section_order'])
                && (int) $row['section_order'] > 0) {
                $section_order = (int) $row['section_order'];
            }

            if ($section_order === null) {
                $section_order = 99;
            }

            $insert[] = array(
                'bom_id'          => $bom_id,
                'line_no'         => $line_no,
                'item_id'         => null,
                'section_name'    => $section_name,
                'section_order'   => $section_order,
                'erp_code'        => $erp !== '' ? $erp : null,
                'description'     => $desc !== '' ? $desc : $part,
                'part_no'         => $part,
                'manufacturer'    => isset($row['manufacturer']) ? $trim($row['manufacturer'], 64) : '',
                'qty'             => $qty,
                'computed_qty'    => $qty,
                'is_overridden'   => 0,
                'override_reason' => null,
                'uom'             => isset($row['uom']) && $trim($row['uom'], 16) !== ''
                                        ? $trim($row['uom'], 16) : 'NOS',
                'formula_code'    => 'MANUAL',
                'variant_code'    => null,
                'usage_remark'    => null,
                'remarks'         => null,
                'user_remark'     => $remark !== '' ? $remark : null,
                'issue_severity'  => $erp === '' ? 'no_erp' : 'none',
                'is_optional'     => 0,
                'feature_code'    => null,
                'panel_location'  => null,
                'is_manual_add'   => 1,
                'inserted_after'  => $line_no > 1 ? $line_no - 1 : null,
                'is_confirmed'    => 0,
                'is_acknowledged' => 0,
            );
            $added++;
        }

        $removed = array();
        foreach ($existing as $id => $line) {
            if (!isset($keep[$id])) {
                $removed[] = $line->description . ' (' . $line->part_no . ') x' . (int) $line->qty;
            }
        }

        // Refuse to empty the document. A BOM with no items reads as
        // "nothing is required for this machine", which is the one wrong
        // answer procurement must never be handed.
        if (count($keep) + count($insert) === 0) {
            return array('ok' => false, 'kept' => 0, 'added' => 0, 'removed' => 0,
                         'error' => 'Every line would be removed. A BOM with no items cannot be saved.');
        }

        $this->db->trans_begin();

        if (!empty($removed)) {
            $this->db->where('bom_id', $bom_id);

            // GUARD THE EMPTY CASE.
            //
            // CodeIgniter 3.1.4's _where_in() only short-circuits on NULL,
            // not on an empty array, so where_not_in('id', array()) emits
            //
            //     DELETE FROM abom_bom_line WHERE bom_id = N AND id NOT IN ()
            //
            // which is MariaDB error 1064, a syntax error. With db_debug on
            // that is a database error page -- an HTTP 500 carrying no JSON,
            // so the browser could only report "HTTP 500. Nothing was
            // written."
            //
            // $keep is empty exactly when EVERY existing line is being
            // removed in one save: clone a BOM, clear the rows, put your own
            // in. A reasonable thing to do, and it could not be saved.
            //
            // Nothing kept means every line of this BOM goes, so the
            // bom_id filter alone is already the correct statement. The
            // "would empty the document" check above has already refused
            // the case where there is nothing to put back.
            if (!empty($keep)) {
                $this->db->where_not_in('id', array_keys($keep));
            }

            $this->db->delete($this->line_table);
        }

        if (!empty($insert)) {
            $this->db->insert_batch($this->line_table, $insert);
        }

        $this->recount($bom_id);

        $this->log_audit('abom_bom', $bom_id, 'edit_lines', array(
            'removed' => $removed,
        ), array(
            'kept'  => $kept,
            'added' => $added,
        ), $user_id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();

            return array('ok' => false, 'kept' => 0, 'added' => 0, 'removed' => 0,
                         'error' => 'The changes could not be saved. Nothing was written.');
        }

        $this->db->trans_commit();

        return array('ok' => true, 'error' => '', 'kept' => $kept,
                     'added' => $added, 'removed' => count($removed));
    }

    /**
     * Replaces a saved BOM's configuration AND its generated lines.
     *
     * This is what makes a duplicate useful. A clone is only a starting
     * point if the machine it describes can then be changed, and the
     * axis count, track count and speed are exactly the fields that
     * decide which build is used and what every quantity comes to. So
     * changing them has to re-derive the lines — a header saying 6
     * tracks above a sheet quoting 12 tracks' worth of temperature cards
     * is a document that will be built wrong.
     *
     * What the engineer put in BY HAND is carried across:
     *   - hand-added rows, in full
     *   - typed remarks, matched by master item
     *   - quantity overrides, matched by master item
     * Anything whose item is not in the new build cannot be carried and
     * is reported as dropped rather than silently lost.
     *
     * NOT the same as save_lines(), which edits the lines of a fixed
     * configuration. This one changes the machine.
     *
     * @param  int    $bom_id
     * @param  array  $header  abom_bom columns to write
     * @param  array  $lines   fresh engine output, already merged
     * @param  int    $user_id
     * @return bool
     */
    public function replace_lines($bom_id, array $header, array $lines, $user_id = 0)
    {
        $bom_id = (int) $bom_id;
        $before = $this->get_bom($bom_id);

        if (!$before || empty($lines)) {
            return false;
        }

        $this->db->trans_begin();

        $header['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $bom_id)->update($this->bom_table, $header);

        // The whole line set goes and comes back. Matching row by row
        // would be a lot of work to preserve ids that mean nothing once
        // the build has changed under them, and nothing else in the
        // schema holds a foreign key onto abom_bom_line.id.
        //
        // KNOWN CONSEQUENCE: get_audit() finds per-LINE audit rows by
        // current line id, so quantity-override and row-edit history
        // recorded against the old ids drops out of this BOM's trail.
        // The rows themselves are not deleted, and the reconfiguration
        // is logged against abom_bom below with the before/after
        // configuration, which is the entry that explains why the
        // earlier line history stops. Preserving the per-line trail
        // across a build change would mean carrying ids for parts that
        // are no longer on the document.
        $this->db->where('bom_id', $bom_id)->delete($this->line_table);

        $rows = array();
        $n    = 0;

        foreach ($lines as $line) {
            $n++;
            $rows[] = array(
                'bom_id'          => $bom_id,
                'line_no'         => $n,
                'item_id'         => !empty($line->item_id) ? (int) $line->item_id : null,
                'section_name'    => $line->section_name,
                'section_order'   => (int) $line->section_order,
                'erp_code'        => $line->erp_code,
                'description'     => $line->description,
                'part_no'         => $line->part_no,
                'manufacturer'    => $line->manufacturer,
                'qty'             => (int) $line->qty,
                'computed_qty'    => (int) $line->computed_qty,
                'is_overridden'   => !empty($line->is_overridden) ? 1 : 0,
                'override_reason' => isset($line->override_reason) ? $line->override_reason : null,
                'uom'             => $line->uom,
                'formula_code'    => $line->formula_code,
                'variant_code'    => isset($line->variant_code) ? $line->variant_code : null,
                'usage_remark'    => $line->usage_remark,
                'remarks'         => $line->remarks,
                'user_remark'     => (isset($line->user_remark) && $line->user_remark !== '')
                                        ? $line->user_remark : null,
                'issue_severity'  => $line->issue_severity,
                'is_optional'     => (int) $line->is_optional,
                'feature_code'    => $line->feature_code,
                'panel_location'  => $line->panel_location,
                'is_manual_add'   => !empty($line->is_manual_add) ? 1 : 0,
                'is_confirmed'    => 0,
                'is_acknowledged' => 0,
            );
        }

        $this->db->insert_batch($this->line_table, $rows);
        $this->recount($bom_id);

        $this->log_audit('abom_bom', $bom_id, 'reconfigure', array(
            'machine_model' => $before->machine_model,
            'axes'          => (int) $before->axes,
            'tracks'        => (int) $before->tracks,
            'speed_ppm'     => (int) $before->speed_ppm,
            'motion_type'   => $before->motion_type,
            'df_ref'        => $before->df_ref,
            'total_lines'   => (int) $before->total_lines,
        ), array(
            'machine_model' => isset($header['machine_model']) ? $header['machine_model'] : $before->machine_model,
            'axes'          => isset($header['axes']) ? (int) $header['axes'] : (int) $before->axes,
            'tracks'        => isset($header['tracks']) ? (int) $header['tracks'] : (int) $before->tracks,
            'speed_ppm'     => isset($header['speed_ppm']) ? (int) $header['speed_ppm'] : (int) $before->speed_ppm,
            'motion_type'   => isset($header['motion_type']) ? $header['motion_type'] : $before->motion_type,
            'df_ref'        => isset($header['df_ref']) ? $header['df_ref'] : $before->df_ref,
            'total_lines'   => count($rows),
        ), $user_id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    /**
     * Recomputes the header totals from the lines actually present.
     *
     * The header carries total_lines, total_qty and open_issues so the
     * register and the stat cards do not have to aggregate on every
     * read. Anything that changes the line set must call this, or the
     * list says 29 while the document shows 30.
     *
     * @param  int $bom_id
     * @return void
     */
    public function recount($bom_id)
    {
        $lines = $this->db->from($this->line_table)->where('bom_id', (int) $bom_id)->get()->result();

        $total_qty = 0;
        $open      = 0;

        foreach ($lines as $line) {
            $total_qty += (int) $line->qty;

            if ($line->erp_code === null || $line->erp_code === ''
                || $line->issue_severity === 'conflict') {
                $open++;
            }
        }

        $this->db->where('id', (int) $bom_id)->update($this->bom_table, array(
            'total_lines' => count($lines),
            'total_qty'   => $total_qty,
            'open_issues' => $open,
            'updated_at'  => date('Y-m-d H:i:s'),
        ));
    }

    /**
     * SOFT delete. Sets deleted_at; the row, its lines, its approval
     * trail and its revision snapshots all stay exactly where they are.
     *
     * Never a hard DELETE. A BOM that has been through any part of the
     * approval workflow is a record of who signed what, and the audit
     * table would be left pointing at an id that no longer resolves.
     * get_bom() and get_all() already filter on deleted_at, so a deleted
     * BOM disappears from every screen and every export while remaining
     * recoverable by an administrator with one UPDATE.
     *
     * The STATUS RULE is enforced in the controller, not here, so that
     * the message can name the status. This method is the write.
     *
     * @param  int $bom_id
     * @param  int $user_id
     * @return bool
     */
    public function soft_delete($bom_id, $user_id = 0)
    {
        $bom = $this->get_bom($bom_id);

        if (!$bom) {
            return false;                    // already gone, or never existed
        }

        $this->db->where('id', (int) $bom_id)
                 ->update($this->bom_table, array(
                     'deleted_at' => date('Y-m-d H:i:s'),
                     'updated_at' => date('Y-m-d H:i:s'),
                 ));

        $this->log_audit('abom_bom', (int) $bom_id, 'delete', array(
            'bom_no'      => $bom->bom_no,
            'revision'    => $bom->revision,
            'status'      => $bom->status,
            'total_lines' => (int) $bom->total_lines,
        ), null, $user_id);

        return true;
    }

    /**
     * Duplicates a BOM as a NEW draft, with its own number.
     *
     * NOT the same as create_revision(). A revision supersedes the
     * original, keeps the number and increments the revision — it says
     * "this document changed". A duplicate is a different document
     * altogether: a new number at revision 00, and the original is left
     * untouched and still current. That is what you want when the next
     * machine is nearly the same as the last one.
     *
     * Everything descriptive is copied, INCLUDING the engineer's own
     * work: typed remarks, quantity overrides and hand-added rows. That
     * is the entire point — a duplicate that dropped them would just be
     * a slower way of pressing Generate.
     *
     * Nothing about the approval state is copied. The new BOM is a
     * draft, unsigned, prepared by whoever pressed the button. Carrying
     * a signature across to a document its signatory has never seen
     * would be forgery, however convenient.
     *
     * @param  int $bom_id
     * @param  int $user_id
     * @return array ['ok'=>bool, 'bom_id'=>int, 'bom_no'=>string, 'error'=>string]
     */
    public function duplicate($bom_id, $user_id = 0)
    {
        $source = $this->get_bom($bom_id);

        if (!$source) {
            return array('ok' => false, 'bom_id' => 0, 'bom_no' => '',
                         'error' => 'That BOM no longer exists.');
        }

        $now    = date('Y-m-d H:i:s');
        $bom_no = $this->next_bom_no();

        $header = (array) $source;
        unset($header['id']);

        // A joined read (get_all) brings these along; they are not
        // columns on abom_bom and would fail the insert.
        unset($header['family_code'], $header['family_name'],
              $header['variant_code'], $header['variant_name']);

        $header['bom_no']            = $bom_no;
        $header['revision']          = '00';
        // The DF reference is NOT carried onto the copy.
        //
        // It is a drawing number, and a copy is a new document for the
        // next machine — it will have its own. Inheriting the source's
        // number is precisely what puts two live BOMs on one drawing,
        // and it would land the clone in breach of the uniqueness rule
        // the moment it was created, blocking the first edit for a
        // reason the operator did not cause.
        //
        // Nothing is lost: the note below records what this was copied
        // from, and the export filename falls back to the BOM number
        // until a reference is typed.
        $header['df_ref']            = null;
        $header['status']            = 'draft';
        $header['prepared_by']       = $user_id > 0 ? $user_id : null;
        $header['prepared_at']       = $now;
        $header['checked_by']        = null;
        $header['checked_at']        = null;
        $header['eng_approved_by']   = null;
        $header['eng_approved_at']   = null;
        $header['proc_approved_by']  = null;
        $header['proc_approved_at']  = null;
        $header['created_by']        = $user_id > 0 ? $user_id : null;
        $header['created_at']        = $now;
        $header['updated_at']        = $now;
        $header['deleted_at']        = null;
        $header['notes']             = trim('Duplicated from ' . $source->bom_no
                                        . ($source->revision !== '' ? ' rev ' . $source->revision : '')
                                        . '. ' . (string) $source->notes);

        $this->db->trans_begin();

        $this->db->insert($this->bom_table, $header);
        $new_id = (int) $this->db->insert_id();

        if ($new_id > 0) {
            $lines = $this->db->from($this->line_table)
                ->where('bom_id', (int) $bom_id)
                ->order_by('section_order', 'ASC')
                ->order_by('line_no', 'ASC')
                ->get()
                ->result_array();

            $rows = array();
            foreach ($lines as $line) {
                unset($line['id']);
                $line['bom_id'] = $new_id;

                // Line-level acknowledgements are a REVIEW act against a
                // specific document. They do not travel: the checker of
                // the new BOM must look at its conflicts themselves.
                $line['is_confirmed']    = 0;
                $line['is_acknowledged'] = 0;
                $line['ack_comment']     = null;

                $rows[] = $line;
            }

            if (!empty($rows)) {
                $this->db->insert_batch($this->line_table, $rows);
            }

            $this->log_audit('abom_bom', $new_id, 'duplicate', array(
                'source_bom_id' => (int) $bom_id,
                'source_bom_no' => $source->bom_no,
            ), array(
                'bom_no'      => $bom_no,
                'total_lines' => count($rows),
            ), $user_id);
        }

        if ($this->db->trans_status() === false || $new_id <= 0) {
            $this->db->trans_rollback();

            return array('ok' => false, 'bom_id' => 0, 'bom_no' => '',
                         'error' => 'The BOM could not be duplicated. Nothing was written.');
        }

        $this->db->trans_commit();

        return array('ok' => true, 'bom_id' => $new_id, 'bom_no' => $bom_no, 'error' => '');
    }

    public function log_audit($entity, $entity_id, $action, $old, $new, $user_id = 0)
    {
        $this->db->insert('abom_audit_log', array(
            'entity'     => $entity,
            'entity_id'  => (int) $entity_id,
            'action'     => $action,
            'old_values' => ($old === null) ? null : json_encode($old),
            'new_values' => ($new === null) ? null : json_encode($new),
            'user_id'    => $user_id > 0 ? $user_id : null,
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }

    /**
     * Audit rows for one BOM, newest first.
     *
     * @param  int $bom_id
     * @return array
     */
    public function get_audit($bom_id)
    {
        $line_ids = array();
        foreach ($this->db->select('id')->from($this->line_table)
                     ->where('bom_id', (int) $bom_id)->get()->result() as $row) {
            $line_ids[] = (int) $row->id;
        }

        $this->db->from('abom_audit_log')->group_start()
            ->group_start()->where('entity', 'abom_bom')->where('entity_id', (int) $bom_id)->group_end();

        if (!empty($line_ids)) {
            $this->db->or_group_start()
                ->where('entity', 'abom_bom_line')
                ->where_in('entity_id', $line_ids)
                ->group_end();
        }

        return $this->db->group_end()->order_by('id', 'DESC')->get()->result();
    }

    /**
     * Next number in the module's own ABOM series. The existing DF
     * register owns the "DF-" namespace and is never written to here.
     *
     * @return string
     */
    public function next_bom_no()
    {
        $format = $this->config->item('abom_bom_no_format', 'abom');
        if (empty($format)) {
            $format = 'ABOM-%d';
        }

        $row = $this->db->select('bom_no')
            ->from($this->bom_table)
            ->like('bom_no', 'ABOM-', 'after')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        $next = 1;
        if ($row && preg_match('/(\d+)$/', $row->bom_no, $m)) {
            $next = (int) $m[1] + 1;
        }

        return sprintf($format, $next);
    }
}
