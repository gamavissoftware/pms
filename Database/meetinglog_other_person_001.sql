ALTER TABLE `team_time_calculation`
ADD COLUMN `other_person_name` VARCHAR(255) NULL DEFAULT NULL AFTER `user_id`;
