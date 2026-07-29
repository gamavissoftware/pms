<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $session = $this->session->userdata('logged_in');

        if ($session == FALSE) {
            redirect(page_url);
        }

        $this->load->model('User_model', 'user');
        $this->load->model('Dashboard_model', 'reportingdata');
        $this->load->model('Store_model', 'store');
        $this->load->model('Fms_model', 'Fms_model');

        $user_id = $this->session->userdata['logged_in']['user_id'];

        if (empty($user_id)) {
            redirect(site_url(), 'refresh');
        }
    }

    public function machineonfloor()
    {
        $this->load->view('dashboard/machineonfloor');
    }

    public function update_machine_floor_tracking()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
        }

        $df_id = $this->input->post('df_id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        $user_id = $this->session->userdata['logged_in']['user_id'];

        if (empty($df_id) || empty($field)) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid request.'
            ]);
            return;
        }

        $allowed_fields = [
            'location',
            'machine_status',
            'adv_received',
            'adv_pending',
            'before_dispatch',
            'after_dispatch',
            'after_ioc'
        ];

        if (!in_array($field, $allowed_fields)) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid field.'
            ]);
            return;
        }

        $amount_fields = [
            'adv_received',
            'adv_pending',
            'before_dispatch',
            'after_dispatch',
            'after_ioc'
        ];

        if (in_array($field, $amount_fields)) {
            $value = str_replace(',', '', $value);
            $value = is_numeric($value) ? $value : 0;
        }

        $check = $this->db
            ->select('id')
            ->from('machine_floor_tracking')
            ->where('df_id', $df_id)
            ->get();

        $save_data = [
            $field       => $value,
            'updated_by' => $user_id,
            'updated_on' => date('Y-m-d H:i:s')
        ];

        if ($check->num_rows() > 0) {
            $this->db->where('df_id', $df_id);
            $this->db->update('machine_floor_tracking', $save_data);
        } else {
            $save_data['df_id'] = $df_id;
            $save_data['created_on'] = date('Y-m-d H:i:s');

            $this->db->insert('machine_floor_tracking', $save_data);
        }

        if ($this->db->affected_rows() >= 0) {
            echo json_encode([
                'status' => true,
                'message' => 'Updated successfully.',
                'updated_on' => date('d-m-Y h:i A')
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Something went wrong.'
            ]);
        }
    }

   public function mcsdispatchreport()
{
    $this->load->view('dashboard/mcsdispatchreport');
}


public function update_mcs_dispatch_report()
{
    if (!$this->input->is_ajax_request()) {
        show_error('No direct script access allowed');
    }

    $df_id = $this->input->post('df_id');
    $field = $this->input->post('field');
    $value = $this->input->post('value');

    $user_id = $this->session->userdata['logged_in']['user_id'];

    if (empty($df_id) || empty($field)) {
        echo json_encode([
            'status' => false,
            'message' => 'Invalid request.'
        ]);
        return;
    }

    $allowed_fields = [
        'nos_of_machines',
        'invoice_no',
        'invoice_date',
        'invoice_amount',
        'taxable_sale',
        'payment_received',
        'balance_amount',
        'marketing_person',
        'payment_due_status',
        'due_date',
        'remarks',
        'commissioning_status',
        'inc_date'
    ];

    if (!in_array($field, $allowed_fields)) {
        echo json_encode([
            'status' => false,
            'message' => 'Invalid field.'
        ]);
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Amount Fields Cleaning
    |--------------------------------------------------------------------------
    */
    $amount_fields = [
        'invoice_amount',
        'taxable_sale',
        'payment_received',
        'balance_amount'
    ];

    if (in_array($field, $amount_fields)) {
        $value = str_replace(',', '', $value);
        $value = is_numeric($value) ? $value : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Date Fields Formatting
    |--------------------------------------------------------------------------
    */
    $date_fields = [
        'invoice_date',
        'due_date',
        'inc_date'
    ];

    if (in_array($field, $date_fields)) {
        if (!empty($value)) {
            $value = date('Y-m-d', strtotime($value));
        } else {
            $value = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch Existing Record
    |--------------------------------------------------------------------------
    */
    $check = $this->db
        ->select('*')
        ->from('mcs_dispatch_report_tracking')
        ->where('df_id', $df_id)
        ->get();

    $existing_data = ($check->num_rows() > 0) ? $check->row() : null;

    $save_data = [
        $field       => $value,
        'updated_by' => $user_id,
        'updated_on' => date('Y-m-d H:i:s')
    ];

    /*
    |--------------------------------------------------------------------------
    | Auto Calculate Balance Amount
    |--------------------------------------------------------------------------
    | Balance Amount = Invoice Amount - Payment Received
    */
    if ($field == 'invoice_amount' || $field == 'payment_received') {

        $invoice_amount = 0;
        $payment_received = 0;

        if ($existing_data) {
            $invoice_amount = !empty($existing_data->invoice_amount) ? (float)$existing_data->invoice_amount : 0;
            $payment_received = !empty($existing_data->payment_received) ? (float)$existing_data->payment_received : 0;
        }

        if ($field == 'invoice_amount') {
            $invoice_amount = (float)$value;
        }

        if ($field == 'payment_received') {
            $payment_received = (float)$value;
        }

        $balance_amount = $invoice_amount - $payment_received;

        $save_data['balance_amount'] = $balance_amount;
    }

    /*
    |--------------------------------------------------------------------------
    | Insert / Update
    |--------------------------------------------------------------------------
    */
    if ($check->num_rows() > 0) {

        $this->db->where('df_id', $df_id);
        $this->db->update('mcs_dispatch_report_tracking', $save_data);

    } else {

        $save_data['df_id'] = $df_id;
        $save_data['created_on'] = date('Y-m-d H:i:s');

        $this->db->insert('mcs_dispatch_report_tracking', $save_data);
    }

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */
    $response = [
        'status' => true,
        'message' => 'Updated successfully.',
        'updated_on' => date('d-m-Y h:i A')
    ];

    if (isset($save_data['balance_amount'])) {
        $response['balance_amount'] = number_format((float)$save_data['balance_amount'], 2, '.', '');
    }

    echo json_encode($response);
}
}