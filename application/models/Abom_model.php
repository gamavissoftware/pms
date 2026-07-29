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
        $this->db->select('b.*, f.code AS family_code, f.name AS family_name')
            ->from($this->bom_table . ' b')
            ->join('abom_plc_family f', 'f.id = b.plc_family_id', 'left')
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

        return $this->db->order_by('b.id', 'DESC')->get()->result();
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
                    'item_id'         => (int) $line->item_id,
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
                    'usage_remark'    => $line->usage_remark,
                    'remarks'         => $line->remarks,
                    'issue_severity'  => $line->issue_severity,
                    'is_optional'     => (int) $line->is_optional,
                    'feature_code'    => $line->feature_code,
                    'panel_location'  => $line->panel_location,
                    'is_manual_add'   => !empty($line->is_manual_add) ? 1 : 0,
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
