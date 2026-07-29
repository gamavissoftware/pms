<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceMaster extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect(page_url);
        }
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('Master_profile_guard');
        $this->load->model('Service_company_master_model', 'service_company_master');
        $this->master_profile_guard->block_methods(
            array('save_charge', 'save_company', 'toggle_company_status'),
            'This EA profile can review service masters but cannot change them.'
        );
    }

    public function index() {
        $this->service_company_master->ensure_table();

        $data['charges'] = $this->db->get('service_charges_master')->result();
        $data['companies'] = $this->service_company_master->get_all();
        $data['country_options'] = $this->service_company_master->get_country_options();
        $data['company_form'] = $this->get_company_form_defaults();
        $data['active_tab'] = $this->get_active_tab();
        $this->load->view('spares/service_master_view', $data);
    }

    public function save_charge()
{
    $user_id = $this->get_current_user_id();
    $id = $this->input->post('id');
    
    $new_inr = $this->input->post('default_rate_inr');
    $new_usd = $this->input->post('default_rate_usd');

    $data = array(
        'charge_name'      => strtoupper($this->input->post('charge_name')),
        'sac_code'         => $this->input->post('sac_code'),
        'default_rate_inr' => $new_inr,
        'default_rate_usd' => $new_usd,
        'charge_type'      => $this->input->post('charge_type'),
        'status'           => 1
    );

    $this->db->trans_start();

    if ($id) {
        // --- RATE HISTORY LOGIC ---
        $current = $this->db->get_where('service_charges_master', array('id' => $id))->row();
        
        // Only log if the rate actually changed
        if ($current->default_rate_inr != $new_inr || $current->default_rate_usd != $new_usd) {
            $history = array(
                'charge_id'    => $id,
                'old_rate_inr' => $current->default_rate_inr,
                'new_rate_inr' => $new_inr,
                'old_rate_usd' => $current->default_rate_usd,
                'new_rate_usd' => $new_usd,
                'changed_by'   => $user_id,
                'changed_at'   => date('Y-m-d H:i:s')
            );
            $this->db->insert('service_rate_history', $history);
        }

        $this->db->where('id', $id)->update('service_charges_master', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success">Charge and History Updated Successfully</div>');
    } else {
        $this->db->insert('service_charges_master', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success">New Charge Added Successfully</div>');
    }

    $this->db->trans_complete();
    redirect(page_url . 'ServiceMaster?tab=charges');
}

    public function save_company()
    {
        $company_id = (int) $this->input->post('company_id');
        $form_data = $this->collect_company_form_data();

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('country_id', 'Country', 'trim|required|integer|greater_than[0]');
        $this->form_validation->set_rules('address', 'Address', 'trim|required');
        $this->form_validation->set_rules('contact_no', 'Contact No', 'trim|required');
        $this->form_validation->set_rules('email', 'Email ID', 'trim|required|valid_email');
        $this->form_validation->set_rules('currency', 'Currency', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('company_form_data', $form_data);
            $this->session->set_flashdata('message', '<div class="alert alert-danger">' . validation_errors('<div>', '</div>') . '</div>');
            redirect(page_url . 'ServiceMaster?tab=companies');
        }

        if ($this->service_company_master->company_exists($form_data['company_name'], $form_data['country_id'], $company_id)) {
            $this->session->set_flashdata('company_form_data', $form_data);
            $this->session->set_flashdata('message', '<div class="alert alert-danger">A service company with the same name already exists for the selected country.</div>');
            redirect(page_url . 'ServiceMaster?tab=companies');
        }

        $now = date('Y-m-d H:i:s');
        $user_id = $this->get_current_user_id();

        $payload = [
            'company_name' => $form_data['company_name'],
            'country_id' => $form_data['country_id'],
            'address' => $form_data['address'],
            'contact_no' => $form_data['contact_no'],
            'email' => $form_data['email'],
            'tax_label' => $form_data['tax_label'],
            'tax_number' => $form_data['tax_number'],
            'currency' => $form_data['currency'],
            'status' => $form_data['status'],
            'updated_by' => $user_id,
            'updated_at' => $now,
        ];

        if ($company_id > 0) {
            $company = $this->service_company_master->get_by_id($company_id);
            if (!$company) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Requested service company was not found.</div>');
                redirect(page_url . 'ServiceMaster?tab=companies');
            }
        } else {
            $payload['created_by'] = $user_id;
            $payload['created_at'] = $now;
        }

        $saved_id = $this->service_company_master->save($company_id, $payload);

        if ($saved_id > 0) {
            $message = $company_id > 0
                ? 'Service company updated successfully.'
                : 'Service company added successfully.';

            $this->session->set_flashdata('message', '<div class="alert alert-success">' . $message . '</div>');
        } else {
            $this->session->set_flashdata('company_form_data', $form_data);
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Unable to save service company details. Please try again.</div>');
        }

        redirect(page_url . 'ServiceMaster?tab=companies');
    }

    public function toggle_company_status($company_id = 0)
    {
        $company_id = (int) $company_id;
        if ($company_id <= 0) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Requested service company was not found.</div>');
            redirect(page_url . 'ServiceMaster?tab=companies');
        }

        $company = $this->service_company_master->get_by_id($company_id);
        if (!$company) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Requested service company was not found.</div>');
            redirect(page_url . 'ServiceMaster?tab=companies');
        }

        $new_status = (int) $company->status === 1 ? 0 : 1;
        if ($this->service_company_master->update_status($company_id, $new_status, $this->get_current_user_id())) {
            $status_label = $new_status === 1 ? 'activated' : 'inactivated';
            $this->session->set_flashdata('message', '<div class="alert alert-success">Service company ' . $status_label . ' successfully.</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Unable to update service company status. Please try again.</div>');
        }

        redirect(page_url . 'ServiceMaster?tab=companies');
    }

    private function get_current_user_id()
    {
        return !empty($_SESSION['logged_in']['user_id'])
            ? (int) $_SESSION['logged_in']['user_id']
            : 0;
    }

    private function get_active_tab()
    {
        $tab = trim((string) $this->input->get('tab', true));
        return $tab === 'companies' ? 'companies' : 'charges';
    }

    private function get_company_form_defaults()
    {
        $default_country_id = 101;
        $defaults = [
            'company_id' => '',
            'company_name' => '',
            'country_id' => $default_country_id,
            'address' => '',
            'contact_no' => '',
            'email' => '',
            'tax_label' => '',
            'tax_number' => '',
            'currency' => $this->service_company_master->get_default_currency_for_country($default_country_id),
            'status' => 1,
        ];

        $flashdata = $this->session->flashdata('company_form_data');
        if (is_array($flashdata)) {
            $merged = array_merge($defaults, $flashdata);
            if (trim((string) ($merged['currency'] ?? '')) === '') {
                $merged['currency'] = $this->service_company_master->get_default_currency_for_country((int) ($merged['country_id'] ?? $default_country_id));
            }
            return $merged;
        }

        return $defaults;
    }

    private function collect_company_form_data()
    {
        $tax_label = trim((string) $this->input->post('tax_label', true));
        $tax_number = trim((string) $this->input->post('tax_number', true));

        if ($tax_number !== '' && $tax_label === '') {
            $tax_label = 'Tax ID';
        }

        return [
            'company_id' => (int) $this->input->post('company_id'),
            'company_name' => trim((string) $this->input->post('company_name', true)),
            'country_id' => (int) $this->input->post('country_id'),
            'address' => trim((string) $this->input->post('address', true)),
            'contact_no' => trim((string) $this->input->post('contact_no', true)),
            'email' => trim((string) $this->input->post('email', true)),
            'tax_label' => $tax_label,
            'tax_number' => $tax_number,
            'currency' => strtoupper(trim((string) $this->input->post('currency', true))),
            'status' => $this->input->post('status') === '0' ? 0 : 1,
        ];
    }
}
