<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opportunity_model extends CI_Model {

    public function get_opportunity_details($opportunity_id) {
        
        // Subquery to get the latest stage ID for the specific opportunity
        $latest_stage_subquery = "(SELECT lead_stage FROM spare_progress_remarks WHERE lead_id = {$opportunity_id} ORDER BY id DESC LIMIT 1)";

        $this->db->select("
            op.*, 
            cm.company_name,
            cm.contact_person,
            ls.lead_source,
            sls.lead_name as current_stage_name,
            sls.lead_id as current_stage_id,
            sls.sort_order as current_stage_order,
            CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name
        ");
        $this->db->from('opportunities op');
        $this->db->join('spares_customers cm', 'cm.customer_id = op.customer_id', 'left');
        $this->db->join('system_users u', 'u.user_id = op.marketing_person_id', 'left');
        $this->db->join('lead_source ls', 'ls.source_id = op.source_id', 'left');
        $this->db->join('spare_lead_stage sls', "sls.lead_id = {$latest_stage_subquery}", 'left');
        $this->db->where('op.opportunity_id', $opportunity_id);
        
        $query = $this->db->get();
        return $query->row();
    }

    public function get_opportunity_products($opportunity_id) {
        $this->db->select('p.id, p.code, p.description, p.price, op.quantity');
        $this->db->from('opportunity_products op');
        $this->db->join('spare_parts_for_trading p', 'p.id = op.product_id', 'left'); // Assuming a 'products' table
        $this->db->where('op.opportunity_id', $opportunity_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_opportunity_history($opportunity_id) {
        $this->db->select("
            spr.*,
            sls.lead_name as stage_name,
            CONCAT(u.first_name, ' ', u.last_name) as added_by_name
        ");
        $this->db->from('spare_progress_remarks spr');
        $this->db->join('spare_lead_stage sls', 'sls.lead_id = spr.lead_stage', 'left');
        $this->db->join('system_users u', 'u.user_id = spr.added_by', 'left');
        $this->db->where('spr.lead_id', $opportunity_id);
        $this->db->order_by('spr.added_on', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_next_stages($current_stage_id) {
        $this->db->select('sls.lead_id, sls.lead_name,sls.app_access');
        $this->db->from('spares_lead_stage_relation slsr');
        $this->db->join('spare_lead_stage sls', 'sls.lead_id = slsr.stage_relation');
        $this->db->where('slsr.lead_stage_id', $current_stage_id);
        $this->db->order_by('sls.sort_order', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_main_opportunity_status($id, $data) {
        $this->db->where('opportunity_id', $id);
        return $this->db->update('opportunities', $data);
    }

    public function add_progress_remark($data) {
        return $this->db->insert('spare_progress_remarks', $data);
    }

    public function get_opportunity_quotations($opportunity_id) {
        $this->db->select('quotation_id, quotation_no, quotation_date, total_value');
        $this->db->from('quotations');
        $this->db->where('opportunity_id', $opportunity_id);
        $this->db->order_by('quotation_date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_latest_quotation_by_opportunity($opportunity_id) {
        $this->db->from('quotations');
        $this->db->where('opportunity_id', $opportunity_id);
        $this->db->order_by('quotation_id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

     public function get_quotation_products($quotation_id) {
        $this->db->select('qp.*, p.code, p.description as product_full_description, p.price');
        $this->db->from('quotation_products qp');
        $this->db->join('spare_parts_for_trading p', 'p.id = qp.product_id', 'left'); // Change 'products' table and 'id' column if different
        $this->db->where('quotation_id', $quotation_id);
        return $this->db->get()->result();
    }

    public function get_recent_customer_item_quotes($product_ids, $customer_id = 0, $limit = 2) {
        $limit = (int) $limit;

        if ($limit <= 0 || empty($product_ids)) {
            return [];
        }

        $clean_product_ids = [];
        foreach ((array) $product_ids as $product_id) {
            $product_id = (int) $product_id;
            if ($product_id > 0) {
                $clean_product_ids[$product_id] = $product_id;
            }
        }

        if (empty($clean_product_ids)) {
            return [];
        }

        $this->db->select('
            qp.product_id,
            qp.quantity,
            qp.unit_price,
            qp.discount_percent,
            q.quotation_id,
            q.quotation_no,
            q.quotation_date
        ');
        $this->db->from('quotation_products qp');
        $this->db->join('quotations q', 'q.quotation_id = qp.quotation_id', 'inner');
        $this->db->where_in('qp.product_id', array_values($clean_product_ids));
        $this->db->order_by('q.quotation_date', 'DESC');
        $this->db->order_by('q.quotation_id', 'DESC');
        $this->db->order_by('qp.quot_product_id', 'DESC');

        $history_map = [];
        foreach ($this->db->get()->result_array() as $row) {
            $product_id = (int) $row['product_id'];

            if (!isset($history_map[$product_id])) {
                $history_map[$product_id] = [];
            }

            if (count($history_map[$product_id]) >= $limit) {
                continue;
            }

            $history_map[$product_id][] = [
                'quotation_id'      => (int) $row['quotation_id'],
                'quotation_no'      => $row['quotation_no'],
                'quotation_date'    => $row['quotation_date'],
                'quantity'          => (float) $row['quantity'],
                'unit_price'        => (float) $row['unit_price'],
                'discount_percent'  => (float) $row['discount_percent'],
            ];
        }

        return $history_map;
    }

    public function get_recent_customer_item_quote_history($product_id, $customer_id = 0, $limit = 2) {
        $product_id = (int) $product_id;
        if ($product_id <= 0) {
            return [];
        }

        $history_map = $this->get_recent_customer_item_quotes([$product_id], $customer_id, $limit);
        return isset($history_map[$product_id]) ? $history_map[$product_id] : [];
    }

    public function get_opportunity_by_id($id) {
        $this->db->select('o.*, c.company_name, b.name as brand_name'); // Select fields from opportunity, customer, and brand tables
        $this->db->from('opportunities o'); // Alias the opportunities table as 'o'
        $this->db->join('spares_customers c', 'o.customer_id = c.customer_id', 'left');
        $this->db->join('spare_company_brand b', 'o.brand_id = b.id', 'left');
        $this->db->where('o.opportunity_id', $id);
        $query = $this->db->get();
        return $query->row(); // Return a single result object
    }

    public function update_opportunity($id, $opportunity_data, $product_data) {
        $this->db->trans_start(); // Start a database transaction

        // 1. Update the main opportunity details
        $this->db->where('opportunity_id', $id);
        $this->db->update('opportunities', $opportunity_data);

        // 2. Delete all existing products for this opportunity to prevent duplicates
        $this->db->where('opportunity_id', $id);
        $this->db->delete('opportunity_products');

        // 3. Insert the new/updated list of products (if any)
        if (!empty($product_data)) {
            $this->db->insert_batch('opportunity_products', $product_data);
        }

        $this->db->trans_complete(); // Complete the transaction

        return $this->db->trans_status(); // Return true on success, false on failure
    }

public function get_data_for_po($opportunity_id)
{
    $data = [];

    // --- Step 1: Get Opportunity and Customer details ---
    $this->db->select('op.opportunity_id, op.op_no, op.customer_id, cm.company_name, cm.contact_person, cm.email, cm.address as customer_address');
    $this->db->from('opportunities op');
    $this->db->join('spares_customers cm', 'cm.customer_id = op.customer_id', 'left');
    $this->db->where('op.opportunity_id', $opportunity_id);
    $data['details'] = $this->db->get()->row();

    if (!$data['details']) {
        return false; 
    }

    $data['products'] = [];
    $data['details']->source_document = 'Quotation';
    $data['details']->source_document_no = '';

    // --- Step 2A: Prefer the latest PI when available ---
    if ($this->db->table_exists('spare_proforma_invoices')) {
        $latest_pi = $this->db->from('spare_proforma_invoices')
            ->where('opportunity_id', $opportunity_id)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if ($latest_pi) {
            $data['details']->packing_percent = $latest_pi->packing_percent;
            $data['details']->freight_charge = $latest_pi->freight_charge;
            $data['details']->insurance_percent = $latest_pi->insurance_percent;
            $data['details']->source_document = 'PI';
            $data['details']->source_document_no = $latest_pi->pi_no;

            $this->db->select('product_id, quantity, unit_price as price, product_code as code, description');
            $this->db->from('spare_proforma_invoice_items');
            $this->db->where('spare_pi_id', (int) $latest_pi->id);
            $this->db->order_by('sort_order', 'ASC');
            $data['products'] = $this->db->get()->result();
        }
    }

    // --- Step 2B: Fall back to the latest quotation if no PI exists yet ---
    if (empty($data['products'])) {
        $this->db->select('quotation_id, packing_percent, freight_charge, insurance_percent, quotation_no');
        $this->db->from('quotations');
        $this->db->where('opportunity_id', $opportunity_id);
        $this->db->order_by('quotation_id', 'DESC');
        $this->db->limit(1);
        $latest_quote = $this->db->get()->row();

        if ($latest_quote) {
            $data['details']->packing_percent = $latest_quote->packing_percent;
            $data['details']->freight_charge = $latest_quote->freight_charge;
            $data['details']->insurance_percent = $latest_quote->insurance_percent;
            $data['details']->source_document = 'Quotation';
            $data['details']->source_document_no = $latest_quote->quotation_no;

            $this->db->select('qp.product_id, qp.quantity, qp.unit_price as price, p.code, qp.description');
            $this->db->from('quotation_products qp');
            $this->db->join('spare_parts_for_trading p', 'p.id = qp.product_id', 'left');
            $this->db->where('qp.quotation_id', $latest_quote->quotation_id);
            $data['products'] = $this->db->get()->result();
        }
    }
    
    return $data;
}
// In Opportunity_model.php


public function get_po_details_for_pdf($po_id)
{
    $data = [];
    // 1. Get Master PO data and join with customer info
    $this->db->select('po.*, c.company_name, c.address as customer_address, c.tax_number as customer_gst');
    $this->db->from('purchase_orders po');
    $this->db->join('spares_customers c', 'c.customer_id = po.customer_id', 'left');
    $this->db->where('po.po_id', $po_id);
    $data['po_details'] = $this->db->get()->row();

    if (!$data['po_details']) return false;

    // 2. Get PO Product line items
    $this->db->select('pop.*, p.code as product_code');
    $this->db->from('po_products pop');
    $this->db->join('spare_parts_for_trading p', 'p.id = pop.product_id', 'left');
    $this->db->where('pop.po_id', $po_id);
    $data['products'] = $this->db->get()->result();

    return $data;
}

public function get_data_for_order_won($opportunity_id)
{
    // 1. Find the latest PO for this opportunity and all its charge details
    $this->db->select('po.*, op.customer_id, op.marketing_person_id, c.company_name');
    $this->db->from('purchase_orders po');
    $this->db->join('opportunities op', 'op.opportunity_id = po.opportunity_id');
    $this->db->join('spares_customers c', 'c.customer_id = op.customer_id');
    $this->db->where('po.opportunity_id', $opportunity_id);
    $this->db->order_by('po.po_id', 'DESC');
    $this->db->limit(1);
    
    $order_data = $this->db->get()->row();

    if ($order_data) {
        // 2. Fetch the products associated with this PO
        $this->db->select('pp.*, p.code as product_code');
        $this->db->from('po_products pp');
        $this->db->join('spare_parts_for_trading p', 'p.id = pp.product_id', 'left');
        $this->db->where('pp.po_id', $order_data->po_id);
        $order_data->items = $this->db->get()->result();
        
        return $order_data;
    }
    
    return null;
}

public function get_running_orders()
{
    $this->db->select("
        so.order_id,
        so.order_date,
        so.order_value,
        so.status,
        so.opportunity_id,
        c.company_name,
        po.po_no,
        op.op_no,
        op.op_type,
        CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name,
        os.stage_name as current_progress_stage
    ");
    $this->db->from('spares_orders so');
    $this->db->join('spares_customers c', 'c.customer_id = so.customer_id', 'left');
    $this->db->join('purchase_orders po', 'po.po_id = so.po_id', 'left');
    $this->db->join('opportunities op', 'op.opportunity_id = so.opportunity_id', 'left');
    $this->db->join('system_users u', 'u.user_id = so.marketing_person_id', 'left');
    // FIXED: Changed os.id to os.stage_id to match your table structure
    $this->db->join('spares_order_stages os', 'os.stage_id = so.current_stage_id', 'left');

    // Only show orders that are not Completed or Cancelled
    $this->db->where_in('so.status', array('Running', 'In Production', 'Dispatched', 'Pending'));

    // Apply role-based filtering
    $user_data = $this->session->userdata('logged_in');
    $user_role = $user_data['role'];
    
    if ($user_role != 12 && $user_role != 41) { 
        $user_id = $user_data['user_id'];
        $this->db->where('so.marketing_person_id', $user_id);
    }

    $this->db->order_by('so.order_date', 'DESC');
    
    return $this->db->get()->result();
}

public function get_order_details_for_tracking($order_id)
{
    $data = [];

    // 1. Get Master Order Data
    $this->db->select('so.*, c.company_name, po.po_no, CONCAT(u.first_name, " ", u.last_name) as marketing_person_name, sos.stage_name as current_stage_name');
    $this->db->from('spares_orders so');
    $this->db->join('spares_customers c', 'c.customer_id = so.customer_id', 'left');
    $this->db->join('purchase_orders po', 'po.po_id = so.po_id', 'left');
    $this->db->join('system_users u', 'u.user_id = so.marketing_person_id', 'left');
    $this->db->join('spares_order_stages sos', 'sos.stage_id = so.current_stage_id', 'left');
    $this->db->where('so.order_id', $order_id);
    $data['order_details'] = $this->db->get()->row();
    
    if (!$data['order_details']) return false; // Exit if order not found

    // 2. Get Order Products (from the related PO)
    $this->db->select('pop.*, p.code as product_code');
    $this->db->from('po_products pop');
    $this->db->join('spare_parts_for_trading p', 'p.id = pop.product_id', 'left');
    $this->db->where('pop.po_id', $data['order_details']->po_id);
    $data['products'] = $this->db->get()->result();

    // 3. Get Order History
    $this->db->select('sor.*, sos.stage_name, CONCAT(u.first_name, " ", u.last_name) as added_by_name');
    $this->db->from('spares_order_remarks sor');
    $this->db->join('spares_order_stages sos', 'sos.stage_id = sor.order_stage_id');
    $this->db->join('system_users u', 'u.user_id = sor.added_by');
    $this->db->where('sor.order_id', $order_id);
    $this->db->order_by('sor.remark_id', 'DESC');
    $data['history'] = $this->db->get()->result();

    // 4. Get all available stages for the dropdown
    $this->db->order_by('sort_order', 'ASC');
    $data['all_stages'] = $this->db->get('spares_order_stages')->result();
    
    return $data;
}

public function get_kpi_report_data($filters = [])
{
    $data = [];
    
    // 1. Funnel Metrics: Unique opportunities vs unique won stage (ID 10)
    $this->db->select("
        COUNT(DISTINCT op.opportunity_id) as total_opps,
        COUNT(DISTINCT CASE WHEN spr.lead_stage = 10 THEN spr.lead_id END) as won_count
    ");
    $this->db->from('opportunities op');
    $this->db->join('spare_progress_remarks spr', 'spr.lead_id = op.opportunity_id', 'left');
    if(!empty($filters['from_date'])) $this->db->where('op.op_date >=', $filters['from_date']);
    if(!empty($filters['to_date'])) $this->db->where('op.op_date <=', $filters['to_date']);
    $data['funnel'] = $this->db->get()->row();

    // 2. Market Segmentation: Revenue based on finalized orders
    $this->db->select('op.op_type, SUM(so.order_value) as total_val, COUNT(so.order_id) as vol');
    $this->db->from('spares_orders so');
    $this->db->join('opportunities op', 'op.opportunity_id = so.opportunity_id');
    if(!empty($filters['from_date'])) $this->db->where('so.order_date >=', $filters['from_date']);
    if(!empty($filters['to_date'])) $this->db->where('so.order_date <=', $filters['to_date']);
    $this->db->group_by('op.op_type');
    $data['revenue_split'] = $this->db->get()->result();

    // 3. Team Leaderboard: Top performing executives
    $this->db->select("CONCAT(u.first_name, ' ', u.last_name) as name, COUNT(so.order_id) as sales_count, SUM(so.order_value) as total_revenue");
    $this->db->from('spares_orders so');
    $this->db->join('system_users u', 'u.user_id = so.marketing_person_id');
    if(!empty($filters['from_date'])) $this->db->where('so.order_date >=', $filters['from_date']);
    if(!empty($filters['to_date'])) $this->db->where('so.order_date <=', $filters['to_date']);
    $this->db->group_by('so.marketing_person_id');
    $this->db->order_by('total_revenue', 'DESC');
    $data['user_performance'] = $this->db->get()->result();

    // 4. NEW: Pipeline Breakdown (Count per stage)
    // Subquery gets the latest stage for every opportunity
    $latest_stages = "(SELECT lead_id, lead_stage FROM spare_progress_remarks WHERE id IN (SELECT MAX(id) FROM spare_progress_remarks GROUP BY lead_id)) as current_status";
    $this->db->select('sls.lead_name as stage, COUNT(op.opportunity_id) as count');
    $this->db->from('opportunities op');
    $this->db->join($latest_stages, 'current_status.lead_id = op.opportunity_id', 'left');
    $this->db->join('spare_lead_stage sls', 'sls.lead_id = current_status.lead_stage', 'left');
    $this->db->group_by('sls.lead_id');
    $this->db->order_by('sls.sort_order', 'ASC');
    $data['pipeline_breakdown'] = $this->db->get()->result();

    return $data;
}

}
