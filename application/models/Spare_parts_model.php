<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Spare_parts_model
 *
 * This model handles all database operations for the 'spare_parts_for_trading' table.
 */
class Spare_parts_model extends CI_Model {

    private $table = 'spare_parts_for_trading';
    
    // --- Server-side DataTable Configuration ---
    private $column_order = array('id', 'code', 'description', 'price', 'available_qty', 'stock_updated_on', 'status', 'added_on', null); // Columns for ordering
    private $column_search = array('id', 'code', 'description'); // Columns for searching
    private $order = array('id' => 'desc'); // Default order
    private $inventory_columns_available = null;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * The inventory fields are added by Database/spares_master_inventory_001.sql.
     * Keeping this check here lets the page fail softly before the migration is run.
     *
     * @return bool
     */
    public function inventory_columns_available() {
        if ($this->inventory_columns_available === null) {
            $this->inventory_columns_available = $this->db->field_exists('available_qty', $this->table);
        }

        return $this->inventory_columns_available;
    }

    // =========================================================================
    // == ORIGINAL FUNCTIONS (FROM PREVIOUS CONVERSATION)
    // =========================================================================

    /**
     * Fetches and transforms spare part codes. (READ-ONLY)
     */
    public function get_and_transform_codes() {
        $this->db->select("
            id,
            CASE 
                WHEN LEFT(code, 1) = 'A' THEN CONCAT('M11', SUBSTRING(code, 2))
                WHEN LEFT(code, 1) = 'B' THEN CONCAT('M11', SUBSTRING(code, 2))
                WHEN LEFT(code, 1) = 'C' THEN CONCAT('M11', SUBSTRING(code, 2))
                ELSE code 
            END AS modified_code, 
            code AS original_code, 
            description
        ", FALSE);

        $this->db->from($this->table);
        $this->db->group_start();
        $this->db->like('code', 'A', 'after');
        $this->db->or_like('code', 'B', 'after');
        $this->db->or_like('code', 'C', 'after');
        $this->db->group_end();

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    /**
     * [FIXED] Updates spare part codes starting with 'A', 'B', or 'C' to 'M11...'.
     *
     * THIS IS A DESTRUCTIVE OPERATION AND PERMANENTLY MODIFIES THE DATABASE.
     *
     * @return int The number of rows affected by the update.
     */
    public function update_codes_to_m11() {
        // Use a direct SQL expression for the SET clause
        // This replaces the first character with 'M11'
        $this->db->set('code', "CONCAT('M11', SUBSTRING(code, 2))", FALSE);

        // Use a more direct WHERE clause instead of LIKE for an UPDATE
        // This finds all codes that start with 'A', 'B', or 'C'
        $where_clause = "LEFT(code, 1) = 'A' OR LEFT(code, 1) = 'B' OR LEFT(code, 1) = 'C'";
        $this->db->where($where_clause);

        // Execute the update
        $this->db->update($this->table);

        // Return the number of affected rows
        return $this->db->affected_rows();
    }


    // =========================================================================
    // == NEW FUNCTIONS FOR CRUD (ADD, EDIT, LIST, DELETE)
    // =========================================================================

    /**
     * Prepares the query for server-side DataTables.
     */
    private function _get_parts_datatable_query() {
        $this->db->from($this->table);

        $i = 0;
        foreach ($this->column_search as $item) { // loop column 
            if ($this->input->post('search')['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); 
                    $this->db->like($item, $this->input->post('search')['value']);
                } else {
                    $this->db->or_like($item, $this->input->post('search')['value']);
                }
                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if ($this->input->post('order')) { // here order processing
            $order = $this->input->post('order');
            $column_index = isset($order['0']['column']) ? (int) $order['0']['column'] : 0;
            $direction = isset($order['0']['dir']) && strtolower($order['0']['dir']) === 'asc' ? 'asc' : 'desc';
            $column_name = isset($this->column_order[$column_index]) ? $this->column_order[$column_index] : null;

            if (in_array($column_name, array('available_qty', 'stock_updated_on'), true) && !$this->inventory_columns_available()) {
                $column_name = 'id';
            }

            if ($column_name) {
                $this->db->order_by($column_name, $direction);
            }
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    /**
     * Gets the data for server-side DataTables.
     * @return array
     */
    public function get_parts_list_serverside() {
        $this->_get_parts_datatable_query();
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Counts filtered records for DataTables pagination.
     * @return int
     */
    public function count_filtered_parts() {
        $this->_get_parts_datatable_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * Counts all records in the table for DataTables pagination.
     * @return int
     */
    public function count_all_parts() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    /**
     * Gets a single spare part by its ID.
     * @param int $id The part's ID
     * @return object
     */
    public function get_part_by_id($id) {
        $query = $this->db->get_where($this->table, array('id' => $id));
        return $query->row();
    }

    /**
     * Adds a new spare part to the database.
     * @param array $data The data to insert
     * @return bool
     */
    public function add_part($data) {
        // Set the added_on date
        $data['added_on'] = date('Y-m-d');
        return $this->db->insert($this->table, $data);
    }

    /**
     * Updates an existing spare part.
     * @param int $id The ID of the part to update
     * @param array $data The new data
     * @return bool
     */
    public function update_part($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Soft deletes a spare part by setting its status to 0.
     * @param int $id The ID of the part to delete
     * @return bool
     */
    public function delete_part($id) {
        // We do a "soft delete" by setting status to 0 (Inactive)
        // This is safer than permanently deleting data.
        $this->db->where('id', $id);
        return $this->db->update($this->table, array('status' => 0));
    }

    /**
     * Inventory summary for the spare parts master dashboard.
     *
     * @return object
     */
    public function get_inventory_summary() {
        $summary = new stdClass();
        $summary->total_parts = 0;
        $summary->active_parts = 0;
        $summary->available_qty = 0;
        $summary->zero_stock = 0;
        $summary->last_stock_update = null;

        $this->db->from($this->table);
        $summary->total_parts = (int) $this->db->count_all_results();

        $this->db->from($this->table);
        $this->db->where('status', 1);
        $summary->active_parts = (int) $this->db->count_all_results();

        if ($this->inventory_columns_available()) {
            $row = $this->db->select('SUM(available_qty) AS available_qty, SUM(CASE WHEN available_qty <= 0 THEN 1 ELSE 0 END) AS zero_stock, MAX(stock_updated_on) AS last_stock_update', false)
                ->from($this->table)
                ->get()
                ->row();

            if ($row) {
                $summary->available_qty = (float) $row->available_qty;
                $summary->zero_stock = (int) $row->zero_stock;
                $summary->last_stock_update = $row->last_stock_update;
            }
        }

        return $summary;
    }

    /**
     * Finds a spare part by code. Matching is case-insensitive after trimming.
     *
     * @param string $code
     * @return object|null
     */
    public function get_part_by_code($code) {
        $code = trim((string) $code);
        if ($code === '') {
            return null;
        }

        $query = $this->db->where('LOWER(TRIM(code)) = ' . $this->db->escape(strtolower($code)), null, false)
            ->limit(1)
            ->get($this->table);

        return $query->row();
    }

    /**
     * Updates available stock quantity for a spare code.
     *
     * @param string $code
     * @param float $qty
     * @param int|null $user_id
     * @return array
     */
    public function update_stock_by_code($code, $qty, $user_id = null) {
        if (!$this->inventory_columns_available()) {
            return array('ok' => false, 'message' => 'Inventory columns are not available. Run Database/spares_master_inventory_001.sql first.');
        }

        $part = $this->get_part_by_code($code);
        if (!$part) {
            return array('ok' => false, 'message' => 'Part code not found.');
        }

        $data = array(
            'available_qty' => $qty,
            'stock_updated_on' => date('Y-m-d H:i:s'),
            'stock_updated_by' => $user_id
        );

        $this->db->where('id', $part->id);
        $ok = $this->db->update($this->table, $data);

        return array('ok' => $ok, 'message' => $ok ? 'Updated' : 'Database update failed.', 'part' => $part);
    }

    /**
     * Records one stock upload summary.
     *
     * @param array $data
     * @return int
     */
    public function log_stock_upload($data) {
        if (!$this->db->table_exists('spares_master_stock_uploads')) {
            return 0;
        }

        $this->db->insert('spares_master_stock_uploads', $data);
        return (int) $this->db->insert_id();
    }

    /**
     * Recent upload history shown on the master page.
     *
     * @param int $limit
     * @return array
     */
    public function get_recent_stock_uploads($limit = 5) {
        if (!$this->db->table_exists('spares_master_stock_uploads')) {
            return array();
        }

        return $this->db->order_by('uploaded_on', 'DESC')
            ->limit((int) $limit)
            ->get('spares_master_stock_uploads')
            ->result();
    }
}

/* End of file Spare_parts_model.php */
/* Location: ./application/models/Spare_parts_model.php */
