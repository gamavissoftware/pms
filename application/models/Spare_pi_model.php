<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Spare_pi_model extends CI_Model
{
    private $table = 'spare_proforma_invoices';
    private $item_table = 'spare_proforma_invoice_items';

    public function ensure_tables()
    {
        if (!$this->db->table_exists($this->table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `opportunity_id` INT(11) NOT NULL,
                    `quote_id` INT(11) DEFAULT NULL,
                    `customer_id` INT(11) DEFAULT NULL,
                    `pi_no` VARCHAR(80) NOT NULL,
                    `pi_date` DATE DEFAULT NULL,
                    `currency` VARCHAR(10) NOT NULL DEFAULT 'INR',
                    `attention` VARCHAR(255) DEFAULT NULL,
                    `buyer_name` VARCHAR(255) DEFAULT NULL,
                    `buyer_contact` VARCHAR(255) DEFAULT NULL,
                    `buyer_email` VARCHAR(255) DEFAULT NULL,
                    `buyer_address` TEXT DEFAULT NULL,
                    `buyer_gstin` VARCHAR(100) DEFAULT NULL,
                    `consignee_name` VARCHAR(255) DEFAULT NULL,
                    `consignee_contact` VARCHAR(255) DEFAULT NULL,
                    `consignee_phone` VARCHAR(100) DEFAULT NULL,
                    `consignee_address` TEXT DEFAULT NULL,
                    `consignee_gstin` VARCHAR(100) DEFAULT NULL,
                    `reference_quote_no` VARCHAR(100) DEFAULT NULL,
                    `reference_quote_date` DATE DEFAULT NULL,
                    `payment_terms` TEXT DEFAULT NULL,
                    `validity` VARCHAR(255) DEFAULT NULL,
                    `delivery_terms` VARCHAR(255) DEFAULT NULL,
                    `packing_percent` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `packing_charge` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `freight_charge` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `ex_work_charge` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `insurance_percent` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `insurance_charge` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `overall_discount_percent` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `overall_discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `basic_value` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `total_value` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `gst_percent` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `gst_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `amount_in_words` TEXT DEFAULT NULL,
                    `notes` TEXT DEFAULT NULL,
                    `declaration_text` TEXT DEFAULT NULL,
                    `created_by` INT(11) DEFAULT NULL,
                    `created_at` DATETIME DEFAULT NULL,
                    `updated_by` INT(11) DEFAULT NULL,
                    `updated_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_spare_pi_opp` (`opportunity_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }

        if (!$this->db->table_exists($this->item_table)) {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS `{$this->item_table}` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `spare_pi_id` INT(11) NOT NULL,
                    `sort_order` INT(11) NOT NULL DEFAULT 0,
                    `product_id` INT(11) DEFAULT NULL,
                    `product_code` VARCHAR(100) DEFAULT NULL,
                    `hsn_code` VARCHAR(50) DEFAULT NULL,
                    `description` TEXT DEFAULT NULL,
                    `quantity` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `unit` VARCHAR(20) NOT NULL DEFAULT 'NOS',
                    `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `discount_percent` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `total_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                    `created_at` DATETIME DEFAULT NULL,
                    `updated_at` DATETIME DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_spare_pi_items_master` (`spare_pi_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        }

        if ($this->db->table_exists($this->item_table) && !$this->db->field_exists('unit', $this->item_table)) {
            $this->db->query(
                "ALTER TABLE `{$this->item_table}`
                 ADD `unit` VARCHAR(20) NOT NULL DEFAULT 'NOS' AFTER `quantity`"
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

    public function get_items($spare_pi_id)
    {
        $this->ensure_tables();

        return $this->db->where('spare_pi_id', (int) $spare_pi_id)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->item_table)
            ->result();
    }

    public function get_next_pi_sequence()
    {
        $this->ensure_tables();

        $row = $this->db->select_max('id', 'max_id')->get($this->table)->row();
        return (int) ($row->max_id ?? 0) + 1;
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
