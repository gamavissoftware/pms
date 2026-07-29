ALTER TABLE `prestogroup_teams`
    ADD COLUMN `show_all_team_tasks` tinyint(1) NOT NULL DEFAULT 0 AFTER `by_pass`,
    ADD COLUMN `allow_task_assignment` tinyint(1) NOT NULL DEFAULT 0 AFTER `show_all_team_tasks`,
    ADD KEY `idx_show_all_team_tasks` (`show_all_team_tasks`),
    ADD KEY `idx_allow_task_assignment` (`allow_task_assignment`);
