-- Spares execution workflow module
-- This layer keeps order-task scheduling separate from the legacy task module.

CREATE TABLE `spares_execution_orders` (
  `execution_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `workflow_type` enum('IN_STOCK','STANDARD','CUSTOM') NOT NULL DEFAULT 'STANDARD',
  `commit_date` date NOT NULL,
  `priority` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `execution_status` enum('Draft','Scheduled','In Progress','On Hold','Completed','Cancelled') NOT NULL DEFAULT 'Scheduled',
  `marketing_owner_id` int(11) NOT NULL,
  `schedule_notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`execution_order_id`),
  UNIQUE KEY `uniq_spares_execution_order` (`order_id`),
  KEY `idx_spares_execution_commit` (`commit_date`),
  KEY `idx_spares_execution_status` (`execution_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `spares_execution_task_master` (
  `task_master_id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_type` enum('IN_STOCK','STANDARD','CUSTOM') NOT NULL,
  `task_code` varchar(50) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `default_owner_id` int(11) DEFAULT NULL,
  `sequence_no` int(11) NOT NULL,
  `sla_days` int(11) NOT NULL DEFAULT 1,
  `depends_on_code` varchar(50) DEFAULT NULL,
  `can_start_parallel` tinyint(1) NOT NULL DEFAULT 0,
  `extension_allowed` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`task_master_id`),
  UNIQUE KEY `uniq_spares_execution_task_code` (`workflow_type`,`task_code`),
  KEY `idx_spares_execution_task_seq` (`workflow_type`,`sequence_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `spares_execution_tasks` (
  `execution_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `execution_order_id` int(11) NOT NULL,
  `task_master_id` int(11) DEFAULT NULL,
  `task_code` varchar(50) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `sequence_no` int(11) NOT NULL,
  `depends_on_task_id` int(11) DEFAULT NULL,
  `planned_start_date` date DEFAULT NULL,
  `planned_end_date` date NOT NULL,
  `actual_start_date` datetime DEFAULT NULL,
  `actual_end_date` datetime DEFAULT NULL,
  `task_status` enum('Pending','Open','In Progress','Completed','Blocked','On Hold','Cancelled') NOT NULL DEFAULT 'Pending',
  `completion_percent` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `is_overdue` tinyint(1) NOT NULL DEFAULT 0,
  `last_remark` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`execution_task_id`),
  KEY `idx_spares_execution_order_tasks` (`execution_order_id`),
  KEY `idx_spares_execution_status_due` (`task_status`,`planned_end_date`),
  KEY `idx_spares_execution_assigned` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `spares_execution_task_updates` (
  `update_id` int(11) NOT NULL AUTO_INCREMENT,
  `execution_task_id` int(11) NOT NULL,
  `update_type` enum('Remark','Status','Assignment','Attachment','Extension Request','Extension Decision','Schedule','Bulk Status') NOT NULL DEFAULT 'Remark',
  `previous_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `next_action_date` date DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  `attachment_original_name` varchar(255) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_on` datetime NOT NULL,
  PRIMARY KEY (`update_id`),
  KEY `idx_spares_execution_task_update` (`execution_task_id`,`added_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `spares_execution_extension_requests` (
  `extension_request_id` int(11) NOT NULL AUTO_INCREMENT,
  `execution_task_id` int(11) NOT NULL,
  `current_due_date` date NOT NULL,
  `requested_due_date` date NOT NULL,
  `reason` text NOT NULL,
  `request_status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `requested_by` int(11) NOT NULL,
  `requested_on` datetime NOT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_on` datetime DEFAULT NULL,
  `review_remarks` text DEFAULT NULL,
  PRIMARY KEY (`extension_request_id`),
  KEY `idx_spares_execution_extension_status` (`request_status`,`requested_on`),
  KEY `idx_spares_execution_extension_task` (`execution_task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `spares_execution_alert_log` (
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

CREATE TABLE `spares_execution_alert_runs` (
  `run_id` int(11) NOT NULL AUTO_INCREMENT,
  `run_mode` enum('MANUAL','CRON_CLI','CRON_WEB') NOT NULL,
  `sent_count` int(11) NOT NULL DEFAULT 0,
  `skipped_count` int(11) NOT NULL DEFAULT 0,
  `unscheduled_sent_count` int(11) NOT NULL DEFAULT 0,
  `overdue_sent_count` int(11) NOT NULL DEFAULT 0,
  `stale_sent_count` int(11) NOT NULL DEFAULT 0,
  `pending_extension_sent_count` int(11) NOT NULL DEFAULT 0,
  `triggered_by` int(11) NOT NULL DEFAULT 0,
  `run_note` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`run_id`),
  KEY `idx_spares_execution_alert_runs_created` (`created_at`),
  KEY `idx_spares_execution_alert_runs_mode` (`run_mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `spares_execution_task_master`
(`workflow_type`, `task_code`, `task_name`, `department_id`, `default_owner_id`, `sequence_no`, `sla_days`, `depends_on_code`, `can_start_parallel`, `extension_allowed`, `is_active`, `created_by`, `created_at`)
VALUES
('IN_STOCK', 'ORDER_ACK', 'Order Acknowledgement', NULL, NULL, 10, 1, NULL, 0, 0, 1, 0, NOW()),
('IN_STOCK', 'STORE_STOCK_CHECK', 'Store Stock Verification', NULL, NULL, 20, 1, 'ORDER_ACK', 0, 1, 1, 0, NOW()),
('IN_STOCK', 'STORE_ISSUE', 'Store Issue for Dispatch', NULL, NULL, 30, 1, 'STORE_STOCK_CHECK', 0, 1, 1, 0, NOW()),
('IN_STOCK', 'DISPATCH_DOCS', 'Dispatch Documentation', NULL, NULL, 40, 1, 'STORE_ISSUE', 0, 1, 1, 0, NOW()),
('IN_STOCK', 'DISPATCH', 'Dispatch to Customer', NULL, NULL, 50, 1, 'DISPATCH_DOCS', 0, 1, 1, 0, NOW()),
('STANDARD', 'ORDER_ACK', 'Order Acknowledgement', NULL, NULL, 10, 1, NULL, 0, 0, 1, 0, NOW()),
('STANDARD', 'STOCK_REVIEW', 'Stock Review and Shortage Confirmation', NULL, NULL, 20, 1, 'ORDER_ACK', 0, 1, 1, 0, NOW()),
('STANDARD', 'INDENT_CREATE', 'Indent Creation', NULL, NULL, 30, 1, 'STOCK_REVIEW', 0, 1, 1, 0, NOW()),
('STANDARD', 'PURCHASE_PO', 'Purchase Order Release', NULL, NULL, 40, 2, 'INDENT_CREATE', 0, 1, 1, 0, NOW()),
('STANDARD', 'VENDOR_FOLLOWUP', 'Vendor Follow-up', NULL, NULL, 50, 2, 'PURCHASE_PO', 0, 1, 1, 0, NOW()),
('STANDARD', 'MATERIAL_GRN', 'Material Receipt and GRN', NULL, NULL, 60, 1, 'VENDOR_FOLLOWUP', 0, 1, 1, 0, NOW()),
('STANDARD', 'STORE_ACK', 'Store Acknowledgement', NULL, NULL, 70, 1, 'MATERIAL_GRN', 0, 1, 1, 0, NOW()),
('STANDARD', 'DISPATCH_DOCS', 'Dispatch Documentation', NULL, NULL, 80, 1, 'STORE_ACK', 0, 1, 1, 0, NOW()),
('STANDARD', 'DISPATCH', 'Dispatch to Customer', NULL, NULL, 90, 1, 'DISPATCH_DOCS', 0, 1, 1, 0, NOW()),
('CUSTOM', 'ORDER_ACK', 'Order Acknowledgement', NULL, NULL, 10, 1, NULL, 0, 0, 1, 0, NOW()),
('CUSTOM', 'TECH_SPEC_FREEZE', 'Technical Specification Freeze', NULL, NULL, 20, 1, 'ORDER_ACK', 0, 1, 1, 0, NOW()),
('CUSTOM', 'DESIGN_BOM', 'Design Drawing and BOM Release', NULL, NULL, 30, 2, 'TECH_SPEC_FREEZE', 0, 1, 1, 0, NOW()),
('CUSTOM', 'PPC_PLAN', 'PPC Production Planning', NULL, NULL, 40, 1, 'DESIGN_BOM', 0, 1, 1, 0, NOW()),
('CUSTOM', 'PURCHASE_INDENT', 'Purchase Indent and Sourcing', NULL, NULL, 50, 2, 'PPC_PLAN', 0, 1, 1, 0, NOW()),
('CUSTOM', 'MATERIAL_GRN', 'Material Receipt and GRN', NULL, NULL, 60, 1, 'PURCHASE_INDENT', 0, 1, 1, 0, NOW()),
('CUSTOM', 'PRODUCTION', 'Production and Assembly', NULL, NULL, 70, 3, 'MATERIAL_GRN', 0, 1, 1, 0, NOW()),
('CUSTOM', 'QC_RELEASE', 'Quality Check and Release', NULL, NULL, 80, 1, 'PRODUCTION', 0, 1, 1, 0, NOW()),
('CUSTOM', 'STORE_TRANSFER', 'Transfer to Store', NULL, NULL, 90, 1, 'QC_RELEASE', 0, 1, 1, 0, NOW()),
('CUSTOM', 'DISPATCH_DOCS', 'Dispatch Documentation', NULL, NULL, 100, 1, 'STORE_TRANSFER', 0, 1, 1, 0, NOW()),
('CUSTOM', 'DISPATCH', 'Dispatch to Customer', NULL, NULL, 110, 1, 'DISPATCH_DOCS', 0, 1, 1, 0, NOW());
