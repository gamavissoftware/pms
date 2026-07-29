<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_spare_quotation_model extends CI_Model
{
    private $quote_table = 'order_spare_quotations';
    private $item_table = 'order_spare_quotation_items';
    private $history_table = 'order_spare_quotation_history';

    public function ensure_tables()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->quote_table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `received_po_id` INT NOT NULL,
            `parent_quotation_id` INT UNSIGNED DEFAULT NULL,
            `quotation_no` VARCHAR(60) NOT NULL,
            `revision_no` INT UNSIGNED NOT NULL DEFAULT 0,
            `quotation_date` DATE NOT NULL,
            `currency` VARCHAR(5) NOT NULL DEFAULT 'INR',
            `attention` VARCHAR(150) DEFAULT NULL,
            `subject` VARCHAR(255) DEFAULT NULL,
            `payment_terms` VARCHAR(255) DEFAULT NULL,
            `validity` VARCHAR(150) DEFAULT NULL,
            `delivery_terms` VARCHAR(255) DEFAULT NULL,
            `notes` TEXT DEFAULT NULL,
            `sub_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
            `discount_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
            `taxable_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
            `gst_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
            `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
            `status` VARCHAR(20) NOT NULL DEFAULT 'Quoted',
            `won_po_no` VARCHAR(100) DEFAULT NULL,
            `won_po_date` DATE DEFAULT NULL,
            `won_order_value` DECIMAL(15,2) DEFAULT NULL,
            `won_po_attachment` VARCHAR(255) DEFAULT NULL,
            `won_remarks` TEXT DEFAULT NULL,
            `created_by` INT NOT NULL,
            `created_on` DATETIME NOT NULL,
            `updated_by` INT DEFAULT NULL,
            `updated_on` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_order_spare_quote_no` (`quotation_no`),
            KEY `idx_order_spare_po` (`received_po_id`),
            KEY `idx_order_spare_owner` (`created_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->item_table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `quotation_id` INT UNSIGNED NOT NULL,
            `line_no` INT UNSIGNED NOT NULL,
            `item_description` TEXT NOT NULL,
            `hsn_code` VARCHAR(30) DEFAULT NULL,
            `quantity` DECIMAL(12,3) NOT NULL DEFAULT 0,
            `unit` VARCHAR(30) NOT NULL DEFAULT 'Nos',
            `unit_rate` DECIMAL(15,2) NOT NULL DEFAULT 0,
            `discount_percent` DECIMAL(6,2) NOT NULL DEFAULT 0,
            `gst_percent` DECIMAL(6,2) NOT NULL DEFAULT 0,
            `taxable_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
            `gst_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
            `line_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `idx_order_spare_quote_item` (`quotation_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->history_table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `received_po_id` INT NOT NULL,
            `quotation_id` INT UNSIGNED DEFAULT NULL,
            `event_type` VARCHAR(30) NOT NULL,
            `remarks` TEXT DEFAULT NULL,
            `added_by` INT NOT NULL,
            `added_on` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_order_spare_history_po` (`received_po_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function get_owned_po($po_id, $user_id)
    {
        return $this->db->select('id, company_name, pono, podate, order_value, df_number, added_by')
            ->from('poreceived')->where('id', (int) $po_id)->where('added_by', (int) $user_id)->get()->row();
    }

    public function get_quotes($po_id, $user_id)
    {
        return $this->db->from($this->quote_table)->where('received_po_id', (int) $po_id)
            ->where('created_by', (int) $user_id)->order_by('revision_no', 'DESC')->order_by('id', 'DESC')->get()->result();
    }

    public function get_quote($quote_id, $user_id)
    {
        return $this->db->from($this->quote_table)->where('id', (int) $quote_id)
            ->where('created_by', (int) $user_id)->get()->row();
    }

    public function get_items($quote_id)
    {
        return $this->db->from($this->item_table)->where('quotation_id', (int) $quote_id)
            ->order_by('line_no', 'ASC')->get()->result();
    }

    public function next_quotation_number()
    {
        $next = (int) $this->db->count_all($this->quote_table) + 1;
        do {
            $number = 'OSQ/' . date('Y') . '/' . str_pad($next++, 5, '0', STR_PAD_LEFT);
            $exists = $this->db->where('quotation_no', $number)->count_all_results($this->quote_table) > 0;
        } while ($exists);
        return $number;
    }

    public function next_revision_details($quotation_no, $received_po_id, $user_id)
    {
        $base = preg_replace('/\/R\d+$/', '', (string) $quotation_no);
        $row = $this->db->select_max('revision_no', 'max_revision')->from($this->quote_table)
            ->where('received_po_id', (int) $received_po_id)->where('created_by', (int) $user_id)
            ->group_start()->where('quotation_no', $base)->or_like('quotation_no', $base . '/R', 'after')->group_end()
            ->get()->row();
        $revision = max(1, (int) ($row->max_revision ?? 0) + 1);
        return array('revision_no' => $revision, 'quotation_no' => $base . '/R' . $revision);
    }

    public function save_quote(array $header, array $items, $event_type, $remarks = '')
    {
        $this->db->trans_begin();
        $this->db->insert($this->quote_table, $header);
        $quote_id = (int) $this->db->insert_id();
        foreach ($items as $item) {
            $item['quotation_id'] = $quote_id;
            $this->db->insert($this->item_table, $item);
        }
        $this->add_history($header['received_po_id'], $quote_id, $event_type, $remarks, $header['created_by']);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 0;
        }
        $this->db->trans_commit();
        return $quote_id;
    }

    public function mark_won($quote_id, $user_id, array $data)
    {
        $quote = $this->get_quote($quote_id, $user_id);
        if (!$quote) return false;
        $this->db->trans_begin();
        $this->db->where('received_po_id', $quote->received_po_id)->where('created_by', (int) $user_id)
            ->update($this->quote_table, array('status' => 'Superseded'));
        $data['status'] = 'Won';
        $data['updated_by'] = (int) $user_id;
        $data['updated_on'] = date('Y-m-d H:i:s');
        $this->db->where('id', (int) $quote_id)->where('created_by', (int) $user_id)->update($this->quote_table, $data);
        $this->add_history($quote->received_po_id, $quote_id, 'Order Won', $data['won_remarks'], $user_id);
        if ($this->db->trans_status() === false) { $this->db->trans_rollback(); return false; }
        $this->db->trans_commit();
        return true;
    }

    private function add_history($po_id, $quote_id, $event, $remarks, $user_id)
    {
        $this->db->insert($this->history_table, array('received_po_id' => (int) $po_id,
            'quotation_id' => (int) $quote_id, 'event_type' => $event, 'remarks' => $remarks,
            'added_by' => (int) $user_id, 'added_on' => date('Y-m-d H:i:s')));
    }
}
