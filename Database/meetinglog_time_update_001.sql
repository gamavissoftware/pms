ALTER TABLE `team_time_calculation`
ADD COLUMN `meeting_end_time` TIME NULL DEFAULT NULL AFTER `meeting_time`;
