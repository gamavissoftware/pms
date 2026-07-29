<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service_company_master_model extends CI_Model
{
    private $table = 'service_company_master';

    public function ensure_table()
    {
        if (!$this->db->table_exists($this->table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `company_name` VARCHAR(255) NOT NULL,
                    `country_id` INT(11) NOT NULL,
                    `address` TEXT DEFAULT NULL,
                    `contact_no` VARCHAR(100) DEFAULT NULL,
                    `email` VARCHAR(255) DEFAULT NULL,
                    `tax_label` VARCHAR(100) DEFAULT NULL,
                    `tax_number` VARCHAR(150) DEFAULT NULL,
                    `currency` VARCHAR(10) NOT NULL DEFAULT 'INR',
                    `status` TINYINT(1) NOT NULL DEFAULT 1,
                    `created_by` INT(11) DEFAULT NULL,
                    `created_at` DATETIME DEFAULT NULL,
                    `updated_by` INT(11) DEFAULT NULL,
                    `updated_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_service_company_country` (`country_id`),
                    KEY `idx_service_company_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }
    }

    public function get_all()
    {
        $this->ensure_table();

        return $this->db
            ->select('scm.*, co.country_name')
            ->from($this->table . ' scm')
            ->join('countries co', 'co.country_id = scm.country_id', 'left')
            ->order_by('scm.status', 'DESC')
            ->order_by('scm.company_name', 'ASC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        $this->ensure_table();

        return $this->db
            ->select('scm.*, co.country_name')
            ->from($this->table . ' scm')
            ->join('countries co', 'co.country_id = scm.country_id', 'left')
            ->where('scm.id', (int) $id)
            ->limit(1)
            ->get()
            ->row();
    }

    public function company_exists($company_name, $country_id, $exclude_id = 0)
    {
        $this->ensure_table();

        $sql = "SELECT id
                FROM {$this->table}
                WHERE LOWER(TRIM(company_name)) = ?
                  AND country_id = ?";

        $params = [
            strtolower(trim((string) $company_name)),
            (int) $country_id,
        ];

        if ((int) $exclude_id > 0) {
            $sql .= " AND id != ?";
            $params[] = (int) $exclude_id;
        }

        return $this->db->query($sql, $params)->num_rows() > 0;
    }

    public function save($id, array $data)
    {
        $this->ensure_table();

        if ((int) $id > 0) {
            $updated = $this->db->where('id', (int) $id)->update($this->table, $data);
            return $updated ? (int) $id : 0;
        }

        $inserted = $this->db->insert($this->table, $data);
        return $inserted ? (int) $this->db->insert_id() : 0;
    }

    public function update_status($id, $status, $user_id = null)
    {
        $this->ensure_table();

        $data = [
            'status' => (int) $status === 1 ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($user_id !== null) {
            $data['updated_by'] = (int) $user_id;
        }

        return $this->db->where('id', (int) $id)->update($this->table, $data);
    }

    public function get_country_options()
    {
        if (!$this->db->table_exists('countries')) {
            return [];
        }

        $this->db->select('country_id, country_name, country_code, taxtype');
        $this->db->from('countries');

        if ($this->db->field_exists('country_status', 'countries')) {
            $this->db->where('country_status', 1);
        }

        $this->db->order_by('country_name', 'ASC');

        $countries = $this->db->get()->result_array();
        $country_currency_map = $this->get_saved_country_currency_map();

        foreach ($countries as &$country) {
            $country_id = (int) ($country['country_id'] ?? 0);
            $country['suggested_currency'] = $country_currency_map[$country_id]
                ?? $this->infer_currency_from_country($country);
        }
        unset($country);

        return $countries;
    }

    public function get_default_currency_for_country($country_id)
    {
        $country_id = (int) $country_id;
        if ($country_id <= 0 || !$this->db->table_exists('countries')) {
            return 'INR';
        }

        $country_currency_map = $this->get_saved_country_currency_map();
        if (isset($country_currency_map[$country_id]) && $country_currency_map[$country_id] !== '') {
            return $country_currency_map[$country_id];
        }

        $country = $this->db
            ->select('country_id, country_name, country_code, taxtype')
            ->from('countries')
            ->where('country_id', $country_id)
            ->limit(1)
            ->get()
            ->row_array();

        if (empty($country)) {
            return 'INR';
        }

        return $this->infer_currency_from_country($country);
    }

    private function get_saved_country_currency_map()
    {
        $this->ensure_table();

        $rows = $this->db
            ->select('country_id, currency, status, updated_at, id')
            ->from($this->table)
            ->where('currency !=', '')
            ->order_by('status', 'DESC')
            ->order_by('updated_at', 'DESC')
            ->order_by('id', 'DESC')
            ->get()
            ->result_array();

        $map = [];
        foreach ($rows as $row) {
            $country_id = (int) ($row['country_id'] ?? 0);
            if ($country_id <= 0 || isset($map[$country_id])) {
                continue;
            }

            $currency = strtoupper(trim((string) ($row['currency'] ?? '')));
            if ($currency !== '') {
                $map[$country_id] = $currency;
            }
        }

        return $map;
    }

    private function infer_currency_from_country(array $country)
    {
        $country_code = strtoupper(trim((string) ($country['country_code'] ?? '')));
        $country_name = strtoupper(trim((string) ($country['country_name'] ?? '')));

        $currency_map = [
            'IN' => 'INR',
            'US' => 'USD',
            'AE' => 'AED',
            'GB' => 'GBP',
            'AU' => 'AUD',
            'CA' => 'CAD',
            'CN' => 'CNY',
            'JP' => 'JPY',
            'KR' => 'KRW',
            'SA' => 'SAR',
            'QA' => 'QAR',
            'OM' => 'OMR',
            'KW' => 'KWD',
            'BH' => 'BHD',
            'SG' => 'SGD',
            'MY' => 'MYR',
            'TH' => 'THB',
            'VN' => 'VND',
            'ID' => 'IDR',
            'PH' => 'PHP',
            'BD' => 'BDT',
            'NP' => 'NPR',
            'LK' => 'LKR',
            'PK' => 'PKR',
            'ZA' => 'ZAR',
            'NG' => 'NGN',
            'KE' => 'KES',
            'TZ' => 'TZS',
            'UG' => 'UGX',
            'CH' => 'CHF',
            'DE' => 'EUR',
            'FR' => 'EUR',
            'IT' => 'EUR',
            'ES' => 'EUR',
            'NL' => 'EUR',
            'BE' => 'EUR',
            'AT' => 'EUR',
            'IE' => 'EUR',
            'PT' => 'EUR',
            'FI' => 'EUR',
            'GR' => 'EUR',
        ];

        if (isset($currency_map[$country_code])) {
            return $currency_map[$country_code];
        }

        if ($country_name === 'INDIA') {
            return 'INR';
        }

        return 'USD';
    }
}
