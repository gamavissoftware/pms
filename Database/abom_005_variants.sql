-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — BUILD VARIANTS (install 005)
--  Target : MySQL 5.7+ / MariaDB 10.3+  (CodeIgniter 3 project)
--
--  Run AFTER Database/abom_001.sql, then run Database/abom_006_seed.sql.
--
--  WHY THIS EXISTS
--  ---------------
--  The module was built from TWO reference BOMs (DF-1826, DF-1827) and
--  scoped every master item to a PLC FAMILY. Generation was therefore
--  "every active item of the detected family", which is correct only
--  while one family means one build.
--
--  The nine SPM1200L / SPM1250P reference BOMs now loaded (DF-1723,
--  DF-1770, DF-1778, DF-1808, DF-1826, DF-1827, DF-1855, DF-1858,
--  DF-1864) break that assumption outright. Inside the FX5 family alone
--  there are two incompatible servo lineages:
--
--     5-7 axis machines  MR-JE-300B / MR-JE-200B  + HG-SN motors
--       8 axis machines  MR-J4-350B / MR-J4-200B  + HG-JR motors
--
--  Flattened into one family catalogue, a 6-axis machine would generate
--  BOTH amplifier ranges and procurement would order twice the drives.
--  The iQ-R family splits three ways for the same reason (panel-mounted
--  10-axis, standalone 15-axis high speed, and the SPM1250P flow-meter
--  build with its remote head and high-speed counters).
--
--  So a BUILD VARIANT is introduced between the family and the item.
--  Detection stays data-driven and first-match-wins, exactly like
--  abom_plc_rule — engineering retunes it by editing rows, not by a
--  code deploy.
--
--  PURELY ADDITIVE to the 001 schema: two new tables, two new nullable
--  columns. No existing column is dropped, renamed or retyped, and
--  nothing outside the abom_ prefix is touched.
-- =====================================================================
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 12. BUILD VARIANT
--
--     A variant is one buildable configuration of a PLC family: which
--     amplifier range, which HMI, which panel layout. Items hang off a
--     variant; the family remains the CPU platform and still drives the
--     family badge and the PLC selection explanation.
--
--     default_j4_units / default_battery / default_panel_location shadow
--     the abom_plc_family columns of the same name. They have to live
--     here as well because they differ BETWEEN variants of one family:
--     DF-1770 (iQ-R) is 9 / 9 in a panel with the machine, DF-1826
--     (iQ-R) is 11 / 12 standalone.
-- ---------------------------------------------------------------------
CREATE TABLE `abom_variant` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `plc_family_id` TINYINT UNSIGNED NOT NULL,
  `code`          VARCHAR(24)  NOT NULL,
  `name`          VARCHAR(96)  NOT NULL,
  `description`   VARCHAR(255) NULL,
  `machine_model` VARCHAR(32)  NULL COMMENT 'NULL = applies to any model',
  `source_df`     VARCHAR(96)  NULL COMMENT 'Reference DFs this variant was extracted from',
  `default_j4_units`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `default_battery`        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `default_panel_location` VARCHAR(64) NULL,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_abom_variant_code` (`code`),
  KEY `ix_abom_variant_family` (`plc_family_id`),
  CONSTRAINT `fk_abom_variant_family` FOREIGN KEY (`plc_family_id`)
     REFERENCES `abom_plc_family`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 13. VARIANT SELECTION RULE
--
--     Same shape and the same evaluation contract as abom_plc_rule:
--     priority ASC, first match wins, NULL bound = unbounded. Evaluated
--     AFTER the family has been decided, so plc_family_id here is a
--     filter on the already-detected family rather than a second
--     detection.
--
--     A variant needs more than one row whenever its condition is an OR
--     (iQ-R high speed is "15 axes OR 180 PPM"), which is exactly why
--     the bounds are not columns on abom_variant itself.
-- ---------------------------------------------------------------------
CREATE TABLE `abom_variant_rule` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `priority`      SMALLINT UNSIGNED NOT NULL,
  `plc_family_id` TINYINT UNSIGNED NULL COMMENT 'NULL = any family',
  `machine_model` VARCHAR(32) NULL COMMENT 'NULL = any model',
  `min_axes`      SMALLINT NULL,
  `max_axes`      SMALLINT NULL,
  `min_speed`     SMALLINT NULL,
  `max_speed`     SMALLINT NULL,
  `motion_type`   ENUM('Intermittent','Continuous','ANY') NOT NULL DEFAULT 'ANY',
  `result_variant_id` SMALLINT UNSIGNED NOT NULL,
  `explanation`   VARCHAR(255) NOT NULL,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `ix_abom_vrule_priority` (`priority`),
  CONSTRAINT `fk_abom_vrule_variant` FOREIGN KEY (`result_variant_id`)
     REFERENCES `abom_variant`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- abom_item.variant_id
--
-- NULLABLE, and deliberately so. A NULL variant_id means "not yet
-- assigned to a build" and such an item is NEVER generated — see
-- Abom_item_model::get_by_variant(). That is the safe failure: an
-- unassigned item is left out of a BOM and shows up in the master-item
-- admin list as unassigned, rather than being silently ordered on every
-- machine.
--
-- No foreign key is added for the same reason the column is nullable:
-- abom_006_seed.sql re-seeds items and variants together inside one
-- transaction, and a FK here would force a delete order that the seed
-- would then have to work around. The integrity that matters (an item
-- pointing at a variant that exists) is enforced by the seed and by the
-- read path, which joins and drops orphans.
-- ---------------------------------------------------------------------
ALTER TABLE `abom_item`
  ADD COLUMN `variant_id` SMALLINT UNSIGNED NULL AFTER `plc_family_id`,
  ADD KEY `ix_abom_item_variant` (`variant_id`,`section_id`);

-- ---------------------------------------------------------------------
-- abom_bom.variant_id + abom_bom.variant_locked
--
-- Which variant a saved BOM was generated from, and whether the engineer
-- overrode the detected one. Without this a BOM re-opened after a
-- variant rule is retuned would silently re-detect to a different build.
-- ---------------------------------------------------------------------
ALTER TABLE `abom_bom`
  ADD COLUMN `variant_id`     SMALLINT UNSIGNED NULL AFTER `plc_family_locked`,
  ADD COLUMN `variant_locked` TINYINT(1) NOT NULL DEFAULT 0 AFTER `variant_id`,
  ADD KEY `ix_abom_bom_variant` (`variant_id`);

-- ---------------------------------------------------------------------
-- abom_bom_line.variant_code
--
-- Frozen onto the line for the same reason every other descriptive field
-- is frozen (001 section 8): a master-data edit must never mutate an
-- approved BOM, so the build the line came from is copied, not joined.
-- ---------------------------------------------------------------------
ALTER TABLE `abom_bom_line`
  ADD COLUMN `variant_code` VARCHAR(24) NULL AFTER `formula_code`;
