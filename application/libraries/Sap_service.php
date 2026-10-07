<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sap_service
{
    private $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
    }

    public function sync_marketing_customer($customer_id)
    {
        $customer_id = (int) $customer_id;
        if ($customer_id <= 0) {
            return array('success' => false, 'message' => 'Invalid customer id.');
        }

        if ($this->already_synced('marketing', $customer_id)) {
            return array('success' => true, 'skipped' => true, 'message' => 'Customer already synced to SAP.');
        }

        $row = $this->ci->db
            ->select('c.*, co.country_name, st.state_name')
            ->from('customer_detail c')
            ->join('countries co', 'co.country_id = c.country', 'left')
            ->join('states st', 'st.state_id = c.state', 'left')
            ->where('c.id', $customer_id)
            ->get()
            ->row_array();

        if (empty($row)) {
            return array('success' => false, 'message' => 'Customer record not found.');
        }

        return $this->create_business_partner('marketing', $customer_id, $this->build_marketing_payload($row));
    }

    public function sync_spares_customer($customer_id)
    {
        $customer_id = (int) $customer_id;
        if ($customer_id <= 0) {
            return array('success' => false, 'message' => 'Invalid customer id.');
        }

        if ($this->already_synced('spares', $customer_id)) {
            return array('success' => true, 'skipped' => true, 'message' => 'Customer already synced to SAP.');
        }

        $row = $this->ci->db
            ->select('c.*, co.country_name')
            ->from('spares_customers c')
            ->join('countries co', 'co.country_id = c.country_id', 'left')
            ->where('c.customer_id', $customer_id)
            ->get()
            ->row_array();

        if (empty($row)) {
            return array('success' => false, 'message' => 'Customer record not found.');
        }

        return $this->create_business_partner('spares', $customer_id, $this->build_spares_payload($row));
    }

    private function create_business_partner($source_type, $source_record_id, $payload)
    {
        if (!$this->sync_table_ready()) {
            log_message('error', 'SAP customer sync table is missing. Run Database/sap_customer_sync_001.sql.');
            return array('success' => false, 'skipped' => true, 'message' => 'SAP sync table missing.');
        }

        if (!$this->is_configured()) {
            $this->record_sync($source_type, $source_record_id, 'SKIPPED', $payload, null, 'SAP Service Layer is not configured.');
            return array('success' => false, 'skipped' => true, 'message' => 'SAP Service Layer is not configured.');
        }

        $login = $this->login();
        if (empty($login['success'])) {
            $this->record_sync($source_type, $source_record_id, 'FAILED', $payload, isset($login['response']) ? $login['response'] : null, isset($login['message']) ? $login['message'] : 'SAP login failed.');
            return $login;
        }

        $result = $this->request('POST', 'BusinessPartners', $payload, $login['cookie']);
        $status = !empty($result['success']) ? 'SUCCESS' : 'FAILED';
        $sap_card_code = null;

        if (!empty($result['decoded']) && is_array($result['decoded']) && !empty($result['decoded']['CardCode'])) {
            $sap_card_code = $result['decoded']['CardCode'];
        }

        $this->record_sync(
            $source_type,
            $source_record_id,
            $status,
            $payload,
            isset($result['body']) ? $result['body'] : null,
            !empty($result['success']) ? null : (isset($result['message']) ? $result['message'] : 'SAP customer create failed.'),
            $sap_card_code
        );

        return $result;
    }

    private function build_marketing_payload($row)
    {
        $country_code = $this->find_sap_country_code(isset($row['country_name']) ? $row['country_name'] : '');
        $state_code = $this->find_sap_state_code(isset($row['state_name']) ? $row['state_name'] : '', $country_code);
        $gst = trim((string) (isset($row['gst']) ? $row['gst'] : ''));
        $pan = trim((string) (isset($row['pan']) ? $row['pan'] : ''));
        if ($pan === '' && strlen($gst) >= 12) {
            $pan = substr($gst, 2, 10);
        }

        $bill_state_code = $state_code;
        if (!empty($row['bill_state']) && (int) $row['bill_state'] !== (int) $row['state']) {
            $bill_state_code = $this->find_sap_state_code_by_id((int) $row['bill_state'], $country_code);
        }

        $ship_state_code = $state_code;
        if (!empty($row['ship_state']) && (int) $row['ship_state'] !== (int) $row['state']) {
            $ship_state_code = $this->find_sap_state_code_by_id((int) $row['ship_state'], $country_code);
        }

        return array(
            'Series' => (int) sap_customer_series,
            'CardCode' => null,
            'CardName' => $this->null_if_empty(isset($row['company_name']) ? $row['company_name'] : ''),
            'CardType' => 'cCustomer',
            'GroupCode' => (int) sap_customer_group_code,
            'Phone1' => $this->null_if_empty(isset($row['contact_no']) ? $row['contact_no'] : ''),
            'Phone2' => $this->null_if_empty(isset($row['alt_contact']) ? $row['alt_contact'] : ''),
            'Fax' => null,
            'ContactPerson' => $this->null_if_empty(isset($row['customer_name']) ? $row['customer_name'] : ''),
            'EmailAddress' => $this->null_if_empty(isset($row['email']) ? $row['email'] : ''),
            'PayTermsGrpCode' => $this->int_or_null(sap_customer_pay_terms_group_code),
            'CreditLimit' => $this->float_or_null(isset($row['credit_limit']) ? $row['credit_limit'] : null),
            'MaxCommitment' => null,
            'FreeText' => $this->null_if_empty(isset($row['address']) ? $row['address'] : ''),
            'SalesPersonCode' => (int) sap_customer_sales_person_code,
            'Currency' => sap_customer_currency,
            'CardForeignName' => null,
            'CurrentAccountBalance' => null,
            'DebitorAccount' => $this->null_if_empty(sap_customer_debitor_account),
            'Valid' => 'tYES',
            'BPAddresses' => array(
                $this->build_address_payload($row, 'bo_BillTo', $country_code, $bill_state_code, $gst, 'bill_'),
                $this->build_address_payload($row, 'bo_ShipTo', $country_code, $ship_state_code, $gst, 'ship_')
            ),
            'ContactEmployees' => array(),
            'BPBankAccounts' => array(),
            'BPFiscalTaxIDCollection' => $pan !== '' ? array(array('TaxId0' => $pan, 'AddrType' => 'bo_BillTo')) : array()
        );
    }

    private function build_spares_payload($row)
    {
        $country_code = $this->find_sap_country_code(isset($row['country_name']) ? $row['country_name'] : '');
        $tax_number = trim((string) (isset($row['tax_number']) ? $row['tax_number'] : ''));
        $pan = strlen($tax_number) >= 12 ? substr($tax_number, 2, 10) : '';

        $base = array(
            'company_name' => isset($row['company_name']) ? $row['company_name'] : '',
            'address' => isset($row['address']) ? $row['address'] : '',
            'city' => '',
            'pincode' => '',
            'contact_no' => isset($row['contact_person_no']) ? $row['contact_person_no'] : '',
            'alt_contact' => isset($row['alternate_contact_no']) ? $row['alternate_contact_no'] : '',
            'customer_name' => isset($row['contact_person']) ? $row['contact_person'] : '',
            'email' => isset($row['email']) ? $row['email'] : ''
        );

        return array(
            'Series' => (int) sap_customer_series,
            'CardCode' => null,
            'CardName' => $this->null_if_empty($base['company_name']),
            'CardType' => 'cCustomer',
            'GroupCode' => (int) sap_customer_group_code,
            'Phone1' => $this->null_if_empty($base['contact_no']),
            'Phone2' => $this->null_if_empty($base['alt_contact']),
            'Fax' => null,
            'ContactPerson' => $this->null_if_empty($base['customer_name']),
            'EmailAddress' => $this->null_if_empty($base['email']),
            'PayTermsGrpCode' => $this->int_or_null(sap_customer_pay_terms_group_code),
            'CreditLimit' => null,
            'MaxCommitment' => null,
            'FreeText' => $this->null_if_empty($base['address']),
            'SalesPersonCode' => (int) sap_customer_sales_person_code,
            'Currency' => sap_customer_currency,
            'CardForeignName' => null,
            'CurrentAccountBalance' => null,
            'DebitorAccount' => $this->null_if_empty(sap_customer_debitor_account),
            'Valid' => 'tYES',
            'BPAddresses' => array(
                $this->build_simple_address_payload($base, 'bo_BillTo', $country_code, $tax_number),
                $this->build_simple_address_payload($base, 'bo_ShipTo', $country_code, $tax_number)
            ),
            'ContactEmployees' => array(),
            'BPBankAccounts' => array(),
            'BPFiscalTaxIDCollection' => $pan !== '' ? array(array('TaxId0' => $pan, 'AddrType' => 'bo_BillTo')) : array()
        );
    }

    private function build_address_payload($row, $address_type, $country_code, $state_code, $gst, $prefix)
    {
        $address = isset($row[$prefix . 'address']) && trim((string) $row[$prefix . 'address']) !== ''
            ? $row[$prefix . 'address']
            : (isset($row['address']) ? $row['address'] : '');
        $city = isset($row[$prefix . 'city']) && trim((string) $row[$prefix . 'city']) !== ''
            ? $row[$prefix . 'city']
            : (isset($row['city']) ? $row['city'] : '');
        $pincode = isset($row[$prefix . 'pincode']) && trim((string) $row[$prefix . 'pincode']) !== ''
            ? $row[$prefix . 'pincode']
            : (isset($row['pincode']) ? $row['pincode'] : '');

        return array(
            'AddressName' => $this->null_if_empty($city) ?: 'PRIMARY',
            'Street' => $this->null_if_empty($address),
            'Block' => null,
            'ZipCode' => $this->null_if_empty($pincode),
            'City' => $this->null_if_empty($city),
            'Country' => $this->null_if_empty($country_code),
            'State' => $this->null_if_empty($state_code),
            'BuildingFloorRoom' => null,
            'AddressType' => $address_type,
            'StreetNo' => null,
            'BPCode' => null,
            'RowNum' => null,
            'GlobalLocationNumber' => null,
            'GSTIN' => $this->null_if_empty($gst),
            'GstType' => $gst !== '' ? 'gstRegularTDSISD' : null
        );
    }

    private function build_simple_address_payload($row, $address_type, $country_code, $gst)
    {
        return array(
            'AddressName' => 'PRIMARY',
            'Street' => $this->null_if_empty(isset($row['address']) ? $row['address'] : ''),
            'Block' => null,
            'ZipCode' => null,
            'City' => null,
            'Country' => $this->null_if_empty($country_code),
            'State' => null,
            'BuildingFloorRoom' => null,
            'AddressType' => $address_type,
            'StreetNo' => null,
            'BPCode' => null,
            'RowNum' => null,
            'GlobalLocationNumber' => null,
            'GSTIN' => $this->null_if_empty($gst),
            'GstType' => $gst !== '' ? 'gstRegularTDSISD' : null
        );
    }

    private function login()
    {
        $payload = array(
            'CompanyDB' => sap_company_db,
            'UserName' => sap_username,
            'Password' => sap_password
        );

        $result = $this->request('POST', 'Login', $payload);
        if (empty($result['success'])) {
            return $result;
        }

        $session_id = '';
        if (!empty($result['decoded']['SessionId'])) {
            $session_id = $result['decoded']['SessionId'];
        }

        if ($session_id === '') {
            return array('success' => false, 'message' => 'SAP login response did not include SessionId.', 'response' => isset($result['body']) ? $result['body'] : null);
        }

        $cookie = 'B1SESSION=' . $session_id;
        if (!empty($result['route_id'])) {
            $cookie .= '; ROUTEID=' . $result['route_id'];
        }

        return array('success' => true, 'cookie' => $cookie);
    }

    private function request($method, $endpoint, $payload = null, $cookie = '')
    {
        $base_url = rtrim(sap_service_layer_base_url, '/');
        $url = $base_url . '/' . ltrim($endpoint, '/');
        $headers = array('Content-Type: application/json', 'Accept: application/json');
        if ($cookie !== '') {
            $headers[] = 'Cookie: ' . $cookie;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, sap_ssl_verify ? 1 : 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, sap_ssl_verify ? 2 : 0);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $raw = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($raw === false) {
            return array('success' => false, 'message' => $curl_error !== '' ? $curl_error : 'SAP request failed.');
        }

        $header = substr($raw, 0, $header_size);
        $body = substr($raw, $header_size);
        $decoded = json_decode($body, true);
        $success = $http_code >= 200 && $http_code < 300;
        $message = $success ? 'OK' : $this->extract_error_message($decoded, $body, $http_code);

        return array(
            'success' => $success,
            'http_code' => $http_code,
            'body' => $body,
            'decoded' => is_array($decoded) ? $decoded : null,
            'route_id' => $this->extract_route_id($header),
            'message' => $message
        );
    }

    private function record_sync($source_type, $source_record_id, $status, $payload, $response, $error_message = null, $sap_card_code = null)
    {
        if (!$this->sync_table_ready()) {
            return;
        }

        $existing = $this->ci->db
            ->select('id, attempt_count')
            ->from('SAP_customer_sync')
            ->where('source_type', $source_type)
            ->where('source_record_id', (int) $source_record_id)
            ->get()
            ->row_array();

        $data = array(
            'sap_card_code' => $sap_card_code,
            'sync_status' => $status,
            'request_payload' => json_encode($payload),
            'response_payload' => $response,
            'error_message' => $error_message,
            'last_attempt_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        if (!empty($existing)) {
            $data['attempt_count'] = (int) $existing['attempt_count'] + 1;
            $this->ci->db->where('id', (int) $existing['id'])->update('SAP_customer_sync', $data);
            return;
        }

        $data['source_type'] = $source_type;
        $data['source_record_id'] = (int) $source_record_id;
        $data['attempt_count'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->ci->db->insert('SAP_customer_sync', $data);
    }

    private function already_synced($source_type, $source_record_id)
    {
        if (!$this->sync_table_ready()) {
            return false;
        }

        return $this->ci->db
            ->from('SAP_customer_sync')
            ->where('source_type', $source_type)
            ->where('source_record_id', (int) $source_record_id)
            ->where('sync_status', 'SUCCESS')
            ->where('sap_card_code IS NOT NULL', null, false)
            ->count_all_results() > 0;
    }

    private function sync_table_ready()
    {
        return $this->ci->db->table_exists('SAP_customer_sync');
    }

    private function is_configured()
    {
        return trim((string) sap_service_layer_base_url) !== ''
            && trim((string) sap_company_db) !== ''
            && trim((string) sap_username) !== ''
            && trim((string) sap_password) !== '';
    }

    private function find_sap_country_code($country_name)
    {
        $country_name = trim((string) $country_name);
        if ($country_name === '' || !$this->ci->db->table_exists('SAP_countries')) {
            return '';
        }

        $row = $this->ci->db
            ->select('sap_code')
            ->from('SAP_countries')
            ->where('LOWER(country_name) =', strtolower($country_name))
            ->get()
            ->row_array();

        if (!empty($row['sap_code'])) {
            return $row['sap_code'];
        }

        $target = $this->normalise_lookup_name($country_name);
        foreach ($this->ci->db->select('sap_code, country_name')->from('SAP_countries')->get()->result_array() as $candidate) {
            if ($this->normalise_lookup_name($candidate['country_name']) === $target) {
                return $candidate['sap_code'];
            }
        }

        return '';
    }

    private function find_sap_state_code_by_id($state_id, $country_code)
    {
        $row = $this->ci->db
            ->select('state_name')
            ->from('states')
            ->where('state_id', (int) $state_id)
            ->get()
            ->row_array();

        return !empty($row['state_name']) ? $this->find_sap_state_code($row['state_name'], $country_code) : '';
    }

    private function find_sap_state_code($state_name, $country_code)
    {
        $state_name = trim((string) $state_name);
        $country_code = trim((string) $country_code);
        if ($state_name === '' || !$this->ci->db->table_exists('SAP_States')) {
            return '';
        }

        $this->ci->db->select('sap_code')->from('SAP_States')->where('LOWER(state_name) =', strtolower($state_name));
        if ($country_code !== '') {
            $this->ci->db->where('country_code', $country_code);
        }

        $row = $this->ci->db->get()->row_array();
        if (!empty($row['sap_code'])) {
            return $row['sap_code'];
        }

        $target = $this->normalise_lookup_name($state_name);
        $this->ci->db->select('sap_code, state_name')->from('SAP_States');
        if ($country_code !== '') {
            $this->ci->db->where('country_code', $country_code);
        }

        foreach ($this->ci->db->get()->result_array() as $candidate) {
            if ($this->normalise_lookup_name($candidate['state_name']) === $target) {
                return $candidate['sap_code'];
            }
        }

        return '';
    }

    private function normalise_lookup_name($value)
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower(trim((string) $value)));
    }

    private function null_if_empty($value)
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function int_or_null($value)
    {
        $value = trim((string) $value);
        return $value === '' ? null : (int) $value;
    }

    private function float_or_null($value)
    {
        $value = trim((string) $value);
        return $value === '' ? null : (float) $value;
    }

    private function extract_error_message($decoded, $body, $http_code)
    {
        if (is_array($decoded) && !empty($decoded['error']['message']['value'])) {
            return $decoded['error']['message']['value'];
        }

        if (is_array($decoded) && !empty($decoded['error']['message'])) {
            return is_array($decoded['error']['message']) ? json_encode($decoded['error']['message']) : $decoded['error']['message'];
        }

        return 'SAP request failed with HTTP ' . $http_code . ($body !== '' ? ': ' . substr($body, 0, 500) : '.');
    }

    private function extract_route_id($header)
    {
        if (preg_match('/ROUTEID=([^;\\s]+)/', $header, $match)) {
            return $match[1];
        }

        return '';
    }
}
