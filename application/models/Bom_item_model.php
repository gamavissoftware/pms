<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bom_item_model
 *
 * Read access to the 71 master line items. Returns rows joined to their
 * section so Bom_engine can emit section_name / section_sort without a
 * second query.
 *
 * Read paths only at this stage (spec section 9, build order step 2).
 * CRUD and audit logging arrive with the master admin screens in build
 * order step 8.
 *
 * This model NEVER computes a quantity. Quantity logic lives only in
 * Bom_engine (spec section 10).
 *
 * PHP 7.4 compatible.
 */
class Bom_item_model extends CI_Model
{
    private $item_table    = 'abom_item';
    private $section_table = 'abom_section';

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
     * Items sharing an ERP code — surfaces the deliberate 4060431
     * conflict, and any new one engineering introduces later.
     *
     * @return array
     */
    public function find_erp_conflicts()
    {
        return $this->db->select('erp_code, COUNT(*) AS total', false)
            ->from($this->item_table)
            ->where('erp_code IS NOT NULL', null, false)
            ->where('erp_code !=', '')
            ->group_by('erp_code')
            ->having('COUNT(*) > 1', null, false)
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
                s.sort_order AS section_sort
            ', false)
            ->from($this->item_table . ' i')
            ->join($this->section_table . ' s', 's.id = i.section_id', 'left');
    }
}
