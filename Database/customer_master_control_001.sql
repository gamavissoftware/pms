CREATE TABLE IF NOT EXISTS `customer_master_control_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_type` varchar(20) NOT NULL,
  `source_record_id` int(11) NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `change_note` text DEFAULT NULL,
  `changed_fields` longtext DEFAULT NULL,
  `previous_snapshot` longtext DEFAULT NULL,
  `updated_snapshot` longtext DEFAULT NULL,
  `changed_by` int(11) NOT NULL,
  `changed_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_customer_master_control_source` (`source_type`,`source_record_id`),
  KEY `idx_customer_master_control_changed_by` (`changed_by`),
  KEY `idx_customer_master_control_changed_on` (`changed_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
