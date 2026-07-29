<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_spare_quotation extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) redirect(page_url);
        $this->load->model('Order_spare_quotation_model', 'order_spare_quote');
        $this->order_spare_quote->ensure_tables();
    }

    private function user_id() { return (int) $_SESSION['logged_in']['user_id']; }

    private function owned_po($po_id)
    {
        $po = $this->order_spare_quote->get_owned_po($po_id, $this->user_id());
        if (!$po) show_error('Order not found or you are not allowed to access it.', 403);
        return $po;
    }

    public function index($po_id = 0)
    {
        $data['po'] = $this->owned_po($po_id);
        $data['quotes'] = $this->order_spare_quote->get_quotes($po_id, $this->user_id());
        $this->load->view('order_spare_quotation/list', $data);
    }

    public function create($po_id = 0)
    {
        $data['po'] = $this->owned_po($po_id);
        $data['quote'] = null;
        $data['items'] = array();
        $data['quotation_no'] = $this->order_spare_quote->next_quotation_number();
        $data['revision_no'] = 0;
        $data['form_action'] = page_url . 'Order_spare_quotation/store';
        $this->load->view('order_spare_quotation/form', $data);
    }

    public function revise($quote_id = 0)
    {
        $quote = $this->order_spare_quote->get_quote($quote_id, $this->user_id());
        if (!$quote) show_error('Quotation not found or access denied.', 403);
        $data['po'] = $this->owned_po($quote->received_po_id);
        $data['quote'] = $quote;
        $data['items'] = $this->order_spare_quote->get_items($quote_id);
        $revision = $this->order_spare_quote->next_revision_details($quote->quotation_no, $quote->received_po_id, $this->user_id());
        $data['revision_no'] = $revision['revision_no'];
        $data['quotation_no'] = $revision['quotation_no'];
        $data['form_action'] = page_url . 'Order_spare_quotation/store';
        $this->load->view('order_spare_quotation/form', $data);
    }

    public function view($quote_id = 0)
    {
        $data['quote'] = $this->order_spare_quote->get_quote($quote_id, $this->user_id());
        if (!$data['quote']) show_error('Quotation not found or access denied.', 403);
        $data['po'] = $this->owned_po($data['quote']->received_po_id);
        $data['items'] = $this->order_spare_quote->get_items($quote_id);
        $this->load->view('order_spare_quotation/view', $data);
    }

    public function store()
    {
        if (strtoupper($this->input->method()) !== 'POST') show_404();
        $po_id = (int) $this->input->post('received_po_id');
        $po = $this->owned_po($po_id);
        $descriptions = (array) $this->input->post('item_description');
        $quantities = (array) $this->input->post('quantity');
        $units = (array) $this->input->post('unit');
        $rates = (array) $this->input->post('unit_rate');
        $discounts = (array) $this->input->post('discount_percent');
        $gsts = (array) $this->input->post('gst_percent');
        $hsns = (array) $this->input->post('hsn_code');
        $items = array(); $sub = 0; $discount_total = 0; $taxable_total = 0; $gst_total = 0;
        foreach ($descriptions as $i => $description) {
            $description = trim($description);
            $qty = max(0, (float) ($quantities[$i] ?? 0));
            $rate = max(0, (float) ($rates[$i] ?? 0));
            if ($description === '' || $qty <= 0) continue;
            $discount = min(100, max(0, (float) ($discounts[$i] ?? 0)));
            $gst = min(100, max(0, (float) ($gsts[$i] ?? 0)));
            $gross = round($qty * $rate, 2); $discount_amount = round($gross * $discount / 100, 2);
            $taxable = round($gross - $discount_amount, 2); $gst_amount = round($taxable * $gst / 100, 2);
            $sub += $gross; $discount_total += $discount_amount; $taxable_total += $taxable; $gst_total += $gst_amount;
            $items[] = array('line_no' => count($items) + 1, 'item_description' => $description,
                'hsn_code' => trim($hsns[$i] ?? ''), 'quantity' => $qty, 'unit' => trim($units[$i] ?? 'Nos') ?: 'Nos',
                'unit_rate' => $rate, 'discount_percent' => $discount, 'gst_percent' => $gst,
                'taxable_amount' => $taxable, 'gst_amount' => $gst_amount, 'line_total' => round($taxable + $gst_amount, 2));
        }
        if (!$items) { $this->session->set_flashdata('message', '<div class="alert alert-danger">Add at least one valid line item.</div>'); redirect(page_url.'Order_spare_quotation/create/'.$po_id); }
        $parent_id = max(0, (int) $this->input->post('parent_quotation_id')) ?: null;
        $revision_no = 0;
        $quotation_no = $this->order_spare_quote->next_quotation_number();
        if ($parent_id) {
            $parent = $this->order_spare_quote->get_quote($parent_id, $this->user_id());
            if (!$parent || (int) $parent->received_po_id !== $po_id) show_error('Invalid parent quotation.', 403);
            $revision = $this->order_spare_quote->next_revision_details($parent->quotation_no, $po_id, $this->user_id());
            $revision_no = $revision['revision_no'];
            $quotation_no = $revision['quotation_no'];
        }
        $header = array('received_po_id' => $po->id, 'parent_quotation_id' => $parent_id,
            'quotation_no' => $quotation_no, 'revision_no' => $revision_no,
            'quotation_date' => $this->valid_date($this->input->post('quotation_date')),
            'currency' => in_array($this->input->post('currency'), array('INR','USD','EUR'), true) ? $this->input->post('currency') : 'INR',
            'attention' => trim($this->input->post('attention')), 'subject' => trim($this->input->post('subject')),
            'payment_terms' => trim($this->input->post('payment_terms')), 'validity' => trim($this->input->post('validity')),
            'delivery_terms' => trim($this->input->post('delivery_terms')), 'notes' => trim($this->input->post('notes')),
            'sub_total' => round($sub,2), 'discount_total' => round($discount_total,2), 'taxable_total' => round($taxable_total,2),
            'gst_total' => round($gst_total,2), 'grand_total' => round($taxable_total+$gst_total,2), 'status' => 'Quoted',
            'created_by' => $this->user_id(), 'created_on' => date('Y-m-d H:i:s'));
        $id = $this->order_spare_quote->save_quote($header, $items, $revision_no ? 'Quotation Revised' : 'Quotation Created', $header['quotation_no']);
        $this->session->set_flashdata('message', $id ? '<div class="alert alert-success">Quotation saved successfully.</div>' : '<div class="alert alert-danger">Quotation could not be saved.</div>');
        redirect(page_url . 'Order_spare_quotation/index/' . $po_id);
    }

    public function mark_won($quote_id = 0)
    {
        $quote = $this->order_spare_quote->get_quote($quote_id, $this->user_id());
        if (!$quote) show_error('Quotation not found or access denied.', 403);
        $this->owned_po($quote->received_po_id);
        $filename = $quote->won_po_attachment;
        if (!empty($_FILES['po_attachment']['name'])) {
            $config = array('upload_path' => FCPATH.'uploads/order_spare_po/', 'allowed_types' => 'pdf|jpg|jpeg|png', 'max_size' => 10240,
                'encrypt_name' => true);
            if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0755, true);
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('po_attachment')) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">'.html_escape(strip_tags($this->upload->display_errors('', ''))).'</div>');
                redirect(page_url.'Order_spare_quotation/index/'.$quote->received_po_id);
            }
            $filename = $this->upload->data('file_name');
        }
        $data = array('won_po_no' => trim($this->input->post('won_po_no')), 'won_po_date' => $this->valid_date($this->input->post('won_po_date')),
            'won_order_value' => max(0, (float) $this->input->post('won_order_value')), 'won_po_attachment' => $filename,
            'won_remarks' => trim($this->input->post('won_remarks')));
        $ok = $data['won_po_no'] !== '' && $this->order_spare_quote->mark_won($quote_id, $this->user_id(), $data);
        $this->session->set_flashdata('message', $ok ? '<div class="alert alert-success">Order marked as won and PO details attached.</div>' : '<div class="alert alert-danger">PO number is required.</div>');
        redirect(page_url.'Order_spare_quotation/index/'.$quote->received_po_id);
    }

    private function valid_date($value) { $time = strtotime((string) $value); return $time ? date('Y-m-d', $time) : date('Y-m-d'); }
}
