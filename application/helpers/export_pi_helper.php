<?php
/** PI-specific commercial terms; legacy invoices fall back to their quotation. */
function export_pi_commercial($ci, $invoice_id, $quote_id)
{
    $terms = (array) $ci->salescrm->quotation_freight_packing_forwarding($quote_id);
    $terms = array_merge(array('packing_charges'=>0, 'forwarding_charges'=>0,
        'insurance'=>0, 'freight'=>1, 'freight_charges'=>0, 'freight_type'=>'',
        'installation'=>0, 'other_charges'=>0, 'discount_type'=>1, 'discountvalue'=>0), $terms);
    $discount = $ci->db->select('discount_type, discountvalue')->from('quotation_discount_data')->where('record_id', $quote_id)->get()->row_array();
    if ($discount) $terms = array_merge($terms, $discount);
    if ($invoice_id && $ci->db->table_exists('performa_invoice_commercial')) {
        $saved = $ci->db->where('record_id', $invoice_id)->get('performa_invoice_commercial')->row_array();
        if ($saved) $terms = array_merge($terms, (array) json_decode($saved['terms_json'], true));
    }
    return $terms;
}

function export_pi_save_commercial($ci, $invoice_id)
{
    // Migration is required before enabling the editable fields.
    if (!$ci->db->table_exists('performa_invoice_commercial')) return;
    $quote_id = $ci->salescrm->getRecordID($ci->uri->segment(4));
    $terms = export_pi_commercial($ci, $invoice_id, $quote_id);
    foreach (array('packing_charges', 'forwarding_charges', 'insurance', 'freight_charges', 'installation', 'other_charges') as $key) {
        $value = $ci->input->post('pi_'.$key);
        if ($value !== null) $terms[$key] = max(0, (float) $value);
    }
    $freight = $ci->input->post('pi_freight');
    if ($freight !== null && in_array((int) $freight, array(1,2,3), true)) $terms['freight'] = (int) $freight;
    $ci->db->replace('performa_invoice_commercial', array('record_id'=>$invoice_id, 'terms_json'=>json_encode($terms)));
}

function export_pi_discount($basic, $terms)
{
    return min(max(0, $basic), max(0, (float) $terms['discount_type'] === 1.0
        ? $basic * (float) $terms['discountvalue'] / 100 : (float) $terms['discountvalue']));
}

/** Resolve the PO's agreed terms, falling back to the quotation only if absent. */
function export_pi_payment_terms($ci, $po_term_id, $quote_id)
{
    $term = array();
    if ((int) $po_term_id > 0) {
        $term = $ci->db->select('id, payment_terms')->from('payment_terms')->where('id', $po_term_id)->get()->row_array();
    }
    if (!$term || trim((string) $term['payment_terms']) === '') {
        $quotation = $ci->db->select('terms_value')->from('quotation_other_information')->where('record_id', $quote_id)->get()->row_array();
        $term = array();
        if ($quotation && (int) $quotation['terms_value'] > 0) {
            $term = $ci->db->select('id, payment_terms')->from('payment_terms')->where('id', $quotation['terms_value'])->get()->row_array();
        }
    }
    $percentage = null;
    if ($term) {
        $milestone = $ci->db->select('payment_percentage')->from('payment_terms_milestone')->where('payment_term_id', $term['id'])->order_by('id', 'ASC')->limit(1)->get()->row_array();
        if ($milestone && is_numeric($milestone['payment_percentage']) && $milestone['payment_percentage'] >= 0 && $milestone['payment_percentage'] <= 100) {
            $percentage = (float) $milestone['payment_percentage'];
        }
    }
    return array('text' => $term ? (string) $term['payment_terms'] : '', 'advance_percentage' => $percentage);
}
