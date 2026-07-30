-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — PERMISSIONS (install 003)
--
--  Creates three submodule rows under module 4 (BOM CORRECTION TOOL)
--  and grants them to the roles that already hold the existing BOM
--  Correction Tool submodule, so the same people can reach the new
--  module without anyone hand-editing a role matrix.
--
--  ID-AGNOSTIC BY DESIGN
--  ---------------------
--  `submodule`.`id` is AUTO_INCREMENT. This script therefore NEVER
--  hardcodes an id: it inserts, asks the database what it assigned via
--  LAST_INSERT_ID(), and prints the three numbers at the end in a form
--  that can be pasted straight into application/config/abom.php.
--
--  That means it behaves identically on a scratch copy and on
--  production, and no stale MAX(id) reading can make it wrong.
--
--  SAFE TO RE-RUN
--  --------------
--  Everything is wrapped in a procedure that pre-flights first and
--  aborts with a clear message rather than creating duplicates:
--
--    * module 4 must exist
--    * no `abom_%` table may be missing (001/002 must have run)
--    * no submodule named 'AUTOMATION BOM %' may already exist
--
--  The DML is wrapped in a transaction, so a partial permissions insert
--  cannot survive a failure.
--
--  Run AFTER Database/abom_001.sql and Database/abom_002_seed.sql.
--
--    mysql -u USER -p DBNAME < Database/abom_003_permissions.sql
-- =====================================================================
SET NAMES utf8mb4;

DELIMITER $$

DROP PROCEDURE IF EXISTS `abom_install_permissions` $$

CREATE PROCEDURE `abom_install_permissions`()
BEGIN
    DECLARE v_module_id      INT DEFAULT 4;
    DECLARE v_generator_id   INT;
    DECLARE v_approvals_id   INT;
    DECLARE v_master_id      INT;
    DECLARE v_seed_submodule INT;
    DECLARE v_count          INT;
    DECLARE v_grants         INT;

    -- Roll back rather than leave a half-applied permission set.
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    -- -----------------------------------------------------------------
    -- PRE-FLIGHT
    -- -----------------------------------------------------------------

    -- 1. The module must exist.
    SELECT COUNT(*) INTO v_count
      FROM `system_modules` WHERE `id` = v_module_id;
    IF v_count = 0 THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'ABORTED: module id 4 (BOM CORRECTION TOOL) does not exist in system_modules. Nothing was changed.';
    END IF;

    -- 2. The module tables must already be installed.
    SELECT COUNT(*) INTO v_count
      FROM `information_schema`.`tables`
     WHERE `table_schema` = DATABASE() AND `table_name` = 'abom_item';
    IF v_count = 0 THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'ABORTED: abom_item not found. Run Database/abom_001.sql and Database/abom_002_seed.sql first. Nothing was changed.';
    END IF;

    -- 3. Do not create duplicates on a re-run.
    SELECT COUNT(*) INTO v_count
      FROM `submodule` WHERE `submodule` LIKE 'AUTOMATION BOM %';
    IF v_count > 0 THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'ABORTED: submodule rows named "AUTOMATION BOM %" already exist. This script has already been run. Nothing was changed - query submodule WHERE moduleid = 4 for the existing ids.';
    END IF;

    -- -----------------------------------------------------------------
    -- INSTALL
    -- -----------------------------------------------------------------
    START TRANSACTION;

    INSERT INTO `submodule` (`moduleid`, `submodule`, `status`, `addedOn`, `dynachem`, `shubhampack`)
    VALUES (v_module_id, 'AUTOMATION BOM GENERATOR', 1, NOW(), 0, 2);
    SET v_generator_id = LAST_INSERT_ID();

    INSERT INTO `submodule` (`moduleid`, `submodule`, `status`, `addedOn`, `dynachem`, `shubhampack`)
    VALUES (v_module_id, 'AUTOMATION BOM APPROVALS', 1, NOW(), 0, 2);
    SET v_approvals_id = LAST_INSERT_ID();

    INSERT INTO `submodule` (`moduleid`, `submodule`, `status`, `addedOn`, `dynachem`, `shubhampack`)
    VALUES (v_module_id, 'AUTOMATION BOM MASTER ITEMS', 1, NOW(), 0, 2);
    SET v_master_id = LAST_INSERT_ID();

    -- -----------------------------------------------------------------
    -- GRANTS
    --
    -- Mirrored from whoever already holds the existing BOM Correction
    -- Tool submodule under module 4. That is a deliberate choice rather
    -- than a guess: it is the closest existing audience, and it is
    -- trivially adjustable afterwards in module_capablity.
    --
    -- If module 4 has no existing submodule, no grants are created and
    -- the summary says so — the module still installs, and an
    -- administrator grants access explicitly.
    -- -----------------------------------------------------------------
    SELECT MIN(`id`) INTO v_seed_submodule
      FROM `submodule`
     WHERE `moduleid` = v_module_id
       AND `id` NOT IN (v_generator_id, v_approvals_id, v_master_id);

    SET v_grants = 0;

    IF v_seed_submodule IS NOT NULL THEN
        INSERT INTO `module_capablity`
            (`acessid`, `role_id`, `moduleid`, `submoduleid`, `submodule_access`,
             `madd`, `medit`, `mremove`, `addedOn`, `upadtedOn`)
        SELECT 0, `role_id`, v_module_id, s.`new_id`, 1,
               `madd`, `medit`, 0, NOW(), NOW()
          FROM `module_capablity` mc
          JOIN (SELECT v_generator_id AS `new_id`
                UNION ALL SELECT v_approvals_id
                UNION ALL SELECT v_master_id) s
         WHERE mc.`moduleid` = v_module_id
           AND mc.`submoduleid` = v_seed_submodule
           AND mc.`submodule_access` = 1;

        SET v_grants = ROW_COUNT();
    END IF;

    COMMIT;

    -- -----------------------------------------------------------------
    -- WHAT TO PASTE INTO application/config/abom.php
    -- -----------------------------------------------------------------
    -- One row per line, so it reads correctly in the mysql client, in
    -- phpMyAdmin and in anything else: a single multi-line string gets
    -- backslash-escaped by the default tabular output mode.
    SELECT '$config[\'abom_submodule_ids\'] = array('        AS `PASTE THIS INTO application/config/abom.php`
    UNION ALL SELECT CONCAT('    \'generator\'    => ', v_generator_id, ',')
    UNION ALL SELECT CONCAT('    \'approvals\'    => ', v_approvals_id, ',')
    UNION ALL SELECT CONCAT('    \'master_items\' => ', v_master_id, ',')
    UNION ALL SELECT ');';

    SELECT v_generator_id AS `generator`,
           v_approvals_id AS `approvals`,
           v_master_id    AS `master_items`,
           v_grants       AS `grant_rows_created`,
           CASE WHEN v_grants = 0
                THEN 'No roles were granted access - grant them in module_capablity (moduleid 4).'
                ELSE 'Grants mirrored from the existing module 4 submodule.'
           END AS `note`;

END $$

DELIMITER ;

CALL `abom_install_permissions`();

DROP PROCEDURE `abom_install_permissions`;
