ALTER TABLE `user_role`
    ADD COLUMN `master_write_access` TINYINT(1) NOT NULL DEFAULT 1 AFTER `status`,
    ADD KEY `idx_user_role_master_write_access` (`master_write_access`);
