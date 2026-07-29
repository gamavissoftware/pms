-- Isolated order-linked spare quotation module. No existing Spares tables are changed.
CREATE TABLE IF NOT EXISTS `order_spare_quotations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `received_po_id` INT NOT NULL,
  `parent_quotation_id` INT UNSIGNED DEFAULT NULL, `quotation_no` VARCHAR(60) NOT NULL,
  `revision_no` INT UNSIGNED NOT NULL DEFAULT 0, `quotation_date` DATE NOT NULL,
  `currency` VARCHAR(5) NOT NULL DEFAULT 'INR', `attention` VARCHAR(150) DEFAULT NULL,
  `subject` VARCHAR(255) DEFAULT NULL, `payment_terms` VARCHAR(255) DEFAULT NULL,
  `validity` VARCHAR(150) DEFAULT NULL, `delivery_terms` VARCHAR(255) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL, `sub_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `discount_total` DECIMAL(15,2) NOT NULL DEFAULT 0, `taxable_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `gst_total` DECIMAL(15,2) NOT NULL DEFAULT 0, `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'Quoted', `won_po_no` VARCHAR(100) DEFAULT NULL,
  `won_po_date` DATE DEFAULT NULL, `won_order_value` DECIMAL(15,2) DEFAULT NULL,
  `won_po_attachment` VARCHAR(255) DEFAULT NULL, `won_remarks` TEXT DEFAULT NULL,
  `created_by` INT NOT NULL, `created_on` DATETIME NOT NULL, `updated_by` INT DEFAULT NULL,
  `updated_on` DATETIME DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `uq_order_spare_quote_no` (`quotation_no`),
  KEY `idx_order_spare_po` (`received_po_id`), KEY `idx_order_spare_owner` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_spare_quotation_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `quotation_id` INT UNSIGNED NOT NULL,
  `line_no` INT UNSIGNED NOT NULL, `item_description` TEXT NOT NULL, `hsn_code` VARCHAR(30) DEFAULT NULL,
  `quantity` DECIMAL(12,3) NOT NULL DEFAULT 0, `unit` VARCHAR(30) NOT NULL DEFAULT 'Nos',
  `unit_rate` DECIMAL(15,2) NOT NULL DEFAULT 0, `discount_percent` DECIMAL(6,2) NOT NULL DEFAULT 0,
  `gst_percent` DECIMAL(6,2) NOT NULL DEFAULT 0, `taxable_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `gst_amount` DECIMAL(15,2) NOT NULL DEFAULT 0, `line_total` DECIMAL(15,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `idx_order_spare_quote_item` (`quotation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_spare_quotation_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `received_po_id` INT NOT NULL,
  `quotation_id` INT UNSIGNED DEFAULT NULL, `event_type` VARCHAR(30) NOT NULL,
  `remarks` TEXT DEFAULT NULL, `added_by` INT NOT NULL, `added_on` DATETIME NOT NULL,
  PRIMARY KEY (`id`), KEY `idx_order_spare_history_po` (`received_po_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
