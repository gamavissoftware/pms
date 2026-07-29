CREATE TABLE IF NOT EXISTS `task_management_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_code` varchar(40) NOT NULL,
  `business_location_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `task_details` mediumtext NOT NULL,
  `reference_url` varchar(500) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `priority` varchar(20) NOT NULL DEFAULT 'MEDIUM',
  `requested_due_date` date DEFAULT NULL,
  `committed_due_date` date DEFAULT NULL,
  `assigned_by_user_id` int(11) NOT NULL,
  `assigned_to_user_id` int(11) NOT NULL,
  `assigned_to_department_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(40) NOT NULL DEFAULT 'AWAITING_DUE_DATE',
  `progress_percent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `last_update_note` text,
  `completed_on` datetime DEFAULT NULL,
  `completed_by_user_id` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_task_management_code` (`task_code`),
  KEY `idx_task_management_assigned_to` (`assigned_to_user_id`,`status`),
  KEY `idx_task_management_assigned_by` (`assigned_by_user_id`,`status`),
  KEY `idx_task_management_due_date` (`committed_due_date`),
  KEY `idx_task_management_updated_on` (`updated_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `task_management_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `update_type` varchar(50) NOT NULL,
  `status` varchar(40) NOT NULL,
  `progress_percent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `due_date` date DEFAULT NULL,
  `update_note` mediumtext NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_task_management_updates_task` (`task_id`,`created_on`),
  KEY `idx_task_management_updates_actor` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `task_management_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `read_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_task_management_notifications_user` (`user_id`,`is_read`,`created_on`),
  KEY `idx_task_management_notifications_task` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
