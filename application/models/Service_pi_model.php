<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service_pi_model extends CI_Model
{
    private $table = 'service_proforma_invoices';
    private $item_table = 'service_proforma_invoice_items';

    public function ensure_tables()
    {
        if (!$this->db->table_exists($this->table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `opportunity_id` INT(11) NOT NULL,
                    `quote_id` INT(11) DEFAULT NULL,
                    `po_id` INT(11) DEFAULT NULL,
                    `customer_id` INT(11) DEFAULT NULL,
                    `pi_no` VARCHAR(50) NOT NULL,
                    `pi_date` DATE DEFAULT NULL,
                    `pi_title` VARCHAR(255) DEFAULT NULL,
                    `currency` VARCHAR(10) NOT NULL DEFAULT 'INR',
                    `buyer_name` VARCHAR(255) DEFAULT NULL,
                    `buyer_contact` VARCHAR(255) DEFAULT NULL,
                    `buyer_address` TEXT DEFAULT NULL,
                    `buyer_gstin` VARCHAR(100) DEFAULT NULL,
                    `consignee_name` VARCHAR(255) DEFAULT NULL,
                    `consignee_contact` VARCHAR(255) DEFAULT NULL,
                    `consignee_address` TEXT DEFAULT NULL,
                    `consignee_ecc_no` VARCHAR(100) DEFAULT NULL,
                    `consignee_phone` VARCHAR(100) DEFAULT NULL,
                    `buyer_order_no` VARCHAR(150) DEFAULT NULL,
                    `buyer_order_date` DATE DEFAULT NULL,
                    `payment_terms` VARCHAR(255) DEFAULT NULL,
                    `bank_name` VARCHAR(255) DEFAULT NULL,
                    `account_no` VARCHAR(100) DEFAULT NULL,
                    `bank_address` TEXT DEFAULT NULL,
                    `ifsc_code` VARCHAR(100) DEFAULT NULL,
                    `account_type` VARCHAR(100) DEFAULT NULL,
                    `account_holder` VARCHAR(255) DEFAULT NULL,
                    `swift_code` VARCHAR(100) DEFAULT NULL,
                    `country_of_origin` VARCHAR(120) DEFAULT NULL,
                    `gst_percent` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `basic_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `gst_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `amount_in_words` TEXT DEFAULT NULL,
                    `declaration_text` TEXT DEFAULT NULL,
                    `notes` TEXT DEFAULT NULL,
                    `created_by` INT(11) DEFAULT NULL,
                    `created_at` DATETIME DEFAULT NULL,
                    `updated_by` INT(11) DEFAULT NULL,
                    `updated_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_service_pi_opp` (`opportunity_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }

        if (!$this->db->table_exists($this->item_table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->item_table}` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `service_pi_id` INT(11) NOT NULL,
                    `sort_order` INT(11) NOT NULL DEFAULT 0,
                    `sac_code` VARCHAR(50) DEFAULT NULL,
                    `description` TEXT DEFAULT NULL,
                    `unit_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `no_of_days` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `no_of_engineers` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `uom` VARCHAR(50) DEFAULT NULL,
                    `is_customer_scope` TINYINT(1) NOT NULL DEFAULT 0,
                    `scope_note` VARCHAR(255) DEFAULT NULL,
                    `row_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `created_at` DATETIME DEFAULT NULL,
                    `updated_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_service_pi_items_master` (`service_pi_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }
    }

    public function get_by_id($id)
    {
        $this->ensure_tables();

        return $this->db->get_where($this->table, ['id' => (int) $id])->row();
    }

    public function get_by_opportunity($opportunity_id)
    {
        $this->ensure_tables();

        return $this->db->where('opportunity_id', (int) $opportunity_id)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get($this->table)
            ->row();
    }

    public function get_items($service_pi_id)
    {
        $this->ensure_tables();

        return $this->db->where('service_pi_id', (int) $service_pi_id)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->item_table)
            ->result();
    }

    public function get_next_pi_no()
    {
        $this->ensure_tables();

        $row = $this->db->query(
            "SELECT MAX(CAST(pi_no AS UNSIGNED)) AS max_pi_no FROM {$this->table}"
        )->row();

        return (int) ($row->max_pi_no ?? 0) + 1;
    }

    public function get_table_name()
    {
        return $this->table;
    }

    public function get_item_table_name()
    {
        return $this->item_table;
    }
}
