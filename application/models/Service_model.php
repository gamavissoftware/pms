<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service_model extends CI_Model {

    public function get_opportunity_details($id) {
        $this->db->select("
            so.*,
            COALESCE(sc.company_name, cm.company_name) as company_name,
            COALESCE(sc.address, cm.address, so.customer_address) as company_address,
            COALESCE(sc.contact_person, cm.customer_name, '') as customer_contact_name,
            COALESCE(sc.contact_person_no, cm.contact_no, so.customer_contact_no) as customer_contact_number,
            COALESCE(sc.email, cm.email, so.customer_email) as customer_email_address,
            COALESCE(sc.tax_number, cm.gst, '') as customer_gst_number,
            COALESCE(sc.country_id, cm.country, 101) as customer_country_id,
            CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name,
            sls.stage_name as current_stage_name
        ");
        $this->db->from('service_opportunities so');
        $this->db->join('spares_customers sc', 'sc.customer_id = so.customer_id AND so.customer_table_origin IN ("spare", "spares")', 'left');
        $this->db->join('customer_detail cm', 'cm.id = so.customer_id AND (so.customer_table_origin IS NULL OR so.customer_table_origin NOT IN ("spare", "spares"))', 'left');
        $this->db->join('system_users u', 'u.user_id = so.marketing_person_id', 'left');
        $this->db->join('service_lead_stages sls', 'sls.stage_id = so.current_stage_id', 'left');
        $this->db->where('so.opportunity_id', $id);
        return $this->db->get()->row();
    }

    public function get_opportunity_history($id) {
        $this->db->select("sh.*, sls.stage_name, CONCAT(u.first_name, ' ', u.last_name) as added_by_name");
        $this->db->from('service_progress_history sh');
        $this->db->join('service_lead_stages sls', 'sls.stage_id = sh.stage_id', 'left');
        $this->db->join('system_users u', 'u.user_id = sh.added_by', 'left');
        $this->db->where('sh.opportunity_id', $id);
        $this->db->order_by('sh.history_id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_next_stages($current_stage_id) {
        if ((int) $current_stage_id === 7) {
            $stage = $this->db->query(
                "SELECT stage_id as lead_id, stage_name as lead_name, app_access
                 FROM service_lead_stages
                 WHERE LOWER(TRIM(stage_name)) = ?
                 LIMIT 1",
                ['create pi']
            )->row_array();

            if (!empty($stage)) {
                $stage['followup_date_required'] = false;
                return [$stage];
            }
        }

        // Logic: Show the immediate next stage based on sort_order
        $this->db->select('sort_order');
        $current = $this->db->get_where('service_lead_stages', ['stage_id' => $current_stage_id])->row();
        
        $this->db->select('stage_id as lead_id, stage_name as lead_name,app_access');
        $this->db->from('service_lead_stages');
        $this->db->where('sort_order >', $current->sort_order ?? 0);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->limit(1); // Force step-by-step movement
        $stages = $this->db->get()->result_array();

        // Add flag for followup requirement (custom logic)
        foreach ($stages as &$s) {
            $s['followup_date_required'] = true; 
        }
        return $stages;
    }
}
