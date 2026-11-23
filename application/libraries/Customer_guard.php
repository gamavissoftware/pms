<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Duplicate guard for the spares / service add-customer popups: the same company name + contact person + phone number
 * or email is the same customer, so a repeated or double submit gets the existing customer instead of a new row.
 * lock() makes two requests that arrive together wait for each other, so the second one sees the first one's row.
 */
class Customer_guard
{
    public function lock($company)
    {
        $ci =& get_instance();
        $name = 'pms_spcust_' . md5(strtolower(trim((string) $company)));
        $ci->db->query('SELECT GET_LOCK(?, 10)', array($name));

        return $name;
    }

    public function unlock($name)
    {
        $ci =& get_instance();
        $ci->db->query('SELECT RELEASE_LOCK(?)', array($name));
    }

    // customer_id of an existing spares_customers row with the same company, contact person and phone or email; 0 when none.
    public function find_spares_customer($company, $contact, $phone, $email)
    {
        $ci =& get_instance();
        $norm = function ($v) { return strtolower(trim(preg_replace('/\s+/', ' ', (string) $v))); };
        $digits = function ($v) { return preg_replace('/[^0-9]/', '', (string) $v); };

        $rows = $ci->db->select('customer_id, contact_person, contact_person_no, email')->from('spares_customers')
            ->where('LOWER(TRIM(company_name))', $norm($company))->order_by('customer_id', 'asc')->limit(50)->get()->result_array();
        foreach ($rows as $row) {
            if ($norm($row['contact_person']) !== $norm($contact)) {
                continue;
            }
            $same_phone = $digits($phone) !== '' && $digits($row['contact_person_no']) === $digits($phone);
            $same_email = $norm($email) !== '' && $norm($row['email']) === $norm($email);
            if ($same_phone || $same_email) {
                return (int) $row['customer_id'];
            }
        }

        return 0;
    }
}
