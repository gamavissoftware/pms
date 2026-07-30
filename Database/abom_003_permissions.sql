-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — PERMISSIONS (install 003)
--
--  Creates three submodule rows under module 4 (BOM CORRECTION TOOL)
--  and grants access to NOBODY.
--
--  GRANTS ARE DELIBERATELY NOT CREATED
--  -----------------------------------
--  Nobody should acquire the authority to approve an automation BOM
--  because an install script assumed it. The script prints a SUGGESTED
--  set of INSERT statements, commented out, derived from whoever already
--  holds the existing BOM Correction Tool submodule — review them, decide
--  who actually belongs on each of the three lists, and run the ones you
--  want. See ROLLOUT.md step 3.
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
    -- NO GRANTS ARE CREATED HERE. See the header.
    -- -----------------------------------------------------------------
    SELECT MIN(`id`) INTO v_seed_submodule
      FROM `submodule`
     WHERE `moduleid` = v_module_id
       AND `id` NOT IN (v_generator_id, v_approvals_id, v_master_id);

    SET v_grants = 0;

    COMMIT;

    -- -----------------------------------------------------------------
    -- OUTPUT 1: what to paste into application/config/abom.php
    --
    -- One row per line, so it reads correctly in the mysql client, in
    -- phpMyAdmin and in anything else: a single multi-line string gets
    -- backslash-escaped by the default tabular output mode.
    -- -----------------------------------------------------------------
    SELECT '$config[\'abom_submodule_ids\'] = array('        AS `1. PASTE THIS INTO application/config/abom.php`
    UNION ALL SELECT CONCAT('    \'generator\'    => ', v_generator_id, ',')
    UNION ALL SELECT CONCAT('    \'approvals\'    => ', v_approvals_id, ',')
    UNION ALL SELECT CONCAT('    \'master_items\' => ', v_master_id, ',')
    UNION ALL SELECT ');';

    -- -----------------------------------------------------------------
    -- OUTPUT 2: the three submodule ids and the grant status
    -- -----------------------------------------------------------------
    SELECT v_generator_id AS `generator`,
           v_approvals_id AS `approvals`,
           v_master_id    AS `master_items`,
           0              AS `grant_rows_created`,
           'NO grants created by design. Review OUTPUT 3 and run only the lines you want.' AS `note`;

    -- -----------------------------------------------------------------
    -- OUTPUT 3: SUGGESTED grants, COMMENTED OUT.
    --
    -- Derived from whoever already holds the existing BOM Correction Tool
    -- submodule under module 4. That is a starting point for review, NOT
    -- a recommendation — those people were granted a different feature
    -- for different reasons.
    --
    -- The three lists should differ:
    --   GENERATOR     engineering, whoever builds BOMs
    --   APPROVALS     the reviewers and approvers
    --   MASTER ITEMS  the smallest list — editing a master item silently
    --                 changes what EVERY future BOM generates
    --
    -- Note: module_capablity.role_id holds a USER id, not a role id.
    -- -----------------------------------------------------------------
    SELECT CONCAT('-- Suggested grants for review. Uncomment the lines you want, then run them.')
             AS `3. SUGGESTED GRANTS (COMMENTED OUT - REVIEW BEFORE RUNNING)`
    UNION ALL
    SELECT CONCAT('-- Source list: users holding submodule ',
                  COALESCE(CAST(v_seed_submodule AS CHAR), 'n/a'),
                  ' under module ', v_module_id, '.')
    UNION ALL
    SELECT '--'
    UNION ALL
    SELECT CONCAT(
        '-- INSERT INTO `module_capablity` (`acessid`,`role_id`,`moduleid`,`submoduleid`,',
        '`submodule_access`,`madd`,`medit`,`mremove`,`addedOn`,`upadtedOn`) VALUES (0, ',
        mc.`role_id`, ', ', v_module_id, ', <SUBMODULE_ID>, 1, 1, 1, 0, NOW(), NOW());',
        '   -- user ', mc.`role_id`,
        COALESCE(CONCAT(' = ', u.`first_name`, ' ', u.`last_name`), ' (not found in system_users)')
    )
      FROM `module_capablity` mc
      LEFT JOIN `system_users` u ON u.`user_id` = mc.`role_id`
     WHERE mc.`moduleid` = v_module_id
       AND mc.`submoduleid` = v_seed_submodule
       AND mc.`submodule_access` = 1
    UNION ALL
    SELECT CONCAT('-- Replace <SUBMODULE_ID> with ', v_generator_id,
                  ' (generator), ', v_approvals_id, ' (approvals) or ',
                  v_master_id, ' (master items).')
    UNION ALL
    SELECT '-- Until at least one grant exists, the module generates and views BOMs but no one can approve.';

END $$

DELIMITER ;

CALL `abom_install_permissions`();

DROP PROCEDURE `abom_install_permissions`;
