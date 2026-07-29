<?php
class Dispatch_model extends CI_Model {

    public function get_dispatch_kpis() {
        $this->db->select('
            COUNT(id) as total_machines,
            SUM(invoice_amt) as total_invoiced,
            SUM(payment_recd) as total_received,
            SUM(balance_amt) as total_balance
        ');
        // Joining with your machine design table for context
        $this->db->from('basic_machine_df_design_form_table');
        return $this->db->get()->row_array();
    }

    public function get_dispatch_report($filters = []) {
        $this->db->select('df.*, po.reference_no as po_number, po.added_on as po_date');
        $this->db->from('basic_machine_df_design_form_table df');
        $this->db->join('purchase_orders po', 'df.po_id = po.id', 'left'); // Hypothetical PO table
        
        if(!empty($filters['from_date'])) {
            $this->db->where('df.invoice_date >=', $filters['from_date']);
        }
        
        return $this->db->get()->result();
    }
}