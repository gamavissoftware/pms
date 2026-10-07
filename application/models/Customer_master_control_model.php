<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customer_master_control_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function module_ready()
    {
        return $this->db->table_exists('customer_master_control_history');
    }

    public function format_user_name($title = '', $first_name = '', $last_name = '')
    {
        $parts = array();
        foreach (array($title, $first_name, $last_name) as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $parts[] = $value;
            }
        }

        if (empty($parts)) {
            return '';
        }

        return ucwords(strtolower(implode(' ', $parts)));
    }

    public function get_country_options()
    {
        return $this->db
            ->select('country_id, country_name')
            ->from('countries')
            ->order_by('country_name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_states_by_country($country_id)
    {
        $country_id = (int) $country_id;
        if ($country_id <= 0) {
            return array();
        }

        // SAP-matched states where SAP has states for the country, otherwise the PMS states
        $this->load->library('Sap_service');
        return $this->sap_service->states_for_country($country_id);
    }

    public function get_marketing_brand_options()
    {
        return $this->db
            ->select('id, name')
            ->from('company_brand')
            ->order_by('name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_spares_brand_options()
    {
        return $this->db
            ->select('id, name')
            ->from('spare_company_brand')
            ->order_by('name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_marketing_user_options()
    {
        return $this->db
            ->select('user_id, title, first_name, last_name')
            ->from('system_users')
            ->where('user_status', 1)
            ->where('hide_profile', 0)
            ->order_by('first_name', 'asc')
            ->order_by('last_name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_shipping_customer_options($exclude_customer_id = 0)
    {
        $this->db->select('customer_id, company_name');
        $this->db->from('spares_customers');
        $this->db->where('status', 1);
        if ((int) $exclude_customer_id > 0) {
            $this->db->where('customer_id !=', (int) $exclude_customer_id);
        }
        $this->db->order_by('company_name', 'asc');

        return $this->db->get()->result_array();
    }

    public function get_customers($filters = array())
    {
        $customers = array();
        $source = isset($filters['source']) ? trim((string) $filters['source']) : '';

        if ($source === '' || $source === 'marketing') {
            $customers = array_merge($customers, $this->get_marketing_customers($filters));
        }

        if ($source === '' || $source === 'spares') {
            $customers = array_merge($customers, $this->get_spares_customers($filters));
        }

        $this->apply_history_meta($customers);

        usort($customers, array($this, 'compare_customer_rows'));

        return $customers;
    }

    public function get_quotation_audit_rows()
    {
        $rows = $this->get_customers(array());
        $marketing_usage = $this->get_marketing_quotation_usage();
        $spares_usage = $this->get_spares_quotation_usage();
        $service_usage = $this->get_service_quotation_usage();
        $opportunity_usage = $this->get_customer_opportunities();

        foreach ($rows as &$row) {
            $source = $row['source_type'];
            $id = (int) $row['record_id'];
            $row['marketing_quote_count'] = ($source === 'marketing' && isset($marketing_usage[$id])) ? (int) $marketing_usage[$id] : 0;
            $row['spares_quote_count'] = ($source === 'spares' && isset($spares_usage[$id])) ? (int) $spares_usage[$id] : 0;
            $row['service_quote_count'] = isset($service_usage[$source][$id]) ? (int) $service_usage[$source][$id] : 0;
            $row['total_quote_count'] = $row['marketing_quote_count'] + $row['spares_quote_count'] + $row['service_quote_count'];
            $row['opportunities'] = isset($opportunity_usage[$source][$id]) ? $opportunity_usage[$source][$id] : array();
            $row['opportunity_count'] = count($row['opportunities']);
        }
        unset($row);

        return $rows;
    }

    private function get_customer_opportunities()
    {
        $result = array('marketing' => array(), 'spares' => array());
        if ($this->db->table_exists('leads')) {
            $sql = "SELECT l.id AS opportunity_id, l.company_name AS customer_id, COALESCE(NULLIF(l.unique_id, ''), CONCAT('Lead #', l.id)) AS opportunity_no,
                           COALESCE(ls.lead_name, 'Stage not recorded') AS stage_name
                    FROM leads l
                    LEFT JOIN progress_remarks pr ON pr.id = (SELECT MAX(pr2.id) FROM progress_remarks pr2 WHERE pr2.lead_id = l.id)
                    LEFT JOIN lead_stage ls ON ls.lead_id = pr.lead_status
                    WHERE l.company_name IS NOT NULL AND l.company_name > 0";
            foreach ($this->db->query($sql)->result_array() as $row) {
                $result['marketing'][(int)$row['customer_id']][] = array('module' => 'Marketing', 'id' => (int)$row['opportunity_id'], 'number' => $row['opportunity_no'], 'stage' => $row['stage_name'], 'url' => 'Leads/view_detail/' . (int)$row['opportunity_id']);
            }
        }
        if ($this->db->table_exists('opportunities')) {
            $sql = "SELECT o.opportunity_id, o.customer_id, COALESCE(NULLIF(o.op_no, ''), CONCAT('Opportunity #', o.opportunity_id)) AS opportunity_no,
                           COALESCE(s.lead_name, 'Stage not recorded') AS stage_name
                    FROM opportunities o
                    LEFT JOIN spare_progress_remarks pr ON pr.id = (SELECT MAX(pr2.id) FROM spare_progress_remarks pr2 WHERE pr2.lead_id = o.opportunity_id)
                    LEFT JOIN spare_lead_stage s ON s.lead_id = pr.lead_stage
                    WHERE o.customer_id IS NOT NULL AND o.customer_id > 0";
            foreach ($this->db->query($sql)->result_array() as $row) {
                $result['spares'][(int)$row['customer_id']][] = array('module' => 'Spares', 'id' => (int)$row['opportunity_id'], 'number' => $row['opportunity_no'], 'stage' => $row['stage_name'], 'url' => 'Spares/opportunity_detail/' . (int)$row['opportunity_id']);
            }
        }
        if ($this->db->table_exists('service_opportunities')) {
            $sql = "SELECT so.opportunity_id, so.customer_id, CASE WHEN so.customer_table_origin IN ('spare','spares') THEN 'spares' ELSE 'marketing' END AS source_type,
                           COALESCE(NULLIF(so.op_no, ''), CONCAT('Service #', so.opportunity_id)) AS opportunity_no,
                           COALESCE(s.stage_name, 'Stage not recorded') AS stage_name
                    FROM service_opportunities so
                    LEFT JOIN service_lead_stages s ON s.stage_id = so.current_stage_id
                    WHERE so.customer_id IS NOT NULL AND so.customer_id > 0";
            foreach ($this->db->query($sql)->result_array() as $row) {
                $source = $row['source_type'];
                $result[$source][(int)$row['customer_id']][] = array('module' => 'Service', 'id' => (int)$row['opportunity_id'], 'number' => $row['opportunity_no'], 'stage' => $row['stage_name'], 'url' => 'ServiceLeads/opportunity_detail/' . (int)$row['opportunity_id']);
            }
        }
        return $result;
    }

    private function get_marketing_quotation_usage()
    {
        if (!$this->db->table_exists('quotation_customer_data') || !$this->db->table_exists('leads')) {
            return array();
        }

        $sql = "SELECT x.customer_id, COUNT(DISTINCT x.quote_id) AS quote_count
                FROM (
                    SELECT q.id AS quote_id, q.customer_id
                    FROM quotation_customer_data q
                    WHERE q.customer_id IS NOT NULL AND q.customer_id > 0
                    UNION ALL
                    SELECT q.id AS quote_id, l.company_name AS customer_id
                    FROM quotation_customer_data q
                    INNER JOIN leads l ON l.id = q.lead_id
                    WHERE l.company_name IS NOT NULL AND l.company_name > 0
                ) x
                GROUP BY x.customer_id";
        return $this->usage_rows_to_map($this->db->query($sql)->result_array());
    }

    private function get_spares_quotation_usage()
    {
        if (!$this->db->table_exists('quotations')) {
            return array();
        }

        $sql = "SELECT x.customer_id, COUNT(DISTINCT x.quote_id) AS quote_count
                FROM (
                    SELECT q.quotation_id AS quote_id, q.customer_id
                    FROM quotations q
                    WHERE q.customer_id IS NOT NULL AND q.customer_id > 0";
        if ($this->db->table_exists('opportunities')) {
            $sql .= " UNION ALL
                      SELECT q.quotation_id AS quote_id, o.customer_id
                      FROM quotations q
                      INNER JOIN opportunities o ON o.opportunity_id = q.opportunity_id
                      WHERE o.customer_id IS NOT NULL AND o.customer_id > 0";
        }
        $sql .= ") x GROUP BY x.customer_id";

        return $this->usage_rows_to_map($this->db->query($sql)->result_array());
    }

    private function get_service_quotation_usage()
    {
        $usage = array('marketing' => array(), 'spares' => array());
        if (!$this->db->table_exists('service_quotations') || !$this->db->table_exists('service_opportunities')) {
            return $usage;
        }

        $sql = "SELECT
                    CASE WHEN so.customer_table_origin IN ('spare', 'spares') THEN 'spares' ELSE 'marketing' END AS source_type,
                    so.customer_id,
                    COUNT(DISTINCT sq.id) AS quote_count
                FROM service_quotations sq
                INNER JOIN service_opportunities so ON so.opportunity_id = sq.opportunity_id
                WHERE so.customer_id IS NOT NULL AND so.customer_id > 0
                GROUP BY source_type, so.customer_id";
        foreach ($this->db->query($sql)->result_array() as $row) {
            $source = $row['source_type'];
            $usage[$source][(int) $row['customer_id']] = (int) $row['quote_count'];
        }
        return $usage;
    }

    private function usage_rows_to_map($rows)
    {
        $result = array();
        foreach ($rows as $row) {
            $result[(int) $row['customer_id']] = (int) $row['quote_count'];
        }
        return $result;
    }

    public function delete_unused_customer($source, $record_id, $changed_by)
    {
        $source = trim((string) $source);
        $record_id = (int) $record_id;
        $audit_rows = $this->get_quotation_audit_rows();
        $target = null;
        foreach ($audit_rows as $row) {
            if ($row['source_type'] === $source && (int) $row['record_id'] === $record_id) {
                $target = $row;
                break;
            }
        }
        if (!$target || (int) $target['total_quote_count'] > 0) {
            return array('success' => false, 'message' => 'Deletion stopped because the record now has quotation usage or no longer exists.');
        }

        $snapshot = $source === 'marketing'
            ? $this->get_marketing_customer_snapshot($record_id)
            : $this->get_spares_customer_snapshot($record_id);
        if (empty($snapshot)) {
            return array('success' => false, 'message' => 'Customer record was not found.');
        }

        $this->db->trans_start();
        $this->log_history($source, $record_id, 'DELETED_UNUSED_DUPLICATE', (int) $changed_by, $snapshot, array(), 'Deleted from duplicate quotation audit.', array('record_deleted'));
        if ($source === 'marketing') {
            $this->delete_marketing_opportunities($record_id);
            $this->delete_service_opportunities($source, $record_id);
            if ($this->db->table_exists('company_multiple_contacts')) {
                $this->db->where('customer_id', $record_id)->delete('company_multiple_contacts');
            }
            $this->db->where('id', $record_id)->delete('customer_detail');
        } else {
            $this->delete_spares_opportunities($record_id);
            $this->delete_service_opportunities($source, $record_id);
            $this->db->where('shipping_customer_id', $record_id)->update('spares_customers', array('shipping_customer_id' => null));
            $this->db->where('customer_id', $record_id)->delete('spares_customers');
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return array('success' => false, 'message' => 'The customer could not be deleted because it is still linked to other records.');
        }
        return array('success' => true, 'message' => 'Unused duplicate customer and its zero-quotation opportunities deleted successfully.');
    }

    private function delete_marketing_opportunities($customer_id)
    {
        if (!$this->db->table_exists('leads')) return;
        $ids = array_column($this->db->select('id')->from('leads')->where('company_name', (int)$customer_id)->get()->result_array(), 'id');
        if (empty($ids)) return;
        foreach (array('progress_remarks', 'lead_products', 'lead_assigned_to_team_member') as $table) {
            if ($this->db->table_exists($table) && $this->db->field_exists('lead_id', $table)) $this->db->where_in('lead_id', $ids)->delete($table);
        }
        $this->db->where_in('id', $ids)->delete('leads');
    }

    private function delete_spares_opportunities($customer_id)
    {
        if (!$this->db->table_exists('opportunities')) return;
        $ids = array_column($this->db->select('opportunity_id')->from('opportunities')->where('customer_id', (int)$customer_id)->get()->result_array(), 'opportunity_id');
        if (empty($ids)) return;
        foreach (array('opportunity_products', 'spare_progress_remarks') as $table) {
            if (!$this->db->table_exists($table)) continue;
            $field = $table === 'spare_progress_remarks' ? 'lead_id' : 'opportunity_id';
            $this->db->where_in($field, $ids)->delete($table);
        }
        $this->db->where_in('opportunity_id', $ids)->delete('opportunities');
    }

    private function delete_service_opportunities($source, $customer_id)
    {
        if (!$this->db->table_exists('service_opportunities')) return;
        $this->db->select('opportunity_id')->from('service_opportunities')->where('customer_id', (int)$customer_id);
        if ($source === 'spares') $this->db->where_in('customer_table_origin', array('spare', 'spares'));
        else $this->db->group_start()->where('customer_table_origin IS NULL', null, false)->or_where_not_in('customer_table_origin', array('spare', 'spares'))->group_end();
        $ids = array_column($this->db->get()->result_array(), 'opportunity_id');
        if (empty($ids)) return;
        foreach (array('service_progress_history') as $table) {
            if ($this->db->table_exists($table) && $this->db->field_exists('opportunity_id', $table)) $this->db->where_in('opportunity_id', $ids)->delete($table);
        }
        $this->db->where_in('opportunity_id', $ids)->delete('service_opportunities');
    }

    public function compare_customer_rows($left, $right)
    {
        $left_timestamp = strtotime(!empty($left['sort_date']) ? $left['sort_date'] : '1970-01-01 00:00:00');
        $right_timestamp = strtotime(!empty($right['sort_date']) ? $right['sort_date'] : '1970-01-01 00:00:00');

        if ($left_timestamp === $right_timestamp) {
            $left_id = !empty($left['record_id']) ? (int) $left['record_id'] : 0;
            $right_id = !empty($right['record_id']) ? (int) $right['record_id'] : 0;
            if ($left_id === $right_id) {
                return 0;
            }

            return ($left_id < $right_id) ? 1 : -1;
        }

        return ($left_timestamp < $right_timestamp) ? 1 : -1;
    }

    public function get_customer_detail($source, $record_id)
    {
        if ($source === 'marketing') {
            return $this->get_marketing_customer_detail($record_id);
        }

        if ($source === 'spares') {
            return $this->get_spares_customer_detail($record_id);
        }

        return array();
    }

    public function get_customer_history($source, $record_id)
    {
        if (!$this->module_ready()) {
            return array();
        }

        $rows = $this->db
            ->select('
                h.*,
                u.title,
                u.first_name,
                u.last_name
            ')
            ->from('customer_master_control_history h')
            ->join('system_users u', 'u.user_id = h.changed_by', 'left')
            ->where('h.source_type', trim((string) $source))
            ->where('h.source_record_id', (int) $record_id)
            ->order_by('h.changed_on', 'desc')
            ->order_by('h.id', 'desc')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['changed_by_name'] = $this->format_user_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );

            $changed_fields = array();
            if (!empty($row['changed_fields'])) {
                $decoded = json_decode($row['changed_fields'], true);
                if (is_array($decoded)) {
                    $changed_fields = $decoded;
                }
            }
            $row['changed_fields_list'] = $changed_fields;
        }
        unset($row);

        return $rows;
    }

    public function update_marketing_customer($record_id, $changed_by, $payload = array())
    {
        $current_snapshot = $this->get_marketing_customer_snapshot($record_id);
        if (empty($current_snapshot)) {
            return array(
                'success' => false,
                'message' => 'Marketing customer record not found.'
            );
        }

        $company_name = trim((string) (isset($payload['company_name']) ? $payload['company_name'] : ''));
        if ($company_name === '') {
            return array(
                'success' => false,
                'message' => 'Company name is required.'
            );
        }

        $resolved_brand_value = $this->resolve_marketing_brand_value(
            isset($payload['company_brand']) ? $payload['company_brand'] : '',
            isset($current_snapshot['company_brand']) ? $current_snapshot['company_brand'] : ''
        );

        $next_snapshot = array(
            'company_id' => $this->normalise_int_string(isset($payload['company_id']) ? $payload['company_id'] : ''),
            'customer_ref_no' => $this->normalise_int_string(isset($payload['customer_ref_no']) ? $payload['customer_ref_no'] : ''),
            'title' => trim((string) (isset($payload['title']) ? $payload['title'] : '')),
            'company_name' => $company_name,
            'customer_name' => trim((string) (isset($payload['customer_name']) ? $payload['customer_name'] : '')),
            'customer_designation' => trim((string) (isset($payload['customer_designation']) ? $payload['customer_designation'] : '')),
            'customer_alias' => trim((string) (isset($payload['customer_alias']) ? $payload['customer_alias'] : '')),
            'company_brand' => $resolved_brand_value,
            'email' => trim((string) (isset($payload['email']) ? $payload['email'] : '')),
            'designation' => trim((string) (isset($payload['designation']) ? $payload['designation'] : '')),
            'branchlocation' => trim((string) (isset($payload['branchlocation']) ? $payload['branchlocation'] : '')),
            'contact_no' => trim((string) (isset($payload['contact_no']) ? $payload['contact_no'] : '')),
            'alt_contact' => trim((string) (isset($payload['alt_contact']) ? $payload['alt_contact'] : '')),
            'country' => $this->normalise_int_string(isset($payload['country']) ? $payload['country'] : ''),
            'state' => $this->normalise_int_string(isset($payload['state']) ? $payload['state'] : ''),
            'city' => trim((string) (isset($payload['city']) ? $payload['city'] : '')),
            'pincode' => trim((string) (isset($payload['pincode']) ? $payload['pincode'] : '')),
            'gst' => trim((string) (isset($payload['gst']) ? $payload['gst'] : '')),
            'pan' => trim((string) (isset($payload['pan']) ? $payload['pan'] : '')),
            'address' => trim((string) (isset($payload['address']) ? $payload['address'] : '')),
            'bill_address' => trim((string) (isset($payload['bill_address']) ? $payload['bill_address'] : '')),
            'bill_state' => $this->normalise_int_string(isset($payload['bill_state']) ? $payload['bill_state'] : ''),
            'bill_city' => trim((string) (isset($payload['bill_city']) ? $payload['bill_city'] : '')),
            'bill_pincode' => trim((string) (isset($payload['bill_pincode']) ? $payload['bill_pincode'] : '')),
            'bill_email' => trim((string) (isset($payload['bill_email']) ? $payload['bill_email'] : '')),
            'ship_address' => trim((string) (isset($payload['ship_address']) ? $payload['ship_address'] : '')),
            'ship_state' => $this->normalise_int_string(isset($payload['ship_state']) ? $payload['ship_state'] : ''),
            'ship_city' => trim((string) (isset($payload['ship_city']) ? $payload['ship_city'] : '')),
            'ship_pincode' => trim((string) (isset($payload['ship_pincode']) ? $payload['ship_pincode'] : '')),
            'ship_email' => trim((string) (isset($payload['ship_email']) ? $payload['ship_email'] : '')),
            'msme_number' => trim((string) (isset($payload['msme_number']) ? $payload['msme_number'] : '')),
            'credit_period' => $this->normalise_int_string(isset($payload['credit_period']) ? $payload['credit_period'] : ''),
            'credit_limit' => $this->normalise_decimal_string(isset($payload['credit_limit']) ? $payload['credit_limit'] : ''),
            'order_max_limit' => $this->normalise_decimal_string(isset($payload['order_max_limit']) ? $payload['order_max_limit'] : ''),
            'payment_type' => $this->normalise_int_string(isset($payload['payment_type']) ? $payload['payment_type'] : ''),
            'credit_days' => $this->normalise_int_string(isset($payload['credit_days']) ? $payload['credit_days'] : ''),
            'tds_appl' => $this->normalise_toggle_string(isset($payload['tds_appl']) ? $payload['tds_appl'] : '0'),
            'tds_per' => $this->normalise_decimal_string(isset($payload['tds_per']) ? $payload['tds_per'] : ''),
            'customer_type' => $this->normalise_int_string(isset($payload['customer_type']) ? $payload['customer_type'] : ''),
            'payment_term_approval' => $this->normalise_toggle_string(isset($payload['payment_term_approval']) ? $payload['payment_term_approval'] : '1'),
            'payment_approved_On' => $this->normalise_datetime_string(isset($payload['payment_approved_On']) ? $payload['payment_approved_On'] : ''),
            'payment_approved_By' => $this->normalise_int_string(isset($payload['payment_approved_By']) ? $payload['payment_approved_By'] : ''),
            'assigned_to' => $this->normalise_int_string(isset($payload['assigned_to']) ? $payload['assigned_to'] : ''),
            'assign_customer_for_trail' => $this->normalise_toggle_string(isset($payload['assign_customer_for_trail']) ? $payload['assign_customer_for_trail'] : '0'),
            'opening_balance' => $this->normalise_decimal_string(isset($payload['opening_balance']) ? $payload['opening_balance'] : ''),
            'gst_verified' => $this->normalise_toggle_string(isset($payload['gst_verified']) ? $payload['gst_verified'] : '0'),
            'exhibitiion_email_sent' => $this->normalise_toggle_string(isset($payload['exhibitiion_email_sent']) ? $payload['exhibitiion_email_sent'] : '0'),
            'banglore_exhibition' => $this->normalise_toggle_string(isset($payload['banglore_exhibition']) ? $payload['banglore_exhibition'] : '0'),
            'status' => $this->normalise_toggle_string(isset($payload['status']) ? $payload['status'] : '1'),
            'additional_contacts' => $this->normalise_contact_rows(isset($payload['additional_contacts']) ? $payload['additional_contacts'] : array())
        );

        $before_compare = $this->normalise_snapshot($current_snapshot);
        $after_compare = $this->normalise_snapshot($next_snapshot);
        if ($before_compare === $after_compare) {
            return array(
                'success' => true,
                'changed' => false,
                'message' => 'No customer information changed.'
            );
        }

        $update_data = array(
            'company_id' => (int) $next_snapshot['company_id'],
            'customer_ref_no' => (int) $next_snapshot['customer_ref_no'],
            'title' => $next_snapshot['title'],
            'company_name' => $next_snapshot['company_name'],
            'customer_name' => $next_snapshot['customer_name'],
            'customer_designation' => $next_snapshot['customer_designation'],
            'customer_alias' => $next_snapshot['customer_alias'],
            'company_brand' => $next_snapshot['company_brand'],
            'email' => $next_snapshot['email'],
            'designation' => $next_snapshot['designation'],
            'branchlocation' => $next_snapshot['branchlocation'],
            'contact_no' => $next_snapshot['contact_no'],
            'alt_contact' => $next_snapshot['alt_contact'],
            'country' => (int) $next_snapshot['country'],
            'state' => (int) $next_snapshot['state'],
            'city' => $next_snapshot['city'],
            'pincode' => (int) $this->normalise_int_string($next_snapshot['pincode']),
            'gst' => $next_snapshot['gst'],
            'pan' => $next_snapshot['pan'],
            'address' => $next_snapshot['address'],
            'bill_address' => $next_snapshot['bill_address'],
            'bill_state' => (int) $next_snapshot['bill_state'],
            'bill_city' => $next_snapshot['bill_city'],
            'bill_pincode' => $next_snapshot['bill_pincode'],
            'bill_email' => $next_snapshot['bill_email'],
            'ship_address' => $next_snapshot['ship_address'],
            'ship_state' => (int) $next_snapshot['ship_state'],
            'ship_city' => $next_snapshot['ship_city'],
            'ship_pincode' => $next_snapshot['ship_pincode'],
            'ship_email' => $next_snapshot['ship_email'],
            'msme_number' => $next_snapshot['msme_number'],
            'credit_period' => (int) $next_snapshot['credit_period'],
            'credit_limit' => $next_snapshot['credit_limit'],
            'order_max_limit' => $next_snapshot['order_max_limit'],
            'payment_type' => (int) $next_snapshot['payment_type'],
            'credit_days' => (int) $next_snapshot['credit_days'],
            'tds_appl' => (int) $next_snapshot['tds_appl'],
            'tds_per' => $next_snapshot['tds_per'],
            'customer_type' => (int) $next_snapshot['customer_type'],
            'payment_term_approval' => (int) $next_snapshot['payment_term_approval'],
            'payment_approved_On' => $next_snapshot['payment_approved_On'] !== '' ? $next_snapshot['payment_approved_On'] : '0000-00-00 00:00:00',
            'payment_approved_By' => (int) $next_snapshot['payment_approved_By'],
            'assigned_to' => (int) $next_snapshot['assigned_to'],
            'assign_customer_for_trail' => (int) $next_snapshot['assign_customer_for_trail'],
            'opening_balance' => $next_snapshot['opening_balance'],
            'gst_verified' => (int) $next_snapshot['gst_verified'],
            'exhibitiion_email_sent' => (int) $next_snapshot['exhibitiion_email_sent'],
            'banglore_exhibition' => (int) $next_snapshot['banglore_exhibition'],
            'status' => (int) $next_snapshot['status'],
            'updated_on' => date('Y-m-d H:i:s'),
            'updated_by' => (int) $changed_by
        );

        $changed_fields = $this->build_changed_fields(
            $current_snapshot,
            $next_snapshot,
            $this->get_marketing_field_labels()
        );

        $this->db->trans_start();

        $this->db->where('id', (int) $record_id);
        $this->db->update('customer_detail', $update_data);

        if (
            $this->normalise_snapshot(isset($current_snapshot['additional_contacts']) ? $current_snapshot['additional_contacts'] : array()) !==
            $this->normalise_snapshot($next_snapshot['additional_contacts'])
        ) {
            $this->sync_marketing_contacts($record_id, $next_snapshot['additional_contacts'], $changed_by);
        }

        $this->log_history(
            'marketing',
            $record_id,
            'UPDATED_CUSTOMER',
            $changed_by,
            $current_snapshot,
            $next_snapshot,
            isset($payload['change_note']) ? $payload['change_note'] : '',
            $changed_fields
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return array(
                'success' => false,
                'message' => 'Could not update the marketing customer right now. Please try again.'
            );
        }

        return array(
            'success' => true,
            'changed' => true,
            'message' => 'Marketing customer information updated successfully.'
        );
    }

    public function update_spares_customer($record_id, $changed_by, $payload = array())
    {
        $current_snapshot = $this->get_spares_customer_snapshot($record_id);
        if (empty($current_snapshot)) {
            return array(
                'success' => false,
                'message' => 'Spares customer record not found.'
            );
        }

        $company_name = trim((string) (isset($payload['company_name']) ? $payload['company_name'] : ''));
        if ($company_name === '') {
            return array(
                'success' => false,
                'message' => 'Company name is required.'
            );
        }

        $resolved_brand_id = $this->resolve_spares_brand_id(isset($payload['brand_id']) ? $payload['brand_id'] : '');

        $next_snapshot = array(
            'company_name' => $company_name,
            'brand_id' => $resolved_brand_id,
            'address' => trim((string) (isset($payload['address']) ? $payload['address'] : '')),
            'country_id' => $this->normalise_int_string(isset($payload['country_id']) ? $payload['country_id'] : ''),
            'state_id' => $this->normalise_int_string(isset($payload['state_id']) ? $payload['state_id'] : ''),
            'email' => trim((string) (isset($payload['email']) ? $payload['email'] : '')),
            'contact_person' => trim((string) (isset($payload['contact_person']) ? $payload['contact_person'] : '')),
            'contact_person_no' => trim((string) (isset($payload['contact_person_no']) ? $payload['contact_person_no'] : '')),
            'alternate_contact_no' => trim((string) (isset($payload['alternate_contact_no']) ? $payload['alternate_contact_no'] : '')),
            'tax_number' => trim((string) (isset($payload['tax_number']) ? $payload['tax_number'] : '')),
            'shipping_customer_id' => $this->normalise_int_string(isset($payload['shipping_customer_id']) ? $payload['shipping_customer_id'] : ''),
            'status' => $this->normalise_toggle_string(isset($payload['status']) ? $payload['status'] : '1')
        );

        $before_compare = $this->normalise_snapshot($current_snapshot);
        $after_compare = $this->normalise_snapshot($next_snapshot);
        if ($before_compare === $after_compare) {
            return array(
                'success' => true,
                'changed' => false,
                'message' => 'No customer information changed.'
            );
        }

        $update_data = array(
            'company_name' => $next_snapshot['company_name'],
            'brand_id' => (int) $next_snapshot['brand_id'],
            'address' => $next_snapshot['address'],
            'country_id' => (int) $next_snapshot['country_id'],
            'state_id' => (int) $next_snapshot['state_id'] > 0 ? (int) $next_snapshot['state_id'] : null,
            'email' => $next_snapshot['email'],
            'contact_person' => $next_snapshot['contact_person'],
            'contact_person_no' => $next_snapshot['contact_person_no'],
            'alternate_contact_no' => $next_snapshot['alternate_contact_no'],
            'tax_number' => $next_snapshot['tax_number'],
            'shipping_customer_id' => (int) $next_snapshot['shipping_customer_id'],
            'status' => (int) $next_snapshot['status']
        );

        // state_id exists only after Database/spares_customers_state_001.sql is run
        if (!$this->db->field_exists('state_id', 'spares_customers')) {
            unset($update_data['state_id'], $current_snapshot['state_id'], $next_snapshot['state_id']);
        }

        $changed_fields = $this->build_changed_fields(
            $current_snapshot,
            $next_snapshot,
            $this->get_spares_field_labels()
        );

        $this->db->trans_start();

        $this->db->where('customer_id', (int) $record_id);
        $this->db->update('spares_customers', $update_data);

        $this->log_history(
            'spares',
            $record_id,
            'UPDATED_CUSTOMER',
            $changed_by,
            $current_snapshot,
            $next_snapshot,
            isset($payload['change_note']) ? $payload['change_note'] : '',
            $changed_fields
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return array(
                'success' => false,
                'message' => 'Could not update the spares customer right now. Please try again.'
            );
        }

        return array(
            'success' => true,
            'changed' => true,
            'message' => 'Spares customer information updated successfully.'
        );
    }

    private function get_marketing_customers($filters)
    {
        $this->db->select("
            c.id,
            c.customer_ref_no,
            c.company_name,
            c.customer_name,
            c.contact_no,
            c.email,
            c.country,
            c.state,
            c.city,
            c.address,
            c.status,
            c.added_on,
            c.added_by,
            c.updated_on,
            c.updated_by,
            c.company_brand,
            CASE
                WHEN c.company_brand REGEXP '^[0-9]+$' THEN cb.name
                ELSE c.company_brand
            END AS brand_name,
            co.country_name,
            st.state_name,
            creator.title AS creator_title,
            creator.first_name AS creator_first_name,
            creator.last_name AS creator_last_name,
            updater.title AS updater_title,
            updater.first_name AS updater_first_name,
            updater.last_name AS updater_last_name,
            (
                SELECT COUNT(1)
                FROM company_multiple_contacts cmc
                WHERE cmc.customer_id = c.id
            ) AS additional_contact_count
        ", false);
        $this->db->from('customer_detail c');
        $this->db->join('company_brand cb', 'cb.id = c.company_brand', 'left');
        $this->db->join('countries co', 'co.country_id = c.country', 'left');
        $this->db->join('states st', 'st.state_id = c.state AND st.country_id = c.country', 'left');
        $this->db->join('system_users creator', 'creator.user_id = c.added_by', 'left');
        $this->db->join('system_users updater', 'updater.user_id = c.updated_by', 'left');

        $this->apply_common_filters('marketing', $filters);
        $this->db->order_by('c.updated_on', 'desc');
        $this->db->order_by('c.added_on', 'desc');
        $rows = $this->db->get()->result_array();

        foreach ($rows as &$row) {
            $created_by_name = $this->format_user_name(
                isset($row['creator_title']) ? $row['creator_title'] : '',
                isset($row['creator_first_name']) ? $row['creator_first_name'] : '',
                isset($row['creator_last_name']) ? $row['creator_last_name'] : ''
            );
            $updated_by_name = $this->format_user_name(
                isset($row['updater_title']) ? $row['updater_title'] : '',
                isset($row['updater_first_name']) ? $row['updater_first_name'] : '',
                isset($row['updater_last_name']) ? $row['updater_last_name'] : ''
            );

            $row = array(
                'source_type' => 'marketing',
                'source_label' => 'Marketing',
                'record_id' => (int) $row['id'],
                'record_code' => trim((string) $row['customer_ref_no']),
                'company_name' => trim((string) $row['company_name']),
                'brand_name' => trim((string) $row['brand_name']),
                'contact_person' => trim((string) $row['customer_name']),
                'contact_no' => trim((string) $row['contact_no']),
                'email' => trim((string) $row['email']),
                'country_name' => trim((string) $row['country_name']),
                'state_name' => trim((string) $row['state_name']),
                'city' => trim((string) $row['city']),
                'location_summary' => $this->build_location_summary($row['country_name'], $row['state_name'], $row['city']),
                'status' => (int) $row['status'],
                'created_on' => $this->safe_datetime_value(isset($row['added_on']) ? $row['added_on'] : ''),
                'created_by_name' => $created_by_name,
                'updated_on' => $this->safe_datetime_value(isset($row['updated_on']) ? $row['updated_on'] : ''),
                'updated_by_name' => $updated_by_name,
                'sort_date' => $this->safe_datetime_value(isset($row['updated_on']) ? $row['updated_on'] : '') !== ''
                    ? $row['updated_on']
                    : $row['added_on'],
                'additional_contact_count' => (int) $row['additional_contact_count']
            );
        }
        unset($row);

        return $rows;
    }

    private function get_spares_customers($filters)
    {
        $this->db->select("
            c.customer_id,
            c.company_name,
            c.brand_id,
            c.address,
            c.country_id,
            c.email,
            c.contact_person,
            c.contact_person_no,
            c.alternate_contact_no,
            c.tax_number,
            c.status,
            c.created_at,
            c.shipping_customer_id,
            co.country_name,
            b.name AS brand_name,
            ship.company_name AS shipping_customer_name
        ", false);
        $this->db->from('spares_customers c');
        $this->db->join('countries co', 'co.country_id = c.country_id', 'left');
        $this->db->join('spare_company_brand b', 'b.id = c.brand_id', 'left');
        $this->db->join('spares_customers ship', 'ship.customer_id = c.shipping_customer_id', 'left');

        $this->apply_common_filters('spares', $filters);
        $this->db->order_by('c.created_at', 'desc');
        $this->db->order_by('c.customer_id', 'desc');
        $rows = $this->db->get()->result_array();

        foreach ($rows as &$row) {
            $row = array(
                'source_type' => 'spares',
                'source_label' => 'Spares',
                'record_id' => (int) $row['customer_id'],
                'record_code' => '',
                'company_name' => trim((string) $row['company_name']),
                'brand_name' => trim((string) $row['brand_name']),
                'contact_person' => trim((string) $row['contact_person']),
                'contact_no' => trim((string) $row['contact_person_no']),
                'email' => trim((string) $row['email']),
                'country_name' => trim((string) $row['country_name']),
                'state_name' => '',
                'city' => '',
                'location_summary' => trim((string) $row['country_name']),
                'status' => (int) $row['status'],
                'created_on' => $this->safe_datetime_value(isset($row['created_at']) ? $row['created_at'] : ''),
                'created_by_name' => '',
                'updated_on' => '',
                'updated_by_name' => '',
                'sort_date' => $row['created_at'],
                'shipping_customer_name' => trim((string) $row['shipping_customer_name']),
                'tax_number' => trim((string) $row['tax_number'])
            );
        }
        unset($row);

        return $rows;
    }

    private function apply_common_filters($source_type, $filters)
    {
        $status = isset($filters['status']) ? trim((string) $filters['status']) : '';
        if ($status === '0' || $status === '1') {
            if ($source_type === 'marketing') {
                $this->db->where('c.status', (int) $status);
            } else {
                $this->db->where('c.status', (int) $status);
            }
        }

        $country_id = isset($filters['country_id']) ? (int) $filters['country_id'] : 0;
        if ($country_id > 0) {
            if ($source_type === 'marketing') {
                $this->db->where('c.country', $country_id);
            } else {
                $this->db->where('c.country_id', $country_id);
            }
        }

        $marketing_user_id = isset($filters['marketing_user_id']) ? (int) $filters['marketing_user_id'] : 0;
        if ($source_type === 'marketing' && $marketing_user_id > 0) {
            $this->db->where('c.added_by', $marketing_user_id);
        }

        $from_date = trim((string) (isset($filters['from_date']) ? $filters['from_date'] : ''));
        if ($this->is_valid_date($from_date)) {
            if ($source_type === 'marketing') {
                $this->db->where('DATE(COALESCE(NULLIF(c.updated_on, "0000-00-00 00:00:00"), c.added_on)) >=', $from_date);
            } else {
                $this->db->where('DATE(c.created_at) >=', $from_date);
            }
        }

        $to_date = trim((string) (isset($filters['to_date']) ? $filters['to_date'] : ''));
        if ($this->is_valid_date($to_date)) {
            if ($source_type === 'marketing') {
                $this->db->where('DATE(COALESCE(NULLIF(c.updated_on, "0000-00-00 00:00:00"), c.added_on)) <=', $to_date);
            } else {
                $this->db->where('DATE(c.created_at) <=', $to_date);
            }
        }

        $keyword = trim((string) (isset($filters['keyword']) ? $filters['keyword'] : ''));
        if ($keyword !== '') {
            $this->db->group_start();
            if ($source_type === 'marketing') {
                $this->db->like('c.company_name', $keyword);
                $this->db->or_like('c.customer_name', $keyword);
                $this->db->or_like('c.email', $keyword);
                $this->db->or_like('c.contact_no', $keyword);
                $this->db->or_like('c.customer_ref_no', $keyword);
                $this->db->or_like('c.gst', $keyword);
                $this->db->or_like('c.address', $keyword);
            } else {
                $this->db->like('c.company_name', $keyword);
                $this->db->or_like('c.contact_person', $keyword);
                $this->db->or_like('c.email', $keyword);
                $this->db->or_like('c.contact_person_no', $keyword);
                $this->db->or_like('c.tax_number', $keyword);
                $this->db->or_like('c.address', $keyword);
            }
            $this->db->group_end();
        }
    }

    private function apply_history_meta(&$customers)
    {
        if (empty($customers)) {
            return;
        }

        $history_map = array();
        if ($this->module_ready()) {
            $marketing_ids = array();
            $spares_ids = array();
            foreach ($customers as $customer) {
                if ($customer['source_type'] === 'marketing') {
                    $marketing_ids[] = (int) $customer['record_id'];
                } elseif ($customer['source_type'] === 'spares') {
                    $spares_ids[] = (int) $customer['record_id'];
                }
            }

            if (!empty($marketing_ids)) {
                $history_map = array_merge($history_map, $this->get_latest_history_for_source('marketing', array_unique($marketing_ids)));
            }
            if (!empty($spares_ids)) {
                $history_map = array_merge($history_map, $this->get_latest_history_for_source('spares', array_unique($spares_ids)));
            }
        }

        foreach ($customers as &$customer) {
            $history_key = $customer['source_type'] . ':' . (int) $customer['record_id'];
            $history_row = isset($history_map[$history_key]) ? $history_map[$history_key] : array();

            $last_action_on = '';
            $last_action_by_name = '';
            if (!empty($history_row)) {
                $last_action_on = !empty($history_row['changed_on']) ? $history_row['changed_on'] : '';
                $last_action_by_name = !empty($history_row['changed_by_name']) ? $history_row['changed_by_name'] : '';
            } elseif (!empty($customer['updated_on'])) {
                $last_action_on = $customer['updated_on'];
                $last_action_by_name = $customer['updated_by_name'];
            } else {
                $last_action_on = $customer['created_on'];
                $last_action_by_name = $customer['created_by_name'];
            }

            $customer['history_changed_fields'] = !empty($history_row['changed_fields_list']) ? $history_row['changed_fields_list'] : array();
            $customer['last_action_on'] = $last_action_on;
            $customer['last_action_by_name'] = $last_action_by_name;
            if ($last_action_on !== '') {
                $customer['sort_date'] = $last_action_on;
            }
        }
        unset($customer);
    }

    private function get_latest_history_for_source($source_type, $record_ids)
    {
        if (empty($record_ids)) {
            return array();
        }

        $rows = $this->db
            ->select('
                h.*,
                u.title,
                u.first_name,
                u.last_name
            ')
            ->from('customer_master_control_history h')
            ->join('system_users u', 'u.user_id = h.changed_by', 'left')
            ->where('h.source_type', trim((string) $source_type))
            ->where_in('h.source_record_id', $record_ids)
            ->order_by('h.changed_on', 'desc')
            ->order_by('h.id', 'desc')
            ->get()
            ->result_array();

        $result = array();
        foreach ($rows as $row) {
            $key = $source_type . ':' . (int) $row['source_record_id'];
            if (isset($result[$key])) {
                continue;
            }

            $changed_fields = array();
            if (!empty($row['changed_fields'])) {
                $decoded = json_decode($row['changed_fields'], true);
                if (is_array($decoded)) {
                    $changed_fields = $decoded;
                }
            }

            $row['changed_by_name'] = $this->format_user_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );
            $row['changed_fields_list'] = $changed_fields;
            $result[$key] = $row;
        }

        return $result;
    }

    private function get_marketing_customer_detail($record_id)
    {
        $customer = $this->db
            ->select("
                c.*,
                co.country_name,
                st.state_name,
                bst.state_name AS bill_state_name,
                sst.state_name AS ship_state_name,
                CASE
                    WHEN c.company_brand REGEXP '^[0-9]+$' THEN cb.name
                    ELSE c.company_brand
                END AS brand_name,
                creator.title AS creator_title,
                creator.first_name AS creator_first_name,
                creator.last_name AS creator_last_name,
                updater.title AS updater_title,
                updater.first_name AS updater_first_name,
                updater.last_name AS updater_last_name,
                assigned.title AS assigned_title,
                assigned.first_name AS assigned_first_name,
                assigned.last_name AS assigned_last_name,
                approver.title AS approver_title,
                approver.first_name AS approver_first_name,
                approver.last_name AS approver_last_name
            ", false)
            ->from('customer_detail c')
            ->join('company_brand cb', 'cb.id = c.company_brand', 'left')
            ->join('countries co', 'co.country_id = c.country', 'left')
            ->join('states st', 'st.state_id = c.state AND st.country_id = c.country', 'left')
            ->join('states bst', 'bst.state_id = c.bill_state AND bst.country_id = c.country', 'left')
            ->join('states sst', 'sst.state_id = c.ship_state AND sst.country_id = c.country', 'left')
            ->join('system_users creator', 'creator.user_id = c.added_by', 'left')
            ->join('system_users updater', 'updater.user_id = c.updated_by', 'left')
            ->join('system_users assigned', 'assigned.user_id = c.assigned_to', 'left')
            ->join('system_users approver', 'approver.user_id = c.payment_approved_By', 'left')
            ->where('c.id', (int) $record_id)
            ->get()
            ->row_array();

        if (empty($customer)) {
            return array();
        }

        $customer['source_type'] = 'marketing';
        $customer['source_label'] = 'Marketing';
        $customer['created_by_name'] = $this->format_user_name(
            isset($customer['creator_title']) ? $customer['creator_title'] : '',
            isset($customer['creator_first_name']) ? $customer['creator_first_name'] : '',
            isset($customer['creator_last_name']) ? $customer['creator_last_name'] : ''
        );
        $customer['updated_by_name'] = $this->format_user_name(
            isset($customer['updater_title']) ? $customer['updater_title'] : '',
            isset($customer['updater_first_name']) ? $customer['updater_first_name'] : '',
            isset($customer['updater_last_name']) ? $customer['updater_last_name'] : ''
        );
        $customer['assigned_to_name'] = $this->format_user_name(
            isset($customer['assigned_title']) ? $customer['assigned_title'] : '',
            isset($customer['assigned_first_name']) ? $customer['assigned_first_name'] : '',
            isset($customer['assigned_last_name']) ? $customer['assigned_last_name'] : ''
        );
        $customer['payment_approved_by_name'] = $this->format_user_name(
            isset($customer['approver_title']) ? $customer['approver_title'] : '',
            isset($customer['approver_first_name']) ? $customer['approver_first_name'] : '',
            isset($customer['approver_last_name']) ? $customer['approver_last_name'] : ''
        );
        $customer['additional_contacts'] = $this->db
            ->select('contactpersonname, personcontactno, personemailid, designation, branchlocation')
            ->from('company_multiple_contacts')
            ->where('customer_id', (int) $record_id)
            ->order_by('id', 'asc')
            ->get()
            ->result_array();

        return $customer;
    }

    private function get_spares_customer_detail($record_id)
    {
        $customer = $this->db
            ->select('
                c.*,
                co.country_name,
                b.name AS brand_name,
                ship.company_name AS shipping_customer_name
            ')
            ->from('spares_customers c')
            ->join('countries co', 'co.country_id = c.country_id', 'left')
            ->join('spare_company_brand b', 'b.id = c.brand_id', 'left')
            ->join('spares_customers ship', 'ship.customer_id = c.shipping_customer_id', 'left')
            ->where('c.customer_id', (int) $record_id)
            ->get()
            ->row_array();

        if (empty($customer)) {
            return array();
        }

        $customer['source_type'] = 'spares';
        $customer['source_label'] = 'Spares';

        return $customer;
    }

    private function get_marketing_customer_snapshot($record_id)
    {
        $customer = $this->db
            ->select('*')
            ->from('customer_detail')
            ->where('id', (int) $record_id)
            ->get()
            ->row_array();

        if (empty($customer)) {
            return array();
        }

        $contacts = $this->db
            ->select('contactpersonname, personcontactno, personemailid, designation, branchlocation')
            ->from('company_multiple_contacts')
            ->where('customer_id', (int) $record_id)
            ->order_by('id', 'asc')
            ->get()
            ->result_array();

        return array(
            'company_id' => $this->normalise_int_string(isset($customer['company_id']) ? $customer['company_id'] : ''),
            'customer_ref_no' => $this->normalise_int_string(isset($customer['customer_ref_no']) ? $customer['customer_ref_no'] : ''),
            'title' => trim((string) (isset($customer['title']) ? $customer['title'] : '')),
            'company_name' => trim((string) (isset($customer['company_name']) ? $customer['company_name'] : '')),
            'customer_name' => trim((string) (isset($customer['customer_name']) ? $customer['customer_name'] : '')),
            'customer_designation' => trim((string) (isset($customer['customer_designation']) ? $customer['customer_designation'] : '')),
            'customer_alias' => trim((string) (isset($customer['customer_alias']) ? $customer['customer_alias'] : '')),
            'company_brand' => trim((string) (isset($customer['company_brand']) ? $customer['company_brand'] : '')),
            'email' => trim((string) (isset($customer['email']) ? $customer['email'] : '')),
            'designation' => trim((string) (isset($customer['designation']) ? $customer['designation'] : '')),
            'branchlocation' => trim((string) (isset($customer['branchlocation']) ? $customer['branchlocation'] : '')),
            'contact_no' => trim((string) (isset($customer['contact_no']) ? $customer['contact_no'] : '')),
            'alt_contact' => trim((string) (isset($customer['alt_contact']) ? $customer['alt_contact'] : '')),
            'country' => $this->normalise_int_string(isset($customer['country']) ? $customer['country'] : ''),
            'state' => $this->normalise_int_string(isset($customer['state']) ? $customer['state'] : ''),
            'city' => trim((string) (isset($customer['city']) ? $customer['city'] : '')),
            'pincode' => trim((string) (isset($customer['pincode']) ? $customer['pincode'] : '')),
            'gst' => trim((string) (isset($customer['gst']) ? $customer['gst'] : '')),
            'pan' => trim((string) (isset($customer['pan']) ? $customer['pan'] : '')),
            'address' => trim((string) (isset($customer['address']) ? $customer['address'] : '')),
            'bill_address' => trim((string) (isset($customer['bill_address']) ? $customer['bill_address'] : '')),
            'bill_state' => $this->normalise_int_string(isset($customer['bill_state']) ? $customer['bill_state'] : ''),
            'bill_city' => trim((string) (isset($customer['bill_city']) ? $customer['bill_city'] : '')),
            'bill_pincode' => trim((string) (isset($customer['bill_pincode']) ? $customer['bill_pincode'] : '')),
            'bill_email' => trim((string) (isset($customer['bill_email']) ? $customer['bill_email'] : '')),
            'ship_address' => trim((string) (isset($customer['ship_address']) ? $customer['ship_address'] : '')),
            'ship_state' => $this->normalise_int_string(isset($customer['ship_state']) ? $customer['ship_state'] : ''),
            'ship_city' => trim((string) (isset($customer['ship_city']) ? $customer['ship_city'] : '')),
            'ship_pincode' => trim((string) (isset($customer['ship_pincode']) ? $customer['ship_pincode'] : '')),
            'ship_email' => trim((string) (isset($customer['ship_email']) ? $customer['ship_email'] : '')),
            'msme_number' => trim((string) (isset($customer['msme_number']) ? $customer['msme_number'] : '')),
            'credit_period' => $this->normalise_int_string(isset($customer['credit_period']) ? $customer['credit_period'] : ''),
            'credit_limit' => $this->normalise_decimal_string(isset($customer['credit_limit']) ? $customer['credit_limit'] : ''),
            'order_max_limit' => $this->normalise_decimal_string(isset($customer['order_max_limit']) ? $customer['order_max_limit'] : ''),
            'payment_type' => $this->normalise_int_string(isset($customer['payment_type']) ? $customer['payment_type'] : ''),
            'credit_days' => $this->normalise_int_string(isset($customer['credit_days']) ? $customer['credit_days'] : ''),
            'tds_appl' => $this->normalise_toggle_string(isset($customer['tds_appl']) ? $customer['tds_appl'] : '0'),
            'tds_per' => $this->normalise_decimal_string(isset($customer['tds_per']) ? $customer['tds_per'] : ''),
            'customer_type' => $this->normalise_int_string(isset($customer['customer_type']) ? $customer['customer_type'] : ''),
            'payment_term_approval' => $this->normalise_toggle_string(isset($customer['payment_term_approval']) ? $customer['payment_term_approval'] : '1'),
            'payment_approved_On' => $this->normalise_datetime_string(isset($customer['payment_approved_On']) ? $customer['payment_approved_On'] : ''),
            'payment_approved_By' => $this->normalise_int_string(isset($customer['payment_approved_By']) ? $customer['payment_approved_By'] : ''),
            'assigned_to' => $this->normalise_int_string(isset($customer['assigned_to']) ? $customer['assigned_to'] : ''),
            'assign_customer_for_trail' => $this->normalise_toggle_string(isset($customer['assign_customer_for_trail']) ? $customer['assign_customer_for_trail'] : '0'),
            'opening_balance' => $this->normalise_decimal_string(isset($customer['opening_balance']) ? $customer['opening_balance'] : ''),
            'gst_verified' => $this->normalise_toggle_string(isset($customer['gst_verified']) ? $customer['gst_verified'] : '0'),
            'exhibitiion_email_sent' => $this->normalise_toggle_string(isset($customer['exhibitiion_email_sent']) ? $customer['exhibitiion_email_sent'] : '0'),
            'banglore_exhibition' => $this->normalise_toggle_string(isset($customer['banglore_exhibition']) ? $customer['banglore_exhibition'] : '0'),
            'status' => $this->normalise_toggle_string(isset($customer['status']) ? $customer['status'] : '1'),
            'additional_contacts' => $this->normalise_contact_rows($contacts)
        );
    }

    private function get_spares_customer_snapshot($record_id)
    {
        $customer = $this->db
            ->select('*')
            ->from('spares_customers')
            ->where('customer_id', (int) $record_id)
            ->get()
            ->row_array();

        if (empty($customer)) {
            return array();
        }

        return array(
            'company_name' => trim((string) (isset($customer['company_name']) ? $customer['company_name'] : '')),
            'brand_id' => $this->normalise_int_string(isset($customer['brand_id']) ? $customer['brand_id'] : ''),
            'address' => trim((string) (isset($customer['address']) ? $customer['address'] : '')),
            'country_id' => $this->normalise_int_string(isset($customer['country_id']) ? $customer['country_id'] : ''),
            'state_id' => $this->normalise_int_string(isset($customer['state_id']) ? $customer['state_id'] : ''),
            'email' => trim((string) (isset($customer['email']) ? $customer['email'] : '')),
            'contact_person' => trim((string) (isset($customer['contact_person']) ? $customer['contact_person'] : '')),
            'contact_person_no' => trim((string) (isset($customer['contact_person_no']) ? $customer['contact_person_no'] : '')),
            'alternate_contact_no' => trim((string) (isset($customer['alternate_contact_no']) ? $customer['alternate_contact_no'] : '')),
            'tax_number' => trim((string) (isset($customer['tax_number']) ? $customer['tax_number'] : '')),
            'shipping_customer_id' => $this->normalise_int_string(isset($customer['shipping_customer_id']) ? $customer['shipping_customer_id'] : ''),
            'status' => $this->normalise_toggle_string(isset($customer['status']) ? $customer['status'] : '1')
        );
    }

    private function sync_marketing_contacts($record_id, $contacts, $changed_by)
    {
        $this->db->where('customer_id', (int) $record_id);
        $this->db->delete('company_multiple_contacts');

        if (empty($contacts)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($contacts as $contact) {
            $this->db->insert('company_multiple_contacts', array(
                'customer_id' => (int) $record_id,
                'contactpersonname' => trim((string) (isset($contact['contactpersonname']) ? $contact['contactpersonname'] : '')),
                'personcontactno' => trim((string) (isset($contact['personcontactno']) ? $contact['personcontactno'] : '')),
                'personemailid' => trim((string) (isset($contact['personemailid']) ? $contact['personemailid'] : '')),
                'designation' => trim((string) (isset($contact['designation']) ? $contact['designation'] : '')),
                'branchlocation' => trim((string) (isset($contact['branchlocation']) ? $contact['branchlocation'] : '')),
                'added_on' => $now,
                'added_by' => (int) $changed_by
            ));
        }
    }

    private function log_history($source_type, $record_id, $action_type, $changed_by, $before_snapshot, $after_snapshot, $change_note = '', $changed_fields = array())
    {
        if (!$this->module_ready()) {
            return;
        }

        $this->db->insert('customer_master_control_history', array(
            'source_type' => trim((string) $source_type),
            'source_record_id' => (int) $record_id,
            'action_type' => trim((string) $action_type),
            'change_note' => trim((string) $change_note),
            'changed_fields' => !empty($changed_fields) ? json_encode(array_values($changed_fields)) : json_encode(array()),
            'previous_snapshot' => json_encode($before_snapshot),
            'updated_snapshot' => json_encode($after_snapshot),
            'changed_by' => (int) $changed_by,
            'changed_on' => date('Y-m-d H:i:s')
        ));
    }

    private function build_changed_fields($before_snapshot, $after_snapshot, $labels)
    {
        $changed_fields = array();
        foreach ($labels as $field_key => $label) {
            $before_value = isset($before_snapshot[$field_key]) ? $before_snapshot[$field_key] : '';
            $after_value = isset($after_snapshot[$field_key]) ? $after_snapshot[$field_key] : '';
            if ($this->normalise_snapshot($before_value) !== $this->normalise_snapshot($after_value)) {
                $changed_fields[] = $label;
            }
        }

        return array_values(array_unique($changed_fields));
    }

    private function get_marketing_field_labels()
    {
        return array(
            'company_id' => 'Company Link',
            'customer_ref_no' => 'Customer Ref No',
            'title' => 'Title',
            'company_name' => 'Company Name',
            'customer_name' => 'Primary Contact',
            'customer_designation' => 'Customer Designation',
            'customer_alias' => 'Customer Alias',
            'company_brand' => 'Brand',
            'email' => 'Email',
            'designation' => 'Primary Contact Designation',
            'branchlocation' => 'Branch Location',
            'contact_no' => 'Primary Contact No',
            'alt_contact' => 'Alternate Contact No',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'pincode' => 'Pincode',
            'gst' => 'GST',
            'pan' => 'PAN',
            'address' => 'Primary Address',
            'bill_address' => 'Billing Address',
            'bill_state' => 'Billing State',
            'bill_city' => 'Billing City',
            'bill_pincode' => 'Billing Pincode',
            'bill_email' => 'Billing Email',
            'ship_address' => 'Shipping Address',
            'ship_state' => 'Shipping State',
            'ship_city' => 'Shipping City',
            'ship_pincode' => 'Shipping Pincode',
            'ship_email' => 'Shipping Email',
            'msme_number' => 'MSME Number',
            'credit_period' => 'Credit Period',
            'credit_limit' => 'Credit Limit',
            'order_max_limit' => 'Order Max Limit',
            'payment_type' => 'Payment Type',
            'credit_days' => 'Credit Days',
            'tds_appl' => 'TDS Applicable',
            'tds_per' => 'TDS Percentage',
            'customer_type' => 'Customer Type',
            'payment_term_approval' => 'Payment Term Approval',
            'payment_approved_On' => 'Payment Approved On',
            'payment_approved_By' => 'Payment Approved By',
            'assigned_to' => 'Assigned To',
            'assign_customer_for_trail' => 'Assigned For Trial',
            'opening_balance' => 'Opening Balance',
            'gst_verified' => 'GST Verified',
            'exhibitiion_email_sent' => 'Intro Email Status',
            'banglore_exhibition' => 'Bangalore Exhibition Flag',
            'status' => 'Status',
            'additional_contacts' => 'Additional Contacts'
        );
    }

    private function get_spares_field_labels()
    {
        return array(
            'company_name' => 'Company Name',
            'brand_id' => 'Brand',
            'address' => 'Address',
            'country_id' => 'Country',
            'state_id' => 'State',
            'email' => 'Email',
            'contact_person' => 'Contact Person',
            'contact_person_no' => 'Contact Person No',
            'alternate_contact_no' => 'Alternate Contact No',
            'tax_number' => 'Tax Number',
            'shipping_customer_id' => 'Shipping Customer',
            'status' => 'Status'
        );
    }

    private function resolve_marketing_brand_value($brand_value, $current_brand_value = '')
    {
        $brand_value = trim((string) $brand_value);
        $current_brand_value = trim((string) $current_brand_value);
        if ($brand_value === '') {
            return '';
        }

        if ($brand_value === $current_brand_value && !$this->is_positive_integer_string($current_brand_value)) {
            return $current_brand_value;
        }

        if ($this->is_positive_integer_string($brand_value)) {
            return (string) ((int) $brand_value);
        }

        $query = $this->db
            ->select('id')
            ->from('company_brand')
            ->where('LOWER(name)', strtolower($brand_value))
            ->get();

        if ($query->num_rows() > 0) {
            return (string) ((int) $query->row()->id);
        }

        $this->db->insert('company_brand', array('name' => $brand_value));
        return (string) ((int) $this->db->insert_id());
    }

    private function resolve_spares_brand_id($brand_value)
    {
        $brand_value = trim((string) $brand_value);
        if ($brand_value === '') {
            return '0';
        }

        if ($this->is_positive_integer_string($brand_value)) {
            return (string) ((int) $brand_value);
        }

        $query = $this->db
            ->select('id')
            ->from('spare_company_brand')
            ->where('LOWER(name)', strtolower($brand_value))
            ->get();

        if ($query->num_rows() > 0) {
            return (string) ((int) $query->row()->id);
        }

        $this->db->insert('spare_company_brand', array('name' => $brand_value));
        return (string) ((int) $this->db->insert_id());
    }

    private function build_location_summary($country_name, $state_name, $city)
    {
        $parts = array();
        foreach (array($city, $state_name, $country_name) as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $parts[] = $value;
            }
        }

        return implode(', ', $parts);
    }

    private function normalise_contact_rows($contacts)
    {
        $normalised = array();
        if (!is_array($contacts)) {
            return $normalised;
        }

        foreach ($contacts as $contact) {
            if (!is_array($contact)) {
                continue;
            }

            $row = array(
                'contactpersonname' => trim((string) (isset($contact['contactpersonname']) ? $contact['contactpersonname'] : '')),
                'personcontactno' => trim((string) (isset($contact['personcontactno']) ? $contact['personcontactno'] : '')),
                'personemailid' => trim((string) (isset($contact['personemailid']) ? $contact['personemailid'] : '')),
                'designation' => trim((string) (isset($contact['designation']) ? $contact['designation'] : '')),
                'branchlocation' => trim((string) (isset($contact['branchlocation']) ? $contact['branchlocation'] : ''))
            );

            if (
                $row['contactpersonname'] === '' &&
                $row['personcontactno'] === '' &&
                $row['personemailid'] === '' &&
                $row['designation'] === '' &&
                $row['branchlocation'] === ''
            ) {
                continue;
            }

            $normalised[] = $row;
        }

        return $normalised;
    }

    private function normalise_snapshot($value)
    {
        if (is_array($value)) {
            $is_associative = $this->is_associative_array($value);
            $normalised = array();
            foreach ($value as $key => $child) {
                $normalised[$key] = $this->normalise_snapshot($child);
            }

            if ($is_associative) {
                ksort($normalised);
                return $normalised;
            }

            return array_values($normalised);
        }

        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    private function is_associative_array($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }

    private function normalise_int_string($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '0';
        }

        return (string) ((int) $value);
    }

    private function normalise_toggle_string($value)
    {
        return ((int) $value === 1) ? '1' : '0';
    }

    private function normalise_decimal_string($value)
    {
        $value = trim((string) $value);
        if ($value === '' || !is_numeric($value)) {
            return '0.00';
        }

        return number_format((float) $value, 2, '.', '');
    }

    private function normalise_datetime_string($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00 00:00:00') {
            return '';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false || $timestamp <= 0) {
            return '';
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function safe_datetime_value($value)
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00 00:00:00' || $value === '0000-00-00') {
            return '';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false || $timestamp <= 0) {
            return '';
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function is_positive_integer_string($value)
    {
        return preg_match('/^[1-9][0-9]*$/', trim((string) $value)) === 1;
    }

    private function is_valid_date($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false || $timestamp <= 0) {
            return false;
        }

        return date('Y-m-d', $timestamp) === $value;
    }
}
