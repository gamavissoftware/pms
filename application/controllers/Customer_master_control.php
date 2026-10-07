<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_master_control extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Session logged out. Please login again to continue.</div>');
            redirect(page_url);
        }

        $this->load->model('Customer_master_control_model', 'customer_master_control');
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('Master_profile_guard');
        $this->master_profile_guard->block_methods(
            array('edit', 'update', 'delete_duplicate', 'get_states'),
            'This EA profile can review customer master data but cannot change it.'
        );
    }

    public function index()
    {
        $filters = $this->get_filters();
        $customers = $this->customer_master_control->get_customers($filters);

        $source_counts = array(
            'marketing' => 0,
            'spares' => 0
        );

        foreach ($customers as $customer) {
            if (!empty($customer['source_type']) && isset($source_counts[$customer['source_type']])) {
                $source_counts[$customer['source_type']]++;
            }
        }

        $data = array(
            'filters' => $filters,
            'customers' => $customers,
            'country_options' => $this->customer_master_control->get_country_options(),
            'marketing_user_options' => $this->customer_master_control->get_marketing_user_options(),
            'module_ready' => $this->customer_master_control->module_ready(),
            'migration_file' => 'Database/customer_master_control_001.sql',
            'source_counts' => $source_counts
        );

        $this->load->view('customer_master_control/index', $data);
    }

    public function duplicate_report()
    {
        $scope = $this->input->get('scope', true) === 'all' ? 'all' : 'duplicates';
        $usage = trim((string) $this->input->get('usage', true));
        if (!in_array($usage, array('used', 'unused'), true)) {
            $usage = 'all';
        }
        $keyword = trim((string) $this->input->get('keyword', true));
        $rows = $this->customer_master_control->get_quotation_audit_rows();
        $groups = array();

        foreach ($rows as $index => &$row) {
            $row['match_key'] = $this->company_match_key($row['company_name']);
            $key = $row['match_key'] !== '' ? $row['match_key'] : '__record_' . $index;
            $groups[$key][] = $index;
        }
        unset($row);

        $summary = array('total_records' => count($rows), 'duplicate_groups' => 0, 'duplicate_records' => 0, 'used_records' => 0, 'unused_records' => 0);
        foreach ($groups as $indices) {
            if (count($indices) > 1) {
                $summary['duplicate_groups']++;
                $summary['duplicate_records'] += count($indices);
            }
        }

        $filtered = array();
        foreach ($rows as $row) {
            $is_used = (int) $row['total_quote_count'] > 0;
            $is_duplicate = isset($groups[$row['match_key']]) && count($groups[$row['match_key']]) > 1;
            $summary[$is_used ? 'used_records' : 'unused_records']++;
            if ($scope === 'duplicates' && !$is_duplicate) continue;
            if ($usage === 'used' && !$is_used) continue;
            if ($usage === 'unused' && $is_used) continue;
            if ($keyword !== '' && stripos($row['company_name'] . ' ' . $row['contact_person'] . ' ' . $row['email'], $keyword) === false) continue;
            $row['is_duplicate'] = $is_duplicate;
            $row['duplicate_count'] = $is_duplicate ? count($groups[$row['match_key']]) : 1;
            $filtered[] = $row;
        }

        usort($filtered, function ($a, $b) {
            if ($a['match_key'] === $b['match_key']) return strcmp($a['source_type'], $b['source_type']);
            return strcmp($a['match_key'], $b['match_key']);
        });

        $this->load->view('customer_master_control/duplicate_report', array(
            'rows' => $filtered,
            'summary' => $summary,
            'filters' => array('scope' => $scope, 'usage' => $usage, 'keyword' => $keyword)
        ));
    }

    public function delete_duplicate($source = '', $record_id = 0)
    {
        $source = $this->normalise_source($source);
        $record_id = (int) $record_id;
        if ($this->input->method(true) !== 'POST' || $source === '' || $record_id <= 0) {
            $this->set_flash_message('danger', 'Invalid delete request.');
            redirect(page_url . 'Customer_master_control/duplicate_report');
        }
        if ($this->get_current_user_id() <= 0) {
            $this->set_flash_message('danger', 'Current user could not be identified. Please login again.');
            redirect(page_url);
        }

        $rows = $this->customer_master_control->get_quotation_audit_rows();
        $target = null;
        $matching_records = 0;
        foreach ($rows as $row) {
            if ($row['source_type'] === $source && (int) $row['record_id'] === $record_id) {
                $target = $row;
                break;
            }
        }
        if ($target) {
            $target_key = $this->company_match_key($target['company_name']);
            foreach ($rows as $row) {
                if ($target_key !== '' && $this->company_match_key($row['company_name']) === $target_key) $matching_records++;
            }
        }

        if (!$target || $matching_records < 2 || (int) $target['total_quote_count'] !== 0) {
            $this->set_flash_message('danger', 'This record cannot be deleted. It must be a duplicate and have zero Marketing, Spares and Service quotations.');
            redirect(page_url . 'Customer_master_control/duplicate_report');
        }

        $result = $this->customer_master_control->delete_unused_customer($source, $record_id, $this->get_current_user_id());
        $this->set_flash_message(!empty($result['success']) ? 'success' : 'danger', $result['message']);
        redirect(page_url . 'Customer_master_control/duplicate_report');
    }

    private function company_match_key($name)
    {
        $name = html_entity_decode(strtolower(trim((string) $name)), ENT_QUOTES, 'UTF-8');
        $name = str_replace('&', ' and ', $name);
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
            if ($converted !== false) $name = $converted;
        }
        $tokens = preg_split('/[^a-z0-9]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        $legal_words = array('pvt', 'private', 'ltd', 'limited', 'llp', 'inc', 'incorporated', 'corp', 'corporation', 'co', 'company');
        $tokens = array_values(array_diff($tokens, $legal_words));
        return implode('', $tokens);
    }

    public function edit($source = '', $record_id = 0)
    {
        $source = $this->normalise_source($source);
        $record_id = (int) $record_id;

        if ($source === '' || $record_id <= 0) {
            $this->set_flash_message('danger', 'Requested customer record was not found.');
            redirect(page_url . 'Customer_master_control');
        }

        $customer = $this->customer_master_control->get_customer_detail($source, $record_id);
        if (empty($customer)) {
            $this->set_flash_message('danger', 'Requested customer record was not found.');
            redirect(page_url . 'Customer_master_control');
        }

        $country_id = 0;
        if ($source === 'marketing') {
            $country_id = !empty($customer['country']) ? (int) $customer['country'] : 0;
        } else {
            $country_id = !empty($customer['country_id']) ? (int) $customer['country_id'] : 0;
        }

        $data = array(
            'source' => $source,
            'customer' => $customer,
            'history' => $this->customer_master_control->get_customer_history($source, $record_id),
            'module_ready' => $this->customer_master_control->module_ready(),
            'migration_file' => 'Database/customer_master_control_001.sql',
            'country_options' => $this->customer_master_control->get_country_options(),
            'state_options' => $this->customer_master_control->get_states_by_country($country_id),
            'marketing_brand_options' => $this->customer_master_control->get_marketing_brand_options(),
            'spares_brand_options' => $this->customer_master_control->get_spares_brand_options(),
            'marketing_user_options' => $this->customer_master_control->get_marketing_user_options(),
            'shipping_customer_options' => $this->customer_master_control->get_shipping_customer_options($source === 'spares' ? $record_id : 0)
        );

        $this->load->view('customer_master_control/form', $data);
    }

    public function update($source = '', $record_id = 0)
    {
        $source = $this->normalise_source($source);
        $record_id = (int) $record_id;

        if ($source === '' || $record_id <= 0) {
            $this->set_flash_message('danger', 'Requested customer record was not found.');
            redirect(page_url . 'Customer_master_control');
        }

        $customer = $this->customer_master_control->get_customer_detail($source, $record_id);
        if (empty($customer)) {
            $this->set_flash_message('danger', 'Requested customer record was not found.');
            redirect(page_url . 'Customer_master_control');
        }

        $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');

        if ($this->form_validation->run() === false) {
            $this->set_flash_message('danger', validation_errors());
            redirect(page_url . 'Customer_master_control/edit/' . $source . '/' . $record_id);
        }

        $current_user_id = $this->get_current_user_id();
        if ($current_user_id <= 0) {
            $this->set_flash_message('danger', 'Current user could not be identified. Please login again.');
            redirect(page_url);
        }

        if ($source === 'marketing') {
            $payload = $this->collect_marketing_payload();
            $payload['additional_contacts'] = $this->collect_additional_contacts();
            $result = $this->customer_master_control->update_marketing_customer($record_id, $current_user_id, $payload);
        } else {
            $payload = $this->collect_spares_payload();
            $result = $this->customer_master_control->update_spares_customer($record_id, $current_user_id, $payload);
        }

        if (!empty($result['success'])) {
            $this->set_flash_message(!empty($result['changed']) ? 'success' : 'warning', $result['message']);
        } else {
            $this->set_flash_message('danger', $result['message']);
        }

        redirect(page_url . 'Customer_master_control/edit/' . $source . '/' . $record_id);
    }

    public function get_states()
    {
        if (!$this->session->userdata('logged_in')) {
            echo '<option value="0">Select State</option>';
            return;
        }

        $country_id = (int) $this->input->post('country_id');
        $selected_state_id = (int) $this->input->post('selected_state_id');
        $states = $this->customer_master_control->get_states_by_country($country_id);

        echo '<option value="0">Select State</option>';
        foreach ($states as $state) {
            $selected = ((int) $state['state_id'] === $selected_state_id) ? ' selected="selected"' : '';
            echo '<option value="' . (int) $state['state_id'] . '"' . $selected . '>' . html_escape($state['state_name']) . '</option>';
        }
    }

    private function get_filters()
    {
        $status_filter = trim((string) $this->input->get('status', true));

        return array(
            'source' => $this->normalise_source($this->input->get('source', true)),
            'status' => ($status_filter === '0' || $status_filter === '1') ? $status_filter : '',
            'country_id' => (int) $this->input->get('country_id', true),
            'marketing_user_id' => (int) $this->input->get('marketing_user_id', true),
            'from_date' => trim((string) $this->input->get('from_date', true)),
            'to_date' => trim((string) $this->input->get('to_date', true)),
            'keyword' => trim((string) $this->input->get('keyword', true))
        );
    }

    private function collect_marketing_payload()
    {
        return array(
            'company_id' => trim((string) $this->input->post('company_id', true)),
            'customer_ref_no' => trim((string) $this->input->post('customer_ref_no', true)),
            'title' => trim((string) $this->input->post('title', true)),
            'company_name' => trim((string) $this->input->post('company_name', true)),
            'customer_name' => trim((string) $this->input->post('customer_name', true)),
            'customer_designation' => trim((string) $this->input->post('customer_designation', true)),
            'customer_alias' => trim((string) $this->input->post('customer_alias', true)),
            'company_brand' => trim((string) $this->input->post('company_brand', true)),
            'email' => trim((string) $this->input->post('email', true)),
            'designation' => trim((string) $this->input->post('designation', true)),
            'branchlocation' => trim((string) $this->input->post('branchlocation', true)),
            'contact_no' => trim((string) $this->input->post('contact_no', true)),
            'alt_contact' => trim((string) $this->input->post('alt_contact', true)),
            'country' => trim((string) $this->input->post('country', true)),
            'state' => trim((string) $this->input->post('state', true)),
            'city' => trim((string) $this->input->post('city', true)),
            'pincode' => trim((string) $this->input->post('pincode', true)),
            'gst' => trim((string) $this->input->post('gst', true)),
            'pan' => trim((string) $this->input->post('pan', true)),
            'address' => trim((string) $this->input->post('address', true)),
            'bill_address' => trim((string) $this->input->post('bill_address', true)),
            'bill_state' => trim((string) $this->input->post('bill_state', true)),
            'bill_city' => trim((string) $this->input->post('bill_city', true)),
            'bill_pincode' => trim((string) $this->input->post('bill_pincode', true)),
            'bill_email' => trim((string) $this->input->post('bill_email', true)),
            'ship_address' => trim((string) $this->input->post('ship_address', true)),
            'ship_state' => trim((string) $this->input->post('ship_state', true)),
            'ship_city' => trim((string) $this->input->post('ship_city', true)),
            'ship_pincode' => trim((string) $this->input->post('ship_pincode', true)),
            'ship_email' => trim((string) $this->input->post('ship_email', true)),
            'msme_number' => trim((string) $this->input->post('msme_number', true)),
            'credit_period' => trim((string) $this->input->post('credit_period', true)),
            'credit_limit' => trim((string) $this->input->post('credit_limit', true)),
            'order_max_limit' => trim((string) $this->input->post('order_max_limit', true)),
            'payment_type' => trim((string) $this->input->post('payment_type', true)),
            'credit_days' => trim((string) $this->input->post('credit_days', true)),
            'tds_appl' => trim((string) $this->input->post('tds_appl', true)),
            'tds_per' => trim((string) $this->input->post('tds_per', true)),
            'customer_type' => trim((string) $this->input->post('customer_type', true)),
            'payment_term_approval' => trim((string) $this->input->post('payment_term_approval', true)),
            'payment_approved_On' => trim((string) $this->input->post('payment_approved_On', true)),
            'payment_approved_By' => trim((string) $this->input->post('payment_approved_By', true)),
            'assigned_to' => trim((string) $this->input->post('assigned_to', true)),
            'assign_customer_for_trail' => trim((string) $this->input->post('assign_customer_for_trail', true)),
            'opening_balance' => trim((string) $this->input->post('opening_balance', true)),
            'gst_verified' => trim((string) $this->input->post('gst_verified', true)),
            'exhibitiion_email_sent' => trim((string) $this->input->post('exhibitiion_email_sent', true)),
            'banglore_exhibition' => trim((string) $this->input->post('banglore_exhibition', true)),
            'status' => trim((string) $this->input->post('status', true)),
            'change_note' => trim((string) $this->input->post('change_note', true))
        );
    }

    private function collect_spares_payload()
    {
        return array(
            'company_name' => trim((string) $this->input->post('company_name', true)),
            'brand_id' => trim((string) $this->input->post('brand_id', true)),
            'address' => trim((string) $this->input->post('address', true)),
            'country_id' => trim((string) $this->input->post('country_id', true)),
            'email' => trim((string) $this->input->post('email', true)),
            'contact_person' => trim((string) $this->input->post('contact_person', true)),
            'contact_person_no' => trim((string) $this->input->post('contact_person_no', true)),
            'alternate_contact_no' => trim((string) $this->input->post('alternate_contact_no', true)),
            'tax_number' => trim((string) $this->input->post('tax_number', true)),
            'shipping_customer_id' => trim((string) $this->input->post('shipping_customer_id', true)),
            'status' => trim((string) $this->input->post('status', true)),
            'change_note' => trim((string) $this->input->post('change_note', true))
        );
    }

    private function collect_additional_contacts()
    {
        $names = (array) $this->input->post('contactpersonname_extra');
        $numbers = (array) $this->input->post('personcontactno_extra');
        $emails = (array) $this->input->post('personemailid_extra');
        $designations = (array) $this->input->post('designation_extra');
        $branches = (array) $this->input->post('branchlocation_extra');

        $contacts = array();
        $row_count = max(count($names), count($numbers), count($emails), count($designations), count($branches));
        for ($i = 0; $i < $row_count; $i++) {
            $contact = array(
                'contactpersonname' => isset($names[$i]) ? trim((string) $names[$i]) : '',
                'personcontactno' => isset($numbers[$i]) ? trim((string) $numbers[$i]) : '',
                'personemailid' => isset($emails[$i]) ? trim((string) $emails[$i]) : '',
                'designation' => isset($designations[$i]) ? trim((string) $designations[$i]) : '',
                'branchlocation' => isset($branches[$i]) ? trim((string) $branches[$i]) : ''
            );

            if (
                $contact['contactpersonname'] === '' &&
                $contact['personcontactno'] === '' &&
                $contact['personemailid'] === '' &&
                $contact['designation'] === '' &&
                $contact['branchlocation'] === ''
            ) {
                continue;
            }

            $contacts[] = $contact;
        }

        return $contacts;
    }

    private function normalise_source($source)
    {
        $source = strtolower(trim((string) $source));
        if ($source === 'marketing' || $source === 'spares') {
            return $source;
        }

        return '';
    }

    private function get_current_user_id()
    {
        return isset($this->session->userdata['logged_in']['user_id'])
            ? (int) $this->session->userdata['logged_in']['user_id']
            : 0;
    }

    private function set_flash_message($type, $message)
    {
        $type = trim((string) $type);
        if ($type === '') {
            $type = 'info';
        }

        $message = trim((string) $message);
        if ($message === '') {
            return;
        }

        $this->session->set_flashdata('message', '<div class="alert alert-' . $type . ' alert-dismissable">' . $message . '</div>');
    }
}
