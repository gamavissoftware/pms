-- Spares Execution Module incremental patch
-- Use this file only if `spares_execution_module.sql` was already imported earlier.
-- Safe to run in phpMyAdmin on an existing setup.

CREATE TABLE IF NOT EXISTS `spares_execution_alert_log` (
  `alert_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_type` enum('UNSCHEDULED_ORDER','OVERDUE_TASK','STALE_TASK','PENDING_EXTENSION') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `recipient_user_id` int(11) NOT NULL,
  `notification_type` varchar(100) NOT NULL,
  `notification_id` int(11) DEFAULT NULL,
  `alert_message` text DEFAULT NULL,
  `alert_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`alert_log_id`),
  UNIQUE KEY `uniq_spares_execution_alert_daily` (`alert_type`,`reference_id`,`recipient_user_id`,`alert_date`),
  KEY `idx_spares_execution_alert_recipient` (`recipient_user_id`,`alert_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
