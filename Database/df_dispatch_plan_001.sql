CREATE TABLE IF NOT EXISTS `df_dispatch_plans` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `df_id` INT(11) DEFAULT NULL,
  `df_no` VARCHAR(100) NOT NULL,
  `priority` INT(11) NOT NULL DEFAULT 0,
  `model` VARCHAR(500) DEFAULT NULL,
  `product` VARCHAR(255) DEFAULT NULL,
  `automation` VARCHAR(100) DEFAULT NULL,
  `planned_dispatch_date` DATE NOT NULL,
  `fat_date` DATE DEFAULT NULL,
  `completion_percent` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
  `frame_status` VARCHAR(255) DEFAULT NULL,
  `design_owner` VARCHAR(150) DEFAULT NULL,
  `marketing_owner` VARCHAR(150) DEFAULT NULL,
  `status` ENUM('Planned','In Progress','At Risk','Ready','Dispatched','On Hold') NOT NULL DEFAULT 'Planned',
  `remarks` TEXT DEFAULT NULL,
  `created_by` INT(11) NOT NULL,
  `created_on` DATETIME NOT NULL,
  `updated_by` INT(11) DEFAULT NULL,
  `updated_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_df_dispatch_plan_df_date` (`df_no`, `planned_dispatch_date`),
  KEY `idx_df_dispatch_plan_date_status` (`planned_dispatch_date`, `status`),
  KEY `idx_df_dispatch_plan_df_id` (`df_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `df_dispatch_plans`
  MODIFY `priority` INT(11) NOT NULL DEFAULT 0;

UPDATE `df_dispatch_plans` SET `priority` = 0 WHERE `priority` = 999;

CREATE TABLE IF NOT EXISTS `df_dispatch_dependencies` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `plan_id` INT(11) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `title` VARCHAR(500) NOT NULL,
  `owner_user_id` INT(11) DEFAULT NULL,
  `due_date` DATE NOT NULL,
  `priority` ENUM('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `status` ENUM('Open','In Progress','Blocked','Closed') NOT NULL DEFAULT 'Open',
  `remarks` TEXT DEFAULT NULL,
  `created_by` INT(11) NOT NULL,
  `created_on` DATETIME NOT NULL,
  `updated_by` INT(11) DEFAULT NULL,
  `updated_on` DATETIME DEFAULT NULL,
  `completed_on` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_df_dispatch_dependency_plan_status` (`plan_id`, `status`),
  KEY `idx_df_dispatch_dependency_owner_due` (`owner_user_id`, `due_date`),
  CONSTRAINT `fk_df_dispatch_dependency_plan`
    FOREIGN KEY (`plan_id`) REFERENCES `df_dispatch_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
