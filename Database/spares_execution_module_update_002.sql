-- Spares Execution Module incremental patch
-- Use this file only if previous Spares execution SQL files were already imported.
-- Safe to run in phpMyAdmin on an existing setup.

CREATE TABLE IF NOT EXISTS `spares_execution_alert_runs` (
  `run_id` int(11) NOT NULL AUTO_INCREMENT,
  `run_mode` enum('MANUAL','CRON_CLI','CRON_WEB') NOT NULL,
  `sent_count` int(11) NOT NULL DEFAULT 0,
  `skipped_count` int(11) NOT NULL DEFAULT 0,
  `unscheduled_sent_count` int(11) NOT NULL DEFAULT 0,
  `overdue_sent_count` int(11) NOT NULL DEFAULT 0,
  `pending_extension_sent_count` int(11) NOT NULL DEFAULT 0,
  `triggered_by` int(11) NOT NULL DEFAULT 0,
  `run_note` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`run_id`),
  KEY `idx_spares_execution_alert_runs_created` (`created_at`),
  KEY `idx_spares_execution_alert_runs_mode` (`run_mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
