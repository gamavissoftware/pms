<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bom_model
 *
 * BOM header and line persistence.
 *
 * READ PATHS ONLY at this stage. save(), create_revision() and the
 * frozen-snapshot write path arrive with build order step 5.
 *
 * abom_bom_line is a frozen snapshot by design: it duplicates
 * description, part number and manufacturer instead of joining back to
 * abom_item. A master item corrected next month MUST NOT retroactively
 * change a BOM that procurement already approved (spec section 10).
 * Nothing in this model joins abom_bom_line to abom_item.
 *
 * PHP 7.4 compatible.
 */
class Bom_model extends CI_Model
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
        $this->load->library('Bom_engine');

        foreach ($lines as $line) {
            $line->row_class     = $this->Bom_engine->row_class($line);
            $line->status_badges = $this->Bom_engine->status_badges($line);
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
