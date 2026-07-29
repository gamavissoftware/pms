ALTER TABLE `system_users`
    ADD COLUMN `project_coordinator_user_id` int(11) DEFAULT NULL AFTER `marketing_person`,
    ADD KEY `idx_project_coordinator_user_id` (`project_coordinator_user_id`);
